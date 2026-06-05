import { responseServerError } from "../constants.js"
import { Reminder } from "../models/index.js"

async function create(req, res) {
  try {
    const { user_id } = req.user
    const { title, description, dueAt, recurringType, notifyBefore } = req.body

    if (!title || !dueAt) {
      return res.status(400).send({ msg: "title y dueAt son requeridos" })
    }

    const reminder = new Reminder({
      userId: user_id,
      title,
      description: description || "",
      dueAt: new Date(dueAt),
      recurringType: recurringType || "none",
      notifyBefore: notifyBefore || [10, 60, 1440],
      notifiedOffsets: []
    })

    const saved = await reminder.save()
    res.status(201).send(saved)
  } catch (error) {
    responseServerError(res, error)
  }
}

async function getAll(req, res) {
  try {
    const { user_id } = req.user

    const reminders = await Reminder.find({ userId: user_id, isActive: true })
      .sort({ dueAt: 1 })

    res.status(200).send(reminders)
  } catch (error) {
    responseServerError(res, error)
  }
}

async function getById(req, res) {
  try {
    const { user_id } = req.user
    const { id } = req.params

    const reminder = await Reminder.findOne({ _id: id, userId: user_id })

    if (!reminder) {
      return res.status(404).send({ msg: "Recordatorio no encontrado" })
    }

    res.status(200).send(reminder)
  } catch (error) {
    responseServerError(res, error)
  }
}

async function update(req, res) {
  try {
    const { user_id } = req.user
    const { id } = req.params
    const { title, description, dueAt, recurringType, notifyBefore, isActive } = req.body

    const reminder = await Reminder.findOne({ _id: id, userId: user_id })

    if (!reminder) {
      return res.status(404).send({ msg: "Recordatorio no encontrado" })
    }

    if (title !== undefined) reminder.title = title
    if (description !== undefined) reminder.description = description
    if (dueAt !== undefined) reminder.dueAt = new Date(dueAt)
    if (recurringType !== undefined) reminder.recurringType = recurringType
    if (notifyBefore !== undefined) reminder.notifyBefore = notifyBefore
    if (isActive !== undefined) reminder.isActive = isActive

    reminder.notifiedOffsets = []

    const saved = await reminder.save()
    res.status(200).send(saved)
  } catch (error) {
    responseServerError(res, error)
  }
}

async function remove(req, res) {
  try {
    const { user_id } = req.user
    const { id } = req.params

    const reminder = await Reminder.findOneAndDelete({ _id: id, userId: user_id })

    if (!reminder) {
      return res.status(404).send({ msg: "Recordatorio no encontrado" })
    }

    res.status(200).send({ msg: "Recordatorio eliminado" })
  } catch (error) {
    responseServerError(res, error)
  }
}

export const ReminderController = {
  create,
  getAll,
  getById,
  update,
  remove
}
