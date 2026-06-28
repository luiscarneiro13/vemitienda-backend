import mongoose from "mongoose"

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
    isBot: { type: Boolean, default: false }
})

userSchema.index({ isBot: 1 })

export const User = mongoose.model("User", userSchema)