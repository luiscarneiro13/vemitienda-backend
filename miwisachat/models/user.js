import mongoose from "mongoose"
import { SERVER_URL } from "../constants.js"

const UPLOADS_BASE = `${SERVER_URL}/api/miwisachat/uploads`

const userSchema = mongoose.Schema({
    email: {
        type: String,
        unique: true
    },
    firstname: String,
    lastname: String,
    password: String,
    avatar: String,
    expo_token: String,
    expo_token_updated_at: Date,
    isBot: { type: Boolean, default: false },
    tokenVersion: { type: Number, default: 0 }
})

userSchema.index({ isBot: 1 })

userSchema.set("toJSON", {
    transform: (doc, ret) => {
        if (ret.avatar && !ret.avatar.startsWith("http")) {
            ret.avatar = `${UPLOADS_BASE}/${ret.avatar}`
        }
        delete ret.password
        delete ret.tokenVersion
        delete ret.__v
        return ret
    }
})

export const User = mongoose.model("User", userSchema)