<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import store from '../../../store/index';
import { computed, onMounted, ref } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import EquipStatus from '@/Components/EquipStatus.vue';
import EquipNav from '@/Components/EquipNav.vue';


import { Menu, MenuItems, MenuItem, MenuButton } from '@headlessui/vue'



const props = defineProps({
    equipment: Object,
    equipment_categories: Array,
    equipment_categories_counts: Array,
    equipment_sizes_counts: Array,
    equipment_sizes: Array,
    equipment_location: Array

})


const selectCategory = (category) => {
    store.dispatch('equipment/updateCategory', category);
    updateUrl()
};
const selectSize = (size) => {
    store.dispatch('equipment/updateSize', size);
    updateUrl()
};

const updateUrl = () => {
    if (selectedCategory.value || selectedSize.value) {
        router.get(route('equip.index', { category_id: selectedCategory.value, size_id: selectedSize.value }));
    }
};

const isDropdownOpen = ref(false);

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const closeDropdown = () => {
    isDropdownOpen.value = false;
};

const isInputVisible = computed(() => store.getters['equipment/getInputLocationShown']);
const isDropdownVisible = computed(() => store.getters['equipment/getDropdownShown']);
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);


const toggleInputLocation = (value) => {
    store.dispatch('equipment/updateInputLocationShown', value);
};





const currentPage = ref(props.equipment.current_page || 1);
const lastPage = ref(props.equipment.last_page || 1);




// Fetch initial data on component mount
onMounted(() => {

    const { current_page, total } = props.equipment;

    // Dispatch the pagination state to Vuex store
    store.dispatch('pagination/updatePagination', {
        currentPage: current_page,
        totalPages: total,
    });
    currentPage.value = props.equipment.current_page;
    lastPage.value = props.equipment.last_page;

    store.dispatch('fetchLocations')
    store.dispatch('equipment/updateEquipmentCategories', props.equipment_categories)
    store.dispatch('equipment/updateEquipmentSizesCounts', props.equipment_sizes_counts)
    store.dispatch('equipment/updateEquipmentCategoriesCounts', props.equipment_categories_counts)
    store.dispatch('equipment/updateEquipmentSizes', props.equipment_sizes)

});


</script>

