


import Vue from 'vue'
import axios from 'axios'

export default function ({ store }) {
     // const baseURL = 'http://localhost:8001/api/v1';
     const baseURL = "https://api.oredoh.org/api/v1";

     const instance = axios.create({
          baseURL: baseURL,
     })

     instance.interceptors.request.use(config => {
          const token = localStorage.getItem('oredoh-token')
          if (token) {
               config.headers.common['Authorization'] = `Bearer ${token}`
          }
          return config
     })
     instance.interceptors.response.use(
          response => {
               return response
          },
          error => {
               if (error?.response.status === 401) {
                    store.dispatch('ChangeAuthorization', true)
               }
               return Promise.reject(error)
          }
     )

     Vue.prototype.$axios2 = instance
}