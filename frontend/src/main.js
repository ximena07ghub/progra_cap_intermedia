import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { pinia } from './stores'

import './assets/styles/tailwind.css'
import './assets/styles/main.css'
import './assets/styles/components.css'
import './assets/styles/forms.css'

createApp(App)
  .use(pinia)
  .use(router)
  .mount('#app')

