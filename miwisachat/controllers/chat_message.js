import fs from "fs"
import path from "path"
import { randomUUID as uuidv4 } from "crypto"
import { fileTypeFromFile } from "file-type"
import sanitizeHtml from "sanitize-html"
import rateLimit, { ipKeyGenerator } from "express-rate-limit"
import AdmZip from "adm-zip"
import validator from "validator"
import mongoose from "mongoose"
import { responseServerError } from "../constants.js"
import { ChatMessage, User, Chat } from "../models/index.js"
import { io, getFilePath, sendPushNotification, getOther, getPublicUrl, getVideoDuration, generateThumbnail } from "../utils/index.js"

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

export const videoRateLimiter = rateLimit({
    windowMs: 60 * 1000,
    max: 5,
    keyGenerator: (req) => req.user?.user_id?.toString() || ipKeyGenerator(req),
    standardHeaders: true,
    legacyHeaders: false,
    handler: (req, res) =>
        res.status(429).json({ msg: "Límite de subida de videos alcanzado. Intenta en un minuto." }),
})

export const fileRateLimiter = rateLimit({
    windowMs: 60 * 1000,
    max: 10,
    keyGenerator: (req) => req.user?.user_id?.toString() || ipKeyGenerator(req),
    standardHeaders: true,
    legacyHeaders: false,
    handler: (req, res) =>
        res.status(429).json({ msg: "Límite de subida de archivos alcanzado. Intenta en un minuto." }),
})

async function sendText(req, res) {

    try {

        const { chat_id, message } = req.body
        const { user_id } = req.user

        const chat_message = new ChatMessage({
            chat: chat_id,
            user: user_id,
            message,
            type: "TEXT"
        })

        const messageStorage = await chat_message.save()
        const data = await messageStorage.populate(["user"]);

        const chat = await Chat.findOne({ _id: chat_id })

        // Verifico si el usuario tiene un token de expo para enviarle notificación:
        const otherUserId = getOther(user_id, chat.participant_one, chat.participant_two)

        if (otherUserId) {
            const otherUser = await User.findOne({ _id: otherUserId._id })
            if (otherUser?.expo_token) {
                const senderName = data.user?.firstname
                    ? `${data.user.firstname} ${data.user.lastname}`
                    : data.user?.email
                const senderAvatar = getPublicUrl(data.user?.avatar)
                const notificaction = {
                    title: senderName,
                    body: message,
                    richContent: senderAvatar ? { image: senderAvatar } : undefined
                }
                await sendPushNotification(otherUser.expo_token, notificaction)
            }
        }

        //Para emitir el mensaje en los chats. Aquí se especifica sobre que chat se va a emitir
        io.sockets.in(chat_id).emit("message", data)

        //Esto es para enviar un evento de nuevo mensaje
        io.sockets.in(`${chat_id}_notify`).emit("message_notify", data)

        res.status(201).send(messageStorage)

    } catch (error) {
        console.error("Error en el envío del mensaje:", error);
        responseServerError(res, error)
    }
}

async function sendImage(req, res) {

    try {

        const { chat_id } = req.body
        const { user_id } = req.user

        const message = getFilePath(req.files.image)

        const chat_message = new ChatMessage({
            chat: chat_id,
            user: user_id,
            message,
            type: "IMAGE"
        })

        const messageStorage = await chat_message.save()
        const data = await messageStorage.populate(["user"]);

        const chat = await Chat.findOne({ _id: chat_id })

        // Verifico si el usuario tiene un token de expo para enviarle notificación:
        const otherUserId = getOther(user_id, chat.participant_one, chat.participant_two)

        if (otherUserId) {
            const otherUser = await User.findOne({ _id: otherUserId })
            if (otherUser?.expo_token) {
                const senderName = data.user?.firstname
                    ? `${data.user.firstname} ${data.user.lastname}`
                    : data.user?.email
                const senderAvatar = getPublicUrl(data.user?.avatar)
                const notificaction = {
                    title: senderName,
                    body: '📷 Imagen',
                    richContent: senderAvatar ? { image: senderAvatar } : undefined
                }
                await sendPushNotification(otherUser.expo_token, notificaction)
            }
        }

        //Para emitir el mensaje en los chats. Aquí se especifica sobre que chat se va a emitir
        io.sockets.in(chat_id).emit("message", data)

        //Esto es para enviar un evento de nuevo mensaje
        io.sockets.in(`${chat_id}_notify`).emit("message_notify", data)

        res.status(201).send(messageStorage)

    } catch (error) {
        responseServerError(res, error)
    }

}

