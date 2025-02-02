<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideMenu from '@/Layouts/SideMenu.vue';
import Equipment from '@/Pages/Equip/Index.vue'
import { usePage } from '@inertiajs/vue3';
import store from '../../../store';
import { computed, onMounted, ref, toRaw, watch } from 'vue';
import DashboardToolbar from '@/Components/Dashboard/DashboardToolbar.vue';


const page = usePage()

const user = computed(() => page.props.auth.user)

const checkNewNotifications = () => {
    store.dispatch('notifications/checkNewNotifications')
}
const updateActive = (tab) => {
    store.dispatch('dashboard/updateActiveTab', tab);
}

const updateUserId = () => {
    store.dispatch('updateUserId', toRaw(user.value.id))
}



const props = defineProps({
    equipment:Object,
    equipment_categories:Array,
    equipment_categories_counts:Number,
    equipment_sizes_counts:Number,
    equipment_sizes:Array,
    equipment_location:Array,
    selectedCategory:String,
    location_counts:Object,
    equipment_on_rent_count:Number,
    activeEquipment:Object,
    contragents_count:Number
})

onMounted(() => {
    updateUserId()
    checkNewNotifications(toRaw(user.value.id))
})

</script>

<template>

    <AuthenticatedLayout>
        <DashboardToolbar dashboard-page-type="rent" />

        <div class="bg-my-gray grid p-4 gap-4 lg:grid-cols-6">
            <div class="flex py-2 ">
                <div class="bg-white py-2 px-3 sm:w-full">
                    <span class="text-my-gray-300 truncate">Всего заказчиков</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">104</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white py-2 px-3 sm:w-full">
                    <span class="text-my-gray-300 truncate">Новых заказчиков</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">16</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white py-2 px-3 sm:w-full">
                    <span class="text-my-gray-300 truncate">Активных заказчиков</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">104</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white py-2 px-3 sm:w-full">
                    <span class="text-my-gray-300 truncate">Всего заказчиков</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">104</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white py-2 px-3 sm:w-full">
                    <span class="text-my-gray-300">В аренде</span>
                    <div class="flex justify-between">
                        <h4 class="text-2xl font-bold">104</h4>
                        <span class="bg-my-gray px-5 items-center justify-center my-auto rounded-2xl">-2 </span>
                    </div>
                </div>
            </div>
            <div class="flex py-2 ">
                <div class="bg-white py-2 px-3 sm:w-full">
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
