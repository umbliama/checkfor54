<script setup>
import EquipNav from '@/Components/Equip/EquipNav.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import store from '../../../store/index';
import { computed, reactive } from 'vue';
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const menuActive = computed(() => store.getters['equipment/getMenuActiveItem']);

const repairs = computed(() => store.getters['equipment/getEquipmentRepairs'])
const tests = computed(() => store.getters['equipment/getEquipmentTests'])
const seriesActive = computed(() => store.getters['equipment/getSeriesActive'])

const setCategoryId = (categoryId) => {
    store.dispatch('equipment/updateCategory', categoryId)

    updateUrl();
}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    updateUrl();
}

const getLocationName = (id) => {
    const location = props.equipment_location.find(loc => loc.id === id);
    return location ? location.name : 'Location not found';

}
const setSeriesId = (seriesId) => {
    store.dispatch('equipment/updateSeriesId', seriesId)
    updateUrl()
    if (selectedCategory.value && selectedSize.value && seriesActive.value) {
        console.log(selectedSize.value, seriesActive.value)
        updateTestTable(selectedCategory.value, selectedSize.value, seriesActive.value);
    }
}

const updateTestTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentTest', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}
const updateUrl = () => {
    if (selectedCategory.value && selectedSize.value) {
        router.get(route('equip.tests', { category_id: selectedCategory.value, size_id: selectedSize.value, series: seriesActive.value }));
    }
};

const props = defineProps({
    equipmentSeries: Array,
    equipment_location: Array,
    equipment_repairs: Array,
    equipment_tests: Array,
    equipment_categories: Array,
    equipment_categories_counts: Array,
    equipment_sizes_counts: Array,
    equipment_sizes: Array,
})



const form = reactive({
    'test_date': null,
    'location_id': null,
    'expense': null,
    'description': null,
    'category_id': null,
    'size_id': null,
    'series': null
})
function submit() {
    router.post('/equip/tests', {
        test_date: form.test_date,
        location_id: form.location_id,
        expense: form.expense,
        description: form.description,
        category_id: selectedCategory.value,
        size_id: selectedSize.value,
        series: seriesActive.value
    })
    updateTestTable(selectedCategory.value, selectedSize.value, seriesActive.value);
}


</script>

<template>
    <AuthenticatedLayout>
        <EquipNav></EquipNav>

        <div class="container mt-5 md:mx-auto">
            <nav class="m-5 rounded-xl bg-my-gray">
                <div class="max-w-screen-xl py-2 px-4 ">
                    <div class="mt-6 flex items-center">
                        <ul
                            class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-md w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                                @click="setCategoryId(item.id)" v-for="item in equipment_categories" :key="item.id">
                                <Link class="flex items-center justify-around"
                                    :href="route('equip.tests', { category_id: item.id })">
                                {{ item.name }}
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
                                    :href="route('equip.tests', { size_id: item.id })">
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
                <ul class="flex  flex-col lg:flex sm:hidden">
                    <li :class="{ 'bg-my-gray': seriesActive === series }" class="border-b-2  border-gray-200 px-2 py-4"
                        @click="setSeriesId(series)" v-for="series in equipmentSeries">{{ series }}</li>
                </ul>
                <div class="flex w-full flex-col p-4">
                    <h3 class="font-roboto text-side-gray-text  font-semibold text-lg">Добавить новую запись испытания
                    </h3>
                    <div class="sm:overflow-x-auto">
                        <div class="grid grid-cols-5  border border-gray-200 bg-gray-50 flex items-center">
                            <div class="text-gray-600 font-medium px-4 py-2 ">Дата испытания</div>
                            <div class="text-gray-600 font-medium px-4 py-2 ">Место проведения</div>
                            <div class="text-gray-600 font-medium px-4 py-2 ">Описание</div>
                            <div class="text-gray-600 font-medium px-4 py-2 ">Расход</div>
                            <div class="text-gray-600 font-medium px-4 py-2">
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
                        </div>
                        <div class="grid  grid-cols-5 border-t border-gray-200">
                            <!-- Data Row -->
                            <div class="flex items-center border flex items-center">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <!-- SVG Content -->
                                </svg>
                                <input type="date" v-model="form.test_date"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="border flex items-center">
                                <select v-model="form.location_id"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option v-for="location in equipment_location" :value="location.id">{{ location.name
                                        }}</option>
                                </select>
                            </div>
                            <div class="border flex items-center">
                                <textarea v-model="form.description"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    rows="2">Lorem ipsum dolor sit amet...</textarea>
                            </div>
                            <div class="border flex items-center">
                                <input v-model="form.expense"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="280000">
                            </div>
                            <div class="flex border items-center px-4 py-2">
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
                    </div>
                    <div class="flex justify-end mt-2">

                        <button @click="submit"
                            class="text-center bg-my-gray    justify-end  py-3 px-10 bg-gray-300 p">Сохранить</button>

                    </div>

                    <div class="overflow-x-auto mt-5 lg:pb-0 sm:pb-20">
                        <div
                            class="grid grid-cols-5 bg-gray-50 p-2 border-b border-gray-200 font-semibold text-gray-600">
                            <div class="p-2">Дата испытания</div>
                            <div class="p-2">Место проведения</div>
                            <div class="p-2">Описание</div>
                            <div class="p-2">Расход</div>
                            <div class="p-2 flex"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
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
                        </div>
                        <div v-for="test in tests" :key="test.id" class="grid grid-cols-5 border-b border-gray-200 p-2">
                            <div class="p-2">{{ test.test_date }}</div>
                            <div class="p-2">{{ getLocationName(test.location_id) }}</div>
                            <div class="p-2 overflow-hidden text-ellipsis ">{{ test.description }}</div>
                            <div class="p-2">{{ test.expense }}</div>
                            <div class="p-2 flex items-center gap-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                        fill="#21272A" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.65802 7.03955C4.91232 6.71259 5.38353 6.65369 5.71049 6.90799L12 11.7999L18.2896 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5964 7.36651 19.5375 7.83772 19.2105 8.09202L12.4605 13.342C12.1897 13.5527 11.8104 13.5527 11.5396 13.342L4.78958 8.09202C4.46262 7.83772 4.40372 7.36651 4.65802 7.03955Z"
                                        fill="#21272A" />
                                </svg>

                                <div class="flex justify-center items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                            fill="#687182" />
                                        <path
                                            d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                            fill="#687182" />
                                        <path
                                            d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                            fill="#687182" />
                                    </svg>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>

        </div>
    </AuthenticatedLayout>

</template>
