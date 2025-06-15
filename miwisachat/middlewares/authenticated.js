import { jwt } from "../utils/index.js"

function asureAuth(req, res, next) {
    if (!req.headers.authoritation) {
        return res
            .status(406)
            .send({ msg: "La petición no tiene la cabecera de autenticación" })
    }

    const token = req.headers.authoritation.replace("Bearer ", "")

    try {
        const hasExpired = jwt.hasExpiredToken(token)

        if (hasExpired) { return res.status(400).send({ ms: "Token expirado" }) }

        const payload = jwt.decoded(token)
        req.user = payload

        next()

    } catch (error) {
        return res.status(400).send({ msg: "Token inválido" })
    }
}

export const mdAuth = {
    asureAuth,
}