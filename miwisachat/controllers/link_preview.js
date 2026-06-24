import fetch from "node-fetch"
import rateLimit, { ipKeyGenerator } from "express-rate-limit"
import validator from "validator"
import ssrfAgent from "ssrf-agent"
import { validateSSRF } from "../utils/ssrfCheck.js"
import { LinkPreviewCache } from "../models/index.js"
import { responseServerError } from "../constants.js"

export const linkPreviewLimiter = rateLimit({
    windowMs: 60 * 1000,
    max: 20,
    keyGenerator: (req) => req.user?.user_id?.toString() || ipKeyGenerator(req),
    standardHeaders: true,
    legacyHeaders: false,
    handler: (req, res) =>
        res.status(429).json({ msg: "Demasiadas solicitudes. Intenta de nuevo en un minuto." }),
})

function getOgMeta(html, property) {
    const patterns = [
        new RegExp(`<meta[^>]+property=["']og:${property}["'][^>]+content=["']([^"']+)["']`, "i"),
        new RegExp(`<meta[^>]+content=["']([^"']+)["'][^>]+property=["']og:${property}["']`, "i"),
    ]
    for (const re of patterns) {
        const m = html.match(re)
        if (m) return m[1]
    }
    return null
}

function getMetaByName(html, name) {
    const patterns = [
        new RegExp(`<meta[^>]+name=["']${name}["'][^>]+content=["']([^"']+)["']`, "i"),
        new RegExp(`<meta[^>]+content=["']([^"']+)["'][^>]+name=["']${name}["']`, "i"),
    ]
    for (const re of patterns) {
        const m = html.match(re)
        if (m) return m[1]
    }
    return null
}

async function scrapeOgTags(url) {
    const controller = new AbortController()
    const timer = setTimeout(() => controller.abort(), 5000)

    try {
        // ssrf-agent como segunda capa de protección contra DNS rebinding
        const response = await fetch(url, {
            signal: controller.signal,
            agent: ssrfAgent(url),
            headers: {
                "User-Agent": "Mozilla/5.0 (compatible; LinkPreviewBot/1.0)",
                Accept: "text/html,application/xhtml+xml",
            },
            redirect: "follow",
        })

        const contentType = response.headers.get("content-type") || ""
        if (!contentType.includes("text/html")) return null

        const html = await response.text()

        const title = getOgMeta(html, "title") || getMetaByName(html, "twitter:title") || html.match(/<title[^>]*>([^<]+)<\/title>/i)?.[1] || null
        const description = getOgMeta(html, "description") || getMetaByName(html, "description") || getMetaByName(html, "twitter:description") || null
        const image = getOgMeta(html, "image") || getMetaByName(html, "twitter:image") || null
        const siteName = getOgMeta(html, "site_name") || null

        return { title, description, image, siteName }
    } finally {
        clearTimeout(timer)
    }
}

async function getLinkPreview(req, res) {
    try {
        const { url } = req.query

        if (!url || !validator.isURL(url, { protocols: ["http", "https"], require_protocol: true })) {
            return res.status(400).json({ msg: "URL no proporcionada o inválida" })
        }

        // Buscar en caché primero
        const cached = await LinkPreviewCache.findOne({ url })
        if (cached) return res.status(200).json(cached.preview)

        // Validación SSRF primaria: DNS lookup + rangos privados
        try {
            await validateSSRF(url)
        } catch (err) {
            return res.status(err.status).json({ msg: err.msg })
        }

        // Scraping de OG tags
        let tags
        try {
            tags = await scrapeOgTags(url)
        } catch {
            return res.status(422).json({ msg: "No se pudo obtener el preview del enlace" })
        }

        if (!tags) {
            return res.status(422).json({ msg: "No se pudo obtener el preview del enlace" })
        }

        // Normalizar URL de imagen relativa → absoluta
        let image = tags.image
        if (image) {
            try {
                image = new URL(image).href
            } catch {
                const base = new URL(url)
                image = new URL(image, `${base.protocol}//${base.host}`).href
            }
        }

        const parsedUrl = new URL(url)
        const favicon = `${parsedUrl.protocol}//${parsedUrl.hostname}/favicon.ico`

        const preview = {
            url,
            title: tags.title || null,
            description: tags.description || null,
            image,
            siteName: tags.siteName || null,
            favicon,
        }

        // Guardar en caché (upsert para evitar race conditions)
        await LinkPreviewCache.findOneAndUpdate(
            { url },
            { url, preview, cachedAt: new Date() },
            { upsert: true, new: true }
        )

        return res.status(200).json(preview)
    } catch (error) {
        responseServerError(res, error)
    }
}

export const LinkPreviewController = { getLinkPreview }
