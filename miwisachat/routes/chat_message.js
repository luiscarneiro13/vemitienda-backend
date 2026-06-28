import express from "express"
import multer from "multer"
import fs from "fs"
import { randomUUID } from "crypto"
import { ChatMessageController, videoRateLimiter, fileRateLimiter } from "../controllers/index.js"
import multiparty from "connect-multiparty"
import { mdAuth } from "../middlewares/index.js"

// Garantizar que los directorios existen antes de que multer intente usarlos
fs.mkdirSync("uploads/videos", { recursive: true })
fs.mkdirSync("uploads/files", { recursive: true })

const mdUpload = multiparty({ uploadDir: "./uploads/images" })

const videoUpload = multer({
    storage: multer.diskStorage({
        destination: (req, file, cb) => cb(null, "uploads/videos/"),
        filename: (req, file, cb) => cb(null, `tmp_${randomUUID()}`),
    }),
    limits: { fileSize: 50 * 1024 * 1024 },
})

const fileUpload = multer({
    storage: multer.diskStorage({
        destination: (req, file, cb) => cb(null, "uploads/files/"),
        filename: (req, file, cb) => cb(null, `tmp_${randomUUID()}`),
    }),
    limits: { fileSize: 25 * 1024 * 1024 },
})

function wrapMulter(upload, field) {
    return (req, res, next) => {
        upload.single(field)(req, res, (err) => {
            if (!err) return next()
            if (err.code === "LIMIT_FILE_SIZE") {
                return res.status(400).json({ msg: "El archivo excede el tamaño máximo permitido" })
            }
            return res.status(400).json({ msg: err.message || "Error al procesar el archivo" })
        })
    }
}

const api = express.Router()

// Endpoints existentes (sin modificar)
api.post("/chat/message", [mdAuth.asureAuth], ChatMessageController.sendText)
api.post("/chat/message/image", [mdAuth.asureAuth, mdUpload], ChatMessageController.sendImage)
api.get("/chat/message/:chat_id", [mdAuth.asureAuth], ChatMessageController.getAll)
api.get("/chat/message/total/:chat_id", [mdAuth.asureAuth], ChatMessageController.getTotalMessages)
api.get("/chat/message/last/:chat_id", [mdAuth.asureAuth], ChatMessageController.getLastMessage)
api.get("/chat/message/markAllAsRead/:chat_id", [mdAuth.asureAuth], ChatMessageController.markAllAsRead)

// Nuevos endpoints
api.delete("/chat/message/delete", [mdAuth.asureAuth], ChatMessageController.deleteMessages)
api.post("/chat/message/forward", [mdAuth.asureAuth], ChatMessageController.forwardMessages)
api.post("/chat/message/link", [mdAuth.asureAuth], ChatMessageController.sendLink)
api.post("/chat/message/video", [mdAuth.asureAuth, videoRateLimiter, wrapMulter(videoUpload, "video")], ChatMessageController.sendVideo)
api.post("/chat/message/file", [mdAuth.asureAuth, fileRateLimiter, wrapMulter(fileUpload, "file")], ChatMessageController.sendFile)

export const chatMessageRoutes = api
