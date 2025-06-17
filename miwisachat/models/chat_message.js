import mongoose from "mongoose"

const ChatMessageSchema = mongoose.Schema(
    {

        chat: {
            type: mongoose.Schema.ObjectId,
            ref: "Chat"
        },

        user: {
            type: mongoose.Schema.ObjectId,
            ref: "User"
        },

        message: String,

        type: {
            type: String,
            enum: ["TEXT", "IMAGE"]
        },

        readBy: [{
            type: mongoose.Schema.Types.ObjectId,
            ref: "User"
        }]


    },
    {
        timestamps: true
    }
)

export const ChatMessage = mongoose.model("ChatMessage", ChatMessageSchema)