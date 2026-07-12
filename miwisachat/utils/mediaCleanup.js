import fs from "fs"
import path from "path"

function safeUnlink(filePath) {
    return fs.promises.unlink(filePath).catch((error) => {
        if (error.code !== "ENOENT") {
            console.error(`No se pudo eliminar el archivo ${filePath}:`, error.message)
        }
    })
}

// Borra del disco las imágenes/videos/archivos de los mensajes eliminados,
// pero solo si ningún otro mensaje (p. ej. un reenvío que comparte el mismo
// archivo físico) sigue referenciándolos.
export async function deleteOrphanAttachments(Model, messages, deletedIds) {
    const tasks = []

    for (const msg of messages) {
        if (msg.type === "IMAGE" && msg.message && !msg.message.startsWith("http")) {
            const stillReferenced = await Model.exists({
                type: "IMAGE",
                message: msg.message,
                _id: { $nin: deletedIds },
            })
            if (!stillReferenced) {
                tasks.push(safeUnlink(path.join("uploads", msg.message)))
            }
        }

        if ((msg.type === "VIDEO" || msg.type === "FILE") && msg.attachment?.url) {
            const stillReferenced = await Model.exists({
                "attachment.url": msg.attachment.url,
                _id: { $nin: deletedIds },
            })
            if (!stillReferenced) {
                const dir = msg.type === "VIDEO" ? "uploads/videos" : "uploads/files"
                tasks.push(safeUnlink(path.join(dir, msg.attachment.url)))
                if (msg.type === "VIDEO" && msg.attachment.thumbnail) {
                    tasks.push(safeUnlink(path.join(dir, msg.attachment.thumbnail)))
                }
            }
        }
    }

    await Promise.all(tasks)
}
