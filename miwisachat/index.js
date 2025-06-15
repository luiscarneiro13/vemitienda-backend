import { server } from "./app.js"
import { IP_SERVER, PORT, DB_USER, DB_PASSWORD, DB_HOST, DB_PARAMS } from "./constants.js"
import { io } from "./utils/socketServer.js"
import mongoose from "mongoose"

const mongoDbUrl = `mongodb+srv://${DB_USER}:${DB_PASSWORD}@${DB_HOST}/?${DB_PARAMS}`

const connectToMongo = async () => {
    try {
        mongoose.set('strictQuery', false);

        await mongoose.connect(mongoDbUrl);

        console.log('MongoDB conectado exitosamente');

        server.listen(PORT, () => {
            console.log("####################")
            console.log("##### API REST #####")
            console.log("####################")
            console.log(`http://${IP_SERVER}:${PORT}/api`)

            io.sockets.on("connection", (socket) => {
                console.log("Nuevo usuario conectado")

                socket.on("disconnet", () => {
                    console.log("Usuario desconectado")
                })

                socket.on("subscribe", (room) => {
                    socket.join(room)
                })

                socket.on("unsubscribe", (room) => {
                    socket.leave(room)
                })
            })
        })

    } catch (error) {
        console.error('Error al conectar con MongoDB:', error);
        process.exit(1);
    }
};

connectToMongo();