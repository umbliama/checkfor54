<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { EquipMenuItems } from '../../../constants/index';
import { Link, router } from '@inertiajs/vue3';
import { computed, onMounted, reactive } from 'vue';

import store from '../../../store/index';
import EquipNav from '@/Components/EquipNav.vue';
import { MenuButton, MenuItems, MenuItem, Menu } from '@headlessui/vue';

const seriesActive = computed(() => store.getters['equipment/getSeriesActive'])

const repairs = computed(() => store.getters['equipment/getEquipmentRepairs'])
const tests = computed(() => store.getters['equipment/getEquipmentTests'])

const props = defineProps({
    equipment: Array,
    equipment_categories: Array,
    equipment_categories_counts: Array,
    equipment_sizes_counts: Array,
    equipment_sizes: Array,
    equipment_location: Array,
    equipment_series: Array,
    equipment_repairs: Array

})

const setCategoryId = (categoryId) => {
    store.dispatch('equipment/updateCategory', categoryId)
    updateUrl();
}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    updateUrl();
}

const setSeriesId = (seriesId) => {
    store.dispatch('equipment/updateSeriesId', seriesId)
    updateUrl();
    if (selectedCategory.value && selectedSize.value && seriesActive.value) {
        console.log(selectedSize.value, seriesActive.value)
        updateReportTable(selectedCategory.value, selectedSize.value, seriesActive.value);
        updateRepairTable(selectedCategory.value, selectedSize.value, seriesActive.value)
        updateTestsTable(selectedCategory.value, selectedSize.value, seriesActive.value)
    }
}


const updateRepairTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentRepair', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}

const updateReportTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentReport', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}

const updateTestsTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentTest', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}


const updateUrl = () => {
    if (selectedCategory.value && selectedSize.value) {
        router.get(route('equip.report', { category_id: selectedCategory.value, size_id: selectedSize.value, series: seriesActive.value }));
    }
};

const setMenuItem = (item) => {
    store.dispatch('equipment/updateMenuItem', item)
}




const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const menuActive = computed(() => store.getters['equipment/getMenuActiveItem']);
const equipment_categories = computed(() => store.getters['equipment/getEquipmentCategories'])
const equipment_sizes_counts = computed(() => store.getters['equipment/getEquipmentSizesCounts'])
const equipment_categories_counts = computed(() => store.getters['equipment/getEquipmentSizesCounts'])
const equipment_sizes = computed(() => store.getters['equipment/getEquipmentSizes'])


const form = reactive({})

