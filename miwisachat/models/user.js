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
    expo_token_updated_at: Date
})

export const User = mongoose.model("User", userSchema)