import mongoose from "mongoose"
import { SERVER_URL } from "../constants.js"

const UPLOADS_BASE = `${SERVER_URL}/api/miwisachat/uploads`

const GroupSchema = mongoose.Schema(
    {
        name: String,
        image: String,
        creator: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "User"
        },
        participants: [
            {
                type: mongoose.Schema.Types.ObjectId,
                ref: "User"
            }
        ],
    }
)

GroupSchema.index({ participants: 1 })
GroupSchema.index({ creator: 1 })

GroupSchema.set("toJSON", {
    transform: (doc, ret) => {
        if (ret.image && !ret.image.startsWith("http")) {
            ret.image = `${UPLOADS_BASE}/${ret.image}`
        }
        delete ret.__v
        return ret
    }
})

export const Group = mongoose.model("Group", GroupSchema)