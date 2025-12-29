/**
 * Formats a price (in Taiwan dollars) to a currency string.
 * Backend amounts are integers in TWD, so no cent conversion.
 * @param {number} amount - Price in dollars (integer preferred).
 * @param {string} currency - Currency symbol (default: NT$).
 * @returns {string} e.g. "NT$ 28,900".
 */
export function formatPrice(amount, currency = 'NT$') {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return `${currency} 0`
    }

    return `${currency} ${Number(amount).toLocaleString('en-US', {
        minimumFractionDigits: Number.isInteger(Number(amount)) ? 0 : 2,
        maximumFractionDigits: 2
    })}`
}
