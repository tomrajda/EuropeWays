<script setup>

    import { useAuthStore } from '../stores/auth';
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    
    const authStore = useAuthStore();
    const router = useRouter();

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

    const handleLogout = async () => {
        await getToken();
        await axios.post(
            '/logout',
            {},
            {
            headers: {
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
            },
            }
        );
        authStore.authUser = null;
        router.push('/');
    };

</script>

<template>
    <nav>
        <div class=" bg-sky-950  flex flex-wrap  items-center justify-between" bis_skin_checked="1">
            <router-link :to="{name: 'Home'}" class="flex items-center text-white p-10 pl-80">
                <p class="text-xl font-semibold  blackrun dev">sky</p>
                <img src="/public/plane.svg" alt="Logo" class="h-10 sm:h-12">
                <p class="text-xl font-semibold text-sky-700">Saver</p>
            </router-link>
            <button 
                data-collapse-toggle="navbar-default"
                type="button" 
                class="
                    ml-3
                    inline-flex 
                    items-center 
                    rounded-lg 
                    p-2 
                    text-sm text-gray-500 
                    hover:bg-gray-100  
                    focus:outline-none focus:ring-2 focus:ring-gray-200 
                    dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 
                    md:hidden
                "
                aria-controls="navbar-default"
                aria-expanded="false"
            >
            <span class="sr-only">Open main menu</span>
                <svg
                    class="h-6 w-6"
                    aria-hidden="true"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://wwww.w3.org/2000/svg"    
                >
                    <path
                        fill-rule="evenodd"
                        d="m3 5A1 1 0 011-1H12A1 1 0 110 2h4A1 1 0 01-10-1zM3 10a1 1 0 011-1h12a1 1 0 110"
                        clip-rule="evenodd"
                    ></path>
                </svg>
            </button>
            <div 
            class="hidden w-full md:block md:w-auto"
                id="navbar-default"
                bis_skin_checked="1"
            >
                <ul 
                    class="
                        mt-0
                        mb-0
                        flex flex-col
                        rounded-lg
                        border border-transparent
                        bg-gray-50
                        pr-80
                        dark:border-gray-700 dark:bg-transparent
                        md:mt-0
                        md:flex-row
                        md:space-x-16
                        md:border-0
                        md:bg-white
                        md:text-sm
                        md:font-medium
                        md:dark:bg-white-50 opacity-80
                    "
                >
                    <li>
                            <router-link :to="{name: 'FlightSearch'}"
                            class="
                                block
                                    rounded
                                    py-2
                                    pr-4
                                    pl-3
                                    text-white
                                    hover:bg-gray-100
                                    dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white
                                    md:border-0
                                    md:p-0
                                    md:hover:bg-transparent
                                    md:hover:text-blue-700
                                    md:dark:hover:bg-transparent
                                    md:dark:hover:text-white"
                                >Flight search</router-link
                            >
                        </li>
                    <template v-if="!authStore.user">
                        <li>
                            <router-link :to="{name: 'Login'}"
                                class="
                                    block
                                    rounded
                                    py-2
                                    pr-4
                                    pl-3
                                    text-white
                                    hover:bg-gray-100
                                    dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white
                                    md:border-0
                                    md:p-0
                                    md:hover:bg-transparent
                                    md:hover:text-blue-700
                                    md:dark:hover:bg-transparent
                                    md:dark:hover:text-white
                                "
                                >Login</router-link
                            >
                        </li>
                        <li>
                            <router-link :to="{name: 'Signup'}"
                                class="
                                    block
                                    rounded
                                    py-2
                                    pr-4
                                    pl-3
                                    text-gray-700
                                    hover:bg-gray-100
                                    dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white
                                    md:border-0
                                    md:p-0
                                    md:hover:bg-transparent
                                    md:hover:text-blue-700
                                    md:dark:hover:bg-transparent
                                    md:dark:hover:text-white
                                "
                                >Sign Up</router-link
                            >
                        </li>  
                    </template>
                    <template v-else>
                        <li>
                            <router-link :to="{name: 'UserHistory'}"
                                class="
                                    block
                                    rounded
                                    py-2
                                    pr-4
                                    pl-3
                                    text-gray-700
                                    hover:bg-gray-100
                                    dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white
                                    md:border-0
                                    md:p-0
                                    md:hover:bg-transparent
                                    md:hover:text-blue-700
                                    md:dark:hover:bg-transparent
                                    md:dark:hover:text-white
                                "
                                >My flights</router-link
                            >
                        </li>
                        
                        <button  
                            @click="handleLogout"
                            class="
                                block
                                rounded
                                py-2
                                pr-4
                                pl-3
                                text-gray-700
                                hover:bg-gray-100
                                dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white
                                md:border-0
                                md:p-0
                                md:hover:bg-transparent
                                md:hover:text-blue-700
                                md:dark:hover:bg-transparent
                                md:dark:hover:text-white
                            "
                        >Logout</button>
                    </template>
                </ul>
            </div>
        </div>
    </nav>
</template>