import { responseServerError } from "../constants.js"
import { Group, User, GroupMessage } from "../models/index.js"
import { getFilePath } from "../utils/index.js"

async function create(req, res) {

    try {
        const group = new Group(req.body)
        const { user_id } = req.user
        group.creator = user_id
        group.participants = JSON.parse(req.body.participants);
        
        // Solo agregar user_id si no está ya en la lista
        if (!group.participants.includes(user_id)) {
            group.participants.push(user_id);
        }

        if (req.files.image) {
            const imagePath = getFilePath(req.files.image)
            group.image = imagePath
        }

        const groupStorage = await group.save()

        res.status(201).send(groupStorage)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function getAll(req, res) {

    try {

        const { user_id } = req.user

        const groups = await Group.find({ participants: user_id }).populate(["creator", "participants"])

        const arrayGroups = []

        for await (const group of groups) {

            const response = await GroupMessage.findOne({ group: group._id }).sort({ createdAt: -1 })

            arrayGroups.push({
                ...group._doc,
                last_message_date: response?.createdAt || null
            })

        }

        res.status(200).send(arrayGroups)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function getGroup(req, res) {

    try {

        const id = req.params.id
        const { user_id } = req.user

        const group = await Group.findOne({ _id: id, participants: user_id }).populate(["creator", "participants"])

        if (!group) {
            return res.status(403).json({ msg: "Acceso denegado" })
        }

        res.status(200).send(group)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function updateGroup(req, res) {

    try {
        const { id } = req.params
        const { user_id } = req.user
        const { name } = req.body

        const group = await Group.findOne({ _id: id, participants: user_id })
        if (!group) return res.status(403).json({ msg: "Acceso denegado" })
        if (group.creator.toString() !== user_id.toString()) return res.status(403).json({ msg: "Solo el creador puede modificar el grupo" })

        const updateData = {}
        if (name) updateData.name = name

        if (req.files.image) {
            const imagePath = getFilePath(req.files.image)
            updateData.image = imagePath
        }

        const updated = await Group.findByIdAndUpdate(id, updateData, { new: true })

        res.status(200).send(updated)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function exitGroup(req, res) {

    try {

        const { id } = req.params
        const { user_id } = req.user

        const group = await Group.findOne({ _id: id, participants: user_id })
        if (!group) return res.status(403).json({ msg: "Acceso denegado" })

        const newParticipants = group.participants.filter((participants) => {
            participants.toString() !== user_id
        })

        const newData = { ...group._doc, participants: newParticipants }

        const groupStorage = await Group.findByIdAndUpdate(id, newData)

        res.status(200).send(groupStorage)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function addParticipants(req, res) {

    try {
        const { id } = req.params
        const { user_id } = req.user
        const { users_id } = req.body

        const group = await Group.findOne({ _id: id, participants: user_id })
        if (!group) return res.status(403).json({ msg: "Acceso denegado" })
        if (group.creator.toString() !== user_id.toString()) return res.status(403).json({ msg: "Solo el creador puede agregar participantes" })
        const users = await User.find({ _id: users_id })

        const arrayObjectIds = []

        users.forEach((user) => {
            arrayObjectIds.push(user._id)
        })

        const newData = {
            ...group._doc,
            participants: [...group.participants, ...users_id]
        }

        const groupStorage = await Group.findByIdAndUpdate(id, newData)

        res.status(200).send(groupStorage)

    } catch (error) {
        responseServerError(res, error)
    }

}

async function banParticipant(req, res) {

    try {

        const { group_id, user_id: target_user_id } = req.body
        const { user_id } = req.user

        const group = await Group.findOne({ _id: group_id, participants: user_id })
        if (!group) return res.status(403).json({ msg: "Acceso denegado" })
        if (group.creator.toString() !== user_id.toString()) return res.status(403).json({ msg: "Solo el creador puede expulsar participantes" })

        const newParticipants = group.participants.filter((participant) => {
            return participant.toString() !== target_user_id
        })

        const newData = {
            ...group._doc,
            participants: newParticipants
        }

        const groupStorage = await Group.findByIdAndUpdate(group_id, newData)

        res.status(200).send(groupStorage)

    } catch (error) {
        responseServerError(res, error)
    }

}

export const GroupController = {
    create,
    getAll,
    getGroup,
    updateGroup,
    exitGroup,
    addParticipants,
    banParticipant,
}