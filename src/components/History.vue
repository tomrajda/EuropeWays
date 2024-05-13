<script setup>
  import { ref } from 'vue';
  import axios from 'axios';

  const userData = ref({
    api_url: '',
    amount: 0
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

  const saveHistory = async () => {
    try {
      await axios.get('/sanctum/csrf-cookie');

      const xsrfToken = getCookie('XSRF-TOKEN');

      const config = {
        headers: {
          'X-XSRF-TOKEN': xsrfToken,
        },
      };

      const response = await axios.post('/api/save-history', userData.value, config);
      
      console.log(response.data);
    } catch (error) {
      console.error(error);
    }
  };


</script>

<template>
  <div>
    <form @submit.prevent="saveHistory">
      <label for="api_url">api_url:</label>
      <input type="text" id="api_url" v-model="userData.api_url" required>
      
      <label for="email">amount:</label>
      <input type="number" step="0.01" id="amount" v-model="userData.amount" required>

      <button type="submit">Save</button>
    </form>
  </div>
</template>