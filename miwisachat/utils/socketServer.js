import { Server as SocketServer } from "socket.io"

export let io = null

export function inistSocketServer(server) {
    io = new SocketServer(server, {
        cors: {
            origin: "*"
        },
        path: "/api/miwisachat/socket.io"
    })
}