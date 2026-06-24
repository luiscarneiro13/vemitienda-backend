import ffmpeg from "fluent-ffmpeg"
import ffmpegInstaller from "@ffmpeg-installer/ffmpeg"
import ffprobeInstaller from "@ffprobe-installer/ffprobe"
import { execSync } from "child_process"

// En Alpine/Docker, usa el ffmpeg del sistema si está disponible; si no, el del instalador.
function resolveFFmpegPath() {
    try {
        const syspath = execSync("which ffmpeg", { stdio: "pipe" }).toString().trim()
        if (syspath) return syspath
    } catch {}
    return ffmpegInstaller.path
}

function resolveFFprobePath() {
    try {
        const syspath = execSync("which ffprobe", { stdio: "pipe" }).toString().trim()
        if (syspath) return syspath
    } catch {}
    return ffprobeInstaller.path
}

ffmpeg.setFfmpegPath(resolveFFmpegPath())
ffmpeg.setFfprobePath(resolveFFprobePath())

export function getVideoDuration(filePath) {
    return new Promise((resolve, reject) => {
        ffmpeg.ffprobe(filePath, (err, metadata) => {
            if (err) return reject(err)
            resolve(metadata?.format?.duration || 0)
        })
    })
}

export function generateThumbnail(videoPath, outputFolder, outputFilename) {
    return new Promise((resolve, reject) => {
        ffmpeg(videoPath)
            .on("end", resolve)
            .on("error", reject)
            .screenshots({
                count: 1,
                timestamps: [1],
                filename: outputFilename,
                folder: outputFolder,
                size: "320x?",
            })
    })
}
