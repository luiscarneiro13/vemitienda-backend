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

ChatSchema.index({ participant_one: 1, participant_two: 1 })
ChatSchema.index({ participant_two: 1, participant_one: 1 })
ChatSchema.index({ pinned: -1, updatedAt: -1 })

export const Chat = mongoose.model("Chat", ChatSchema)