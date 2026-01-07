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

// ================= INIT FUNCTION =================
function initializeWhatsApp() {
    if (isInitializing) {
        console.log("ℹ️ Initialization already in progress...");
        return;
    }

    if (client && client.info) {
        console.log("ℹ️ Client already connected");
        return;
    }

    isInitializing = true;
    console.log("🔄 Initializing WhatsApp client...");

    try {
        client = new Client({
            authStrategy: new LocalAuth({
                dataPath: "./sessions",
                clientId: "ppdb-bot",
            }),
            puppeteer: {
                headless: "new",
                args: ["--no-sandbox", "--disable-setuid-sandbox"],
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
            console.log("✅ AUTHENTICATED - Session saved");
            isInitializing = false;
        });

        client.on("ready", () => {
            console.log("✅ WHATSAPP BOT READY");
            isConnected = true;
            lastQr = null;
            reconnectAttempts = 0;
            isInitializing = false;
        });

        client.on("auth_failure", (msg) => {
            console.error("❌ AUTH FAILURE:", msg);
            isConnected = false;
            isInitializing = false;
            reconnectAttempts++;

            if (reconnectAttempts <= config.MAX_RECONNECT_ATTEMPTS) {
                console.log(
                    `🔄 Reconnecting (${reconnectAttempts}/${config.MAX_RECONNECT_ATTEMPTS})...`
                );
                setTimeout(initializeWhatsApp, config.RECONNECT_INTERVAL);
            }
        });

        client.on("disconnected", (reason) => {
            console.log("❌ DISCONNECTED:", reason);
            isConnected = false;
            isInitializing = false;
            reconnectAttempts++;

            if (reconnectAttempts <= config.MAX_RECONNECT_ATTEMPTS) {
                console.log(
                    `🔄 Auto reconnect (${reconnectAttempts}/${config.MAX_RECONNECT_ATTEMPTS})`
                );
                setTimeout(initializeWhatsApp, 5000);
            }
        });

        client.on("error", (err) => {
            console.error("❌ CLIENT ERROR:", err);
            isInitializing = false;
        });

        client.initialize();
    } catch (err) {
        console.error("❌ INIT ERROR:", err);
        isInitializing = false;
        setTimeout(initializeWhatsApp, config.RECONNECT_INTERVAL);
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
    if (!isConnected) {
        return res.status(503).json({
            success: false,
            message: "WhatsApp belum terhubung",
        });
    }

    try {
        const { phone, message } = req.body;

        // 1️⃣ Bersihkan nomor
        const formattedPhone = phone.replace(/\D/g, "");

        // 2️⃣ Resolve number ke WhatsApp ID
        const numberId = await client.getNumberId(formattedPhone);

        if (!numberId) {
            return res.status(404).json({
                success: false,
                message: "Nomor WhatsApp tidak terdaftar",
            });
        }

        // 3️⃣ Kirim pesan
        const sent = await client.sendMessage(numberId._serialized, message);

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
setInterval(() => {
    if (
        !isConnected &&
        reconnectAttempts < config.MAX_RECONNECT_ATTEMPTS &&
        !isInitializing
    ) {
        console.log("🔄 Health check reconnect...");
        initializeWhatsApp();
    }
}, config.HEALTH_CHECK_INTERVAL);

// ================= GRACEFUL SHUTDOWN =================
process.on("SIGINT", async () => {
    console.log("🛑 Shutting down...");
    if (client) await client.destroy();
    process.exit(0);
});
