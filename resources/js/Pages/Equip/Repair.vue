<script setup>
import EquipNav from '@/Components/Equip/EquipNav.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import store from '../../../store/index';
import { computed, reactive } from 'vue';
import { MenuItem, Menu, MenuItems, MenuButton } from '@headlessui/vue';

const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const menuActive = computed(() => store.getters['equipment/getMenuActiveItem']);
const repairs = computed(() => store.getters['equipment/getEquipmentRepairs'])
const seriesActive = computed(() => store.getters['equipment/getSeriesActive'])

const setCategoryId = (categoryId) => {
    store.dispatch('equipment/updateCategory', categoryId)
    updateUrl();
}

const getLocationName = (id) => {
    const location = props.equipment_location.find(loc => loc.id === id);
    return location ? location.name : 'Location not found';

}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    updateUrl();
}

const setSeriesId = (seriesId) => {
    store.dispatch('equipment/updateSeriesId', seriesId)
    if (selectedCategory.value && selectedSize.value && seriesActive.value) {
        console.log(selectedSize.value, seriesActive.value)
        updateUrl()
        updateRepairTable(selectedCategory.value, selectedSize.value, seriesActive.value);
    }
}

const updateRepairTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentRepair', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}
const updateUrl = () => {
    if (selectedCategory.value && selectedSize.value) {
        router.get(route('equip.repair', { category_id: selectedCategory.value, size_id: selectedSize.value, series: seriesActive.value }));
    }
};

const props = defineProps({
    equipmentSeries: Array,
    equipment_location: Array,
    equipment_repairs: Array,
    equipment_sizes: Array,
    equipment_categories_counts: Array,
    equipment_sizes_counts: Array,
    equipment_categories: Array
})



const form = reactive({
    'repair_date': null,
    'location_id': null,
    'expense': null,
    'description': null,
    'category_id': null,
    'size_id': null,
    'series': null
})
function submit() {
    router.post('/equip/repair', {
        repair_date: form.repair_date,
        location_id: form.location_id,
        expense: form.expense,
        description: form.description,
        category_id: selectedCategory.value,
        size_id: selectedSize.value,
        series: seriesActive.value
    })
    updateRepairTable(selectedCategory.value, selectedSize.value, seriesActive.value);

}


</script>

