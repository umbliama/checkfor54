<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideMenu from '@/Layouts/SideMenu.vue';
import Equipment from '@/Pages/Equip/Index.vue'
import { usePage } from '@inertiajs/vue3';
import store from '../../store';
import { ref, watch } from 'vue';

const updateActive = (tab) => {
    store.dispatch('dashboard/updateActiveTab', tab);
}

const props = defineProps({
    categories: Array
})


</script>

<template>

    <AuthenticatedLayout>
        <template #header>

            <nav class="bg-white-50 bg-my-gray sm:overflow-x-auto">
                <div class=" sm:overflow-x-auto flex items-center px-4 py-3 mx-auto">
                    <div class="flex items-center sm:overflow-x-auto ">
                        <ul
                            class="flex flex-row font-medium  mt-0 space-x-8 whitespace-nowrap rtl:space-x-reverse text-sm">
                            <li @click="updateActive('rent')">
                                <a href="#" class="text-my-nav-text text-lg sm:text-md hover:underline"
                                    aria-current="page">В
                                    аренде</a>
                            </li>
                            <li>
                                <a href="#" class="text-my-nav-text text-lg sm:text-md hover:underline">Свободно</a>
                            </li>
                            <li>
                                <a href="#" class="text-my-nav-text text-lg sm:text-md hover:underline">На сервисе</a>
                            </li>
                            <li>
                                <a href="#" class="text-my-nav-text text-lg sm:text-md hover:underline">Анализ</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>



        </template>
        <div class="bg-my-gray">
            <div class="flex py-2 ">
                <div class="bg-white p-5 sm:w-full">
                    <span class="text-my-gray-300">Всего заказчиков</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">104</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white p-5 sm:w-full">
                    <span class="text-my-gray-300">Новых заказчиков</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">16</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white p-5 sm:w-full">
                    <span class="text-my-gray-300">Активных заказчиков</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">104</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white p-5 sm:w-full">
                    <span class="text-my-gray-300">Всего заказчиков</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">104</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white p-5 sm:w-full">
                    <span class="text-my-gray-300">В аренде</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">104</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white p-5 sm:w-full">
                    <span class="text-my-gray-300">На складе</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">104</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="p-6">


                <!-- Two main sections: Equipment Status and Progress -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Left column -->
                    <div class="bg-white rounded shadow-sm p-2 ">
                        <!-- Equipment status (e.g. VSD, YSS, etc.) -->
                        <div class="flex justify-between items-center mb-4 sm:overflow-x-auto">
                            <div v-for="item in categories" :key="item.id" class="p-4 bg-white rounded shadow-sm">
                                <div class="text-sm text-gray-500">{{ item.name }}</div>
                            </div>
                            <div>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <div class="text-4xl font-bold">{{ equipmentInUse }}%</div>
                            <p>In Use</p>
                        </div>

                        <ul class="space-y-2">
                            <li v-for="item in equipmentList" :key="item.id" class="flex justify-between">
                                <span>{{ item.name }}</span>
                                <span>{{ item.status }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Right column (Pie chart and progress indicators) -->
                    <div class="bg-white rounded shadow-sm p-6">
                        <h3 class="font-semibold mb-4">Rental Status</h3>
                        <!-- Placeholder for pie chart (you can integrate with a chart library) -->
                        <div class="flex justify-center items-center h-32">
                            <div class="bg-gray-200 rounded-full w-24 h-24"></div>
                        </div>
                    </div>
                </div>

                <!-- Revenue section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Revenue percentage list -->
                    <div class="bg-white rounded shadow-sm p-6">
                        <h3 class="font-semibold mb-4">Revenue Percentage</h3>
                        <ul class="space-y-2">
                            <li v-for="revenue in revenues" :key="revenue.company" class="flex justify-between">
                                <span>{{ revenue.company }}</span>
                                <span>{{ revenue.percent }}% (₽{{ revenue.amount }})</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Revenue percentage pie chart -->
                    <div class="bg-white rounded shadow-sm p-6">
                        <h3 class="font-semibold mb-4">Revenue Breakdown</h3>
                        <div class="flex justify-center items-center h-32">
                            <div class="bg-gray-200 rounded-full w-24 h-24"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
