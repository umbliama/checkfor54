<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideMenu from '@/Layouts/SideMenu.vue';
import { MenuItem, MenuItems, Menu, MenuButton } from '@headlessui/vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref, toRaw } from 'vue';
import store from '../../../store/index';

const props = defineProps({
  services: Object,
  contragents_names: Array,
  contragents: Array,
  count_services_active: Number,
  count_services_inactive: Number,
  formatted_data_months: Object
})

const page = usePage()

const user = computed(() => page.props.auth.user)



const minusYear = () => {
  store.dispatch('services/updateDecSelectedYear')
}
const plusYear = () => {
  store.dispatch('services/updateIncSelectedYear')
}

const selectActive = (value) => {
  store.dispatch('services/updateSelectedActive', value)
}

const filterDatesActive = () => {
  const raw = toRaw(props.services.data)

  console.log(raw)
  return raw.filter(service => {
    const serviceDate = new Date(service.service_date);

    const isYearMatch = serviceDate.getFullYear() == getYear.value;

    const isMonthMatch = getMonth.value === 'all' || serviceDate.getMonth() === getMonthNumber(getMonth.value)


    const isActive = service.active === 1;
    return isYearMatch && isMonthMatch && isActive;

  })
}
const filterDatesInActive = () => {
  const raw = toRaw(props.services.data)

  return raw.filter(service => {
    const serviceDate = new Date(service.service_date);

    const isYearMatch = serviceDate.getFullYear() == getYear.value;

    const isMonthMatch = getMonth.value === 'all' || serviceDate.getMonth() === getMonthNumber(getMonth.value)

    const isActive = service.active === 0
    return isYearMatch && isMonthMatch && isActive;

  })
}

const getMonthNumber = (month) => {
  const months = {
    'jan': 0,
    'feb': 1,
    'mar': 2,
    'apr': 3,
    'may': 4,
    'jun': 5,
    'jul': 6,
    'aug': 7,
    'sept': 8,
    'oct': 9,
    'novb': 10,
    'dec': 11
  };
  return months[month] ?? null; // Return null if no match found

}

const updateMonth = (month) => {
  store.dispatch('services/updateSelectedMonth', month)
}
const showSubservices = ref({});

// Toggle subservices display
const toggleSubservice = (index) => {
  showSubservices.value[index] = !showSubservices.value[index];
};

const nameContragent = (contragents_names, id) => {
  return contragents_names[id]
}

const getMonth = computed(() => store.getters['services/getSelectedMonth']);
const getYear = computed(() => store.getters['services/getSelectedYear']);
const selectedActive = computed(() => store.getters['services/getSelectedActive']);


</script>

