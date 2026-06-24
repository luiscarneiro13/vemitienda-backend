import dns from "dns"
import { promisify } from "util"

const dnsLookup = promisify(dns.lookup)

// RFC 1918 privadas, loopback, link-local e IPv6 privadas
const PRIVATE_PATTERNS = [
    /^10\./,
    /^172\.(1[6-9]|2\d|3[01])\./,
    /^192\.168\./,
    /^127\./,
    /^169\.254\./,
    /^0\./,
    /^::1$/,
    /^fc[0-9a-f]{2}:/i,
    /^fd[0-9a-f]{2}:/i,
    /^fe80:/i,
    /^::ffff:10\./i,
    /^::ffff:172\.(1[6-9]|2\d|3[01])\./i,
    /^::ffff:192\.168\./i,
    /^::ffff:127\./i,
]

function isPrivateIP(ip) {
    return PRIVATE_PATTERNS.some((re) => re.test(ip))
}

/**
 * Valida que una URL sea segura (no apunte a IPs privadas).
 * Lanza un objeto { status, msg } si la validación falla.
 */
export async function validateSSRF(urlString) {
    let parsedUrl
    try {
        parsedUrl = new URL(urlString)
    } catch {
        throw { status: 400, msg: "URL inválida" }
    }

    if (!["http:", "https:"].includes(parsedUrl.protocol)) {
        throw { status: 400, msg: "Solo se permiten protocolos http y https" }
    }

    let address
    try {
        const result = await dnsLookup(parsedUrl.hostname)
        address = result.address
    } catch {
        throw { status: 400, msg: "No se pudo resolver el hostname" }
    }

    if (isPrivateIP(address)) {
        throw { status: 403, msg: "La URL apunta a una dirección IP privada (bloqueado por seguridad)" }
    }

    return address
}
