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
        message: String,
        type: {
            type: "String",
            enum: ["TEXT", "IMAGE"]
        },

        readBy: [{ type: mongoose.Schema.ObjectId, ref: "User" }]
    },
    {
        timestamps: true
    }
)

export const GroupMessage = mongoose.model("GroupMessage", GroupMessageSchema)