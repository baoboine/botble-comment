/**
 * Localstorage manage
 */
export default {
    get(key, defaultValue = null, format = (v) => v) {
        return format(localStorage.getItem(key) ? localStorage.getItem(key) : defaultValue)
    },

    set(key, val) {
        localStorage.setItem(key, val)
    },

    remove(key) {
        localStorage.removeItem(key)
    }
}
