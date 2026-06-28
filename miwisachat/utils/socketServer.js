import { Server as SocketServer } from "socket.io"
import jwt from "jsonwebtoken"
import mongoose from "mongoose"
import { JWT_SECRET_KEY } from "../constants.js"

export let io = null

export function inistSocketServer(server) {
    io = new SocketServer(server, {
        cors: {
            origin: "*"
        },
        path: "/api/miwisachat/socket.io",
        pingTimeout: 20000,
        pingInterval: 25000,
        maxHttpBufferSize: 1e6,
    })

    io.use((socket, next) => {
        const token = socket.handshake.auth?.token
        if (!token) return next(new Error("Token requerido"))
        try {
            const payload = jwt.verify(token, JWT_SECRET_KEY)
            socket.user = payload
            next()
        } catch {
            next(new Error("Token inválido"))
        }
    })
}
