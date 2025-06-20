import { responseServerError } from "../constants.js"
import { Group, GroupMessage } from "../models/index.js"
import { getFilePath, io } from "../utils/index.js"
import { getOtherParticipants, sendPushNotification } from "../utils/index.js"

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

        const group = await Group.findById({ _id: group_id }).populate(["creator", "participants"])

        const otherUsers = getOtherParticipants(user_id, group.participants)

        if (otherUsers) {
            for (const otherUser of otherUsers) {
                
                if (otherUser?.expo_token) {
                    const notification = {
                        title: 'Nuevo mensaje',
                        body: 'Mensaje de texto...',
                        data: { group_id }, // Lo puedes acceder al tocar la notificación
                    };

                    await sendPushNotification(otherUser.expo_token, notification);
                }
            }
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

        const group = await Group.find({ _id: group_id })

        // Verifico si el usuario tiene un token de expo para enviarle notificación:
        const otherUsers = getOtherParticipants(user_id, group.participants)

        if (otherUsers) {
            for (const otherUser of otherUsers) {
                if (otherUser?.expo_token) {
                    const notification = {
                        title: 'Nuevo mensaje',
                        body: 'Mensaje de texto...',
                        data: { group_id }, // Lo puedes acceder al tocar la notificación
                    };

                    await sendPushNotification(otherUser.expo_token, notification);
                }
            }
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

async function markAllAsRead(req, res) {
    try {
        const { group_id } = req.params
        const { user_id } = req.user

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

export const GroupMessageController = {
    sendText,
    sendImage,
    getAll,
    getTotalMessages,
    getLastMessage,
    markAllAsRead,
}