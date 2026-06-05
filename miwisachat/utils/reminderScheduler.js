import cron from "node-cron"
import { Reminder, User, Chat, ChatMessage } from "../models/index.js"
import { io } from "./socketServer.js"
import { sendPushNotification } from "./expoPush.js"

const BOT_EMAIL = "bot@recordatorios.local"

async function ensureBotUser() {
  let bot = await User.findOne({ email: BOT_EMAIL })

  if (!bot) {
    const bcrypt = await import("bcryptjs")
    const hash = await bcrypt.hash(Date.now().toString(), 10)

    bot = new User({
      email: BOT_EMAIL,
      firstname: "Recordatorios",
      lastname: "",
      password: hash,
      isBot: true,
      avatar: "bot.webp"
    })

    await bot.save()
    console.log("✅ Bot de recordatorios creado")
  }

  return bot
}

async function ensureChatWithBot(userId, botId) {
  let chat = await Chat.findOne({
    participant_one: { $in: [userId, botId] },
    participant_two: { $in: [userId, botId] }
  })

  if (!chat) {
    chat = new Chat({ participant_one: userId, participant_two: botId })
    await chat.save()
    console.log(`💬 Chat con bot creado para usuario ${userId}`)
  }

  return chat
}

async function sendBotMessage(chat, bot, title) {
  const message = new ChatMessage({
    chat: chat._id,
    user: bot._id,
    message: `⏰ Recordatorio: ${title}`,
    type: "TEXT"
  })

  await message.save()
  const data = await message.populate("user")

  io.sockets.in(String(chat._id)).emit("message", data)
  io.sockets.in(`${chat._id}_notify`).emit("message_notify", data)
}

async function checkReminders(bot) {
  const now = new Date()

  const reminders = await Reminder.find({ isActive: true }).populate("userId")

  for (const reminder of reminders) {
    const user = reminder.userId
    if (!user) continue

    for (const offset of reminder.notifyBefore) {
      if (reminder.notifiedOffsets.includes(offset)) continue

      const notifyAt = new Date(reminder.dueAt.getTime() - offset * 60000)
      if (notifyAt > now) continue

      reminder.notifiedOffsets.push(offset)

      try {
        const chat = await ensureChatWithBot(user._id, bot._id)
        await sendBotMessage(chat, bot, reminder.title)

        if (user.expo_token) {
          await sendPushNotification(user.expo_token, {
            title: "⏰ Recordatorio",
            body: reminder.title
          })
        }

        console.log(`🔔 Notificado: "${reminder.title}" (offset -${offset}min)`)
      } catch (err) {
        console.error(`Error notificando recordatorio ${reminder._id}:`, err)
      }
    }

    if (!reminder.dueNotified && reminder.dueAt <= now) {
      reminder.dueNotified = true

      try {
        const chat = await ensureChatWithBot(user._id, bot._id)
        await sendBotMessage(chat, bot, reminder.title)

        if (user.expo_token) {
          await sendPushNotification(user.expo_token, {
            title: "⏰ Recordatorio",
            body: reminder.title
          })
        }

        console.log(`✅ Recordatorio cumplido: "${reminder.title}"`)
      } catch (err) {
        console.error(`Error enviando mensaje de dueAt ${reminder._id}:`, err)
      }
    }

    const allNotified = reminder.dueNotified &&
      reminder.notifiedOffsets.length >= reminder.notifyBefore.length

    if (allNotified) {
      if (reminder.recurringType === "daily") {
        reminder.dueAt = new Date(reminder.dueAt.getTime() + 86400000)
        reminder.notifiedOffsets = []
        reminder.dueNotified = false
      } else if (reminder.recurringType === "weekly") {
        reminder.dueAt = new Date(reminder.dueAt.getTime() + 604800000)
        reminder.notifiedOffsets = []
        reminder.dueNotified = false
      } else if (reminder.recurringType === "monthly") {
        const next = new Date(reminder.dueAt)
        next.setMonth(next.getMonth() + 1)
        reminder.dueAt = next
        reminder.notifiedOffsets = []
        reminder.dueNotified = false
      } else {
        reminder.isActive = false
      }
    }

    await reminder.save()
  }
}

export async function startReminderScheduler() {
  const bot = await ensureBotUser()
  console.log("🤖 Bot de recordatorios listo")

  cron.schedule("* * * * *", async () => {
    try {
      await checkReminders(bot)
    } catch (error) {
      console.error("Error en scheduler de recordatorios:", error)
    }
  })

  console.log("⏰ Scheduler de recordatorios iniciado (cada 1 minuto)")
}
