
import { ref } from 'vue'

const visible = ref(false)
const message = ref('')
const color = ref('info')
const timeout = ref(3000)
let timeoutId = null

export function useSnackbar() {
    function notify(msg, options = {}) {
        message.value = msg
        color.value = options.color || 'info'
        timeout.value = options.duration || 3000
        visible.value = true

        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => {
            visible.value = false
        }, timeout.value)
    }

    return {
        visible,
        message,
        color,
        timeout,
        notify,
    }
}
