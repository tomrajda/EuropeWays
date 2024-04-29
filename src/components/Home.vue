<script setup>
    
    import { onMounted } from 'vue';
    import { useAuthStore } from '../stores/auth';

    const authStore = useAuthStore();

    onMounted(async () => {
        await authStore.getToken();
        await authStore.getUser();
    });

</script>

<template>
    <div v-if="authStore.user">
        <h1>Welcome again, {{ authStore.user.name }}!</h1>
        <h1>Your e-mail address is {{ authStore.user.email }}</h1> 
    </div>
    <div v-else>
        <h1>Please, login</h1>
    </div>
</template>