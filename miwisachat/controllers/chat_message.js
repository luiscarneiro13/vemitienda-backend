import { responseServerError } from "../constants.js"
import { ChatMessage, User, Chat } from "../models/index.js"
import { io, getFilePath, sendPushNotification, getOther } from "../utils/index.js"

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
                const notificaction = {
                    title: 'Nuevo mensaje',
                    body: 'Mensaje de texto...',
                    data: { chat_id } // Esto estárá disponible al tocar la notificación
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
                const notificaction = {
                    title: 'Nuevo mensaje',
                    body: 'Mensaje de texto...',
                    data: { chat_id } // Esto estárá disponible al tocar la notificación
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

        const { chat_id } = req.params
        const pageQuery = parseInt(req.query.page, 10)
        const limit = parseInt(req.query.limit, 10) || 20

        // 1) total de mensajes y páginas
        const total = await ChatMessage.countDocuments({ chat: chat_id })
        const totalPages = Math.max(Math.ceil(total / limit), 1)

        // 2) si no viene page o es inválido, usar la última
        const page = (pageQuery >= 1 && pageQuery <= totalPages) ? pageQuery : totalPages

        const skip = (page - 1) * limit

        // 3) consulta paginada y poblada
        const messages = await ChatMessage.find({ chat: chat_id })
            .sort({ createdAt: 1 })
            .skip(skip)
            .limit(limit)
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

        // 4) devolver meta + datos
        return res.status(200).send({
            total,
            totalPages,
            page,
            limit,
            messages
        })

    } catch (error) {
        responseServerError(res, error)
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


export const ChatMessageController = {
    sendText,
    sendImage,
    getAll,
    getTotalMessages,
    getLastMessage,
    markAllAsRead,
}