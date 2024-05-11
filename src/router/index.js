import { createRouter, createWebHistory } from "vue-router";
import Home from '../components/Home.vue'
import Login from '../components/Login.vue'
import Register from '../components/Register.vue'
import OneWayCheapest from '../components/OneWayCheapest.vue'
import ForgotPassword from "../components/ForgotPassword.vue";
import ResetPassword from "../components/ResetPassword.vue";
import SaveData from "../components/SaveData.vue";

const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/login', name: 'Login', component: Login },
    { path: '/signup', name: 'Signup', component: Register },
    { path: '/onewaycheapest', name: 'OneWayCheapest', component: OneWayCheapest },
    { path: '/forgot-password', name: 'ForgotPassword', component: ForgotPassword },
    { path: '/password-reset/:token', name: 'ResetPassword', component: ResetPassword },
    { path: '/savedata', name: 'SaveData', component: SaveData }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;