<template>
  <AuthenticatedLayout>

    <div class="flex-1  sm:w-full">
      <div class="">
        <nav class="bg-my-gray ">
          <div class="max-w-screen-xl px-4 py-3">
            <div
              class="lg:py-4 sm:py-0 lg:overflow-y-visible sm:whitespace-nowrap lg:overflow-x-visible sm:overflow-y-auto sm:overflow-x-auto  items-center">
              <ul
                class="flex flex border-b-2 items-center flex-row font-medium mt-0 space-x-4 gap-4 rtl:space-x-reverse text-sm">
                <li>
                  <div class="flex" :class="{ 'pb-2 border-b-2 border-blue-600': selectedActive }">
                    <Link @click.prevent="selectActive(true)" class="text-lg  ">Открытые аренды</Link>
                    <span class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                      {{ count_services_active }}
                    </span>
                  </div>
                </li>
                <li>
                  <div class="flex" :class="{ 'pb-2 border-b-2 border-blue-600': !selectedActive }">
                    <Link @click.prevent="selectActive(false)" class="text-lg">Закрытые аренды</Link>
                    <span class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                      {{ count_services_inactive }}
                    </span>
                  </div>
                </li>
                <li>
                  <div class="flex">
                    <Link  v-if="user.isAdmin" :href="route('equip.tests')" class="text-lg">Admin</Link>
                  </div>
                </li>
                <Link>

                <Menu as="div" class="relative inline-block text-left">
                  <div>
                    <MenuButton
                      class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                      <svg width="20" height="6" viewBox="0 0 20 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M3 4C3.26522 4 3.51957 3.89464 3.70711 3.70711C3.89464 3.51957 4 3.26522 4 3C4 2.73478 3.89464 2.48043 3.70711 2.29289C3.51957 2.10536 3.26522 2 3 2C2.73478 2 2.48043 2.10536 2.29289 2.29289C2.10536 2.48043 2 2.73478 2 3C2 3.26522 2.10536 3.51957 2.29289 3.70711C2.48043 3.89464 2.73478 4 3 4ZM3 6C2.20435 6 1.44129 5.68393 0.87868 5.12132C0.31607 4.55871 0 3.79565 0 3C0 2.20435 0.31607 1.44129 0.87868 0.87868C1.44129 0.31607 2.20435 0 3 0C3.79565 0 4.55871 0.31607 5.12132 0.87868C5.68393 1.44129 6 2.20435 6 3C6 3.79565 5.68393 4.55871 5.12132 5.12132C4.55871 5.68393 3.79565 6 3 6ZM17 6C16.2044 6 15.4413 5.68393 14.8787 5.12132C14.3161 4.55871 14 3.79565 14 3C14 2.20435 14.3161 1.44129 14.8787 0.87868C15.4413 0.31607 16.2044 0 17 0C17.7956 0 18.5587 0.31607 19.1213 0.87868C19.6839 1.44129 20 2.20435 20 3C20 3.79565 19.6839 4.55871 19.1213 5.12132C18.5587 5.68393 17.7956 6 17 6ZM17 4C17.2652 4 17.5196 3.89464 17.7071 3.70711C17.8946 3.51957 18 3.26522 18 3C18 2.73478 17.8946 2.48043 17.7071 2.29289C17.5196 2.10536 17.2652 2 17 2C16.7348 2 16.4804 2.10536 16.2929 2.29289C16.1054 2.48043 16 2.73478 16 3C16 3.26522 16.1054 3.51957 16.2929 3.70711C16.4804 3.89464 16.7348 4 17 4ZM10 6C9.20435 6 8.44129 5.68393 7.87868 5.12132C7.31607 4.55871 7 3.79565 7 3C7 2.20435 7.31607 1.44129 7.87868 0.87868C8.44129 0.31607 9.20435 0 10 0C10.7956 0 11.5587 0.31607 12.1213 0.87868C12.6839 1.44129 13 2.20435 13 3C13 3.79565 12.6839 4.55871 12.1213 5.12132C11.5587 5.68393 10.7956 6 10 6ZM10 4C10.2652 4 10.5196 3.89464 10.7071 3.70711C10.8946 3.51957 11 3.26522 11 3C11 2.73478 10.8946 2.48043 10.7071 2.29289C10.5196 2.10536 10.2652 2 10 2C9.73478 2 9.48043 2.10536 9.29289 2.29289C9.10536 2.48043 9 2.73478 9 3C9 3.26522 9.10536 3.51957 9.29289 3.70711C9.48043 3.89464 9.73478 4 10 4Z"
                          fill="#697077" />
                      </svg>

                    </MenuButton>
                  </div>

                  <transition enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <MenuItems
                      class="sm:fixed lg:absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                      <div class="py-1">
                        <MenuItem v-slot="{ active }">
                        <Link :href="route('services.create')" class="flex items-center justify-around"
                          :class="['block px-4 py-2 text-sm']">Добавить аренду <svg width="16" height="16"
                          viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M8.74999 2.75003C8.74999 2.33582 8.4142 2.00003 7.99999 2.00003C7.58578 2.00003 7.24999 2.33582 7.24999 2.75003V7.25H2.75C2.33579 7.25 2 7.58578 2 8C2 8.41421 2.33579 8.75 2.75 8.75H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75H13.25C13.6642 8.75 14 8.41421 14 8C14 7.58578 13.6642 7.25 13.25 7.25H8.74999V2.75003Z"
                            fill="#464F60" />
                        </svg>
                        </Link>
                        </MenuItem>
                      </div>
                    </MenuItems>
                  </transition>
                </Menu>
                </Link>
              </ul>
            </div>

          </div>
        </nav>
      </div>

      <nav class="lg:px-4 md:bg-white sm:bg-my-gray rounded-lg mt-4">
        <div class="max-w-screen-xl py-2">
          <div class="mt-6 flex items-center">
            <ul
              class="flex overflow-x-auto sm:px-2 flex-row  border-b-2 border-gray-200 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">

              <li :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'all' }"
                class="text-my-nav-text text-md w-full" @click="updateMonth('all')">
                Все
              </li>
              <li @click="updateMonth('jan')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'jan' }"
                class="text-my-nav-text text-md w-full">
                Январь
              </li>
              <li @click="updateMonth('feb')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'feb' }"
                class="text-my-nav-text text-md w-full">
                Февраль
              </li>
              <li @click="updateMonth('mar')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'mar' }"
                class="text-my-nav-text text-md w-full">
                Март
              </li>
              <li @click="updateMonth('apr')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'apr' }"
                class="text-my-nav-text text-md w-full">
                Апрель
              </li>
              <li @click="updateMonth('may')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'may' }"
                class="text-my-nav-text text-md w-full">
                Май
              </li>
              <li @click="updateMonth('jun')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'jun' }"
                class="text-my-nav-text text-md w-full">
                Июнь
              </li>
              <li @click="updateMonth('jul')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'jul' }"
                class="text-my-nav-text text-md w-full">
                Июль
              </li>
              <li @click="updateMonth('aug')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'aug' }"
                class="text-my-nav-text text-md w-full">
                Август
              </li>
              <li @click="updateMonth('sept')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'sept' }"
                class="text-my-nav-text text-md w-full">
                Сентябрь
              </li>
              <li @click="updateMonth('oct')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'oct' }"
                class="text-my-nav-text text-md w-full">
                Октябрь
              </li>
              <li @click="updateMonth('novb')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'novb' }"
                class="text-my-nav-text text-md w-full">
                Ноябрь
              </li>
              <li @click="updateMonth('dec')" :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'dec' }"
                class="text-my-nav-text text-md w-full">
                Декабрь
              </li>
            </ul>
          </div>
        </div>
      </nav>


      <div class="lg:hidden md:hidden flex bg-my-gray mt-4 p-4">

        <select v-model="seriesActive" class="text-gray-500 border-gray-200" name="" id="">
          <option value="">Номер</option>
          <option v-for="series in equipment_series" @click="setSeriesId(series)" value="">{{ series }}
          </option>
        </select>
        <div class="flex mx-2 items-center">
          <svg width="5" height="34" viewBox="0 0 3 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="path-1-inside-1_2101_84576" fill="white">
              <path d="M0.5 0H2.5V28H0.5V0Z" />
            </mask>
            <path d="M0.5 0H2.5V28H0.5V0Z" fill="white" />
            <path
              d="M0 0V1H1V0H0ZM0 3V5H1V3H0ZM0 7V9H1V7H0ZM0 11V13H1V11H0ZM0 15V17H1V15H0ZM0 19V21H1V19H0ZM0 23V25H1V23H0ZM0 27V28H1V27H0ZM-0.5 0V1H1.5V0H-0.5ZM-0.5 3V5H1.5V3H-0.5ZM-0.5 7V9H1.5V7H-0.5ZM-0.5 11V13H1.5V11H-0.5ZM-0.5 15V17H1.5V15H-0.5ZM-0.5 19V21H1.5V19H-0.5ZM-0.5 23V25H1.5V23H-0.5ZM-0.5 27V28H1.5V27H-0.5Z"
              fill="#C1C7CD" mask="url(#path-1-inside-1_2101_84576)" />
          </svg>

        </div>
        <form action="" method="GET" class="max-w-md">
          <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Search</label>
          <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
              <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
              </svg>
            </div>
            <input type="search" name="search" id="default-search"
              class="block w-full ps-10 text-sm text-gray-900 border-b-2 border-gray-200 border-t-0 border-l-0 border-r-0 bg-white-50 py-4"
              placeholder="Поиск">
          </div>
        </form>
      </div>

      <div class="flex my-4 lg:px-4 justify-between lg:pr-10">
        <h3>Список открытых аренд:</h3>

        <div class="flex space-x-4">
          <div @click.prevent="minusYear" class="flex ">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_2051_7507)">
                <rect x="24" width="24" height="24" rx="5" transform="rotate(90 24 0)" fill="#F7F9FC" />
                <path
                  d="M10.686 11.929L15.636 16.879C15.8182 17.0676 15.919 17.3202 15.9167 17.5824C15.9144 17.8446 15.8092 18.0954 15.6238 18.2808C15.4384 18.4662 15.1876 18.5714 14.9254 18.5737C14.6632 18.576 14.4106 18.4752 14.222 18.293L8.565 12.636C8.37753 12.4485 8.27221 12.1942 8.27221 11.929C8.27221 11.6639 8.37753 11.4095 8.565 11.222L14.222 5.56502C14.4106 5.38286 14.6632 5.28207 14.9254 5.28434C15.1876 5.28662 15.4384 5.39179 15.6238 5.5772C15.8092 5.76261 15.9144 6.01342 15.9167 6.27562C15.919 6.53781 15.8182 6.79042 15.636 6.97902L10.686 11.929Z"
                  fill="#697077" />
              </g>
              <rect x="23.5" y="0.5" width="23" height="23" rx="4.5" transform="rotate(90 23.5 0.5)" stroke="#697077" />
              <defs>
                <clipPath id="clip0_2051_7507">
                  <rect x="24" width="24" height="24" rx="5" transform="rotate(90 24 0)" fill="white" />
                </clipPath>
              </defs>
            </svg>


            <p class="mx-2">{{ getYear }}</p>

            <div @click.prevent="plusYear" class="flex">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_2051_7512)">
                  <rect y="24" width="24" height="24" rx="5" transform="rotate(-90 0 24)" fill="#F7F9FC" />
                  <path
                    d="M13.314 12.071L8.36399 7.12098C8.18184 6.93238 8.08104 6.67978 8.08332 6.41758C8.0856 6.15538 8.19077 5.90457 8.37618 5.71916C8.56158 5.53375 8.8124 5.42859 9.07459 5.42631C9.33679 5.42403 9.58939 5.52482 9.77799 5.70698L15.435 11.364C15.6225 11.5515 15.7278 11.8058 15.7278 12.071C15.7278 12.3361 15.6225 12.5905 15.435 12.778L9.77799 18.435C9.58939 18.6171 9.33679 18.7179 9.07459 18.7157C8.8124 18.7134 8.56158 18.6082 8.37618 18.4228C8.19077 18.2374 8.0856 17.9866 8.08332 17.7244C8.08104 17.4622 8.18184 17.2096 8.36399 17.021L13.314 12.071Z"
                    fill="#697077" />
                </g>
                <rect x="0.5" y="23.5" width="23" height="23" rx="4.5" transform="rotate(-90 0.5 23.5)"
                  stroke="#697077" />
                <defs>
                  <clipPath id="clip0_2051_7512">
                    <rect y="24" width="24" height="24" rx="5" transform="rotate(-90 0 24)" fill="white" />
                  </clipPath>
                </defs>
              </svg>
            </div>

          </div>

        </div>
      </div>

      <div class="overflow-x-auto lg:px-4 pb-20">
        <!-- Table header -->
        <div class="min-w-full whitespace-nowrap grid grid-cols-5 gap-52 bg-gray-100 font-bold p-2 rounded-md">
          <div>Заказчик</div>
          <div>Дата отгрузки</div>
          <div>Комментарий</div>
          <div>Доход</div>
          <div><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                fill="#21272A" />
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                fill="#21272A" />
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                fill="#21272A" />
              <path
                d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                fill="#21272A" />
            </svg>
          </div>
        </div>

        <!-- Table body -->
        <div v-if="filterDatesActive().length && selectedActive" class="">
          <div v-for="(service, index) in filterDatesActive()" :key="service.id" class=" border-b border-gray-200 p-2">
            <!-- Main service row -->
            <div class="min-w-full grid grid-cols-5 gap-52 items-center whitespace-nowrap">
              <!-- Service Number with toggle button using SVG -->
              <div class="flex items-center space-x-2">
                <span>{{ service.equipment_info.category.name }} {{ service.equipment_info.size.name }} {{
                  service.equipment_info.series }}</span>
                <button @click="toggleSubservice(index)">
                  <svg v-if="!showSubservices[index]" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                      fill="#242533" />
                  </svg>

                  <svg v-if="showSubservices[index]" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M2.27174 9.29127C2.27174 8.9599 2.54037 8.69127 2.87174 8.69127H14.6283C14.9596 8.69127 15.2283 8.9599 15.2283 9.29127C15.2283 9.62265 14.9596 9.89127 14.6283 9.89127H2.87174C2.54037 9.89127 2.27174 9.62265 2.27174 9.29127Z"
                      fill="#242533" />
                  </svg>
                </button>
              </div>
              <div>{{ service.shipping_date ?? "Не задано" }}</div>
              <div>{{ service.commentary ?? "Не задано" }}</div>
              <div>{{ service.income ?? "Не задано" }}</div>
              <!-- Actions column (empty for now) -->
              <div class="">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z" fill="#21272A"/>
