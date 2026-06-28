import bcrypt from "bcryptjs"
import { User, Chat } from "../models/index.js"
import { SERVER_URL } from "../constants.js"

export async function runSeed() {
    await seedNotasUser()
    await seedNotasChats()
}

async function seedNotasUser() {
    const randomPassword = await bcrypt.hash(Math.random().toString(36), 10)

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

    const realUsers = await User.find({ isBot: { $ne: true } }, { _id: 1 })
    if (realUsers.length === 0) return

    const ops = realUsers.map(user => ({
        updateOne: {
            filter: {
                $or: [
                    { participant_one: user._id, participant_two: notasUser._id },
                    { participant_one: notasUser._id, participant_two: user._id }
                ]
            },
            update: {
                $setOnInsert: { participant_one: user._id, participant_two: notasUser._id },
                $set: { pinned: true }
            },
            upsert: true
        }
    }))

    await Chat.bulkWrite(ops, { ordered: false })
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
