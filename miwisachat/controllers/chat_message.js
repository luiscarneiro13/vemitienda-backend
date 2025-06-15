import { responseServerError } from "../constants.js"
import { ChatMessage } from "../models/index.js"
import { io, getFilePath, convertToWebp, uploadToMegaWebp } from "../utils/index.js"

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

        const { chat_id, base64 } = req.body
        const { user_id } = req.user

        // const message = getFilePath(req.files.image)

        const folderName = "MiWisaChat/uploads/images"
        const webpPath = await convertToWebp(req.files.image)
        const { url } = await uploadToMegaWebp({ filePath: webpPath, folderName })

        const chat_message = new ChatMessage({
            chat: chat_id,
            user: user_id,
            message: url,
            type: "IMAGE"
        })

        const messageStorage = await chat_message.save()
        const data = await messageStorage.populate(["user"]);

        //Para emitir el mensaje en los chats. Aquí se especifica sobre que chat se va a emitir
        io.sockets.in(chat_id).emit("message", data)

        //Esto es para enviar un evento de nuevo mensaje
        io.sockets.in(`${chat_id}_notify`).emit("message_notify", data)

        res.status(201).send(messageStorage)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function getAll(req, res) {

    try {

        const { chat_id } = req.params

        const messages = await ChatMessage.find({ chat: chat_id }).sort({ createdAt: 1 }).populate(["user"]);

        const total = await ChatMessage.countDocuments({ chat: chat_id });

        res.status(200).send({ total, messages });

    } catch (error) {
        console.log(error)
        responseServerError(res, error)
    }

}

async function getTotalMessages(req, res) {

    try {

        const { chat_id } = req.params

        const total = await ChatMessage.countDocuments({ chat: chat_id });

        res.status(200).send(total);

    } catch (error) {
        responseServerError(res, error)
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

export const ChatMessageController = {
    sendText,
    sendImage,
    getAll,
    getTotalMessages,
    getLastMessage,
}