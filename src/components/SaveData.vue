<script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  import { useRouter } from 'vue-router';
  import { useAuthStore } from '../stores/auth';

  const authStore = useAuthStore();
  const router = useRouter();
  const userData = ref({
    username: '',
    email: ''
  });

  const saveData = async () => {
    try {
      // Pobierz token CSRF
      await axios.get('/sanctum/csrf-cookie');

      // Pobierz token XSRF-TOKEN z ciasteczka
      const xsrfToken = getCookie('XSRF-TOKEN');

      // Ustaw nagłówki z tokenem CSRF i XSRF-TOKEN
      const config = {
        headers: {
          'X-XSRF-TOKEN': xsrfToken,
        },
      };

      // Wykonaj zapytanie POST
      const response = await axios.post('/api/save-data', userData.value, config);
      
      console.log(response.data);
    } catch (error) {
      console.error(error);
    }
  };

  // Funkcja do pobierania wartości ciasteczka na podstawie nazwy
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
</script>

<template>
  <div>
    <form @submit.prevent="saveData">
      <label for="username">Username:</label>
      <input type="text" id="username" v-model="userData.username" required>
      
      <label for="email">Email:</label>
      <input type="email" id="email" v-model="userData.email" required>

      <button type="submit">Save</button>
    </form>
  </div>
</template>
