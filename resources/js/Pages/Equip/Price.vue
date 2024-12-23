<script setup>
import EquipNav from '@/Components/Equip/EquipNav.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import store from '../../../store';


import { computed, reactive, toRaw } from 'vue';
import { Link, router } from '@inertiajs/vue3';


const props = defineProps({
    equipment: Object,
    equipment_categories: Array,
    equipment_categories_counts: Array,
    equipment_sizes_counts: Array,
    equipment_sizes: Array,
    equipment_location: Array,
    contragents: Array,
    prices: Array,
    equipmentData: Array
})
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const seriesActive = computed(() => store.getters['equipment/getSeriesActive']);
const menuActive = computed(() => store.getters['equipment/getMenuActiveItem']);
const getTabActive = computed(() => store.getters['equipment/getPriceTabActive']);
const getPriceRowsCount = computed(() => store.getters['equipment/getPriceRowsCount']);


const incRows = () => store.dispatch('equipment/updatePriceRowsCountInc')
const decRows = () => store.dispatch('equipment/updatePriceRowsCountDec')

const sortOrder = computed(() => store.getters['equipment/getSortOrder']);
const sortBy = computed(() => store.getters['equipment/getSortBy']);

const updateSortBy = (value) => store.dispatch("equipment/updateSortBy", value)
const updateSortOrder = (value) => store.dispatch("equipment/updateSortOrder", value)
const toggleSortBy = (column) => {
    if (sortBy.value === column) {
        updateSortOrder(sortOrder.value === 'asc' ? 'desc' : 'asc');
    } else {
        updateSortBy(column);
        updateSortOrder('asc');
    }
};
const selectCategory = (category) => {
    store.dispatch('equipment/updateCategory', category);
    updateUrl()
};
const updateActiveTab = (tab) => {
    store.dispatch('equipment/updatePriceTabActive', tab);
};


const selectSize = (sizeId) => {
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
        router.get(route('equip.price', { category_id: selectedCategory.value, size_id: selectedSize.value, series: seriesActive.value }));
    }
};


const setCategoryId = (categoryId) => {
    store.dispatch('equipment/updateCategory', categoryId)
    updateUrl();
}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    updateUrl();
}

const findEquipmentByCategoryAndSize = (categoryId, sizeId) => {
    const rawData = toRaw(props.equipmentData)
    return rawData.filter((a) => console.log(a.category == categoryId && a.size == sizeId))
}

const sortedPrices = computed(() => {
    // Filter for non-archived prices and sort
    return props.prices
        .filter(price => !price.archive) // Filter non-archived
        .slice()
        .sort((a, b) => {
            let result = 0;

            // Sort by the selected property
            if (sortBy.value === 'date') {
                result = a.store_date.localeCompare(b.store_date);
            }

            // Apply sort order (ascending or descending)
            if (sortOrder.value === 'desc') {
                result = result * -1;
            }

            return result;
        });
});

const sortedPricesArchived = computed(() => {
    // Filter for archived prices and sort
    return props.prices
        .filter(price => price.archive) // Filter archived
        .slice()
        .sort((a, b) => {
            let result = 0;

            // Sort by the selected property
            if (sortBy.value === 'date') {
                result = a.store_date.localeCompare(b.store_date);
            }

            // Apply sort order (ascending or descending)
            if (sortOrder.value === 'desc') {
                result = result * -1;
            }

            return result;
        });
});




const form = reactive({
    'category_id': selectedCategory,
    'size_id': selectedSize,
    'contragent_id': null,
    'store_date': null,
    'notes': null,
    'store_price': null,
    'operation_price': null,
    'archive': 1
})

function submit() {
    router.post('/equip/price', {
        category_id: form.category_id,
        size_id: form.size_id,
        contragent_id: form.contragent_id,
        store_date: form.store_date,
        notes: form.notes,
        store_price: form.store_price,
        operation_price: form.operation_price,
        archive: 0,
    })
}


</script>


