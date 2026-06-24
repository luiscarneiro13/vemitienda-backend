import express from "express"
import { LinkPreviewController, linkPreviewLimiter } from "../controllers/index.js"
import { mdAuth } from "../middlewares/index.js"

const api = express.Router()

api.get("/link-preview", [mdAuth.asureAuth, linkPreviewLimiter], LinkPreviewController.getLinkPreview)

export const linkPreviewRoutes = api
