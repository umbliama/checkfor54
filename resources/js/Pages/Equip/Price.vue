<script setup>
import EquipNav from '@/Components/EquipNav.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import store from '../../../store';
import { computed } from 'vue';
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const menuActive = computed(() => store.getters['equipment/getMenuActiveItem']);
const equipment_categories = computed(() => store.getters['equipment/getEquipmentCategories'])
const equipment_sizes_counts = computed(() => store.getters['equipment/getEquipmentSizesCounts'])
const equipment_categories_counts = computed(() => store.getters['equipment/getEquipmentSizesCounts'])
const equipment_sizes = computed(() => store.getters['equipment/getEquipmentSizes'])
const selectCategory = (category) => {
    store.dispatch('equipment/updateCategory', category);
};



</script>


<template>
    <AuthenticatedLayout>
        <EquipNav></EquipNav>
        <nav class="bg-my-gray rounded-xl">
            <div class="max-w-screen-xl py-2 px-4 ">
                <div class="mt-6 flex items-center sm:overflow-x-auto">
                    <ul
                        class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                        <li class="text-my-nav-text text-md w-full"
                            :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                            @click="selectCategory(item.id)" v-for="item in equipment_categories" :key="item.id">
                            <Link class="flex justify-around"
                                :href="route('equip.report')">
                            {{ item.name }}
                            <span class="ml-1 rounded-xl text-sm flex items-center px-3 py-1 text-white bg-gray-400 ">
                                {{ equipment_categories_counts[item.id] }}
                            </span>
                            </Link>
                        </li>
                    </ul>
                </div>
                <div class="mt-6 flex items-center sm:overflow-x-auto" >
                    <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                        <li class="text-my-nav-text text-lg w-full"
                            :class="{ 'border-b-2 border-blue-600': selectedSize === item.id }"
                            @click="selectSize(item.id)" v-for="item in equipment_sizes" :key="item.id">
                            <Link class="flex justify-around"
                                :href="route('equip.report', { size_id: item.id, category_id: selectedCategory })">
                            {{ item.name }}
                            <span class="ml-1 rounded-xl text-sm flex items-center px-3 py-1 text-white bg-gray-400 ">
                                {{ equipment_sizes_counts[item.id] }}
                            </span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </AuthenticatedLayout>
    
</template>