<template>
    <AuthenticatedLayout>
        <EquipNav></EquipNav>
        <div class="container mt-5 md:mx-auto">
            <nav class="bg-my-gray rounded-xl">
                <div class="max-w-screen-xl py-2 px-4 ">
                    <div class="mt-6 flex items-center sm:overflow-x-auto">
                        <ul
                            class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-md w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                                @click="selectCategory(item.id)" v-for="item in equipment_categories" :key="item.id">
                                <Link class="flex items-center justify-around" :href="route('equip.price')">
                                {{ item.name }}
                                <span
                                    class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                    {{ equipment_categories_counts[item.id] }}
                                </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 flex items-center sm:overflow-x-auto">
                        <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-lg w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedSize === item.id }"
                                @click="selectSize(item.id)" v-for="item in equipment_sizes" :key="item.id">
                                <Link class="flex items-center justify-around"
                                    :href="route('equip.price', { size_id: item.id, category_id: selectedCategory })">
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


            <div class="lg:hidden md:hidden flex bg-my-gray mt-4 p-4">


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
                <ul class="flex flex-col lg:flex  sm:hidden">
                    <li :class="{ 'bg-my-gray': seriesActive === series }" class="border-b-2  border-gray-200 px-2 py-4"
                        @click="setSeriesId(series)" v-for="series in equipmentSeries">{{ series }}</li>
                </ul>
                <div class="flex w-full flex-col p-4">
                    <div class="tabs flex border-b-2 border-gray-300">
                        <div @click="updateActiveTab('price')"
                            :class="{ 'border-b-2 border-blue-600': getTabActive == 'price' }" class="flex mr-2  pb-3">
                            Задать
                            цену оборудования</div>
                        <div @click="updateActiveTab('archive')"
                            :class="{ 'border-b-2 border-blue-600': getTabActive == 'archive' }"
                            class="flex ml-2  pb-3">
                            История цен</div>
                    </div>
                </div>
            </div>

            <div v-if="getTabActive == 'price'" class="p-4 overflow-x-auto whitespace-nowrap">
                <div class="grid bg-table-gray grid-cols-[auto,1fr,1fr,1fr,auto,auto,auto]">
                    <!-- Header Row -->
                    <div @click="incRows" class="col-span-1 flex items-center">
                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M14.9444 8.69444C14.9444 8.31091 14.6335 8 14.25 8C13.8665 8 13.5556 8.31091 13.5556 8.69444V13.5556H8.69444C8.31091 13.5556 8 13.8665 8 14.25C8 14.6335 8.31091 14.9444 8.69444 14.9444H13.5556V19.8056C13.5556 20.1891 13.8665 20.5 14.25 20.5C14.6335 20.5 14.9444 20.1891 14.9444 19.8056V14.9444H19.8056C20.1891 14.9444 20.5 14.6335 20.5 14.25C20.5 13.8665 20.1891 13.5556 19.8056 13.5556H14.9444V8.69444Z"
                                fill="#484964" />
                        </svg>

                    </div>

                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 text-left">Заказчик</div>
                    <div @click="toggleSortBy('date')" :class="{ 'bg-violet border-t-2 border-violet-full': sortBy === 'date' }" class="col-span-1 text-table-heading font-robotoBold font-semibold p-4     text-left text-gray-500">Дата</div>
                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 text-left text-gray-500">Примечание</div>
                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 text-left font-semibold">Цена хранения</div>
                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 text-left font-semibold">Цена наработки</div>
                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 flex items-center justify-start space-x-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <div class="col-span-1 p-4 flex items-center">

