import { responseServerError } from "../constants.js"
import { GroupMessage } from "../models/index.js"
import { io, convertToWebp, uploadToMegaWebp } from "../utils/index.js"

async function sendText(req, res) {

    try {

        const { group_id, message } = req.body
        const { user_id } = req.user

        const group_message = new GroupMessage({
            group: group_id,
            user: user_id,
            message,
            type: "TEXT"
        })

        const dataStorage = await group_message.save()
        await dataStorage.populate(["user"])

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

        const messages = await GroupMessage.find({ group: group_id }).sort({ createdAt: 1 }).populate(["user", "group"]);

        const total = await GroupMessage.countDocuments({ group: group_id });

        res.status(200).send({ total, messages });

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

export const GroupMessageController = {
    sendText,
    sendImage,
    getAll,
    getTotalMessages,
    getLastMessage,
}