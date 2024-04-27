<script setup>

    import { ref } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';

    const router = useRouter();
    const form = ref({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    });

    const getCookie = (cookieName) => {
        const name = cookieName + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookieArray = decodedCookie.split(';');

        for (let i = 0; i < cookieArray.length; i++) {
        let cookie = cookieArray[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) === 0) {
            return cookie.substring(name.length, cookie.length);
        }
        }
        return "";
    };

    const getToken = async () => {
        await axios.get('/sanctum/csrf-cookie');
    };

    const handleRegister = async () => {
        await getToken();
        
        await axios.post(
        '/register', 
        {
            name: form.value.name,
            email: form.value.email,
            password: form.value.password,
            password_confirmation: form.value.password_confirmation
        },
        {
            headers: {
            accept: 'application/json',
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
            },
            withCredentials: true,
        });
        router.push("/");
    };

</script>

<template>
    <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-6 sm:py-12">
        <div class="sm:mx-auto sm:max-w-xl sm:w-full sm:rounded-lg">
            <div class="bg-white px-10 py-10 shadow-xl ring-1 ring-gray-900/5 grid sm:grid-cols-1 gap-8">
                <div class="flex items-center justify-center sm:justify-start">
                    <h1 class="text-2xl font-semibold mr-2 blackrun dev">europe</h1>
                    <img src="./img/Globe.svg" class="h-8 mr-2 text-indigo-600" alt="Tailwind Play" />
                    <h1 class="text-2xl font-semibold text-indigo-600">Ways</h1>
                </div>
                <div class="space-y-6 text-base leading-7 text-gray-600">
                    <div id="app">
                        <form @submit.prevent ="handleRegister">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <input 
                                    id="name" 
                                    v-model="form.name"
                                    type="text" 
                                    class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    placeholder="Username">
                            </div>
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input 
                                    id="email"
                                    v-model="form.email"
                                    type="email" 
                                    class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    placeholder="Email">
                            </div>
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input 
                                    id="password"
                                    v-model="form.password"
                                    type="password" 
                                    class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    placeholder="Password">
                            </div>
                            <div class="mb-4">
                                <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Confirm password</label>
                                <input 
                                    id="confirmPassword"
                                    v-model="form.password_confirmation"
                                    type="password" 
                                    class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    placeholder="Confirm password">
                            </div>
                            <button type="submit" class="w-full py-3 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Sign Up</button>
                        </form>
                        <div class="mt-6 text-sm">
                            <p class="text-gray-600">Already have an account? <router-link :to="{name: 'Login'}" class="text-indigo-600 hover:text-indigo-800">Sign In</router-link></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>