</svg>

                <Menu as="div" class="relative inline-block text-left">
                                        <div class="sm:overflow-x-visible">
                                            <MenuButton
                                                class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                                <svg width="4" height="16" viewBox="0 0 4 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M3.5 2C3.5 2.82843 2.82843 3.5 2 3.5C1.17157 3.5 0.5 2.82843 0.5 2C0.5 1.17157 1.17157 0.5 2 0.5C2.82843 0.5 3.5 1.17157 3.5 2Z"
                                                        fill="#687182" />
                                                    <path
                                                        d="M3.5 8C3.5 8.82843 2.82843 9.5 2 9.5C1.17157 9.5 0.5 8.82843 0.5 8C0.5 7.17157 1.17157 6.5 2 6.5C2.82843 6.5 3.5 7.17157 3.5 8Z"
                                                        fill="#687182" />
                                                    <path
                                                        d="M3.5 14C3.5 14.8284 2.82843 15.5 2 15.5C1.17157 15.5 0.5 14.8284 0.5 14C0.5 13.1716 1.17157 12.5 2 12.5C2.82843 12.5 3.5 13.1716 3.5 14Z"
                                                        fill="#687182" />
                                                </svg>
                                            </MenuButton>
                                        </div>

                                        <transition enter-active-class="transition ease-out duration-100"
                                            enter-from-class="transform opacity-0 scale-95"
                                            enter-to-class="transform opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform opacity-100 scale-100"
                                            leave-to-class="transform opacity-0 scale-95">
                                            <MenuItems
                                                class="absolute top-6 z-200 right-2 justify-between w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                <div class="py-1">


                                                  
                                                  <MenuItem>
                                                    <Link method="get" :href="route('services.edit', service.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Редактировать
                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.0981 3.10827C10.6795 3.52693 10.4702 3.73626 10.2506 3.77677C10.0309 3.81728 9.88682 3.67315 9.59856 3.3849L8.61511 2.40144C8.32686 2.11319 8.18273 1.96906 8.22324 1.74946C8.26375 1.52985 8.47308 1.32052 8.89174 0.901864L9.11838 0.675215C9.53704 0.25656 9.74637 0.0472318 9.96598 0.00672082C10.1856 -0.0337902 10.3297 0.110336 10.618 0.398589L11.6014 1.38204C11.8897 1.6703 12.0338 1.81442 11.9933 2.03403C11.9528 2.25364 11.7434 2.46297 11.3248 2.88162L11.0981 3.10827Z" fill="#464F60"/>
