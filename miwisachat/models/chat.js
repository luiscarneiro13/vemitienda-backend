import mongoose from "mongoose"

const ChatSchema = mongoose.Schema({
    participant_one: {
        type: mongoose.Schema.ObjectId,
        ref: "User"
    },
    participant_two: {
        type: mongoose.Schema.ObjectId,
        ref: "User"
    },
    pinned: {
        type: Boolean,
        default: false
    }
})

export const Chat = mongoose.model("Chat", ChatSchema)