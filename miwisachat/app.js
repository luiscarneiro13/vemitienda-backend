import express from "express"
import { inistSocketServer } from "./utils/index.js"
import http from "http"
import cors from "cors"
import morgan from "morgan"
import dotenv from "dotenv"
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
app.use(`${PREFIX}/public`, express.static("public"))

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

export { server }