</div>

                        <div v-if="getPriceRowsCount > 0" class="col-span-1 text-left"><select v-model="form.contragent_id">

                                <option v-for="agent in contragents" :key="agent.id" :value="agent.id">{{ agent.name }}
                                </option>
                            </select></div>
                        <div v-if="getPriceRowsCount > 0"  class="col-span-1 text-left text-gray-500 "><input v-model="form.store_date" type="date">
                        </div>
                        <div v-if="getPriceRowsCount > 0" class="col-span-1 text-left text-gray-500"><input v-model="form.notes" type="text"></div>
                        <div v-if="getPriceRowsCount > 0" class="col-span-1 text-left font-semibold"><input v-model="form.store_price" type="text"></div>
                        <div v-if="getPriceRowsCount > 0" class="col-span-1 text-left font-semibold"><input v-model="form.operation_price" type="text"></div>
                        <div v-if="getPriceRowsCount > 0"  class="col-span-1 flex items-center justify-start space-x-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
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


                        </div>


                    <!-- Data Rows -->
                    <template v-for="(price, index) in sortedPrices" :key="index">

                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left">{{ price.contragent.name }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left text-gray-500 p-4" :class="{ 'bg-violet': sortBy === 'date' }">{{ price.store_date }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left text-gray-500">{{ price.notes }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left font-semibold">{{ price.store_price }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left font-semibold">{{ price.operation_price }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 flex items-center justify-start space-x-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12 11.7999L18.2896 6.90802C18.6165 6.65372 19.0877 6.71262 19.342 7.03958C19.5964 7.36654 19.5375 7.83775 19.2105 8.09205L12.4605 13.342C12.1897 13.5527 11.8104 13.5527 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
</svg>
                        </div>
                        <div class="col-span-1 flex items-center">
                            <!-- SVG icon here -->
                        </div>
                    </template>


                    <!-- Repeat similar structure for each row -->
                </div>

                <div class="pt-5 flex justify-end">

                    <button @click="submit"
                        class="text-center bg-my-gray    justify-end  py-3 px-10 bg-gray-300 p">Сохранить</button>

                </div>


                <!-- Add New Row Button -->

            </div>


            <div v-if="getTabActive == 'archive'" class="p-4 overflow-x-auto whitespace-nowrap">
                <div class="grid bg-table-gray grid-cols-[auto,1fr,1fr,1fr,auto,auto,auto]">
                    <!-- Header Row -->
                    <div @click="incRows" class="col-span-1 flex items-center">
                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M14.9444 8.69444C14.9444 8.31091 14.6335 8 14.25 8C13.8665 8 13.5556 8.31091 13.5556 8.69444V13.5556H8.69444C8.31091 13.5556 8 13.8665 8 14.25C8 14.6335 8.31091 14.9444 8.69444 14.9444H13.5556V19.8056C13.5556 20.1891 13.8665 20.5 14.25 20.5C14.6335 20.5 14.9444 20.1891 14.9444 19.8056V14.9444H19.8056C20.1891 14.9444 20.5 14.6335 20.5 14.25C20.5 13.8665 20.1891 13.5556 19.8056 13.5556H14.9444V8.69444Z"
                                fill="#484964" />
                        </svg>

                    </div>

                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 text-left">Заказчик</div>
                    <div @click="toggleSortBy('date')" :class="{ 'bg-violet border-t-2 border-violet-full': sortBy === 'date' }" class="col-span-1 text-table-heading font-robotoBold font-semibold p-4     text-left text-gray-500">Дата</div>
                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 text-left text-gray-500">Примечание</div>
                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 text-left font-semibold">Цена хранения</div>
                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 text-left font-semibold">Цена наработки</div>
                    <div class="col-span-1 text-table-heading font-robotoBold font-semibold p-4 flex items-center justify-start space-x-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <div class="col-span-1 p-4 flex items-center">

</div>


                    <!-- Data Rows -->
                    <template v-for="(price, index) in sortedPricesArchived" :key="index">

                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left">{{ price.contragent.name }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left text-gray-500 p-4" :class="{ 'bg-violet': sortBy === 'date' }">{{ price.store_date }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left text-gray-500">{{ price.notes }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left font-semibold">{{ price.store_price }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 text-left font-semibold">{{ price.operation_price }}</div>
                        <div class="col-span-1 text-table-heading font-robotoBold p-4 flex items-center justify-start space-x-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12 11.7999L18.2896 6.90802C18.6165 6.65372 19.0877 6.71262 19.342 7.03958C19.5964 7.36654 19.5375 7.83775 19.2105 8.09205L12.4605 13.342C12.1897 13.5527 11.8104 13.5527 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
</svg>
                        </div>
                        <div class="col-span-1 flex items-center">
                            <!-- SVG icon here -->
                        </div>
                    </template>


                    <!-- Repeat similar structure for each row -->
                </div>

                <div class="pt-5 flex justify-end">

                    <button @click="submit"
                        class="text-center bg-my-gray    justify-end  py-3 px-10 bg-gray-300 p">Сохранить</button>

                </div>


                <!-- Add New Row Button -->

            </div>
        </div>

    </AuthenticatedLayout>

</template>
