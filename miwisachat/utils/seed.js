import bcrypt from "bcryptjs"
import { User, Chat } from "../models/index.js"
import { SERVER_URL } from "../constants.js"

export async function runSeed() {
    await seedNotasUser()
    await seedNotasChats()
}

async function seedNotasUser() {
    const randomPassword = bcrypt.hashSync(Math.random().toString(36), 10)

    await User.findOneAndUpdate(
        { email: "notas@sistema.local" },
        {
            email: "notas@sistema.local",
            firstname: "Notas",
            lastname: "",
            isBot: true,
            avatar: "avatar/notas.webp",
            password: randomPassword,
        },
        { upsert: true, new: true }
    )
}

async function seedNotasChats() {
    const notasUser = await User.findOne({ email: "notas@sistema.local" })
    if (!notasUser) return

    const realUsers = await User.find({ isBot: { $ne: true } })

    for (const user of realUsers) {
        const exists = await Chat.findOne({
            $or: [
                { participant_one: user._id, participant_two: notasUser._id },
                { participant_one: notasUser._id, participant_two: user._id },
            ],
        })

        if (exists) {
            if (!exists.pinned) {
                await Chat.findByIdAndUpdate(exists._id, { pinned: true })
            }
        } else {
            await Chat.create({
                participant_one: user._id,
                participant_two: notasUser._id,
                pinned: true,
            })
        }
    }
}

export async function createNotasChatForUser(userId) {
    const notasUser = await User.findOne({ email: "notas@sistema.local" })
    if (!notasUser) return

    const exists = await Chat.findOne({
        $or: [
            { participant_one: userId, participant_two: notasUser._id },
            { participant_one: notasUser._id, participant_two: userId },
        ],
    })

    if (!exists) {
        await Chat.create({
            participant_one: userId,
            participant_two: notasUser._id,
            pinned: true,
        })
    }
}
