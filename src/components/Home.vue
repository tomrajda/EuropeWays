<script setup>
import { onMounted, ref } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();

onMounted(async () => {
    await authStore.getToken();
    await authStore.getUser();
});

const currentDate = ref('2024-05-24');
const currency = ref('EUR');
const origin = ref('KRK');
const error = ref(null);

const flightInfo = ref({
    BVA: null,
    CIA: null,
    BCN: null
});

const getCheapestFlight = async (destination) => {
    try {
        const apiUrl = `https://www.ryanair.com/api/farfnd/v4/oneWayFares/${origin.value}/${destination}/cheapestPerDay?outboundMonthOfDate=${currentDate.value}&currency=${currency.value}`;
        console.log('API URL:', apiUrl);
        const response = await fetch(apiUrl);

        if (!response.ok) {
            throw new Error('Failed to fetch data');
        }

        const data = await response.json();
        const minFareOneWay = data.outbound.minFare;

        if (!minFareOneWay) {
            throw new Error('No flights available');
        }

        // Konwersja daty na format ISO 8601 zgodny z żądaniami
        const dateObj = new Date(minFareOneWay.arrivalDate);
        const dateISO = `${dateObj.toISOString().split('T')[0]} ${dateObj.getHours()}:${('0' + dateObj.getMinutes()).slice(-2)}`;

        return {
            price: minFareOneWay.price.value,
            date: dateISO
        };

    } catch (error) {
        console.error('Error fetching cheapest flight:', error);
        return null;
    }
};


const fetchFlightInfo = async (destination) => {
    try {
        const flight = await getCheapestFlight(destination);
        console.log(flight);
        if (flight) {
            flightInfo.value[destination] = `Date: ${flight.date}, Price: ${flight.price}`;
        } else {
            flightInfo.value[destination] = 'Failed to fetch flight data';
        }
    } catch (error) {
        console.error('Error fetching flight info:', error);
        flightInfo.value[destination] = 'Failed to fetch flight data';
    }
};

onMounted(async () => {
    await Promise.all([
        fetchFlightInfo('BVA'),
        fetchFlightInfo('CIA'),
        fetchFlightInfo('BCN')
    ]);
});
</script>

<template>
<div class="container max-w-5xl mx-auto bg-zinc-200 bg-opacity-60 shadow-md rounded-lg py-6 px-6 pt-4 pb-10 mb-20 mt-20">
    <h1 class="text-3xl text-center font-semibold mb-8 mt-10">Don't know where you want to go? Our ideas for your trip!</h1>
    
    <div class="grid grid-cols-3 gap-6">
        <!-- Paris -->
        <div class="bg-white bg-opacity-80 text-center p-4 rounded-lg">
            <img src="\public\paris.jpg" alt="Zdjęcie 1" class="mx-auto h-70 w-60 rounded-md mb-4">
            <p class="text-gray-800 text-3xl font-semibold">Paris</p>
            <br>
            <p class="text-gray-800 text-xl font-semibold">{{ flightInfo.BVA }}</p>
        </div>

        <!-- Rome -->
        <div class="bg-white bg-opacity-80 text-center p-4 rounded-lg">
            <img src="\public\rome.jpg" alt="Zdjęcie 2" class="mx-auto h-70 w-60 rounded-md mb-4">
            <p class="text-gray-800 text-3xl font-semibold">Rome</p>
            <br>
            <p class="text-gray-800 text-xl font-semibold">{{ flightInfo.CIA }}</p>
        </div>

        <!-- Barcelona -->
        <div class="bg-white bg-opacity-80 text-center p-4 rounded-lg">
            <img src="\public\barcelona.jpg" alt="Zdjęcie 3" class="mx-auto h-70 w-60 rounded-md mb-4">
            <p class="text-gray-800 text-3xl font-semibold">Barcelona</p>
            <br>
            <p class="text-gray-800 text-xl font-semibold">{{ flightInfo.BCN }}</p>
        </div>
    </div>
</div>
</template>
