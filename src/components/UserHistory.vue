<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const historyData = ref([]);

const fetchHistoryData = async () => {
  try {
    const response = await axios.get('/api/history-data');
    historyData.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

onMounted(() => {
  fetchHistoryData();
});
</script>

<template>
    <div>
        <h2>History Data</h2>
        <ul>
        <li v-for="data in historyData" :key="data.id">
            <p>User ID: {{ data.user_id }}</p>
            <p>API URL: {{ data.api_url }}</p>
            <p>Amount: {{ data.amount }}</p>
        </li>
        </ul>
    </div>
</template>