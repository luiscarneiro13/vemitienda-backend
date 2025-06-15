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
})

export const User = mongoose.model("User", userSchema)