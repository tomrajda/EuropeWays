<script setup>
import { ref, onMounted, watch } from 'vue';

const origin = ref('');
const destination = ref('');
const departDate = ref('');
const returnDate = ref('')
const currency = ref('EUR');
const cheapestFlight = ref(null);
const cheapestReturnFlight = ref(null)
const error = ref(null);
const airports = ref([]);
const airportsAval = ref([]);
const cheapestCallResponse = ref(null)
const cheapestReturnCallResponse = ref(null);
const selectedOption = ref('oneWay')


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
    cheapestFlight.value = data.outbound.fares.find(fare => fare.day == departDate.value);
    if (cheapestFlight.value && cheapestFlight.value.unavailable) {
        throw new Error('Failed to find a flight')
    }
    error.value = null;


  } catch (error) {
    console.error('Error fetching cheapest flight:', error);
    error.value = 'Failed to fetch cheapest flight data';
    cheapestFlight.value = null;
  }
};

const getCheapestReturn = async () => {
    try {
        const apiUrl = `https://www.ryanair.com/api/farfnd/v4/oneWayFares/${destination.value}/${origin.value}/cheapestPerDay?outboundMonthOfDate=${returnDate.value}&currency=${currency.value}`;
        console.log('API URL:', apiUrl);
        const response = await fetch(apiUrl);

        if (!response.ok) {
            throw new Error('Failed to fetch data');
        }
        
        cheapestReturnCallResponse.value = response.status;
        console.log('response:', cheapestReturnCallResponse.value);

        const data = await response.json();
        cheapestReturnFlight.value = data.outbound.fares.find(fare => fare.day == returnDate.value);
        if (cheapestReturnFlight.value && cheapestReturnFlight.value.unavailable) {
            throw new Error('Failed to find a flight')
        }    
        error.value = null;

  } catch (error) {
    console.error('Error fetching cheapest flight:', error);
    error.value = 'Failed to fetch cheapest flight data';
    cheapestReturnFlight.value = null;
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

const fetchAvalAirports = async () => {
    try {
        const apiUrl = `https://www.ryanair.com/api/views/locate/searchWidget/routes/en/airport/${origin.value}`;
        console.log('API URL:', apiUrl);
        const response = await fetch(apiUrl)

        if (!response.ok) {
            throw new Error('Failed to fetch airports data');
        }

        const data = await response.json();
        airportsAval.value = data;

    } catch (error) {
        console.error('Error fetching airports:', error);
        error.value = 'Failed to fetch airports data';
        airportsAval.value = [];
    }
}

const handleSubmit = async () => {
    await getCheapestFlight();
    if (selectedOption.value == 'return') {
        await getCheapestReturn();
    } else {
        cheapestReturnFlight.value = null;
    }
};

onMounted(fetchAirports);

watch(origin, (newValue, oldValue) => {
    fetchAvalAirports();
});

// CONVERTING DATES 

function formatReadableDate(isoDate) {
    const dateObject = new Date(isoDate);

    const year = dateObject.getFullYear();
    const month = (dateObject.getMonth() + 1).toString().padStart(2, '0');
    const day = dateObject.getDate().toString().padStart(2, '0');
    const hours = dateObject.getHours().toString().padStart(2, '0');
    const minutes = dateObject.getMinutes().toString().padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}`;
}


// Saving flight










</script>

<template>
<div class="container mx-auto">

    <h1 class="text-2xl font-semibold mb-6 mt-32" v-if="departDate && !returnDate">Find one way cheapest flight near <b>{{ departDate }}</b></h1>
    <h1 class="text-2xl font-semibold mb-6 mt-32" v-else-if="returnDate && departDate">Find cheapest flight from <b>{{ departDate }}</b> to <b>{{returnDate}}</b></h1>
    <h1 class="text-2xl font-semibold mb-6 mt-32" v-else>Let's Trip!</h1>

    <div>Choose option: </div>
    <label class="inline-flex items-center">
        <div class="mt-3">
            <input type="radio" name="flightOption" value="oneWay" class="form-radio h-6 w-6 text-green-500 rounded" v-model="selectedOption">
            <span class="ml-1 inliml-2 text-gray-700">One way</span>
            
            <input type="radio" name="flightOption" value="return" class="ml-8 form-radio h-6 w-6 text-green-500 rounded" v-model="selectedOption">
            <span class="ml-1 inliml-2 text-gray-700">Return</span>
        </div>
    </label>

  <form @submit.prevent="handleSubmit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
    <div class="mb-4">
      <label for="origin" class="block text-sm font-medium text-gray-700 mb-2">From</label>
      <select id="origin" v-model="origin" class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Departure</option>
        <option v-for="airport in airports" :key="airport.iataCode" :value="airport.iataCode">{{ airport.name }}</option>
      </select>
    </div>

    <div class="mb-4">
      <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">To</label>
      <select id="destination" v-model="destination" :disabled="!origin" class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:bg-gray-300">
        <option value="">Destination</option>
        <option v-for="airport in airportsAval" :key="airport.arrivalAirport.code" :value="airport.arrivalAirport.code">{{ airport.arrivalAirport.name }}</option>
      </select>
    </div>

    <div class="mb-4">
      <label for="departDate" class="block text-sm font-medium text-gray-700 mb-2">Departure Date</label>
      <input type="date" id="departDate" v-model="departDate" class="form-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </div>



    <div v-show="selectedOption == 'return'" class="mb-4">
      <label for="returnDate" class="block text-sm font-medium text-gray-700 mb-2">Return Date</label>
      <input type="date" id="returnDate" v-model="returnDate" :min="departDate" :disabled="!departDate" class="form-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:bg-gray-300">
    </div>


    <div class="mb-4">
      <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
      <select id="currency" v-model="currency" class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="EUR">EUR</option>
        <option value="USD">USD</option>
        <option value="PLN">PLN</option>
      </select>
    </div>
    <button 
        type="submit"
        :disabled="(!origin || !destination || !departDate || (!returnDate && selectedOption === 'return'))"
        class="w-full py-3 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">Search</button>
  </form>

  <div v-if="error" class="text-red-500 mb-8">{{ error }}</div>

  <div v-if="cheapestFlight !== null" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
    <h2 class="text-lg font-semibold mb-4">Cheapest Flight:</h2>
    <p>Departure Date: {{ formatReadableDate(cheapestFlight.departureDate) }}</p>
    <p>Arrival Date: {{ formatReadableDate(cheapestFlight.arrivalDate) }}</p>
    <p>Price: {{ cheapestFlight.price.value }} {{ cheapestFlight.price.currencySymbol }}</p>
  </div>

  <div v-if="cheapestReturnFlight !== null" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
    <h2 class="text-lg font-semibold mb-4">Return:</h2>
    <p>Return Date: {{ formatReadableDate(cheapestReturnFlight.departureDate) }}</p>
    <p>Arrival Date: {{ formatReadableDate(cheapestReturnFlight.arrivalDate) }}</p>
    <p>Price: {{ cheapestReturnFlight.price.value }} {{ cheapestReturnFlight.price.currencySymbol }}</p>
  </div>

  <div v-if="cheapestFlight === null && !error && cheapestCallResponse !== null" class="text-gray-700 bg-white shadow-md rounded px-8 py-4 mb-8">
    <p>No flights found, try different date.</p>
  </div>


  <div v-if="cheapestReturnFlight === null && !error && cheapestReturnCallResponse !== null" class="text-gray-700 bg-white shadow-md rounded px-8 py-4 mb-8">
    <p>No return flights found, try different date.</p>
  </div>

  <button 
    type="button"
    @click="saveFlightsToDatabase"
    :disabled="!canSaveToDatabase"
    class="w-full py-3 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
    Save Flights to Database
  </button>

</div>


</template>