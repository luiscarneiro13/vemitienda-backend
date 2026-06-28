import { responseServerError } from "../constants.js"
import { Chat, ChatMessage } from "../models/index.js"


async function create(req, res) {
    try {
        const { participant_id_one, participant_id_two } = req.body

        // Busco primero a ver si los 2 usuarios ya tienen un chat activo
        // Una sola query con $or cubre ambas combinaciones de participantes

        const found = await Chat.findOne({
            $or: [
                { participant_one: participant_id_one, participant_two: participant_id_two },
                { participant_one: participant_id_two, participant_two: participant_id_one }
            ]
        })

        if (found) {
            res.status(200).send({ msg: "Ya tienes un chat con este usuario" })
            return
        }

        // Sino hay un chat entonces se crea
        const chat = new Chat({
            participant_one: participant_id_one,
            participant_two: participant_id_two
        })

        const chatStorage = await chat.save();
        res.status(201).send(chatStorage);

    } catch (error) {
        responseServerError(res, error)
    }
}

async function getAll(req, res) {

    const { user_id } = req.user

    const search = [{ participant_one: user_id }, { participant_two: user_id }]

    try {
        const chats = await Chat.find({ $or: search }).populate(["participant_one", "participant_two"])

        const arrayChats = []

        for await (const chat of chats) {
            const response = await ChatMessage.findOne({ chat: chat._id }).sort({ createdAt: -1 })
            arrayChats.push({
                ...chat._doc,
                last_message_date: response?.createdAt || null
            })
        }

        // Pinned primero, luego por fecha de último mensaje descendente
        arrayChats.sort((a, b) => {
            if (b.pinned !== a.pinned) return b.pinned ? 1 : -1
            const dateA = a.last_message_date ? new Date(a.last_message_date) : new Date(0)
            const dateB = b.last_message_date ? new Date(b.last_message_date) : new Date(0)
            return dateB - dateA
        })

        res.status(200).send(arrayChats)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function deleteChat(req, res) {

    const chat_id = req.params.id
    const { user_id } = req.user

    try {
        const chat = await Chat.findOne({
            _id: chat_id,
            $or: [{ participant_one: user_id }, { participant_two: user_id }]
        })
        if (!chat) return res.status(403).json({ msg: "Acceso denegado" })
        if (chat.pinned) return res.status(403).json({ msg: "No puedes eliminar este chat" })

        await Chat.findByIdAndDelete(chat_id)
        res.status(200).send(chat)
    } catch (error) {
        responseServerError(res, error)
    }

}

async function getChat(req, res) {

    const chat_id = req.params.id
    const { user_id } = req.user

    try {
        const chat = await Chat.findOne({
            _id: chat_id,
            $or: [{ participant_one: user_id }, { participant_two: user_id }]
        }).populate(["participant_one", "participant_two"])
        if (!chat) return res.status(403).json({ msg: "Acceso denegado" })
        res.status(200).send(chat);
    } catch (error) {
        responseServerError(res, error)
    }

}

export const ChatController = {
    create,
    getAll,
    deleteChat,
    getChat,
}