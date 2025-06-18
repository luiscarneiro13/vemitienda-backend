import { User } from "../models/index.js"
import bcrypt from "bcryptjs"
import { jwt } from "../utils/index.js"
import { responseServerError } from "../constants.js"

async function register(req, res) {

    try {
        const { email, password } = req.body

        const user = new User({
            email: email.toLowerCase(),
            password: password
        })

        const salt = bcrypt.genSaltSync(10)
        const hashPassword = bcrypt.hashSync(password, salt)

        user.password = hashPassword

        const userStorage = await user.save()
        res.status(201).send(userStorage)

    } catch (error) {
        console.error(error)
        res.status(400).send({ msg: "Error al registrar el usuario" })
    }
}

async function login(req, res) {
    try {
        const { email, password, expo_token } = req.body;

        const emailTrimmed = email.trim().toLowerCase();
        const passwordTrimmed = password.trim();

        const userStorage = await User.findOne({ email: emailTrimmed });

        if (!userStorage) {
            return res.status(404).send({ msg: "Usuario no encontrado" });
        }

        const isValidPassword = await bcrypt.compare(passwordTrimmed, userStorage.password);

        if (!isValidPassword) {
            return res.status(400).send({ msg: "Usuario inválido" });
        }

        if (expo_token) {
            userStorage.expo_token = expo_token;
            userStorage.expo_token_updated_at = new Date();
            await userStorage.save();
        }

        res.status(200).send({
            access: jwt.createAccessToken(userStorage),
            refresh: jwt.createRefreshToken(userStorage),
        });
    } catch (error) {
        responseServerError(res, error);
    }
}


async function refreshAccesToken(req, res) {
    const { refreshToken } = req.body

    if (!refreshToken) { res.status(400).send({ msg: "Token requerido" }) }

    const hasExpired = jwt.hasExpiredToken(refreshToken)

    if (hasExpired) { res.status(400).send({ msg: "Token expirado" }) }

    const { user_id } = jwt.decoded(refreshToken)

    try {
        const userStorage = await User.findById(user_id); // Asegúrate de que user_id esté definido, por ejemplo, de req.params o req.body
        const accessToken = jwt.createAccessToken(userStorage);
        res.status(200).send({ accessToken });
    } catch (error) {
        responseServerError(res, error)
    }
}

export const AuthController = {
    register,
    login,
    refreshAccesToken
}

