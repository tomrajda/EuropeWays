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

        <div class="container max-w-5xl mx-auto bg-zinc-200 bg-opacity-60 shadow-md rounded-lg py-6 px-6 pt-4 pb-10 mb-20 mt-20">
            <h1 class="text-3xl text-center font-semibold mb-8 mt-10" >Don't know where you want to go? Our ideas for your trip!</h1>
            
            <div class="grid grid-cols-3 gap-6">

            <div class="bg-white bg-opacity-80 text-center p-4 rounded-lg">
                <img src="\public\paris.jpg" alt="Zdjęcie 1" class="mx-auto h-70 w-60 rounded-md mb-4">
                <p class="text-gray-800 text-3xl font-semibold">Paris</p>
                <br>
                <p class="text-gray-800 text-xl font-semibold">Tutaj można cenę lub date</p>
            </div>
    
            <div class="bg-white bg-opacity-80 text-center p-4 rounded-lg">
                <img src="\public\rome.jpg" alt="Zdjęcie 2" class="mx-auto h-70 w-60 rounded-md mb-4">
                <p class="text-gray-800 text-3xl font-semibold">Rome</p>
                <br>
                <p class="text-gray-800 text-xl font-semibold">Tutaj można cenę lub date</p>
            </div>
    
            <div class="bg-white bg-opacity-80 text-center p-4 rounded-lg">
                <img src="\public\barcelona.jpg" alt="Zdjęcie 3" class="mx-auto h-70 w-60 rounded-md mb-4">
                <p class="text-gray-800 text-3xl font-semibold">Barcelona</p>
                <br>
                <p class="text-gray-800 text-xl font-semibold">Tutaj można cenę lub date</p>
            </div>
    
        </div>
      </div>
    
</template>