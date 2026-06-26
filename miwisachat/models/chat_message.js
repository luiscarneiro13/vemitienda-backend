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
        message: {
            type: String,
            default: ""
        },
        type: {
            type: String,
            enum: ["TEXT", "IMAGE", "LINK", "VIDEO", "FILE"]
        },
        linkPreview: {
            url: String,
            title: String,
            description: String,
            image: String,
            siteName: String,
            favicon: String,
        },
        attachment: {
            url: String,
            mimeType: String,
            thumbnail: String,
            duration: Number,
            originalName: String,
            size: Number,
        },
        replyTo: {
            type: mongoose.Schema.Types.Mixed,
            default: null,
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