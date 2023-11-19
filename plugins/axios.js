import axios from "axios"

export default defineNuxtPlugin((NuxtApp) => {

    axios.defaults.withCredentials = true;
    axios.defaults.baseURL = 'https://localhost'

    return {
        provide: { 
            axios: axios
        },
    }
})