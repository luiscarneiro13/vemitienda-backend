import { Group, User } from '../models/index.js'
import { responseServerError } from '../constants.js'
import { getFilePath } from '../utils/image.js'

async function getMe(req, res) {
    const { user_id } = req.user

    try {
        const response = await User.findById(user_id).select(["-password"])

        if (!response) { res.status(400).send({ msg: "No se encontró el usuario" }) }
        else { res.status(200).send(response) }

    } catch (error) {
        responseServerError(res, error)
    }

}

async function getUsers(req, res) {

    try {
        const { user_id } = req.user
        const users = await User.find({ _id: { $ne: user_id } }).select(["-password"]) //Devuelve todos excepto el usuario logueado

        if (!users) { res.status(400).send({ msg: "No se encontraron usuarios" }) }
        else { res.status(200).send(users) }

    } catch (error) {
        responseServerError(res, error)
    }

}

async function getUser(req, res) {
    try {
        const { id } = req.params
        const user = await User.findById(id).select(["-password"])
        if (!user) { res.status(400).send({ msg: "Usuario no encontrado" }) }
        else { res.status(200).send(user) }
    } catch (error) {
        responseServerError(res)
    }
}

async function updateUser(req, res) {

    try {

        const { user_id } = req.user
        const userData = req.body

        if (req.files.avatar) {
            const imagePath = getFilePath(req.files.avatar)
            userData.avatar = imagePath
        }

        const user = await User.findByIdAndUpdate({ _id: user_id }, userData)

        if (!user) { res.status(400).send({ msg: "No se actualizó el usuario porque no existe" }) }
        else { res.status(200).send(userData) }

    } catch (error) {
        console.log(error)
        responseServerError(res, error)
    }

}

async function getUsersExceptParticipantsGroup(req, res) {

    try {
        const { group_id } = req.params

        const group = await Group.findById(group_id)
        const participantsStrings = group.participants.toString()
        const participants = participantsStrings.split(",")

        const response = await User.find({ _id: { $nin: participants } }).select(["-password"])

        res.status(200).send(response)

    } catch (error) {
        responseServerError(res, error)
    }
}

export const UserController = {
    getMe,
    getUsers,
    getUser,
    updateUser,
    getUsersExceptParticipantsGroup,
}