<template>

    <AuthenticatedLayout>

        <EquipNav></EquipNav>
        <div class="container sm:pb-20 mt-5 md:mx-auto sm:mx-auto ">
            <nav class="bg-my-gray">
                <div class="max-w-screen-xl sm:rounded-2xl py-2 px-4 ">
                    <div class="mt-6 flex items-center">
                        <ul
                            class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-md w-full sm:py-2"
                                :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                                @click="selectCategory(item.id)" v-for="item in equipment_categories" :key="item.id">
                                <Link class="flex items-center justify-around" :href="route('equip.index')">
                                {{ item.name }}
                                <span
                                    class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                    {{ equipment_categories_counts[item.id] }}
                                </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 flex items-center">
                        <ul
                            class="flex flex-row sm:overflow-x-auto font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-lg sm:py-2 w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedSize === item.id }"
                                @click="selectSize(item.id)" v-for="item in equipment_sizes" :key="item.id">
                                <Link class="flex justify-around">
                                {{ item.name }}
                                <span
                                    class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                    {{ equipment_sizes_counts[item.id] }}
                                </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>


            <div class="sm:hidden lg:flex md:flex border-b-2  items-center mt-5 space-x-6">
                <div class="border-b-2 border-blue-900 flex items-center space-x-2">
                    <span class="text-blue-900 font-bold">Все</span>
                </div>

                <div v-for="location in store.getters.cities" class="text-gray-800">{{ location.name }}</div>
            </div>

            <div
                class="lg:hidden md:hidden mt-5  py-2 px-1  sm:bg-gray-200 flex lg:justify-start sm:justify-center items-center">
                <form action="" method="GET" class="max-w-md">
                    <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Поиск</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" name="search" id="default-search"
                            class="block w-full py-4 ps-10 text-sm text-gray-900 border-b-2 border-gray-200  border-t-0 border-l-0 border-r-0 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Поиск">
                    </div>
                </form>
                <div class="ml-4 mr-4">
                    <svg width="3" height="28" viewBox="0 0 3 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="path-1-inside-1_2101_83802" fill="white">
                            <path d="M0.5 0H2.5V28H0.5V0Z" />
                        </mask>
                        <path d="M0.5 0H2.5V28H0.5V0Z" fill="white" />
                        <path
                            d="M0 0V1H1V0H0ZM0 3V5H1V3H0ZM0 7V9H1V7H0ZM0 11V13H1V11H0ZM0 15V17H1V15H0ZM0 19V21H1V19H0ZM0 23V25H1V23H0ZM0 27V28H1V27H0ZM-0.5 0V1H1.5V0H-0.5ZM-0.5 3V5H1.5V3H-0.5ZM-0.5 7V9H1.5V7H-0.5ZM-0.5 11V13H1.5V11H-0.5ZM-0.5 15V17H1.5V15H-0.5ZM-0.5 19V21H1.5V19H-0.5ZM-0.5 23V25H1.5V23H-0.5ZM-0.5 27V28H1.5V27H-0.5Z"
                            fill="#C1C7CD" mask="url(#path-1-inside-1_2101_83802)" />
                    </svg>

                </div>
                <button class="flex  border-2 bg-white py-4 items-center  px-10">
                    <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M2.58007 2L9.06207 10.101C9.34581 10.4558 9.50029 10.8967 9.50007 11.351V18L11.5001 16.5V11.35C11.5001 10.896 11.6545 10.4556 11.9381 10.101L18.4201 2H2.58107H2.58007ZM2.58007 0H18.4201C18.7969 4.70007e-05 19.166 0.106524 19.4849 0.307166C19.8038 0.507808 20.0595 0.794454 20.2227 1.13409C20.3858 1.47372 20.4497 1.85253 20.4069 2.22687C20.3642 2.60122 20.2166 2.95588 19.9811 3.25L13.5001 11.35V16.5C13.5001 16.8105 13.4278 17.1167 13.2889 17.3944C13.1501 17.6721 12.9485 17.9137 12.7001 18.1L10.7001 19.6C10.4029 19.8229 10.0496 19.9586 9.67968 19.9919C9.30976 20.0253 8.93786 19.955 8.60565 19.7889C8.27343 19.6227 7.99404 19.3674 7.79877 19.0515C7.6035 18.7355 7.50007 18.3714 7.50007 18V11.35L1.01907 3.25C0.783588 2.95588 0.635973 2.60122 0.593233 2.22687C0.550493 1.85253 0.614365 1.47372 0.777494 1.13409C0.940622 0.794454 1.19637 0.507808 1.51528 0.307166C1.83419 0.106524 2.2033 4.70007e-05 2.58007 0V0Z"
                            fill="#697077" />
                    </svg>

                    Фильтры</button>
            </div>

            <div class="flex lg:flex md:flex sm:hidden items-baseline">
                <form action="" method="GET" class="max-w-md mt-4">
                    <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">поис</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" name="search" id="default-search"
                            class="block w-full p-1 py-2 ps-10 text-sm text-gray-900 border-b-2 border-gray-200 rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Поиск">
                    </div>
                </form> <button class="flex ml-6 border-2 items-center rounded-lg px-6"><svg width="13" height="9"
                        viewBox="0 0 13 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="2.60474" cy="6.50366" r="2" fill="white" stroke="#415476" />
                        <line x1="4.10474" y1="6.50366" x2="12.1047" y2="6.50366" stroke="#415476" />
                        <circle cx="9.89476" cy="2.8417" r="2" transform="rotate(-177.996 9.89476 2.8417)" fill="white"
                            stroke="#415476" />
                        <line x1="8.10474" y1="2.50366" x2="0.104735" y2="2.50366" stroke="#415476" />
                    </svg>
                    Фильтры</button>
            </div>

            <div v-if="selectedCategory == 1"
                class="lg:overflow-y-visible sm:pb-28 lg:overflow-x-visible sm:overflow-y-auto sm:overflow-x-auto pb-30">
                <table class="w-full mt-5 table-auto">
                    <thead>
                        <tr class="">
                            <th class="sm:text-sm">Производитель</th>
                            <th class="sm:text-sm">Cерия</th>
                            <th class="sm:text-sm">Заходность</th>
                            <th class="sm:text-sm">Длина</th>
                            <th class="sm:text-sm">Статор Ротор</th>
                            <th class="sm:text-sm">Наработка</th>
                            <th class="sm:text-sm">Наработка дс.</th>
                            <th class="sm:text-sm">Дата изготовления</th>
                            <th class="sm:text-sm">Стоимость</th>
                            <th class="sm:text-sm">Примечание</th>
                            <th class="sm:text-sm">Состояние</th>
                            <th class="flex justify-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
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
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="equipment.data" class="whitespace-nowrap" :key="item.id"
                            v-for="item in equipment.data">
                            <td class="text-center border border-slate-200 p-2">
                                <div class="flex justify-start ">
                                    <svg width="20" height="20" viewBox="0 0 14 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.9999 7.66706V11.667C10.9999 12.0206 10.8594 12.3598 10.6094 12.6098C10.3594 12.8599 10.0202 13.0003 9.6666 13.0003H2.33332C1.9797 13.0003 1.64057 12.8599 1.39052 12.6098C1.14047 12.3598 1 12.0206 1 11.667V4.33375C1 3.98013 1.14047 3.641 1.39052 3.39095C1.64057 3.1409 1.9797 3.00043 2.33332 3.00043H6.33329"
                                            stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M9 1H13V4.99997" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5.66663 8.33328L12.9999 1" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="ml-4">
                                        {{ item.manufactor }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center border border-slate-200 p-2">{{ item.series }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.zahodnost }}</td>

                            <td class="text-center border border-slate-200 p-2">{{ item.length }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.stator_rotor }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.operating }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.narabotka_ds }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.manufactor_date }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.price }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.notes }}</td>

                            <td class="text-center border border-slate-200 p-2">
                                <EquipStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />

                            </td>
                            <td class="text-center border flex items-center justify-between border-slate-200 p-2">
                                <div class="mr-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                            fill="#21272A" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                            fill="#21272A" />
                                    </svg></div>
                                <div class="ml-2">


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
                                                    <Link method="delete" :href="route('equip.destroy', item.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Открыть
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z"
                                                            fill="#464F60" />
                                                    </svg>




                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link method="delete" :href="route('equip.destroy', item.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Редактировать
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                            fill="#464F60" />
                                                        <path
                                                            d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                            fill="#464F60" />
                                                    </svg>


                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link method="delete" :href="route('equip.destroy', item.id)"
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

                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>

                                </div>


                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="selectedCategory == 2"
                class="lg:overflow-y-visible sm:pb-28 lg:overflow-x-visible sm:overflow-y-auto sm:overflow-x-auto pb-30">
                <table class="w-full mt-5 table-auto">
                    <thead>
                        <tr class="">
                            <th class="sm:text-sm">Производитель</th>
                            <th class="sm:text-sm">Cерия</th>
                            <th class="sm:text-sm">Длина</th>
                            <th class="sm:text-sm">Наработка</th>
                            <th class="sm:text-sm">Дата изготовления</th>
                            <th class="sm:text-sm">Стоимость</th>
                            <th class="sm:text-sm">Примечание</th>
                            <th class="sm:text-sm">Состояние</th>
                            <th class="flex justify-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
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
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="equipment.data" class="whitespace-nowrap" :key="item.id"
                            v-for="item in equipment.data">
                            <td class="text-center border border-slate-200 p-2">
                                <div class="flex justify-start ">
                                    <svg width="20" height="20" viewBox="0 0 14 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.9999 7.66706V11.667C10.9999 12.0206 10.8594 12.3598 10.6094 12.6098C10.3594 12.8599 10.0202 13.0003 9.6666 13.0003H2.33332C1.9797 13.0003 1.64057 12.8599 1.39052 12.6098C1.14047 12.3598 1 12.0206 1 11.667V4.33375C1 3.98013 1.14047 3.641 1.39052 3.39095C1.64057 3.1409 1.9797 3.00043 2.33332 3.00043H6.33329"
                                            stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M9 1H13V4.99997" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5.66663 8.33328L12.9999 1" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="ml-4">
                                        {{ item.manufactor }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center border border-slate-200 p-2">{{ item.series }}</td>

                            <td class="text-center border border-slate-200 p-2">{{ item.length }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.operating }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.manufactor_date }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.price }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.notes }}</td>

                            <td class="text-center border border-slate-200 p-2">
                                <EquipStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />

                            </td>
                            <td class="text-center border flex items-center justify-between border-slate-200 p-2">
                                <div class="mr-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                            fill="#21272A" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                            fill="#21272A" />
                                    </svg></div>
                                <div class="ml-2">


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
                                                    <Link method="delete" :href="route('equip.edit', item.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Открыть
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z"
                                                            fill="#464F60" />
                                                    </svg>




                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link method="delete" :href="route('equip.edit', item.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Редактировать
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                            fill="#464F60" />
                                                        <path
                                                            d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                            fill="#464F60" />
                                                    </svg>


                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link method="delete" :href="route('equip.destroy', item.id)"
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

                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>

                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="selectedCategory == 3 || selectedCategory == 4 || selectedCategory == 5 || selectedCategory == 7"
                class="sm:overflow-x-auto lg:overflow-y-visible sm:pb-28 lg:overflow-x-visible sm:overflow-y-auto sm:overflow-x-auto pb-30">
                <table class="w-full mt-5 table-auto">
                    <thead>
                        <tr class="">
                            <th class="sm:text-sm">Производитель</th>
                            <th class="sm:text-sm">Cерия</th>
                            <th class="sm:text-sm">Диаметр</th>
                            <th class="sm:text-sm">Длина</th>
                            <th class="sm:text-sm">Длина с резьбой</th>
                            <th class="sm:text-sm">Резьбы</th>
                            <th class="sm:text-sm">Наработка</th>
                            <th class="sm:text-sm">Дата изготовления</th>
                            <th class="sm:text-sm">Стоимость</th>
                            <th class="sm:text-sm">Примечание</th>
                            <th class="sm:text-sm">Состояние</th>
                            <th class="flex justify-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
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
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="equipment.data" class="whitespace-nowrap" :key="item.id"
                            v-for="item in equipment.data">
                            <td class="text-center border border-slate-200 p-2">
                                <div class="flex justify-start ">
                                    <svg width="20" height="20" viewBox="0 0 14 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.9999 7.66706V11.667C10.9999 12.0206 10.8594 12.3598 10.6094 12.6098C10.3594 12.8599 10.0202 13.0003 9.6666 13.0003H2.33332C1.9797 13.0003 1.64057 12.8599 1.39052 12.6098C1.14047 12.3598 1 12.0206 1 11.667V4.33375C1 3.98013 1.14047 3.641 1.39052 3.39095C1.64057 3.1409 1.9797 3.00043 2.33332 3.00043H6.33329"
                                            stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M9 1H13V4.99997" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5.66663 8.33328L12.9999 1" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="ml-4">
                                        {{ item.manufactor }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center border border-slate-200 p-2">{{ item.series }}</td>

                            <td class="text-center border border-slate-200 p-2">{{ item.diameter }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.length }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.length_rezba }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.rezbi }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.operating }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.manufactor_date }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.price }}</td>
                            <td class="text-center border border-slate-200 p-2">{{ item.notes }}</td>

                            <td class="text-center border border-slate-200 p-2">
                                <EquipStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />

                            </td>
                            <td class="text-center border flex items-center justify-between border-slate-200 p-2">
                                <div class="mr-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                            fill="#21272A" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                            fill="#21272A" />
                                    </svg></div>
                                <div>


                                    <Menu as="div" class="relative inline-block text-left">
                                        <div class="sm:overflow-x-visible">
                                            <MenuButton
                                                class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                                <svg width="14" height="4" viewBox="0 0 14 4" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2.33337 4C1.80294 4 1.29423 3.78929 0.91916 3.41421C0.544088 3.03914 0.333374 2.53043 0.333374 2C0.333374 1.46957 0.544088 0.960859 0.91916 0.585786C1.29423 0.210714 1.80294 0 2.33337 0C2.86381 0 3.37251 0.210714 3.74759 0.585786C4.12266 0.960859 4.33337 1.46957 4.33337 2C4.33337 2.53043 4.12266 3.03914 3.74759 3.41421C3.37251 3.78929 2.86381 4 2.33337 4ZM11.6667 4C11.1363 4 10.6276 3.78929 10.2525 3.41421C9.87742 3.03914 9.66671 2.53043 9.66671 2C9.66671 1.46957 9.87742 0.960859 10.2525 0.585786C10.6276 0.210714 11.1363 0 11.6667 0C12.1971 0 12.7058 0.210714 13.0809 0.585786C13.456 0.960859 13.6667 1.46957 13.6667 2C13.6667 2.53043 13.456 3.03914 13.0809 3.41421C12.7058 3.78929 12.1971 4 11.6667 4ZM7.00004 4C6.46961 4 5.9609 3.78929 5.58583 3.41421C5.21075 3.03914 5.00004 2.53043 5.00004 2C5.00004 1.46957 5.21075 0.960859 5.58583 0.585786C5.9609 0.210714 6.46961 0 7.00004 0C7.53047 0 8.03918 0.210714 8.41426 0.585786C8.78933 0.960859 9.00004 1.46957 9.00004 2C9.00004 2.53043 8.78933 3.03914 8.41426 3.41421C8.03918 3.78929 7.53047 4 7.00004 4Z"
                                                        fill="#697077" />
                                                </svg>

                                            </MenuButton>
                                        </div>

                                        <transition enter-active-class="transition ease-out duration-100"
                                            enter-from-class="transform opacity-0 scale-95"
                                            enter-to-class="transform opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform opacity-100 scale-100"
                                            leave-to-class="transform opacity-0 scale-95">
                                            <MenuItems style="z-index: 10;"
                                                class="absolute top-0  right-0 mt-10 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5  z-200 focus:outline-none">

                                                <div class="py-1">
                                                    <MenuItem class="">
                                                    <Link :href="route('equip.edit', item.id)"
                                                        class="flex items-center justify-between"
                                                        :class="['block px-4 py-2 text-sm']">Редактировать<svg
                                                        width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                            fill="#464F60" />
                                                        <path
                                                            d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                            fill="#464F60" />
                                                    </svg>
                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link :href="route('equip.edit', item.id)"
                                                        class="flex items-center justify-between"
                                                        :class="['block px-4 py-2 text-sm']">Посмотреть<svg width="12"
                                                        height="12" viewBox="0 0 12 12" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12 6C12 9.31375 9.31384 12 6 12C2.68616 12 0 9.31375 0 6C0 2.68625 2.68616 0 6 0C9.31384 0 12 2.68625 12 6ZM4.70239 4.65747H6.82398V9.17688C6.82398 9.61435 6.46933 9.96899 6.03186 9.96899C5.59439 9.96899 5.23975 9.61435 5.23975 9.17688V6.06226H4.70239C4.31447 6.06226 4 5.74778 4 5.35986C4 4.97194 4.31447 4.65747 4.70239 4.65747ZM5.00977 2.93247C5.00977 2.80778 5.03192 2.69149 5.07587 2.58359C5.12345 2.47236 5.18773 2.37629 5.26909 2.29539C5.35044 2.21449 5.44524 2.15048 5.55383 2.10326C5.60323 2.0824 5.65408 2.06615 5.70674 2.0545C5.77357 2.03987 5.84294 2.03247 5.91485 2.03247C6.03688 2.03247 6.15202 2.05604 6.26061 2.10326C6.29003 2.11599 6.31836 2.12998 6.3456 2.14515C6.41933 2.18623 6.4858 2.23634 6.54536 2.29539C6.62671 2.37629 6.691 2.47236 6.73858 2.58359C6.76328 2.63984 6.7818 2.69835 6.79342 2.75912C6.80432 2.815 6.80977 2.87279 6.80977 2.93247C6.80977 2.99965 6.80287 3.06492 6.78906 3.12822C6.77744 3.18239 6.76037 3.23512 6.73858 3.2864C6.691 3.3943 6.62671 3.48865 6.54536 3.56955C6.50359 3.61099 6.45856 3.64801 6.40989 3.6806C6.3634 3.71157 6.31364 3.73866 6.26061 3.76168C6.15202 3.80891 6.03688 3.83247 5.91485 3.83247C5.85129 3.83247 5.78991 3.82678 5.73071 3.81532C5.66933 3.80358 5.61049 3.7857 5.55383 3.76168C5.44524 3.71446 5.35044 3.65045 5.26909 3.56955C5.18773 3.48865 5.12345 3.3943 5.07587 3.2864C5.03192 3.17517 5.00977 3.05716 5.00977 2.93247Z"
                                                            fill="#464F60" />
                                                    </svg>


                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link :href="route('equip.destroy', item.id)"
                                                        class="flex items-center justify-between"
                                                        :class="['block px-4 py-2 text-sm']" method="delete">Удалить<svg
                                                        width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                            fill="#DA1E28" />
                                                    </svg>



                                                    </Link>
                                                    </MenuItem>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>


                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pagination class="mt-5" :links="equipment.links" />


        </div>
    </AuthenticatedLayout>
</template>