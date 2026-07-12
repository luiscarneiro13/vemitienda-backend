import express from "express"
import { inistSocketServer } from "./utils/index.js"
import http from "http"
import cors from "cors"
import morgan from "morgan"
import dotenv from "dotenv"
import { fileURLToPath } from "url"
import path from "path"

const __dirname = path.dirname(fileURLToPath(import.meta.url))
import {
    authRoutes,
    userRoutes,
    chatRoutes,
    chatMessageRoutes,
    groupRoutes,
    groupMessageRoutes,
    reminderRoutes,
    linkPreviewRoutes,
    mediaRoutes,
} from "./routes/index.js"

dotenv.config()
const app = express()
const server = http.createServer(app)

inistSocketServer(server)

const PREFIX = "/api/miwisachat"

//configure static folder
app.use(`${PREFIX}/public`, express.static(path.join(__dirname, "public")))
app.use(`${PREFIX}/uploads/images`, express.static(path.join(__dirname, "uploads/images")))
app.use(`${PREFIX}/uploads/avatar`, express.static(path.join(__dirname, "uploads/avatar")))
app.use(`${PREFIX}/uploads/group`, express.static(path.join(__dirname, "uploads/group")))

app.use(express.json({ limit: "20mb" }))
app.use(express.urlencoded({ extended: true, limit: "20mb" }))

//Configure header http cors
app.use(cors())

//Configure loguer http request
app.use(morgan(process.env.NODE_ENV === "production" ? "combined" : "dev"))


//Configure routing
app.use(PREFIX, authRoutes)
app.use(PREFIX, userRoutes)
app.use(PREFIX, chatRoutes)
app.use(PREFIX, chatMessageRoutes)
app.use(PREFIX, groupRoutes)
app.use(PREFIX, groupMessageRoutes)
app.use(PREFIX, reminderRoutes)
app.use(PREFIX, linkPreviewRoutes)
app.use(PREFIX, mediaRoutes)

//Servir build web de MiWisaChat (Expo Web) bajo /miwisachat
//OJO: distinto de PREFIX ("/api/miwisachat"), no interfiere con las rutas de API
const WEB_PREFIX = "/miwisachat"
const publicWebDir = path.join(__dirname, "public-web")

app.use(WEB_PREFIX, express.static(publicWebDir))

//Fallback SPA: cualquier GET bajo /miwisachat/* que no matcheó un archivo estático
//responde con index.html para que el ruteo del cliente (React Navigation) funcione
//al refrescar la página en cualquier ruta
app.get(`${WEB_PREFIX}/*splat`, (req, res) => {
    const indexPath = path.join(publicWebDir, "index.html")
    res.sendFile(indexPath, (err) => {
        if (err) {
            res.status(200).send(
                "Build web no encontrado, ejecuta el build del cliente y cópialo aquí (miwisachat/public-web/)."
            )
        }
    })
})

export { server }