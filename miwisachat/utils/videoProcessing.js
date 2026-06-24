import ffmpeg from "fluent-ffmpeg"
import ffmpegInstaller from "@ffmpeg-installer/ffmpeg"

ffmpeg.setFfmpegPath(ffmpegInstaller.path)

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
