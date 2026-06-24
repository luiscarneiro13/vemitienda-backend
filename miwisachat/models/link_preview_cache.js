import mongoose from "mongoose"

const LinkPreviewCacheSchema = mongoose.Schema({
    url: { type: String, unique: true, index: true },
    preview: {
        url: String,
        title: String,
        description: String,
        image: String,
        siteName: String,
        favicon: String,
    },
    cachedAt: { type: Date, default: Date.now, expires: 86400 },
})

export const LinkPreviewCache = mongoose.model("LinkPreviewCache", LinkPreviewCacheSchema)
