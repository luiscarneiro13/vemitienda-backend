import fs from "fs"
import path from "path"
import { randomUUID as uuidv4 } from "crypto"
import { fileTypeFromFile } from "file-type"
import sanitizeHtml from "sanitize-html"
import AdmZip from "adm-zip"
import validator from "validator"
import mongoose from "mongoose"
import { responseServerError } from "../constants.js"
import { Group, GroupMessage } from "../models/index.js"
import { getFilePath, io, getPublicUrl, getVideoDuration, generateThumbnail } from "../utils/index.js"
import { getOtherParticipants, sendPushNotification } from "../utils/index.js"

const ALLOWED_VIDEO_MIMES = ["video/mp4", "video/quicktime", "video/x-msvideo", "video/webm"]

const ALLOWED_FILE_MIMES = [
    "application/pdf",
    "application/msword",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "application/vnd.ms-excel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "application/vnd.ms-powerpoint",
    "application/vnd.openxmlformats-officedocument.presentationml.presentation",
    "application/zip",
    "text/plain",
]

const FORBIDDEN_EXTENSIONS = [".exe", ".sh", ".bat", ".php", ".js", ".py"]

function sanitizeOriginalName(name) {
    return name.replace(/[^a-zA-Z0-9.\-]/g, "_").substring(0, 255)
}

function hasForbiddenExtension(filename) {
    return FORBIDDEN_EXTENSIONS.includes(path.extname(filename).toLowerCase())
}

function checkZipBomb(filePath, maxRatio = 10) {
    try {
        const zip = new AdmZip(filePath)
        const entries = zip.getEntries()
        const { size: compressedSize } = fs.statSync(filePath)
        let totalUncompressed = 0
        for (const entry of entries) {
            totalUncompressed += entry.header.size
            if (totalUncompressed > compressedSize * maxRatio) return false
        }
        return true
    } catch {
        return false
    }
}