<path d="M0.954058 11.9107C0.454198 12.0029 0.204269 12.049 0.077628 11.9224C-0.0490128 11.7957 -0.00290857 11.5458 0.0893002 11.0459L0.311753 9.84003C0.35173 9.62332 0.371719 9.51497 0.43005 9.41009C0.488381 9.30521 0.579134 9.21446 0.76064 9.03295L6.31438 3.47921C6.73303 3.06056 6.94236 2.85123 7.16197 2.81072C7.38158 2.77021 7.5257 2.91433 7.81396 3.20259L8.79741 4.18604C9.08566 4.4743 9.22979 4.61842 9.18928 4.83803C9.14877 5.05764 8.93944 5.26697 8.52078 5.68562L2.96705 11.2394C2.78554 11.4209 2.69479 11.5116 2.58991 11.5699C2.48503 11.6283 2.37668 11.6483 2.15997 11.6882L0.954058 11.9107Z" fill="#464F60"/>
</svg>



                                                    </Link>
                                                    </MenuItem>

                                                    <MenuItem>
                                                    <Link method="delete" :href="route('services.destroy', service.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Удалить
                                                    <svg width="12" height="14" viewBox="0 0 12 14" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M2.75 2.75V3.25H0.75C0.335786 3.25 0 3.58579 0 4C0 4.41421 0.335786 4.75 0.75 4.75H1.51389L1.89504 11.6109C1.95392 12.6708 2.8305 13.5 3.89196 13.5H8.10802C9.16948 13.5 10.0461 12.6708 10.1049 11.6109L10.4861 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H9.25V2.75C9.25 1.50736 8.24264 0.5 7 0.5H5C3.75736 0.5 2.75 1.50736 2.75 2.75ZM5 2C4.58579 2 4.25 2.33579 4.25 2.75V3.25H7.75V2.75C7.75 2.33579 7.41421 2 7 2H5ZM5.25 6.75C5.25 6.33579 4.91421 6 4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V11.25C3.75 11.6642 4.08579 12 4.5 12C4.91421 12 5.25 11.6642 5.25 11.25V6.75ZM8.25 6.75C8.25 6.33579 7.91421 6 7.5 6C7.08579 6 6.75 6.33579 6.75 6.75V11.25C6.75 11.6642 7.08579 12 7.5 12C7.91421 12 8.25 11.6642 8.25 11.25V6.75Z"
                                                            fill="#DC4067" />
                                                    </svg>



                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link method="post" :href="route('services.createIncident', service.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Инциндент
                                                    <svg width="12" height="14" viewBox="0 0 12 14" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M2.75 2.75V3.25H0.75C0.335786 3.25 0 3.58579 0 4C0 4.41421 0.335786 4.75 0.75 4.75H1.51389L1.89504 11.6109C1.95392 12.6708 2.8305 13.5 3.89196 13.5H8.10802C9.16948 13.5 10.0461 12.6708 10.1049 11.6109L10.4861 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H9.25V2.75C9.25 1.50736 8.24264 0.5 7 0.5H5C3.75736 0.5 2.75 1.50736 2.75 2.75ZM5 2C4.58579 2 4.25 2.33579 4.25 2.75V3.25H7.75V2.75C7.75 2.33579 7.41421 2 7 2H5ZM5.25 6.75C5.25 6.33579 4.91421 6 4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V11.25C3.75 11.6642 4.08579 12 4.5 12C4.91421 12 5.25 11.6642 5.25 11.25V6.75ZM8.25 6.75C8.25 6.33579 7.91421 6 7.5 6C7.08579 6 6.75 6.33579 6.75 6.75V11.25C6.75 11.6642 7.08579 12 7.5 12C7.91421 12 8.25 11.6642 8.25 11.25V6.75Z"
                                                            fill="#DC4067" />
                                                    </svg>



                                                    </Link>
                                                    </MenuItem>

                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>

              </div>
            </div>

            <!-- Subservices (shown when expanded) -->
            <div v-if="showSubservices[index]" class="mt-2">
              <div class="" v-if="service.subservices.length > 0">
                <div v-for="subservice in service.subservices" :key="subservice.id"
                  class="grid whitespace-nowrap grid-cols-5 gap-52 bg-gray-50 py-2 rounded-md">
                  <div>{{ subservice.equipment.category.name }} {{subservice.equipment.size.name}} {{
                    subservice.equipment.series }}</div>
                  <div>{{ subservice.shipping_date }}</div>
                  <div>{{ subservice.commentary ?? "Не задано" }}</div>
                  <div>{{ subservice.income ?? "Не задано" }}</div>
                  <div></div>
                </div>
              </div>
              <div v-else class="p-2 bg-red-50 text-red-500">
                Нет добавленного оборудования
              </div>
            </div>
          </div>
        </div>
        <div v-if="filterDatesInActive().length && !selectedActive" class="">
          <div v-for="(service, index) in filterDatesInActive()" :key="service.id"
            class=" border-b border-gray-200 p-2">
            <div class="min-w-full grid grid-cols-5 gap-52 items-center whitespace-nowrap">
              <div class="flex items-center space-x-2">
                <span>{{ service.equipment_info.category.name }} {{ service.equipment_info.size.name }} {{
                  service.equipment_info.series }}</span>
                <button @click="toggleSubservice(index)">
                  <svg v-if="!showSubservices[index]" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                      fill="#242533" />
                  </svg>

                  <svg v-if="showSubservices[index]" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M2.27174 9.29127C2.27174 8.9599 2.54037 8.69127 2.87174 8.69127H14.6283C14.9596 8.69127 15.2283 8.9599 15.2283 9.29127C15.2283 9.62265 14.9596 9.89127 14.6283 9.89127H2.87174C2.54037 9.89127 2.27174 9.62265 2.27174 9.29127Z"
                      fill="#242533" />
                  </svg>
                </button>
              </div>
              <div>{{ service.shipping_date }}</div>
              <div>{{ service.commentary }}</div>
              <div>{{ service.income ?? "Не задано" }}</div>
              <div class="flex justify-center items-center space-x-2">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                    fill="#21272A" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M4.65799 7.03952C4.91229 6.71256 5.3835 6.65366 5.71046 6.90796L12 11.7998L18.2895 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5963 7.36648 19.5374 7.83769 19.2105 8.09199L12.4605 13.342C12.1896 13.5526 11.8104 13.5526 11.5395 13.342L4.78955 8.09199C4.46259 7.83769 4.40369 7.36648 4.65799 7.03952Z"
                    fill="#21272A" />
                </svg>
                <Menu as="div" class="relative inline-block text-left">
                                        <div class="sm:overflow-x-visible">
                                            <MenuButton
                                                class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                                <svg width="4" height="16" viewBox="0 0 4 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M3.5 2C3.5 2.82843 2.82843 3.5 2 3.5C1.17157 3.5 0.5 2.82843 0.5 2C0.5 1.17157 1.17157 0.5 2 0.5C2.82843 0.5 3.5 1.17157 3.5 2Z"
                                                        fill="#687182" />
                                                    <path
                                                        d="M3.5 8C3.5 8.82843 2.82843 9.5 2 9.5C1.17157 9.5 0.5 8.82843 0.5 8C0.5 7.17157 1.17157 6.5 2 6.5C2.82843 6.5 3.5 7.17157 3.5 8Z"
                                                        fill="#687182" />
                                                    <path
                                                        d="M3.5 14C3.5 14.8284 2.82843 15.5 2 15.5C1.17157 15.5 0.5 14.8284 0.5 14C0.5 13.1716 1.17157 12.5 2 12.5C2.82843 12.5 3.5 13.1716 3.5 14Z"
                                                        fill="#687182" />
                                                </svg>
                                            </MenuButton>
                                        </div>

                                        <transition enter-active-class="transition ease-out duration-100"
                                            enter-from-class="transform opacity-0 scale-95"
                                            enter-to-class="transform opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform opacity-100 scale-100"
                                            leave-to-class="transform opacity-0 scale-95">
                                            <MenuItems
                                                class="absolute top-6 z-200 right-2 justify-between w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                <div class="py-1">


                                                  
                                                  <MenuItem>
                                                    <Link method="delete" :href="route('services.edit', service.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Редактировать
                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.0981 3.10827C10.6795 3.52693 10.4702 3.73626 10.2506 3.77677C10.0309 3.81728 9.88682 3.67315 9.59856 3.3849L8.61511 2.40144C8.32686 2.11319 8.18273 1.96906 8.22324 1.74946C8.26375 1.52985 8.47308 1.32052 8.89174 0.901864L9.11838 0.675215C9.53704 0.25656 9.74637 0.0472318 9.96598 0.00672082C10.1856 -0.0337902 10.3297 0.110336 10.618 0.398589L11.6014 1.38204C11.8897 1.6703 12.0338 1.81442 11.9933 2.03403C11.9528 2.25364 11.7434 2.46297 11.3248 2.88162L11.0981 3.10827Z" fill="#464F60"/>
