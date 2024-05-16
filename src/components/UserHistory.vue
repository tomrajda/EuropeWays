<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const flightHistory = ref([]);
const loading = ref(true);
const error = ref(null);

const getCookie = (cookieName) => {
  const name = cookieName + "=";
  const decodedCookie = decodeURIComponent(document.cookie);
  const cookieArray = decodedCookie.split(';');

  for (let i = 0; i < cookieArray.length; i++) {
    let cookie = cookieArray[i].trim();
    if (cookie.indexOf(name) === 0) {
      return cookie.substring(name.length, cookie.length);
    }
  }
  return "";
};

const fetchFlightHistory = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie');

    const xsrfToken = getCookie('XSRF-TOKEN');

    const config = {
      headers: {
        'X-XSRF-TOKEN': xsrfToken,
      },
    };

    const response = await axios.get('/api/flight-history', config);
    flightHistory.value = response.data;
    loading.value = false;
  } catch (err) {
    console.error(err);
    error.value = 'Failed to fetch flight history';
    loading.value = false;
  }
};

const deleteFlight = async (flightId) => {
  try {
    await axios.get('/sanctum/csrf-cookie');

    const xsrfToken = getCookie('XSRF-TOKEN');

    const config = {
      headers: {
        'X-XSRF-TOKEN': xsrfToken,
      },
    };

    await axios.delete(`/api/flight-history/${flightId}`, config);
    flightHistory.value = flightHistory.value.filter(flight => flight.id !== flightId);
  } catch (err) {
    console.error(err);
    error.value = 'Failed to delete flight';
  }
};

const extractDetails = (url) => {
  const regex = /\/oneWayFares\/([A-Z]{3})\/([A-Z]{3})\/cheapestPerDay\?outboundMonthOfDate=([0-9]{4}-[0-9]{2}-[0-9]{2})/;
  const match = url.match(regex);
  const currencyMatch = url.match(/currency=([A-Z]{3})/);
  if (match) {
    return {
      departure: match[1],
      destination: match[2],
      when: match[3],
      currency: currencyMatch ? currencyMatch[1] : ''
    };
  }
  return {
    departure: '',
    destination: '',
    when: '',
    currency: ''
  };
};

onMounted(fetchFlightHistory);
</script>

<template>
  <div class="container mx-auto">
    <h1 class="text-2xl font-semibold mb-6 mt-32">User Flight History</h1>

    <div v-if="loading" class="text-gray-700">
      Loading...
    </div>
    
    <div v-if="error" class="text-red-500">
      {{ error }}
    </div>

    <div v-if="flightHistory.length && !loading" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
      <h2 class="text-lg font-semibold mb-4">Round-trip flights</h2>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">When</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(flight, index) in flightHistory" :key="index">
            <td class="px-6 py-4 whitespace-nowrap">
              <div v-for="url in flight.api_url" :key="url">
                {{ extractDetails(url).departure }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div v-for="url in flight.api_url" :key="url">
                {{ extractDetails(url).destination }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div v-for="url in flight.api_url" :key="url">
                {{ extractDetails(url).when }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div v-for="(price, priceIndex) in flight.amount" :key="priceIndex">
                {{ price }} {{ extractDetails(flight.api_url[priceIndex]).currency }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <button @click="deleteFlight(flight.id)" class="text-red-500 bg-transparent border border-red-500 rounded-md px-4 py-2 transition duration-300 ease-in-out hover:bg-red-500 hover:text-white">
                Remove
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>