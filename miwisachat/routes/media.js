import express from "express"
import { MediaController } from "../controllers/index.js"
import { mdAuth } from "../middlewares/index.js"

const api = express.Router()

api.get("/media/:filename", [mdAuth.asureAuth], MediaController.serveMedia)

export const mediaRoutes = api
