import { jwt } from "../utils/index.js"

function asureAuth(req, res, next) {
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

        req.user = payload

        next()

    } catch (error) {
        return res.status(400).send({ msg: "Token inválido" })
    }
}

export const mdAuth = {
    asureAuth,
}