// async function getAll(req, res) {

//     try {

//         const { chat_id } = req.params

//         const messages = await ChatMessage.find({ chat: chat_id }).sort({ createdAt: 1 }).populate([
//             {
//                 path: "user"
//             },
//             {
//                 path: "chat",
//                 populate: [
//                     { path: "participant_one" },
//                     { path: "participant_two" }
//                 ]
//             }
//         ])

//         const total = await ChatMessage.countDocuments({ chat: chat_id });

//         res.status(200).send({ total, messages });

//     } catch (error) {
//         console.log(error)
//         responseServerError(res, error)
//     }

// }

async function getAll(req, res) {
    
    try {
        const { chat_id } = req.params;
        const pageQuery = parseInt(req.query.page, 10) || 1;
        const limitQuery = parseInt(req.query.limit, 10) || 20;

        // 1) total de mensajes y total de páginas
        const total = await ChatMessage.countDocuments({ chat: chat_id });
        const totalPages = Math.max(Math.ceil(total / limitQuery), 1);

        // 2) Validar que page esté dentro de rango
        const page = (pageQuery >= 1 && pageQuery <= totalPages) ? pageQuery : totalPages;

        // 3) Calcular el offset (skip) real
        const skip = (page - 1) * limitQuery;

        // 4) Consulta paginada DESC (más recientes primero)
        const messages = await ChatMessage.find({ chat: chat_id })
            .sort({ createdAt: -1 })
            .skip(skip)
            .limit(limitQuery)
            .populate([
                { path: "user" },
                {
                    path: "chat",
                    populate: [
                        { path: "participant_one" },
                        { path: "participant_two" }
                    ]
                }
            ]);

        // 5) Responder con metadatos y datos
        return res.status(200).json({
            total,
            totalPages,
            page,
            limit: limitQuery,
            messages
        });

    } catch (error) {
        responseServerError(res, error);
    }
}

async function getTotalMessages(req, res) {
    try {
        const { chat_id } = req.params;
        const { user_id } = req.user; // Usuario autenticado

        // Contamos los mensajes del chat que no fueron enviados por el usuario
        // y que además no han sido marcados como leídos por el usuario.
        const total = await ChatMessage.countDocuments({
            chat: chat_id,
            user: { $ne: user_id },
            readBy: { $ne: user_id }
        });

        res.status(200).send({ total });
    } catch (error) {
        responseServerError(res, error);
    }
}


async function getLastMessage(req, res) {

    try {

        const { chat_id } = req.params

        const response = await ChatMessage.findOne({ chat: chat_id }).sort({ createdAt: -1 })

        res.status(200).send(response || {});

    } catch (error) {
        responseServerError(res, error)
    }
}

async function markAllAsRead(req, res) {
    try {
        const { chat_id } = req.params
        const { user_id } = req.user

        // Marcar como leído solo los mensajes que aún NO tienen a este usuario en readBy
        const result = await ChatMessage.updateMany(
            {
                chat: chat_id,
                user: { $ne: user_id },        // solo mensajes de la otra persona
                readBy: { $ne: user_id }       // y que aún no hayas leído
            },
            {
                $push: { readBy: user_id }     // agregar tu ID al array
            }
        )

        res.status(200).send({ updated: result.modifiedCount })
    } catch (error) {
        responseServerError(res, error)
    }
}


