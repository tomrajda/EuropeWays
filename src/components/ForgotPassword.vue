<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();

const router = useRouter();

const email = ref('');
const status = ref('');

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

const handleForgotPassword = async (email) => {
  await getToken();
  try {
  const response = await axios.post(
    '/forgot-password', 
    {
      email: email,
    },
    {
      headers: {
        accept: 'application/json',
        'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
      },
      withCredentials: true,
    }
  );
  status.value = response.data.status;
  await authStore.getUser();
  router.push("/");
} catch(error){
  if(error.response.status === 422) {
    authStore.setErrors(error.response.data.errors);
  }
}
};
</script>

<template>
  <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-6 sm:py-12">
    <div class="sm:mx-auto sm:max-w-xl sm:w-full sm:rounded-lg">
      <div class="bg-white px-10 py-10 shadow-xl ring-1 ring-gray-900/5 grid sm:grid-cols-1 gap-8">
        <div class="flex items-center justify-center sm:justify-start">
          <h1 class="text-2xl font-semibold mr-2 text-black">europe</h1>
          <img src="./img/Globe.svg" class="h-8 mr-2 text-indigo-600" alt="Tailwind Play" />
          <h1 class="text-2xl font-semibold text-indigo-600">Ways</h1>
        </div>
        <div class="space-y-6 text-base leading-7 text-gray-600">
          <div id="app">
            <form @submit.prevent="handleForgotPassword(email)">
              <div class="mb-8">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Please, enter your account email</label>
                <input 
                  id="email" 
                  type="email"
                  v-model="email" 
                  class="form-input mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                  placeholder="Email"
                />
              </div>
              <button 
                type="submit" 
                class="w-full rounded-lg bg-indigo-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Submit
              </button>
            </form>
            <div class="mt-6 m-2 p-2 text-green-900 font-semibold bg-green-300 rounded-md" v-if="status">{{ status }}</div>
            <div class="mt-6 m-2 text-red-900 font-semibold bg-red-300 rounded-md">
              <span v-for="error in authStore.errors" :key="error">
                  <div>{{ error[0] }}</div>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
