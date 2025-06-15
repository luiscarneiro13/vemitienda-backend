import { webcrypto } from "crypto"
if (!globalThis.crypto) globalThis.crypto = webcrypto

import sharp from "sharp"
import path from "path"
import fs from "fs"
import { Storage } from "megajs"

export async function convertToWebp(file) {

    if (!file || !file.path) {
        throw new Error("Archivo no válido o no recibido correctamente por form-data.")
    }

    const inputPath = file.path
    const outputName = path.basename(inputPath).replace(/\.\w+$/, ".webp")
    const outputPath = path.join("/tmp", outputName)

    await sharp(inputPath)
        .webp({ quality: 80 })
        .toFile(outputPath)

    return outputPath

}

export async function uploadToMegaWebp({ filePath, folderName = "MiWisaChat" }) {

    const email = process.env.MEGA_EMAIL
    const password = process.env.MEGA_PASSWORD

    const storage = await new Storage({ email, password }).ready
    const stat = fs.statSync(filePath)
    const stream = fs.createReadStream(filePath)

    // Procesar cada nivel de carpeta (subniveles incluidos)
    const folders = folderName.split("/")
    let currentFolder = storage.root

    for (const name of folders) {
        let next = currentFolder.children.find(f => f.name === name && f.directory)
        if (!next) next = await currentFolder.mkdir(name)
        currentFolder = next
    }

    // Subir dentro de la carpeta final
    const file = await currentFolder.upload(
        { name: path.basename(filePath), size: stat.size },
        stream
    ).complete

    const url = await file.link()
    return { url }
    
}


