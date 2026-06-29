import mongoose from "mongoose"

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
        delete ret.__v
        return ret
    }
})

export const Group = mongoose.model("Group", GroupSchema)