import express from "express"
import { inistSocketServer } from "./utils/index.js"
import http from "http"
import bodyParser from "body-parser"
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
} from "./routes/index.js"

dotenv.config()
const app = express()
const server = http.createServer(app)

inistSocketServer(server)

// Configure body parser
app.use(bodyParser.urlencoded({ extended: true }))
app.use(bodyParser.json())

const PREFIX = "/api/miwisachat"

//configure static folder
app.use(`${PREFIX}/uploads`, express.static("uploads"))

app.use(express.json({ limit: "20mb" }))
app.use(express.urlencoded({ extended: true, limit: "20mb" }))

//Configure header http cors
app.use(cors())

//Configure loguer http request
app.use(morgan("dev"))


//Configure routing
app.use(PREFIX, authRoutes)
app.use(PREFIX, userRoutes)
app.use(PREFIX, chatRoutes)
app.use(PREFIX, chatMessageRoutes)
app.use(PREFIX, groupRoutes)
app.use(PREFIX, groupMessageRoutes)

export { server }