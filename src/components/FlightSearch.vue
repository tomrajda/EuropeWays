<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const statusMessage = ref('')
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
const userData = ref({
  api_url: '',
  amount: 0
});

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

watch(selectedOption, (newValue, oldValue) => {
    statusMessage.value = '';
    if (newValue === 'oneWay') {
        returnDate.value = '';
        cheapestReturnFlight.value = null;
        cheapestReturnCallResponse.value = null;
    }
});

watch([origin, destination, departDate, returnDate], () => {
    // Reset status message when clicking on search again
    statusMessage.value = '';
});



watch(statusMessage, (newValue, oldValue) => {


  // Ustaw timer na 5 sekund, po którym komunikat zniknie
  setTimeout(() => {

    // Wyczyść komunikat
    statusMessage.value = '';
  }, 5000);
});

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

const savedAPIURLs = ref([]);
const savedPrices = ref([]);

const saveFlightDetails = () => {
  // Clear previous data
  savedAPIURLs.value = [];
  savedPrices.value = [];

  if (cheapestFlight.value) {
    savedAPIURLs.value.push(`https://www.ryanair.com/api/farfnd/v4/oneWayFares/${origin.value}/${destination.value}/cheapestPerDay?outboundMonthOfDate=${departDate.value}&currency=${currency.value}`);
    savedPrices.value.push(cheapestFlight.value.price.value);
  }
  if (selectedOption.value === 'return' && cheapestReturnFlight.value) {
    savedAPIURLs.value.push(`https://www.ryanair.com/api/farfnd/v4/oneWayFares/${destination.value}/${origin.value}/cheapestPerDay?outboundMonthOfDate=${returnDate.value}&currency=${currency.value}`);
    savedPrices.value.push(cheapestReturnFlight.value.price.value);
  }

  // Set the userData object with updated values as arrays
  userData.value.api_url = savedAPIURLs.value;
  userData.value.amount = savedPrices.value;

  // Debug: Log userData
  console.log("userData:", userData.value);

  // Display saved data in console
  console.log("Saved API URLs:", savedAPIURLs.value);
  console.log("Saved Prices:", savedPrices.value);
};



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

    // Ensure api_url and amount are arrays before sending the request
    const dataToSend = {
      api_url: Array.isArray(userData.value.api_url) ? userData.value.api_url : [],
      amount: Array.isArray(userData.value.amount) ? userData.value.amount : []
    };

    const response = await axios.post('/api/save-flight-history', dataToSend, config);
    
    console.log(response.data);
  } catch (error) {
    console.error(error);
  }
};

</script>

