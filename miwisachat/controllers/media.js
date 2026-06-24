import fs from "fs"
import path from "path"
import { ChatMessage, GroupMessage } from "../models/index.js"
import { responseServerError } from "../constants.js"

async function serveMedia(req, res) {
    try {
        const { filename } = req.params
        const { user_id } = req.user

        // Buscar el mensaje que contiene este archivo en ChatMessage o GroupMessage
        const chatMessage = await ChatMessage.findOne({
            $or: [{ "attachment.url": filename }, { "attachment.thumbnail": filename }],
        }).populate("chat")

        const groupMessage = chatMessage
            ? null
            : await GroupMessage.findOne({
                  $or: [{ "attachment.url": filename }, { "attachment.thumbnail": filename }],
              }).populate("group")

        const message = chatMessage || groupMessage

        if (!message) {
            return res.status(404).json({ msg: "Archivo no encontrado" })
        }

        // Verificar que el usuario pertenece al chat o grupo
        if (chatMessage) {
            const chat = chatMessage.chat
            const isParticipant =
                chat?.participant_one?.toString() === user_id.toString() ||
                chat?.participant_two?.toString() === user_id.toString()
            if (!isParticipant) return res.status(403).json({ msg: "Acceso denegado" })
        } else if (groupMessage) {
            const group = groupMessage.group
            const isParticipant = group?.participants?.some((p) => p.toString() === user_id.toString())
            if (!isParticipant) return res.status(403).json({ msg: "Acceso denegado" })
        }

        // Determinar directorio según mimeType
        const mimeType = message.attachment?.mimeType || ""
        const isThumbnail = message.attachment?.thumbnail === filename
        const isVideo = mimeType.startsWith("video/")

        let filePath
        if (isVideo || isThumbnail) {
            filePath = path.join("uploads/videos", filename)
        } else {
            filePath = path.join("uploads/files", filename)
        }

        if (!fs.existsSync(filePath)) {
            return res.status(404).json({ msg: "Archivo no encontrado en disco" })
        }

        res.setHeader("Content-Type", mimeType || "application/octet-stream")
        fs.createReadStream(filePath).pipe(res)
    } catch (error) {
        responseServerError(res, error)
    }
}

export const MediaController = { serveMedia }
