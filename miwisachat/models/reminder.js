import mongoose from "mongoose"

const ReminderSchema = mongoose.Schema({
  userId: {
    type: mongoose.Schema.ObjectId,
    ref: "User",
    required: true
  },
  title: {
    type: String,
    required: true
  },
  description: String,
  dueAt: {
    type: Date,
    required: true
  },
  recurringType: {
    type: String,
    enum: ["none", "daily", "weekly", "monthly"],
    default: "none"
  },
  notifyBefore: {
    type: [Number],
    default: [10, 60, 1440]
  },
  notifiedOffsets: {
    type: [Number],
    default: []
  },
  isActive: {
    type: Boolean,
    default: true
  }
}, { timestamps: true })

export const Reminder = mongoose.model("Reminder", ReminderSchema)
