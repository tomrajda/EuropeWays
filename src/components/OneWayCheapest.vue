<script setup>
import { ref, onMounted } from 'vue';

const origin = ref('');
const destination = ref('');
const departDate = ref('');
const currency = ref('EUR');
const cheapestFlight = ref(null);
const error = ref(null);
const airports = ref([]);
const cheapestCallResponse = ref(null);

const getCheapestFlight = async () => {
  try {
    const apiUrl = `https://www.ryanair.com/api/farfnd/v4/oneWayFares/${origin.value}/${destination.value}/cheapestPerDay?outboundMonthOfDate=${departDate.value}&currency=${currency.value}`;
    console.log('API URL:', apiUrl);
    const response = await fetch(apiUrl);

    if (!response.ok) {
      throw new Error('Failed to fetch data');
    }
    
    cheapestCallResponse.value = response.status;
    console.log('response:', cheapestCallResponse.value);

    const data = await response.json();
    cheapestFlight.value = data.outbound.minFare;
    error.value = null;

  } catch (error) {
    console.error('Error fetching cheapest flight:', error);
    error.value = 'Failed to fetch cheapest flight data';
    cheapestFlight.value = null;
  }
};

const fetchAirports = async () => {
  try {
    const apiUrl = 'https://www.ryanair.com/api/views/locate/3/airports/en/active';
    const response = await fetch(apiUrl);

    if (!response.ok) {
      throw new Error('Failed to fetch airports data');
    }

    const data = await response.json();
    airports.value = data;
  } catch (error) {
    console.error('Error fetching airports:', error);
    error.value = 'Failed to fetch airports data';
    airports.value = [];
  }
};

onMounted(fetchAirports);
</script>

<template>
<div class="container mx-auto">

  <h1 class="text-2xl font-semibold mb-6 mt-32" v-if="departDate">Find one way cheapest flight near {{ departDate }}</h1>
  <h1 class="text-2xl font-semibold mb-6 mt-32" v-else>Let's Trip!</h1>

  <form @submit.prevent="getCheapestFlight" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
    <div class="mb-4">
      <label for="origin" class="block text-sm font-medium text-gray-700 mb-2">From</label>
      <select id="origin" v-model="origin" class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Departure</option>
        <option v-for="airport in airports" :key="airport.iataCode" :value="airport.iataCode">{{ airport.name }}</option>
      </select>
    </div>
    <div class="mb-4">
      <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">To</label>
      <select id="destination" v-model="destination" class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Destination</option>
        <option v-for="airport in airports" :key="airport.iataCode" :value="airport.iataCode">{{ airport.name }}</option>
      </select>
    </div>
    <div class="mb-4">
      <label for="departDate" class="block text-sm font-medium text-gray-700 mb-2">Departure Date</label>
      <input type="date" id="departDate" v-model="departDate" class="form-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </div>
    <div class="mb-4">
      <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
      <select id="currency" v-model="currency" class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="EUR">EUR</option>
        <option value="USD">USD</option>
        <option value="PLN">PLN</option>
      </select>
    </div>
    <button type="submit" class="w-full py-3 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Search</button>
  </form>

  <div v-if="error" class="text-red-500 mb-8">{{ error }}</div>

  <div v-if="cheapestFlight !== null" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
    <h2 class="text-lg font-semibold mb-4">Cheapest Flight:</h2>
    <p>Departure Date: {{ cheapestFlight.departureDate }}</p>
    <p>Arrival Date: {{ cheapestFlight.arrivalDate }}</p>
    <p>Price: {{ cheapestFlight.price.value }} {{ cheapestFlight.price.currencySymbol }}</p>
  </div>

  <div v-else-if="cheapestFlight === null && !error && cheapestCallResponse !== null" class="text-gray-700 bg-white shadow-md rounded px-8 py-4 mb-8">
    <p>No flights found.</p>
  </div>
</div>

</template>
