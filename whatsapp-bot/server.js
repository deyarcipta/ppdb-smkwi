const { Client, LocalAuth } = require("whatsapp-web.js");
const express = require("express");
const QRCode = require("qrcode");
const cors = require("cors");

const app = express();
app.use(express.json());
app.use(cors());

// ================= CONFIG =================
const config = {
    PORT: process.env.PORT || 3000,
    MAX_RECONNECT_ATTEMPTS: 5,
    RECONNECT_INTERVAL: 10000,
    HEALTH_CHECK_INTERVAL: 300000, // 5 menit
};

// ================= STATE =================
let client = null;
let isConnected = false;
let reconnectAttempts = 0;
let isInitializing = false;
let lastQr = null;

// ================= UTILITIES =================

/**
 * Membungkus Promise dengan batas waktu (timeout)
 */
function withTimeout(promise, ms, errorMessage = "Timeout") {
    let timeoutId;
    const timeoutPromise = new Promise((_, reject) => {
        timeoutId = setTimeout(() => {
            reject(new Error(errorMessage));
        }, ms);
    });
    return Promise.race([
        promise.then((res) => {
            clearTimeout(timeoutId);
            return res;
        }),
        timeoutPromise,
    ]);
}

/**
 * Menghancurkan client WhatsApp yang aktif dan membersihkan memori/Puppeteer
 */
async function destroyClient() {
    if (client) {
        console.log("🔄 Menghancurkan instance client WhatsApp lama...");
        try {
            // Berikan timeout 10 detik agar tidak menggantung jika browser sudah crash
            await withTimeout(
                client.destroy(),
                10000,
                "Timeout saat menutup browser WhatsApp",
            );
            console.log(
                "✅ Instance client WhatsApp lama berhasil dihancurkan",
            );
        } catch (err) {
            console.error(
                "❌ Gagal menghancurkan client WhatsApp lama:",
                err.message,
            );
        }
        client = null;
    }
    isConnected = false;
}

// ================= INIT FUNCTION =================
async function initializeWhatsApp() {
    if (isInitializing) {
        console.log("ℹ️ Inisialisasi sedang berjalan...");
        return;
    }

    isInitializing = true;
    console.log("🔄 Menginisialisasi client WhatsApp...");

    try {
        // Hancurkan client lama terlebih dahulu untuk mencegah kebocoran memori/proses Chromium
        await destroyClient();

        isInitializing = true; // Set kembali ke true setelah destroyClient() membersihkannya

        client = new Client({
            authStrategy: new LocalAuth({
                dataPath: "./sessions",
                clientId: "ppdb-bot",
            }),
            puppeteer: {
                headless: true,
                args: [
                    "--no-sandbox", // WAJIB untuk CentOS/Linux
                    "--disable-setuid-sandbox", // WAJIB untuk CentOS/Linux
                    "--disable-dev-shm-usage", // Mencegah crash pada RAM terbatas/Docker
                    "--disable-accelerated-2d-canvas",
                    "--no-first-run",
                    "--no-zygote",
                    "--disable-gpu", // Tidak diperlukan di server headless
                    "--disable-web-security",
                    "--disable-features=VizDisplayCompositor",
                    "--disable-software-rasterizer",
                ],
            },
            takeoverOnConflict: true,
            takeoverTimeoutMs: 0,
            restartOnAuthFail: true,
        });

        // ================= EVENTS =================

        client.on("qr", async (qr) => {
            console.log("📱 QR RECEIVED");

            try {
                lastQr = await QRCode.toDataURL(qr);
            } catch (err) {
                console.error("❌ QR Convert Error:", err);
                lastQr = null;
            }

            isConnected = false;
            reconnectAttempts = 0;
            isInitializing = false;
        });

        client.on("authenticated", () => {
            console.log("✅ AUTHENTICATED - Session disimpan");
            isInitializing = false;
        });

        client.on("ready", () => {
            console.log("✅ WHATSAPP BOT READY");
            isConnected = true;
            lastQr = null;
            reconnectAttempts = 0;
            isInitializing = false;
        });

        client.on("auth_failure", async (msg) => {
            console.error("❌ AUTH FAILURE:", msg);
            isInitializing = false;

            await destroyClient();
            reconnectAttempts++;

            if (reconnectAttempts <= config.MAX_RECONNECT_ATTEMPTS) {
                console.log(
                    `🔄 Mencoba koneksi kembali (${reconnectAttempts}/${config.MAX_RECONNECT_ATTEMPTS}) dalam ${config.RECONNECT_INTERVAL / 1000}s...`,
                );
                setTimeout(initializeWhatsApp, config.RECONNECT_INTERVAL);
            } else {
                console.error(
                    "❌ Batas percobaan koneksi ulang terlampaui. Menghentikan proses demi PM2 recovery...",
                );
                process.exit(1);
            }
        });

        client.on("disconnected", async (reason) => {
            console.log("❌ DISCONNECTED:", reason);
            isInitializing = false;

            await destroyClient();
            reconnectAttempts++;

            if (reconnectAttempts <= config.MAX_RECONNECT_ATTEMPTS) {
                console.log(
                    `🔄 Koneksi ulang otomatis (${reconnectAttempts}/${config.MAX_RECONNECT_ATTEMPTS}) dalam 5s...`,
                );
                setTimeout(initializeWhatsApp, 5000);
            } else {
                console.error(
                    "❌ Batas percobaan koneksi ulang terlampaui. Menghentikan proses demi PM2 recovery...",
                );
                process.exit(1);
            }
        });

        client.on("error", (err) => {
            console.error("❌ CLIENT ERROR:", err);
            // Jangan langsung crash, biarkan healthcheck atau event disconnected menanganinya
            isInitializing = false;
        });

        client.initialize();
    } catch (err) {
        console.error("❌ INIT ERROR:", err);
        isInitializing = false;

        reconnectAttempts++;
        if (reconnectAttempts <= config.MAX_RECONNECT_ATTEMPTS) {
            setTimeout(initializeWhatsApp, config.RECONNECT_INTERVAL);
        } else {
            console.error(
                "❌ Gagal inisialisasi awal beberapa kali. Menghentikan proses demi PM2...",
            );
            process.exit(1);
        }
    }
}

