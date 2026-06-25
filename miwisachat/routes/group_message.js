import express from "express"
import multer from "multer"
import fs from "fs"
import { GroupMessageController, videoRateLimiter, fileRateLimiter } from "../controllers/index.js"
import multiparty from "connect-multiparty"
import { mdAuth } from "../middlewares/index.js"

// Garantizar que los directorios existen antes de que multer intente usarlos
fs.mkdirSync("uploads/videos", { recursive: true })
fs.mkdirSync("uploads/files", { recursive: true })

const mdUpload = multiparty({ uploadDir: "./uploads/images" })

const videoUpload = multer({
    storage: multer.diskStorage({
        destination: (req, file, cb) => cb(null, "uploads/videos/"),
        filename: (req, file, cb) => cb(null, `tmp_${Date.now()}_${Math.random().toString(36).substring(2, 9)}`),
    }),
    limits: { fileSize: 50 * 1024 * 1024 },
})

const fileUpload = multer({
    storage: multer.diskStorage({
        destination: (req, file, cb) => cb(null, "uploads/files/"),
        filename: (req, file, cb) => cb(null, `tmp_${Date.now()}_${Math.random().toString(36).substring(2, 9)}`),
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
api.post("/group/message", [mdAuth.asureAuth], GroupMessageController.sendText)
api.post("/group/message/image", [mdAuth.asureAuth, mdUpload], GroupMessageController.sendImage)
api.get("/group/message/:group_id", [mdAuth.asureAuth], GroupMessageController.getAll)
api.get("/group/message/total/:group_id", [mdAuth.asureAuth], GroupMessageController.getTotalMessages)
api.get("/group/message/last/:group_id", [mdAuth.asureAuth], GroupMessageController.getLastMessage)

// Nuevos endpoints
api.delete("/group/message/delete", [mdAuth.asureAuth], GroupMessageController.deleteMessages)
api.post("/group/message/link", [mdAuth.asureAuth], GroupMessageController.sendLink)
api.post("/group/message/video", [mdAuth.asureAuth, videoRateLimiter, wrapMulter(videoUpload, "video")], GroupMessageController.sendVideo)
api.post("/group/message/file", [mdAuth.asureAuth, fileRateLimiter, wrapMulter(fileUpload, "file")], GroupMessageController.sendFile)

export const groupMessageRoutes = api
