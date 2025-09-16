import { createRouter, createWebHistory } from 'vue-router'
import Login from '../pages/Login.vue'
import PostsList from '../pages/PostsList.vue'

const routes = [
    { path: '/login', component: Login },
    { path: '/blog-posts', component: PostsList,},
    { path: '/:pathMatch(.*)*', redirect: '/login' },
]

export default createRouter({ history: createWebHistory(), routes })