async function sendLink(req, res) {
    try {
        const { chat_id, message = "", linkPreview } = req.body
        const { user_id } = req.user

        if (!chat_id || !mongoose.isValidObjectId(chat_id)) {
            return res.status(400).json({ msg: "chat_id inválido" })
        }

        const chat = await Chat.findOne({
            _id: chat_id,
            $or: [{ participant_one: user_id }, { participant_two: user_id }],
        })
        if (!chat) return res.status(403).json({ msg: "No perteneces a este chat" })

        if (!linkPreview?.url || !validator.isURL(linkPreview.url, { protocols: ["http", "https"], require_protocol: true })) {
            return res.status(400).json({ msg: "linkPreview.url es requerido y debe ser una URL http/https válida" })
        }

        const sanitizedPreview = {
            ...linkPreview,
            title: sanitizeHtml(linkPreview.title || "", { allowedTags: [], allowedAttributes: {} }),
            description: sanitizeHtml(linkPreview.description || "", { allowedTags: [], allowedAttributes: {} }),
        }

        const chat_message = new ChatMessage({
            chat: chat_id,
            user: user_id,
            message: message || "",
            type: "LINK",
            linkPreview: sanitizedPreview,
        })

        const messageStorage = await chat_message.save()
        const data = await messageStorage.populate(["user", "chat"])

        io.sockets.in(chat_id).emit("message", data)
        io.sockets.in(`${chat_id}_notify`).emit("message_notify", data)

        res.status(201).json(data)
    } catch (error) {
        responseServerError(res, error)
    }
}

async function sendVideo(req, res) {
    const tempPath = req.file?.path

    try {
        const { chat_id } = req.body
        const { user_id } = req.user

        if (!req.file) return res.status(400).json({ msg: "Video requerido" })

        if (!chat_id || !mongoose.isValidObjectId(chat_id)) {
            await fs.promises.unlink(tempPath)
            return res.status(400).json({ msg: "chat_id inválido" })
        }

        const chat = await Chat.findOne({
            _id: chat_id,
            $or: [{ participant_one: user_id }, { participant_two: user_id }],
        })
        if (!chat) {
            await fs.promises.unlink(tempPath)
            return res.status(403).json({ msg: "No perteneces a este chat" })
        }

        // Validar magic number con file-type
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

        let thumbName = null
        try {
            thumbName = `${uuid}_thumb.jpg`
            await generateThumbnail(finalPath, "uploads/videos", thumbName)
        } catch {
            thumbName = null
        }

        const originalName = sanitizeOriginalName(req.file.originalname)

        const chat_message = new ChatMessage({
            chat: chat_id,
            user: user_id,
            message: finalFilename,
            type: "VIDEO",
            attachment: {
                url: finalFilename,
                mimeType: detected.mime,
                thumbnail: thumbName,
                duration,
                originalName,
                size: req.file.size,
            },
        })

        const messageStorage = await chat_message.save()
        const data = await messageStorage.populate(["user", "chat"])

        io.sockets.in(chat_id).emit("message", data)
        io.sockets.in(`${chat_id}_notify`).emit("message_notify", data)

        res.status(201).json(data)
    } catch (error) {
        if (tempPath) fs.promises.unlink(tempPath).catch(() => {})
        responseServerError(res, error)
    }
}

async function sendFile(req, res) {
    const tempPath = req.file?.path

    try {
        const { chat_id } = req.body
        const { user_id } = req.user

        if (!req.file) return res.status(400).json({ msg: "Archivo requerido" })

        if (!chat_id || !mongoose.isValidObjectId(chat_id)) {
            await fs.promises.unlink(tempPath)
            return res.status(400).json({ msg: "chat_id inválido" })
        }

        const chat = await Chat.findOne({
            _id: chat_id,
            $or: [{ participant_one: user_id }, { participant_two: user_id }],
        })
        if (!chat) {
            await fs.promises.unlink(tempPath)
            return res.status(403).json({ msg: "No perteneces a este chat" })
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

        const chat_message = new ChatMessage({
            chat: chat_id,
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

        const messageStorage = await chat_message.save()
        const data = await messageStorage.populate(["user", "chat"])

        io.sockets.in(chat_id).emit("message", data)
        io.sockets.in(`${chat_id}_notify`).emit("message_notify", data)

        res.status(201).json(data)
    } catch (error) {
        if (tempPath) fs.promises.unlink(tempPath).catch(() => {})
        responseServerError(res, error)
    }
}

async function deleteMessages(req, res) {
    try {
        const { ids } = req.body
        if (!Array.isArray(ids) || ids.length === 0) {
            return res.status(400).json({ msg: "ids debe ser un array con al menos un elemento" })
        }
        const result = await ChatMessage.deleteMany({ _id: { $in: ids } })
        res.status(200).json({ deletedCount: result.deletedCount })
    } catch (error) {
        responseServerError(res, error)
    }
}

export const ChatMessageController = {
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