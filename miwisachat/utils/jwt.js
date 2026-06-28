import jsonwebtoken from "jsonwebtoken"
import { JWT_SECRET_KEY } from "../constants.js"

function createAccessToken(user) {
    const expToken = new Date()
    expToken.setHours(expToken.getHours() + 24)

    const payolad = {
        token_type: "access",
        user_id: user.id,
        iat: Math.floor(Date.now() / 1000),
        exp: Math.floor(expToken.getTime() / 1000)
    }

    return jsonwebtoken.sign(payolad, JWT_SECRET_KEY)
}

function createRefreshToken(user) {
    const expToken = new Date()
    expToken.setMonth(expToken.getMonth() + 1) //1 mes

    const payolad = {
        token_type: "refresh",
        user_id: user.id,
        iat: Math.floor(Date.now() / 1000),
        exp: Math.floor(expToken.getTime() / 1000)
    }

    return jsonwebtoken.sign(payolad, JWT_SECRET_KEY)
}

// Lanza excepción si el token es inválido o ha expirado
function decoded(token) {
    return jsonwebtoken.verify(token, JWT_SECRET_KEY)
}

// Mantenida por compatibilidad; verify() ya lanza si expiró,
// pero se puede usar como helper explícito
function hasExpiredToken(token) {
    try {
        jsonwebtoken.verify(token, JWT_SECRET_KEY)
        return false
    } catch {
        return true
    }
}

export const jwt = {
    createAccessToken,
    createRefreshToken,
    decoded,
    hasExpiredToken
}