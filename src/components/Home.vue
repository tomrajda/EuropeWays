<script setup>
    
    import {ref, onMounted} from 'vue';
    import axios from 'axios';

    const user = ref();
    
    onMounted(async() => {
        const data = axios.get('/api/user');
        user.value = data.data;
    });

</script>

<template>
    <h1>Home page</h1>
</template>