import { createRouter, createWebHistory } from "vue-router";
import Home from '../components/Home.vue'
import Login from '../components/Login.vue'
import Register from '../components/Register.vue'
import ForgotPassword from "../components/ForgotPassword.vue";
import ResetPassword from "../components/ResetPassword.vue";
import UserHistory from "../components/UserHistory.vue";
import FlightSearch from "../components/FlightSearch.vue";

const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/login', name: 'Login', component: Login },
    { path: '/signup', name: 'Signup', component: Register },
    { path: '/forgot-password', name: 'ForgotPassword', component: ForgotPassword },
    { path: '/password-reset/:token', name: 'ResetPassword', component: ResetPassword },
    { path: '/myhistory', name: 'UserHistory', component: UserHistory },
    { path: '/flightsearch', name: 'FlightSearch', component: FlightSearch }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;