<path d="M0.954058 11.9107C0.454198 12.0029 0.204269 12.049 0.077628 11.9224C-0.0490128 11.7957 -0.00290857 11.5458 0.0893002 11.0459L0.311753 9.84003C0.35173 9.62332 0.371719 9.51497 0.43005 9.41009C0.488381 9.30521 0.579134 9.21446 0.76064 9.03295L6.31438 3.47921C6.73303 3.06056 6.94236 2.85123 7.16197 2.81072C7.38158 2.77021 7.5257 2.91433 7.81396 3.20259L8.79741 4.18604C9.08566 4.4743 9.22979 4.61842 9.18928 4.83803C9.14877 5.05764 8.93944 5.26697 8.52078 5.68562L2.96705 11.2394C2.78554 11.4209 2.69479 11.5116 2.58991 11.5699C2.48503 11.6283 2.37668 11.6483 2.15997 11.6882L0.954058 11.9107Z" fill="#464F60"/>
</svg>



                                                    </Link>
                                                    </MenuItem>

                                                    <MenuItem>
                                                    <Link method="delete" :href="route('services.destroy', service.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Удалить
                                                    <svg width="12" height="14" viewBox="0 0 12 14" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M2.75 2.75V3.25H0.75C0.335786 3.25 0 3.58579 0 4C0 4.41421 0.335786 4.75 0.75 4.75H1.51389L1.89504 11.6109C1.95392 12.6708 2.8305 13.5 3.89196 13.5H8.10802C9.16948 13.5 10.0461 12.6708 10.1049 11.6109L10.4861 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H9.25V2.75C9.25 1.50736 8.24264 0.5 7 0.5H5C3.75736 0.5 2.75 1.50736 2.75 2.75ZM5 2C4.58579 2 4.25 2.33579 4.25 2.75V3.25H7.75V2.75C7.75 2.33579 7.41421 2 7 2H5ZM5.25 6.75C5.25 6.33579 4.91421 6 4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V11.25C3.75 11.6642 4.08579 12 4.5 12C4.91421 12 5.25 11.6642 5.25 11.25V6.75ZM8.25 6.75C8.25 6.33579 7.91421 6 7.5 6C7.08579 6 6.75 6.33579 6.75 6.75V11.25C6.75 11.6642 7.08579 12 7.5 12C7.91421 12 8.25 11.6642 8.25 11.25V6.75Z"
                                                            fill="#DC4067" />
                                                    </svg>



                                                    </Link>
                                                    </MenuItem>

                                                    <MenuItem>
                                                    <Link method="post" :href="route('services.createIncident', service.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Инциндент
                                                    <svg width="12" height="14" viewBox="0 0 12 14" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M2.75 2.75V3.25H0.75C0.335786 3.25 0 3.58579 0 4C0 4.41421 0.335786 4.75 0.75 4.75H1.51389L1.89504 11.6109C1.95392 12.6708 2.8305 13.5 3.89196 13.5H8.10802C9.16948 13.5 10.0461 12.6708 10.1049 11.6109L10.4861 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H9.25V2.75C9.25 1.50736 8.24264 0.5 7 0.5H5C3.75736 0.5 2.75 1.50736 2.75 2.75ZM5 2C4.58579 2 4.25 2.33579 4.25 2.75V3.25H7.75V2.75C7.75 2.33579 7.41421 2 7 2H5ZM5.25 6.75C5.25 6.33579 4.91421 6 4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V11.25C3.75 11.6642 4.08579 12 4.5 12C4.91421 12 5.25 11.6642 5.25 11.25V6.75ZM8.25 6.75C8.25 6.33579 7.91421 6 7.5 6C7.08579 6 6.75 6.33579 6.75 6.75V11.25C6.75 11.6642 7.08579 12 7.5 12C7.91421 12 8.25 11.6642 8.25 11.25V6.75Z"
                                                            fill="#DC4067" />
                                                    </svg>



                                                    </Link>
                                                    </MenuItem>

                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
              </div>
            </div>
            <div v-if="showSubservices[index]" class="mt-2">
              <div class="" v-if="service.subservices.length > 0">
                <div v-for="subservice in service.subservices" :key="subservice.id"
                  class="grid whitespace-nowrap grid-cols-11 gap-52 bg-gray-50 py-2 rounded-md">

                  <div>{{ subservice.equipment.category.name }} {{ subservice.equipment.size.name }} {{
                    subservice.equipment.series }}</div>
                  <div>{{ subservice.shipping_date ?? "Не задано" }}</div>
                  <div>{{ subservice.commentary ?? "Не задано" }}</div>
                  <div>{{ subservice.income ?? "Не задано" }}</div>
                  <div></div>
                </div>
              </div>
              <div v-else class="p-2 bg-red-50 text-red-500">
                Нет добавленного оборудования
              </div>
            </div>
          </div>
        </div>
      </div>


    </div>

  </AuthenticatedLayout>

</template>