async function sendText(req, res) {

    try {

        const { group_id, message, replyTo } = req.body
        const { user_id } = req.user

        const group_message = new GroupMessage({
            group: group_id,
            user: user_id,
            message,
            type: "TEXT",
            ...(replyTo && { replyTo }),
        })

        const dataStorage = await group_message.save()
        await dataStorage.populate(["user"])

        const group = await Group.findById({ _id: group_id }).populate(["creator", "participants"])

        const otherUsers = getOtherParticipants(user_id, group.participants)

        if (otherUsers) {
            const senderName = dataStorage.user?.firstname
                ? `${dataStorage.user.firstname} ${dataStorage.user.lastname}`
                : dataStorage.user?.email
            const senderAvatar = getPublicUrl(dataStorage.user?.avatar)
            const notification = {
                title: `${senderName} en ${group.name}`,
                body: message,
                richContent: senderAvatar ? { image: senderAvatar } : undefined,
            }
            const notifPromises = otherUsers
                .filter(u => u?.expo_token)
                .map(u => sendPushNotification(u.expo_token, notification))
            if (notifPromises.length > 0) await Promise.all(notifPromises)
        }

        io.sockets.in(group_id).emit("message", dataStorage)
        io.sockets.in(`${group_id}_notify`).emit("message_notify", dataStorage)

        res.status(200).send(dataStorage)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function sendImage(req, res) {

    try {

        const { group_id } = req.body
        const { user_id } = req.user

        const message = getFilePath(req.files.image)

        const group_message = new GroupMessage({
            group: group_id,
            user: user_id,
            message,
            type: "IMAGE"
        })

        const messageStorage = await group_message.save()
        const data = await messageStorage.populate(["user", "group"]);

        const group = await Group.findOne({ _id: group_id }).populate(["creator", "participants"])

        // Verifico si el usuario tiene un token de expo para enviarle notificación:
        const otherUsers = getOtherParticipants(user_id, group.participants)

        if (otherUsers) {
            const senderName = data.user?.firstname
                ? `${data.user.firstname} ${data.user.lastname}`
                : data.user?.email
            const senderAvatar = getPublicUrl(data.user?.avatar)
            const notification = {
                title: `${senderName} en ${group.name}`,
                body: '📷 Imagen',
                richContent: senderAvatar ? { image: senderAvatar } : undefined,
            }
            const notifPromises = otherUsers
                .filter(u => u?.expo_token)
                .map(u => sendPushNotification(u.expo_token, notification))
            if (notifPromises.length > 0) await Promise.all(notifPromises)
        }

        //Para emitir el mensaje en los chats. Aquí se especifica sobre que chat se va a emitir
        io.sockets.in(group_id).emit("message", data)

        //Esto es para enviar un evento de nuevo mensaje
        io.sockets.in(`${group_id}_notify`).emit("message_notify", data)

        res.status(201).send(messageStorage)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function getAll(req, res) {

    try {

        const { group_id } = req.params
        const { user_id } = req.user
        const page = parseInt(req.query.page, 10) || 1
        const limit = Math.min(parseInt(req.query.limit, 10) || 20, 50)

        const group = await Group.findOne({ _id: group_id, participants: user_id })
        if (!group) return res.status(403).json({ msg: "Acceso denegado" })

        const total = await GroupMessage.countDocuments({ group: group_id })
        const totalPages = Math.max(Math.ceil(total / limit), 1)
        const currentPage = Math.min(page, totalPages)
        const skip = (currentPage - 1) * limit

        const messages = await GroupMessage.find({ group: group_id })
            .sort({ createdAt: -1 })
            .skip(skip)
            .limit(limit)
            .populate([{ path: "user", select: "firstname lastname avatar email" }])

        res.status(200).json({ total, totalPages, page: currentPage, limit, messages })

    } catch (error) {
        responseServerError(res, error)
    }

}

async function getTotalMessages(req, res) {

    try {

        const { group_id } = req.params

        const total = await GroupMessage.countDocuments({ group: group_id });

        res.status(200).send(total);

    } catch (error) {
        responseServerError(res, error)
    }

}

async function getLastMessage(req, res) {

    try {

        const { group_id } = req.params

        const response = await GroupMessage.findOne({ group: group_id }).sort({ createdAt: -1 })

        if (response) {
            await response.populate(["group", "user"]);
        }

        res.status(200).send(response || {});

    } catch (error) {
        responseServerError(res, error)
    }
}

async function markAllAsRead(req, res) {
    try {
        const { group_id } = req.params
        const { user_id } = req.user

        const group = await Group.findOne({ _id: group_id, participants: user_id })
        if (!group) return res.status(403).json({ msg: "Acceso denegado" })

        const result = await GroupMessage.updateMany(
            {
                group: group_id,
                user: { $ne: user_id },
                readBy: { $ne: user_id }
            },
            { $push: { readBy: user_id } }
        )

        res.status(200).send({ updated: result.modifiedCount })
    } catch (error) {
        responseServerError(res, error)
    }
}

async function sendLink(req, res) {
    try {
        const { group_id, message = "", linkPreview } = req.body
        const { user_id } = req.user

        if (!group_id || !mongoose.isValidObjectId(group_id)) {
            return res.status(400).json({ msg: "group_id inválido" })
        }

        const group = await Group.findOne({ _id: group_id, participants: user_id }).populate(["creator", "participants"])
        if (!group) return res.status(403).json({ msg: "No perteneces a este grupo" })

        if (!linkPreview?.url || !validator.isURL(linkPreview.url, { protocols: ["http", "https"], require_protocol: true })) {
            return res.status(400).json({ msg: "linkPreview.url es requerido y debe ser una URL http/https válida" })
        }

        const sanitizedPreview = {
            ...linkPreview,
            title: sanitizeHtml(linkPreview.title || "", { allowedTags: [], allowedAttributes: {} }),
            description: sanitizeHtml(linkPreview.description || "", { allowedTags: [], allowedAttributes: {} }),
        }

        const group_message = new GroupMessage({
            group: group_id,
            user: user_id,
            message: message || "",
            type: "LINK",
            linkPreview: sanitizedPreview,
        })

        const messageStorage = await group_message.save()
        const data = await messageStorage.populate(["user"])

        io.sockets.in(group_id).emit("message", data)
        io.sockets.in(`${group_id}_notify`).emit("message_notify", data)

        res.status(200).json(data)
    } catch (error) {
        responseServerError(res, error)
    }
}

async function sendVideo(req, res) {
    const tempPath = req.file?.path

    try {
        const { group_id } = req.body
        const { user_id } = req.user

        if (!req.file) return res.status(400).json({ msg: "Video requerido" })

        if (!group_id || !mongoose.isValidObjectId(group_id)) {
            await fs.promises.unlink(tempPath)
            return res.status(400).json({ msg: "group_id inválido" })
        }

        const group = await Group.findOne({ _id: group_id, participants: user_id }).populate(["creator", "participants"])
        if (!group) {
            await fs.promises.unlink(tempPath)
            return res.status(403).json({ msg: "No perteneces a este grupo" })
        }

        const detected = await fileTypeFromFile(tempPath)
        if (!detected || !ALLOWED_VIDEO_MIMES.includes(detected.mime) || detected.mime === "image/svg+xml") {
            await fs.promises.unlink(tempPath)
            return res.status(400).json({ msg: "Tipo de archivo no permitido. Solo se aceptan video/mp4, video/quicktime, video/x-msvideo y video/webm" })
        }

        const uuid = uuidv4()
        const ext = detected.ext
        const finalFilename = `${uuid}.${ext}`
        const finalPath = path.join("uploads/videos", finalFilename)

        await fs.promises.rename(tempPath, finalPath)

        const duration = await getVideoDuration(finalPath).catch(() => 0)

        const thumbName = `${uuid}_thumb.jpg`
        const originalName = sanitizeOriginalName(req.file.originalname)

        const group_message = new GroupMessage({
            group: group_id,
            user: user_id,
            message: finalFilename,
            type: "VIDEO",
            attachment: {
                url: finalFilename,
                mimeType: detected.mime,
                thumbnail: null,
                duration,
                originalName,
                size: req.file.size,
            },
        })

        const messageStorage = await group_message.save()
        const data = await messageStorage.populate(["user"])

        io.sockets.in(group_id).emit("message", data)
        io.sockets.in(`${group_id}_notify`).emit("message_notify", data)

        res.status(201).json(data)

        generateThumbnail(finalPath, "uploads/videos", thumbName)
            .then(async () => {
                await GroupMessage.findByIdAndUpdate(messageStorage._id, {
                    "attachment.thumbnail": thumbName
                })
                io.sockets.in(group_id.toString()).emit("thumbnail_ready", {
                    messageId: messageStorage._id.toString(),
                    thumbnail: thumbName
                })
            })
            .catch(() => {})
    } catch (error) {
        if (tempPath) fs.promises.unlink(tempPath).catch(() => {})
        responseServerError(res, error)
    }
}

async function sendFile(req, res) {
    const tempPath = req.file?.path

    try {
        const { group_id } = req.body
        const { user_id } = req.user

        if (!req.file) return res.status(400).json({ msg: "Archivo requerido" })

        if (!group_id || !mongoose.isValidObjectId(group_id)) {
            await fs.promises.unlink(tempPath)
            return res.status(400).json({ msg: "group_id inválido" })
        }

        const group = await Group.findOne({ _id: group_id, participants: user_id }).populate(["creator", "participants"])
        if (!group) {
            await fs.promises.unlink(tempPath)
            return res.status(403).json({ msg: "No perteneces a este grupo" })
        }

        if (hasForbiddenExtension(req.file.originalname)) {
            await fs.promises.unlink(tempPath)
            return res.status(400).json({ msg: "Tipo de archivo ejecutable no permitido" })
        }

        const detected = await fileTypeFromFile(tempPath)
        const mime = detected?.mime || "application/octet-stream"

        if (!ALLOWED_FILE_MIMES.includes(mime)) {
            await fs.promises.unlink(tempPath)
            return res.status(400).json({ msg: "Tipo de archivo no permitido" })
        }

        if (mime === "application/zip" && !checkZipBomb(tempPath)) {
            await fs.promises.unlink(tempPath)
            return res.status(400).json({ msg: "Archivo ZIP rechazado por alta ratio de compresión (posible zip bomb)" })
        }

        const uuid = uuidv4()
        const ext = detected?.ext ? `.${detected.ext}` : path.extname(req.file.originalname).toLowerCase()
        const finalFilename = `${uuid}${ext}`
        const finalPath = path.join("uploads/files", finalFilename)

        await fs.promises.rename(tempPath, finalPath)

        const originalName = sanitizeOriginalName(req.file.originalname)

        const group_message = new GroupMessage({
            group: group_id,
            user: user_id,
            message: uuid,
            type: "FILE",
            attachment: {
                url: finalFilename,
                mimeType: mime,
                originalName,
                size: req.file.size,
            },
        })

        const messageStorage = await group_message.save()
        const data = await messageStorage.populate(["user"])

        io.sockets.in(group_id).emit("message", data)
        io.sockets.in(`${group_id}_notify`).emit("message_notify", data)

        res.status(200).json(data)
    } catch (error) {
        if (tempPath) fs.promises.unlink(tempPath).catch(() => {})
        responseServerError(res, error)
    }
}

async function deleteMessages(req, res) {
    try {
        const { ids } = req.body
        const { user_id } = req.user
        if (!Array.isArray(ids) || ids.length === 0) {
            return res.status(400).json({ msg: "ids debe ser un array con al menos un elemento" })
        }
        const result = await GroupMessage.deleteMany({ _id: { $in: ids }, user: user_id })
        res.status(200).json({ deletedCount: result.deletedCount })
    } catch (error) {
        responseServerError(res, error)
    }
}

export const GroupMessageController = {
    sendText,
    sendImage,
    sendLink,
    sendVideo,
    sendFile,
    deleteMessages,
    getAll,
    getTotalMessages,
    getLastMessage,
    markAllAsRead,
}