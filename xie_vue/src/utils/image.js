
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

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
    // Remove logical 'images/' prefix if it's duplicated in path vs storage, 
    // but Laravel usually stores 'images/filename.jpg'.
    // If we assume our server serves from root/images or root/storage/images?
    // Controller uses: store('images', 'public'). 
    // 'public' disk usually linked to /storage. 
    // So 'images/foo.jpg' -> '/storage/images/foo.jpg'.

    // Previous code used: `${baseUrl}/images/${item.image}` where base was localhost:8000.
    // This implies explicit route or symlink.
    // Let's assume standard Laravel `php artisan storage:link`.

    // If the old code added `/images/` manually, it might mean the DB only stores filename?
    // Controller: `$path = $request->file('image')->store('images', 'public');`
    // $path will be "images/hash.jpg".
    // So we need `http://host/storage/images/hash.jpg`.

    // Old code: `${baseUrl}/images/${item.image}`.
    // If item.image is "images/hash.jpg", then it becomes ".../images/images/hash.jpg".
    // If item.image is "hash.jpg", then ".../images/hash.jpg".

    // Let's rely on standard: /storage/ + path.
    // But wait, the previous code logic was: `const baseUrl = api.defaults.baseURL.replace('/api', '')` -> localhost:8000.
    // Then `${baseUrl}/images/${item.image}`.
    // If DB has "images/foo.jpg", this result is "localhost:8000/images/images/foo.jpg".
    // Likely incorrect unless a specific route serves it.

    // Safest approach: Map "images/" path to storage URL.
    // If path starts with "images/", prepend "/storage/".
    // Actually, let's keep it robust.

    // Assuming 'images' in DB means it's in public disk under images folder.
    // A simple heuristic:
    return `${API_BASE_URL.replace('/api', '')}/storage/${imagePath}`
}
