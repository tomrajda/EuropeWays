<script setup>
  
  import { ref } from 'vue';
  import axios from 'axios';
  import { useRouter } from 'vue-router';
  import { useAuthStore } from '../stores/auth';

  const authStore = useAuthStore();

  const router = useRouter();
  const form = ref({
    email: '',
    password: ''
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

  const handleLogin = async () => {
    await getToken();
    
    try {
      await axios.post(
        '/login', 
        {
          email: form.value.email,
          password: form.value.password
        },
        {
          headers: {
            accept: 'application/json',
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
          },
          withCredentials: true,
        }
      );
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
 
</template>
