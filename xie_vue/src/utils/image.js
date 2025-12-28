import api from '../services/api'

/**
 * Resolve product image URL.
 * Handles absolute URLs (http) and relative paths (stored in DB).
 * 
 * @param {string} imagePath - The path from DB
 * @returns {string} Fully qualified URL
 */
export function resolveImageUrl(imagePath) {
    if (!imagePath) return ''
    if (imagePath.startsWith('http')) {
        return imagePath
    }

    // Get backend base URL from api config
    // This allows us to share the same dynamic base URL logic as the API service
    const apiBaseUrl = api.defaults.baseURL || ''
    const baseUrl = apiBaseUrl.replace('/api', '')

    // Check if path already contains storage prefix to avoid double prefixing
    // DB usually stores as "images/filename.jpg"
    // We want: "{baseUrl}/storage/images/filename.jpg"

    let cleanPath = imagePath
    if (cleanPath.startsWith('/')) {
        cleanPath = cleanPath.substring(1)
    }

    if (cleanPath.startsWith('storage/')) {
        return `${baseUrl}/${cleanPath}`
    }

    return `${baseUrl}/storage/${cleanPath}`
}