<template>
    <AuthenticatedLayout>
        <EquipNav></EquipNav>

        <div class="container md:mx-auto sm:mx-auto">
            <nav class="bg-my-gray m-5 rounded-xl">
                <div class="max-w-screen-xl py-2 px-4 ">
                    <div class="mt-6 flex items-center">
                        <ul
                            class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-md w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                                @click="setCategoryId(item.id)" v-for="item in equipment_categories" :key="item.id">
                                <Link class="flex items-center justify-around"
                                    :href="route('equip.repair', { category_id: item.id })">
                                {{ equipment_categories[item.id - 1].name }}
                                <span
                                    class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                    {{ equipment_categories_counts[item.id] }}
                                </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 sm:overflow-x-auto flex items-center">
                        <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-lg w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedSize === item.id }"
                                @click="setSizeId(item.id)" v-for="item in equipment_sizes" :key="item.id">
                                <Link class="flex items-center justify-around"
                                    :href="route('equip.repair', { size_id: item.id })">
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

            <div class="lg:hidden md:hidden flex bg-my-gray  p-4">

                <select v-model="seriesActive" class="text-gray-500 border-gray-200" name="" id="">
                    <option value="">Номер</option>
                    <option v-for="series in equipmentSeries" @click="setSeriesId(series)" :value="series">{{ series }}
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

            <div class="flex m-5">
                <ul class="flex flex-col lg:flex  sm:hidden">
                    <li :class="{ 'bg-my-gray': seriesActive === series }"
                        class="border-b-2 font-roboto font-weight-500     border-gray-200 px-2 py-2"
                        @click="setSeriesId(series)" v-for="series in equipmentSeries">{{ series }}</li>
                </ul>
                <div class="flex w-full flex-col py-1 px-4">
                    <h3 class="font-roboto text-side-gray-text  font-semibold text-lg">Добавить новую запись ремонта
                    </h3>
                    <div class="sm:overflow-x-auto mt-2">
                        <!-- Header Row -->
                        <div class="grid grid-cols-5 bg-gray-50 border-b border-gray-200">
                            <div class="px-4 py-2 text-left text-gray-600 border-r">Дата ремонта</div>
                            <div class="px-4 py-2 text-left text-gray-600 border-r">Место проведения</div>
                            <div class="px-4 py-2 text-left text-gray-600 border-r">Описание</div>
                            <div class="px-4 py-2 text-left text-gray-600 border-r">Расход</div>
                            <div class="px-4 py-2 text-left text-gray-600"><svg width="24" height="24"
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
                            </div>
                        </div>

                        <!-- Data Row -->
                        <div class="grid grid-cols-5 border-b border-gray-200">
                            <!-- Date Input with Icon -->
                            <div class="flex items-center border-r">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                    <path
                                        d="M15.9999 12.6671V16.667C15.9999 17.0207 15.8594 17.3598 15.6094 17.6098C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6098C6.14047 17.3598 6 17.0207 6 16.667V9.33377C6 8.98015 6.14047 8.64101 6.39052 8.39096C6.64057 8.14092 6.9797 8.00044 7.33332 8.00044H11.3333"
                                        stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <input type="date" v-model="form.repair_date"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Location Select -->
                            <div class="border-r  flex items-center">
                                <select v-model="form.location_id"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option v-for="location in equipment_location" :value="location.id">{{ location.name
                                        }}</option>
                                </select>
                            </div>

                            <!-- Description Textarea -->
                            <div class="border-r ">
                                <textarea v-model="form.description"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    rows="2">Lorem ipsum dolor sit amet...</textarea>
                            </div>

                            <!-- Expense Input -->
                            <div class="border-r flex items-center">
                                <input v-model="form.expense"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="280000">
                            </div>

                            <!-- Action Icon -->
                            <div class="flex px-4 w-1/6 items-center ">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                        fill="#21272A" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.65802 7.03955C4.91232 6.71259 5.38353 6.65369 5.71049 6.90799L12 11.7999L18.2896 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5964 7.36651 19.5375 7.83772 19.2105 8.09202L12.4605 13.342C12.1897 13.5527 11.8104 13.5527 11.5396 13.342L4.78958 8.09202C4.46262 7.83772 4.40372 7.36651 4.65802 7.03955Z"
                                        fill="#21272A" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex justify-end">

                            <button @click="submit"
                                class="text-center bg-my-gray    justify-end mt-10 py-3 px-10 bg-gray-300 p">Сохранить</button>

                        </div>
                        <div class="sm:overflow-x-auto mt-4 sm:pb-32">
                            <h3 class="font-roboto text-side-gray-text  font-semibold text-lg">История ремонта
                    </h3>

                            <!-- Header row -->
                            <div
                                class="grid grid-cols-5 bg-gray-50 border-b border-gray-200 text-left text-gray-600 px-4 py-2 font-semibold">
                                <div>Дата ремонта</div>
                                <div>Место проведения</div>
                                <div>Описание</div>
                                <div>Расход</div>
                                <div><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
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
                                    </svg></div>
                            </div>

                            <!-- Data rows -->
                            <div v-for="repair in repairs" :key="repair.id"
                                class="grid grid-cols-5 border-b border-gray-200 px-4 py-2">
                                <div class="flex py-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333"
                                            stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ repair.repair_date }}</div>
                                <div class="py-2">{{ getLocationName(repair.location_id) }}</div>
                                <div class="py-2 overflow-hidden text-ellipsis whitespace-nowrap">{{ repair.description
                                    }}</div>
                                <div class="py-2">{{ repair.expense }}</div>
                                <div class="py-2 space-x-2 flex items-center">
                                    <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 12H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2z" />
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
                                                    <Link method="delete" :href="route('equip.destroyRepair', repair.id)"
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


                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>





                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>


        </div>
    </AuthenticatedLayout>

</template>
