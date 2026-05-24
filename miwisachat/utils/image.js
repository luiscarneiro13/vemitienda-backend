import { SERVER_URL } from "../constants.js"

export function getFilePath(file) {
    const filePath = file.path
    const fileSplit = filePath.split("/")

    return `${fileSplit[1]}/${fileSplit[2]}`
}

export function getPublicUrl(relativePath) {
    if (!relativePath) return undefined
    return `${SERVER_URL}/api/miwisachat/uploads/${relativePath}`
}