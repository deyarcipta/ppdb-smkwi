const { Client, LocalAuth } = require("whatsapp-web.js");
const express = require("express");
const qrcode = require("qrcode-terminal");

const app = express();
app.use(express.json());

// Configuration
const config = {
    PORT: process.env.PORT || 3000,
    MAX_RECONNECT_ATTEMPTS: 5,
    RECONNECT_INTERVAL: 10000,
    HEALTH_CHECK_INTERVAL: 300000, // 5 minutes
};

// State management
let client = null;
let isConnected = false;
let reconnectAttempts = 0;
let isInitializing = false;

function initializeWhatsApp() {
    if (isInitializing) {
        console.log("ℹ️ Initialization already in progress...");
        return;
    }

    if (client && client.info) {
        console.log("ℹ️ Client already connected, skipping reinitialization");
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
                headless: true,
                args: ["--no-sandbox", "--disable-setuid-sandbox"],
                executablePath:
                    "C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe",
            },
            takeoverOnConflict: true,
            takeoverTimeoutMs: 0,
            restartOnAuthFail: true,
        });

        client.on("qr", (qr) => {
            console.log("📱 QR CODE - SCAN SEKARANG:");
            qrcode.generate(qr, { small: true });
            console.log("➡️ WhatsApp > Menu > Linked Devices > Scan QR Code");
            isConnected = false;
            reconnectAttempts = 0;
            isInitializing = false;
        });

        client.on("ready", () => {
            console.log("✅ WHATSAPP BOT READY & CONNECTED!");
            isConnected = true;
            reconnectAttempts = 0;
            isInitializing = false;
        });

        client.on("authenticated", () => {
            console.log("✅ AUTHENTICATED - Session tersimpan!");
            isInitializing = false;
        });

        client.on("auth_failure", (msg) => {
            console.error("❌ AUTH FAILED:", msg);
            isConnected = false;
            isInitializing = false;
            reconnectAttempts++;

            if (reconnectAttempts <= config.MAX_RECONNECT_ATTEMPTS) {
                console.log(
                    `🔄 Attempting reconnect (${reconnectAttempts}/${config.MAX_RECONNECT_ATTEMPTS}) in 10s...`
                );
                setTimeout(
                    () => initializeWhatsApp(),
                    config.RECONNECT_INTERVAL
                );
            } else {
                console.error(
                    "❌ Max reconnection attempts reached. Manual intervention required."
                );
            }
        });

        client.on("disconnected", (reason) => {
            console.log("❌ DISCONNECTED:", reason);
            isConnected = false;
            isInitializing = false;
            reconnectAttempts++;

            if (reconnectAttempts <= config.MAX_RECONNECT_ATTEMPTS) {
                console.log(
                    `🔄 Auto-reconnecting (${reconnectAttempts}/${config.MAX_RECONNECT_ATTEMPTS}) in 5s...`
                );
                setTimeout(() => initializeWhatsApp(), 5000);
            } else {
                console.error(
                    "❌ Max reconnection attempts reached. Please check WhatsApp connection."
                );
            }
        });

        client.on("error", (error) => {
            console.error("❌ CLIENT ERROR:", error);
            isInitializing = false;
        });

        client.initialize();
    } catch (error) {
        console.error("❌ Initialization error:", error);
        isInitializing = false;
        setTimeout(() => initializeWhatsApp(), config.RECONNECT_INTERVAL);
    }
}

// API Routes (same as before with improvements)
app.post("/send-message", async (req, res) => {
    if (!isConnected) {
        initializeWhatsApp();
        return res.status(503).json({
            success: false,
            error: "WhatsApp bot not connected",
            note: "Reconnection in progress",
        });
    }

    try {
        const { phone, message } = req.body;
        const formattedPhone = phone.replace(/\D/g, "");
        const chatId = formattedPhone + "@c.us";

        const sentMessage = await client.sendMessage(chatId, message);

        res.json({
            success: true,
            messageId: sentMessage.id._serialized,
        });
    } catch (error) {
        console.error("❌ Send message error:", error);
        res.status(500).json({
            success: false,
            error: error.message,
        });
    }
});

app.get("/health", (req, res) => {
    res.json({
        status: isConnected ? "connected" : "disconnected",
        ready: isConnected,
        reconnectAttempts: reconnectAttempts,
        isInitializing: isInitializing,
        timestamp: new Date().toISOString(),
    });
});

// Start server
app.listen(config.PORT, () => {
    console.log(
        `🚀 WhatsApp API Server running on http://localhost:${config.PORT}`
    );
    initializeWhatsApp();
});

// Periodic health check
setInterval(() => {
    if (
        !isConnected &&
        reconnectAttempts < config.MAX_RECONNECT_ATTEMPTS &&
        !isInitializing
    ) {
        console.log("🔄 Periodic health check - attempting reconnect...");
        initializeWhatsApp();
    }
}, config.HEALTH_CHECK_INTERVAL);

// Graceful shutdown
process.on("SIGINT", () => {
    console.log("🛑 Shutting down gracefully...");
    if (client) {
        client.destroy();
    }
    process.exit(0);
});
