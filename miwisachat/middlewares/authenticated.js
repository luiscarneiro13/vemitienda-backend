import { jwt } from "../utils/index.js"
import { User } from "../models/index.js"

async function asureAuth(req, res, next) {
    if (!req.headers.authorization) {
        return res
            .status(406)
            .send({ msg: "La petición no tiene la cabecera de autenticación" })
    }

    const token = req.headers.authorization.replace("Bearer ", "")

    try {
        const hasExpired = jwt.hasExpiredToken(token)

        if (hasExpired) { return res.status(400).send({ ms: "Token expirado" }) }

        const payload = jwt.decoded(token)

        if (payload.token_type !== "access") {
            return res.status(401).send({ msg: "Token inválido" })
        }

        const user = await User.findById(payload.user_id).select("tokenVersion")

        if (!user || (payload.token_version ?? 0) !== (user.tokenVersion ?? 0)) {
            return res.status(401).send({ msg: "Sesión cerrada. Inicia sesión nuevamente." })
        }

        req.user = payload

        next()

    } catch (error) {
        return res.status(400).send({ msg: "Token inválido" })
    }
}

export const mdAuth = {
    asureAuth,
}