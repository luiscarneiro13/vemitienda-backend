import mongoose from "mongoose"

const GroupMessageSchema = mongoose.Schema(
    {
        group: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "Group"
        },
        user: {
            type: mongoose.Schema.Types.ObjectId,
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
        readBy: [{ type: mongoose.Schema.ObjectId, ref: "User" }]
    },
    {
        timestamps: true
    }
)

GroupMessageSchema.index({ group: 1, createdAt: -1 })
GroupMessageSchema.index({ group: 1, user: 1 })

export const GroupMessage = mongoose.model("GroupMessage", GroupMessageSchema)