import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Tailwind is loaded first as a utility layer. Legacy styles continue to
// preserve Home/Auth/Catalog while new views are built mainly with utilities.
import './assets/styles/tailwind.css'
import './assets/styles/main.css'
import './assets/styles/components.css'
import './assets/styles/forms.css'

createApp(App).use(router).mount('#app')