<template>
  <div class="container max-w-5xl mx-auto bg-zinc-200 bg-opacity-60 shadow-md rounded-lg px-10 pt-0 pb-12 mb-8">
  
    <h1 class="text-3xl font-semibold mb-6 mt-20 text-gray-800" v-if="departDate && !returnDate"><br>Find one way cheapest flight near <b>{{ departDate }}</b></h1>
    <h1 class="text-3xl font-semibold mb-6 mt-20 text-gray-800" v-else-if="returnDate && departDate"><br>Find cheapest flight from <b>{{ departDate }}</b> to <b>{{returnDate}}</b></h1>
    <h1 class="text-3xl font-semibold mb-4 mt-20 text-gray-700" v-else><br>Millions of cheap flights. One simple search.</h1>
  
    <div><p class="text-2xl font-semibold text-gray-800">Choose option: </p></div>
    <label class="inline-flex items-center">
        <div class="mt-2 py-4">
            <input type="radio" name="flightOption" value="oneWay" class="form-radio h-6 w-6 text-green-500 rounded align-middle" v-model="selectedOption">
            <span class="ml-4 inliml-2 text-gray-700 text-lg">One way</span>
            
            <input type="radio" name="flightOption" value="return" class="ml-8 form-radio h-6 w-6 text-green-500 rounded align-middle" v-model="selectedOption">
            <span class="ml-4 inliml-2 text-gray-700 text-lg">Return</span>
          </div>
      </label>
  
      <form @submit.prevent="handleSubmit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
    <div class="mb-4">
      <label for="origin" class="block text-base font-medium text-gray-700 mb-2">From</label>
      <select id="origin" v-model="origin" class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Departure</option>
        <option v-for="airport in airports" :key="airport.iataCode" :value="airport.iataCode">{{ airport.name }}</option>
      </select>
    </div>
  
    <div class="mb-4">
      <label for="destination" class="block text-base font-medium text-gray-700 mb-2">To</label>
      <select id="destination" v-model="destination" :disabled="!origin" class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:bg-white opacity-80">
        <option value="">Destination</option>
        <option v-for="airport in airportsAval" :key="airport.arrivalAirport.code" :value="airport.arrivalAirport.code">{{ airport.arrivalAirport.name }}</option>
      </select>
    </div>

    <div class="mb-4">
      <label for="departDate" class="block text-base font-medium text-gray-700 mb-2">Departure Date</label>
      <input type="date" id="departDate" v-model="departDate" class="form-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </div>



    <div v-show="selectedOption == 'return'" class="mb-4">
      <label for="returnDate" class="block text-base font-medium text-gray-700 mb-2">Return Date</label>
      <input type="date" id="returnDate" v-model="returnDate" :min="departDate" :disabled="!departDate" class="form-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:bg-white opacity-80">
    </div>
  
  
    <div class="mb-6">
      <label for="currency" class="block text-base font-medium text-gray-700 mb-2">Currency</label>
      <select id="currency" v-model="currency" class="form-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="EUR">EUR</option>
        <option value="USD">USD</option>
        <option value="PLN">PLN</option>
      </select>
    </div>
      <button 
          type="submit"
          :disabled="(!origin || !destination || !departDate || (!returnDate && selectedOption === 'return'))"
          class="w-full py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-sky-800 hover:bg-sky-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-900 disabled:opacity-90">Search</button>
    </form>
  
    <div v-if="error" class="text-red-500 mb-8">{{ error }}</div>
  
    <div class="grid grid-cols-2">
            <div v-if="cheapestFlight !== null" class="bg-white shadow-md  px-8 pt-6 pb-8 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Cheapest flight to your destination:</h2>
            <p><b>Departure Date: </b>{{ formatReadableDate(cheapestFlight.departureDate) }}</p>
          <p><b>Arrival Date:</b> {{ formatReadableDate(cheapestFlight.arrivalDate) }}</p>
          <p><b>Price:</b> {{ cheapestFlight.price.value }} {{ cheapestFlight.price.currencySymbol }}</p>
          </div> 

        <div v-if= "cheapestFlight !== null" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
        <div v-if="cheapestReturnFlight !== null">
        <h2 class="text-2xl font-semibold mb-4">Cheapest return flight:</h2>
        <p><b>Return Date: </b>{{ formatReadableDate(cheapestReturnFlight.departureDate) }}</p>
        <p><b>Arrival Date: </b>{{ formatReadableDate(cheapestReturnFlight.arrivalDate) }}</p>
        <p><b>Price: </b>{{ cheapestReturnFlight.price.value }} {{ cheapestReturnFlight.price.currencySymbol }}</p>
        </div>
      </div>
  

      </div>

        <!-- <div v-if="!authStore.user &&cheapestFlight !== null" class="bg-white shadow-md  px-8 pt-6 pb-8 mb-8">
          <h2 class="text-lg font-semibold mb-4 pt-8 text-center ">Wanna save this flight? First, log in!</h2> <router-link :to="{name: 'Login'}"
          class="block rounded py-2 pr-4 pl-3 text-white text-center bg-sky-700 hover:bg-sky-600 dark:text-white dark:bg-sky-700 
          dark:hover:text-white dark:hover:bg-blue-800 md:border-0 md:p-0 md:hover:bg-sky-900 md:hover:text-white md:dark:hover:bg-sky-900 md:dark:hover:text-white">Login</router-link>
        </div> -->
  
        <div v-if="cheapestFlight !== null && !authStore.user" class="bg-white shadow-md px-4 pt-6 pb-8 mb-8 text-center">
            <h2 class="text-lg font-semibold mb-4 pt-4">Do you like this option?</h2>
            <form @submit.prevent="saveHistory">
                <button type="submit" v-if="cheapestFlight" @click="saveFlightDetails" 
                class="block mx-auto w-full rounded py-2 text-white bg-indigo-700 hover:bg-sky-900 dark:text-white dark:bg-sky-900 dark:hover:text-white dark:hover:bg-sky-800"
                :class="{'bg-gray-400 cursor-not-allowed': cheapestReturnFlight === null && selectedOption === 'return'}"
                :disabled="cheapestReturnFlight === null && selectedOption === 'return'" id="saveButton">
                Save {{ selectedOption === 'return' ? 'Return' : 'One-Way' }} Flight
                </button>
            </form>
          </div>

          <div v-if="statusMessage" :class="[statusMessage === 'Flight successfully added!' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700', 'px-4 py-3 rounded relative mb-8']">
        {{ statusMessage }}
          <button @click="statusMessage = ''" class="absolute top-0 bottom-0 right-0 px-4 py-3">
          <svg class="h-6 w-6 text-black" role="button" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        </button>
        </div>


          <div v-if="cheapestFlight == null && !error && cheapestCallResponse !== null" class="text-gray-700 bg-white shadow-md rounded px-8 py-4 mb-8">
            <p>No flights found, try different date.</p>
          </div>
  
  
          <div v-if="cheapestReturnFlight === null && !error && cheapestReturnCallResponse !== null && selectedOption==='return'" class="text-gray-700 bg-white shadow-md rounded px-8 py-4 mb-8">
            <p>No return flights found, try different date.</p>
          </div>
      </div>
  
  
  </template>