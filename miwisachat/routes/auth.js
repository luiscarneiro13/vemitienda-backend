import express from "express"
import rateLimit, { ipKeyGenerator } from "express-rate-limit"
import { AuthController } from "../controllers/index.js"

const api = express.Router()

const authLimiter = rateLimit({
    max: 10,
    windowMs: 15 * 60 * 1000,
    keyGenerator: (req) => req.user?.user_id?.toString() || ipKeyGenerator(req),
    message: { msg: "Demasiados intentos. Intenta de nuevo en 15 minutos." }
})

api.post("/auth/register", authLimiter, AuthController.register)
api.post("/auth/login", authLimiter, AuthController.login)
api.post("/auth/refresh_access_token", AuthController.refreshAccesToken)

export const authRoutes = api