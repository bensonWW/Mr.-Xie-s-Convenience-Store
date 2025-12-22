/**
 * Formats a price in cents to a currency string.
 * @param {number} cents - The price in cents (integer).
 * @param {string} currency - The currency symbol (default: NT$).
 * @returns {string} The formatted price string (e.g., "NT$ 100").
 */
export function formatPrice(cents, currency = 'NT$') {
    if (cents === null || cents === undefined || isNaN(cents)) {
        return `${currency} 0`
    }
    // Convert cents to dollars (or basic unit)
    const amount = cents / 100

    // Format with commas, no decimals if integer, or 2 decimals if needed?
    // User context implies integer-like handling for convenience store ("分" usually implies we might have decimals if converted back,
    // but usually Taiwan dollars are integers. However, system is using cents internally).
    // Let's assume we display as integer or decimal based on remainder?
    // Professor said: "0.1 + 0.2 != 0.3", implying he hates floats.
    // But for display, if it's 10000 cents ($100), show 100.
    // If it's 10050 cents ($100.50), show 100.50.

    return `${currency} ${amount.toLocaleString('en-US', {
        minimumFractionDigits: amount % 1 === 0 ? 0 : 2,
        maximumFractionDigits: 2
    })}`
}
