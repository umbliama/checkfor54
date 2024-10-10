<script setup>
import EquipNav from '@/Components/EquipNav.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import store from '../../../store/index';
import { computed, reactive } from 'vue';
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const menuActive = computed(() => store.getters['equipment/getMenuActiveItem']);
const equipment_categories = computed(() => store.getters['equipment/getEquipmentCategories'])
const equipment_sizes_counts = computed(() => store.getters['equipment/getEquipmentSizesCounts'])
const equipment_categories_counts = computed(() => store.getters['equipment/getEquipmentSizesCounts'])
const equipment_sizes = computed(() => store.getters['equipment/getEquipmentSizes'])
const repairs = computed(() => store.getters['equipment/getEquipmentRepairs'])
const seriesActive = computed(() => store.getters['equipment/getSeriesActive'])

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

defineProps({
    equipmentSeries: Array,
    equipment_location: Array,
    equipment_repairs: Array
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

        <div class="container mt-5 md:mx-auto sm:mx-auto">
            <nav class="bg-my-gray rounded-xl">
                <div class="max-w-screen-xl py-2 px-4 ">
                    <div class="mt-6 flex items-center">
                        <ul
                            class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-md w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                                @click="setCategoryId(item.id)" v-for="item in equipment_categories" :key="item.id">
                                <Link class="flex items-center justify-around"
                                    :href="route('equip.repair', { category_id: item.id })">
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
                                <Link class="flex items-center justify-around" :href="route('equip.repair', { size_id: item.id })">
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

            <div class="flex">
                <ul class="flex flex-col lg:flex  sm:hidden">
                    <li :class="{ 'bg-my-gray': seriesActive === series }" class="border-b-2  border-gray-200 px-2 py-4"
                        @click="setSeriesId(series)" v-for="series in equipmentSeries">{{ series }}</li>
                </ul>
                <div class="flex w-full flex-col p-4">
                    <h3>Добавить новую запись ремонта</h3>
                    <div class="sm:overflow-x-auto">
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
                                <tr>
                                    <td class="border px-4 py-5 flex items-center">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
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
                                            class="w-full  border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </td>
                                    <td class="border px-4 py-2">
                                        <select v-model="form.location_id"
                                            class="w-full  border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option v-for="location in equipment_location" :value="location.id">{{
                                                location.name }}</option>
                                        </select>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <textarea v-model="form.description"
                                            class="w-full  border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            rows="2">Lorem ipsum dolor sit amet...</textarea>
                                    </td>
                                    <td class="border py-2">
                                        <input v-model="form.expense"
                                            class="w-full  border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-red-500"
                                            value="280000">
                                    </td>
                                    <td class="px-4 py-2 flex">
                                        <div class="flex justify-center items-center">
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
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="p-4">

                <button @click="submit"
                    class="text-center bg-my-gray    justify-end mt-10 py-3 px-10 bg-gray-300 p">Сохранить</button>

            </div>
            <div class="sm:overflow-x-auto sm:pb-32 ">
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

        </div>
    </AuthenticatedLayout>

</template>