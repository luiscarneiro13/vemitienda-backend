import "dotenv/config"
import { server } from "./app.js"
import { IP_SERVER, PORT, DB_USER, DB_PASSWORD, DB_HOST, DB_PARAMS } from "./constants.js"
import { io } from "./utils/socketServer.js"
import mongoose from "mongoose"
import { startReminderScheduler } from "./utils/reminderScheduler.js"
import { runSeed } from "./utils/seed.js"

const mongoDbUrl = `mongodb+srv://${DB_USER}:${DB_PASSWORD}@${DB_HOST}/?${DB_PARAMS}`

const connectToMongo = async () => {
    try {
        mongoose.set('strictQuery', false);

        await mongoose.connect(mongoDbUrl);

        console.log('MongoDB conectado exitosamente');

        startReminderScheduler()
        await runSeed()

        server.listen(PORT, '0.0.0.0', () => {
            console.log(`http://${IP_SERVER}:${PORT}/api`)
        })

        io.sockets.on("connection", (socket) => {
            console.log("Nuevo usuario conectado")

            socket.on("disconnect", () => {
                console.log("Usuario desconectado")
            })

            socket.on("subscribe", (room) => {
                if (!mongoose.Types.ObjectId.isValid(room)) return
                socket.join(room)
            })

            socket.on("unsubscribe", (room) => {
                socket.leave(room)
            })
        })

    } catch (error) {
        console.error('Error al conectar con MongoDB:', error);
        process.exit(1);
    }
};

connectToMongo();