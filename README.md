# App title: EuropeWays (Previously AsiaWays)
* Application Objective: EuropeWays is used to support people who want to travel in Europe.
* UI Design: [Link to Figma](https://www.figma.com/file/9qPMckkVm8SStCAa8Q9LMS/europeWays?type=design&node-id=0%3A1&mode=dev&t=KhvChrnVDq4WOq9k-1)

EuropeWays to aplikacja która spełnia rolę narzędzia wspomagającego podróżowanie po Europie. Jej główną, najważniejszą funkcjonalnością jest możliwość wyszukiwania połączeń lotniczych wśród europejskich miast. Dodatkowo może on sprawdzić aktualne warunki pogodowe w tej lokalizacji oraz najważniejsze informacje na temat tego miejsca, takie jak m.in. waluta, język czy ustrój polityczny. 

Kluczową funkcjonalnością aplikacji jest wyszukiwarka lotów stworzona dzięki wykorzystaniu API tanich linii lotniczych. Na stronie głównej aplikacji (ekran MainPage w Figma) użytkownik może zauważyć polecane, najtańsze loty z lotniska najbliższego jego lokalizacji. By wyszukać bardziej szczegółowo musi on przenieść się do zakładki „find flight”, którą znajdzie na pasku nawigacyjnym. Na tej stronie ([ekran FindFlight w Figma](https://www.figma.com/file/9qPMckkVm8SStCAa8Q9LMS/europeWays?type=design&node-id=0%3A1&mode=dev&t=KhvChrnVDq4WOq9k-1)) może on podać interesujące go miejsce wylotu, lokalizacje docelową, zakres dat w których chciałby odbyć lot w pierwszą stronę jak i powrotną. 

Inną funkcjonalnością jest możliwość sprawdzenia aktualnych warunków pogodowych dla lokalizacji. Umożliwia to zakładka „check weather”, również dostępna z paska nawigacyjnego ([ekran CheckWeather w Figma](https://www.figma.com/file/9qPMckkVm8SStCAa8Q9LMS/europeWays?type=design&node-id=0%3A1&mode=dev&t=KhvChrnVDq4WOq9k-1)). Użytkownik może dostrzec aktualną temperaturę czy prognozę na najbliższe dni. Dane pochodzą z zewnętrznych źródeł i pobierane za pomocą API popularnych stron pogodowych. 

Ostatnią funkcjonalnością jest możliwość podejrzenia najważniejszych informacji związanych z miastem docelowym. Użytkownik podając nazwę miasta może dowiedzieć się jaką walutą posługują się mieszkańcy danego kraju, w jakim języku mówią czy jaka religia jest dominującą. Ta funkcjonalność jest dostępna z paska nawigacyjnego pod nazwą „city guide” ([ekran CityGuide w Figma](https://www.figma.com/file/9qPMckkVm8SStCAa8Q9LMS/europeWays?type=design&node-id=0%3A1&mode=dev&t=KhvChrnVDq4WOq9k-1)). 


<script setup>

import { ref, onBeforeMount } from "vue"

const dogs = ref("")

    onBeforeMount(async() => {
        try{
            const response = await fetch("https://dog.ceo/api/breeds/image/random");
            dogs.value = await response.json();
        }
        catch {
            dogs.value = "wysypalo sie api"
        }


    })

</script>

<template>
    <h1>Dogs breeds</h1>
    <p>{{ dogs }}</p>
</template>

<style scoped>
</style>