onMounted(() => {



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

        <div class="container mt-5 sm:mx-auto md:mx-auto">
            <nav class="bg-my-gray rounded-xl ">
                <div class="max-w-screen-xl py-2 px-4 ">
                    <div class="mt-6 flex items-center">
                        <ul
                            class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-md w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                                @click="setCategoryId(item.id)" v-for="item in equipment_categories" :key="item.id">
                                <Link class="flex justify-around"
                                    :href="route('equip.report', { category_id: item.id })">
                                {{ item.name }}

                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 flex items-center sm:overflow-x-auto">
                        <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-lg w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedSize === item.id }"
                                @click="setSizeId(item.id)" v-for="item in equipment_sizes" :key="item.id">
                                <Link class="flex justify-around" :href="route('equip.report', { size_id: item.id })">
                                {{ item.name }}

                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="lg:hidden md:hidden flex bg-my-gray mt-4 p-4">

                <select class="text-gray-500 border-gray-200" name="" id="">
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
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" name="search" id="default-search"
                            class="block w-full ps-10 text-sm text-gray-900 border-b-2 border-gray-200 border-t-0 border-l-0 border-r-0 bg-white-50 py-4"
                            placeholder="Поиск">
                    </div>
                </form>
            </div>

            <div class="flex">
                <ul class="flex flex-col mt-5 mr-5">
                    <li :class="{ 'bg-my-gray': seriesActive === series }" class="border-b-2  border-gray-200 px-2 py-4"
                        @click="setSeriesId(series)" v-for="series in equipment_series">{{ series }}</li>
                </ul>
                <div class="flex-col">
                    <div v-if="equipment" class="flex w-full mt-5 flex-col sm:overflow-x-auto">
                        <h3 class="text-side-gray-text font-bold">Характеристики</h3>
                        <div class="sm:overflow-x-auto">
                            <table class="w-full mt-5 table-auto">
                                <thead>
                                    <tr class="whitespace-nowrap">
                                        <th class="sm:text-sm">Производитель</th>
                                        <th class="sm:text-sm">Cерия</th>
                                        <th class="sm:text-sm">Дата изготовления</th>
                                        <th class="sm:text-sm">Состояние</th>
                                        <th class="sm:text-sm">Стоимость</th>
                                        <th class="sm:text-sm">Примечание</th>
                                        <th class="flex justify-center"><svg width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                    <tr class="whitespace-nowrap" v-for="item in equipment">
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
                                                    <path d="M5.66663 8.33328L12.9999 1" stroke="#808192"
                                                        stroke-width="1.2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <div class="ml-4">
                                                    {{ item.manufactor }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center border border-slate-200 p-2">{{ item.series }}</td>
                                        <td class="text-center border border-slate-200 p-2">{{ item.manufactor_date }}
                                        </td>
                                        <td class="text-center border border-slate-200 p-2">
                                            <EquipStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />

                                        </td>
                                        <td class="text-center border border-slate-200 p-2">{{ item.price }}</td>
                                        <td class="text-center border border-slate-200 p-2">{{ item.notes }}</td>
                                        <td
                                            class="text-center border flex items-center justify-between border-slate-200 p-2">
                                            <div class="mr-2"><svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                            class="absolute top-6 z-200 right-2  w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                            <div class="py-1">
                                                                <MenuItem>
                                                                <Link :href="route('contragents.create')"
                                                                    class="flex items-center justify-around"
                                                                    :class="['block px-4 py-2 text-sm']">Создать
                                                                контрагента
                                                                <svg width="16" height="16" viewBox="0 0 16 16"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M8.74999 2.75004C8.74999 2.33582 8.4142 2.00004 7.99999 2.00004C7.58578 2.00004 7.24999 2.33582 7.24999 2.75004V7.25H2.75C2.33579 7.25 2 7.58579 2 8C2 8.41422 2.33579 8.75 2.75 8.75H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75H13.25C13.6642 8.75 14 8.41422 14 8C14 7.58579 13.6642 7.25 13.25 7.25H8.74999V2.75004Z"
                                                                        fill="#464F60" />
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
                    </div>

                    <div class="sm:overflow-x-auto sm:pb-20 ">
                        <h3 class="text-side-gray-text font-bold">История ремонта</h3>
                        <table class="table-auto w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">Дата ремонта</th>
                                    <th class="border px-4 py-2 text-left text-gray-600">Место проведения</th>
                                    <th class="border px-4 py-2 text-left text-gray-600">Описание</th>
                                    <th class="border px-4 py-2 text-left text-gray-600">Расход</th>
                                    <th class="border px-4 py-2 text-left text-gray-600"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="repair in repairs" :key="repair.id">
                                    <td class="border px-4 py-2">{{ repair.repair_date }}</td>
                                    <td class="border px-4 py-2">{{ repair.location_id }}</td>
                                    <td class="border px-4 py-2">{{ repair.description }}</td>
                                    <td class="border px-4 py-2">{{ repair.expense }}</td>
                                    <td class="border px-4 py-2 flex justify-center items-center">
                                        <svg class="w-7 h-7 text-gray-500 " fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 12H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                        </svg>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="overflow-x-auto sm:pb-20 ">
                        <h3 class="text-side-gray-text font-bold"> Стендовые испытания</h3>

                        <table class="table-auto w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border px-4 py-2 text-left text-gray-600">Дата испытания</th>
                                    <th class="border px-4 py-2 text-left text-gray-600">Место проведения</th>
                                    <th class="border px-4 py-2 text-left text-gray-600">Описание</th>
                                    <th class="border px-4 py-2 text-left text-gray-600">Расход</th>
                                    <th class="border px-4 py-2 text-left text-gray-600"><svg width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                <tr v-for="test in tests" :key="test.id">
                                    <td class="border px-4 py-2">{{ test.test_date }}</td>
                                    <td class="border px-4 py-2">{{ test.location_id }}</td>
                                    <td class="border px-4 py-2">{{ test.description }}</td>
                                    <td class="border px-4 py-2">{{ test.expense }}</td>
                                    <td class="border px-4 py-2 flex justify-between items-center">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m-1-4h2v2h-2v-2zm-3 8a9 9 0 1118 0 9 9 0 01-18 0z" />
                                        </svg>
                                        <svg class="w-5 h-5 text-gray-500 ml-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 12H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                        </svg>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="p-4">

                <button @click="submit"
                    class="text-center bg-my-gray    justify-end mt-2 py-3 px-10 bg-gray-300 p">Сохранить</button>

            </div>


        </div>

    </AuthenticatedLayout>
</template>