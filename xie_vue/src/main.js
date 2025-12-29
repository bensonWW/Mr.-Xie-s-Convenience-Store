import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'

import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

const options = {
  // You can set your default options here
}

const pinia = createPinia()

createApp(App).use(pinia).use(router).use(Toast, options).mount('#app')
