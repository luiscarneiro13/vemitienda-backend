import mongoose from "mongoose"
import { SERVER_URL } from "../constants.js"

const UPLOADS_BASE = `${SERVER_URL}/api/miwisachat/uploads`
const MEDIA_BASE = `${SERVER_URL}/api/miwisachat/media`

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

GroupMessageSchema.set("toJSON", {
    transform: (doc, ret) => {
        if (ret.type === "IMAGE" && ret.message && !ret.message.startsWith("http")) {
            ret.message = `${UPLOADS_BASE}/${ret.message}`
        }
        if (ret.attachment?.url && !ret.attachment.url.startsWith("http")) {
            ret.attachment.url = `${MEDIA_BASE}/${ret.attachment.url}`
        }
        if (ret.attachment?.thumbnail && !ret.attachment.thumbnail.startsWith("http")) {
            ret.attachment.thumbnail = `${MEDIA_BASE}/${ret.attachment.thumbnail}`
        }
        delete ret.__v
        return ret
    }
})

export const GroupMessage = mongoose.model("GroupMessage", GroupMessageSchema)