// ================= API ROUTES =================

// 🔹 Ambil QR untuk Laravel
app.get("/qr", (req, res) => {
    if (isConnected) {
        return res.json({
            status: "connected",
            qr: null,
        });
    }

    if (!lastQr) {
        return res.json({
            status: "waiting",
            qr: null,
        });
    }

    res.json({
        status: "qr",
        qr: lastQr,
    });
});

// 🔹 Kirim pesan
app.post("/send-message", async (req, res) => {
    if (!client || !isConnected) {
        return res.status(503).json({
            success: false,
            message: "WhatsApp belum siap atau terputus",
        });
    }

    try {
        const { phone, message } = req.body;

        // Cek status client dengan timeout 5 detik
        try {
            const state = await withTimeout(
                client.getState(),
                5000,
                "Timeout memeriksa status koneksi",
            );

            if (state !== "CONNECTED") {
                return res.status(503).json({
                    success: false,
                    message: `WhatsApp state: ${state}`,
                });
            }
        } catch (e) {
            console.error("❌ Gagal memeriksa state client:", e.message);
            return res.status(503).json({
                success: false,
                message: `Session WhatsApp tidak aktif atau tidak merespon: ${e.message}`,
            });
        }

        // Bersihkan nomor
        const formattedPhone = phone.replace(/\D/g, "");

        // Cari nomor WA dengan timeout 10 detik
        const numberId = await withTimeout(
            client.getNumberId(formattedPhone),
            10000,
            "Timeout saat mencari nomor WhatsApp",
        );

        if (!numberId) {
            return res.status(404).json({
                success: false,
                message: "Nomor WhatsApp tidak terdaftar",
            });
        }

        // Kirim pesan dengan timeout 15 detik
        const sent = await withTimeout(
            client.sendMessage(numberId._serialized, message),
            15000,
            "Timeout saat mengirim pesan WhatsApp",
        );

        res.json({
            success: true,
            messageId: sent.id._serialized,
        });
    } catch (err) {
        console.error("❌ SEND ERROR:", err);

        res.status(500).json({
            success: false,
            error: err.message,
        });
    }
});

// 🔹 Health check
app.get("/health", (req, res) => {
    res.json({
        connected: isConnected,
        reconnectAttempts,
        isInitializing,
        timestamp: new Date().toISOString(),
    });
});

// ================= SERVER START =================
app.listen(config.PORT, () => {
    console.log(`🚀 WhatsApp API running at http://localhost:${config.PORT}`);
    initializeWhatsApp();
});

// ================= PERIODIC CHECK =================
setInterval(async () => {
    console.log("🔍 Menjalankan pemeriksaan kesehatan berkala...");

    let isHealthy = false;
    if (client && isConnected) {
        try {
            // Periksa status state secara aktif dengan timeout 5 detik
            const state = await withTimeout(
                client.getState(),
                5000,
                "Timeout status state",
            );
            if (state === "CONNECTED") {
                isHealthy = true;
            } else {
                console.warn(
                    `⚠️ Status state WhatsApp bukan CONNECTED: ${state}`,
                );
            }
        } catch (err) {
            console.error("❌ Gagal memeriksa status WhatsApp:", err.message);
        }
    }

    if (!isHealthy) {
        console.warn("⚠️ Client tidak sehat atau terputus.");

        if (reconnectAttempts >= config.MAX_RECONNECT_ATTEMPTS) {
            console.error(
                "❌ Batas maksimal percobaan koneksi ulang terlampaui. Menghentikan proses agar PM2 melakukan restart...",
            );
            process.exit(1);
        }

        console.log("🔄 Mencoba menginisialisasi ulang client WhatsApp...");
        initializeWhatsApp();
    } else {
        console.log("✅ Kondisi client sehat.");
        reconnectAttempts = 0; // Reset hitungan jika sehat
    }
}, config.HEALTH_CHECK_INTERVAL);

// ================= GRACEFUL SHUTDOWN =================
process.on("SIGINT", async () => {
    console.log("🛑 Mematikan server...");
    await destroyClient();
    process.exit(0);
});
