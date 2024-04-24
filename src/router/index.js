import { createRouter, createWebHistory } from "vue-router";
import Home from '../components/Home.vue'
import Login from '../components/Login.vue'
import Register from '../components/Register.vue'
import OneWayCheapest from '../components/OneWayCheapest.vue'

const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/login', name: 'Login', component: Login },
    { path: '/signup', name: 'Signup', component: Register },
    { path: '/onewaycheapest', name: 'OneWayCheapest', component: OneWayCheapest }
];

const router = createRouter({
    history: createWebHistory(), // You need to call createWebHistory as a function
    routes
});

export default router;