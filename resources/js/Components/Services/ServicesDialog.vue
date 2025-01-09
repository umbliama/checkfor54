<script setup>
import {
    DialogClose,
    DialogContent,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'radix-vue';
import { Link, router } from '@inertiajs/vue3';
import store from '../../../store/index';
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";
import { computed, onMounted, ref, watch } from 'vue';

const model = defineModel();

const statuses = {
    'new': 'Новое',
    'good': 'Хорошее',
    'satisfactory': 'Удовлетворительно',
    'bad': 'Плохое',
    'off': 'Списано',
    'unknown': 'Неизвестный статус'
};
const statuses_colors = {
    'new': 'bg-[#0F62FE]',
    'good': 'bg-[#31C246]',
    'satisfactory': 'bg-[#DAC41E]',
    'bad': 'bg-[#DA1E28]',
    'off': 'bg-[#000000]',
    'unknown': 'bg-[#C0C0C0]'
};

const selectCategory = (category) => {
    store.dispatch('equipment/updateCategory', category);
    fetchEquipmentSizesById(category)
    fetchEquipmentSizes()

};

const fetchEquipmentSizesById = (category) => {
    store.dispatch('services/fetchEquipmentSizesById',category)
}

const fetchEquipmentSizes = () => {
    store.dispatch('services/fetchEquipmentSizesCount')
}

const updateSelectedEquipment = (value) => {
    if (!selectedEquipment.value) {
        // Dispatch action to update selected equipment
        store.dispatch('services/updateSelectedEquipment', value);
    } else {
        // Dispatch action to update sub-equipment array


        store.dispatch('services/updateSubEquipmentArray', value);
        store.dispatch('services/updateSubSelectedEquipmentObjects', {
            subequipment_id: '',
            shipping_date: '',
            period_start_date: '',
            return_date: '',
            period_end_date: '',
            store: '',
            operating: false,
            return_reason: '',
            commentary: '',
        });
    }

    model.value = false;
    selectCategory(null);
    selectSize    (null);
};
const selectSize = (size) => {
    store.dispatch('equipment/updateSize', size);
    if (selectedCategory.value && selectedSize.value) {
        store.dispatch('services/fetchEquipment', {
            category_id:
                selectedCategory.value, size_id: selectedSize.value
        })
    }
};

const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedEquipment = computed(() => store.getters['services/getSelectedEquipment']);
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const equipment_categories = computed(() => store.getters['services/getEquipmentCategories']);
const equipment_categories_counts = computed(() => store.getters['services/getEquipmentCategoriesCounts']);
const equipment_sizes = computed(() => store.getters['services/getEquipmentSizes']);
const equipment_sizes_counts = computed(() => store.getters['services/getEquipmentSizesCounts']);
const equipment = computed(() => store.getters['services/getEquipment']);


const toggleInputLocation = (value) => {
    store.dispatch('equipment/updateInputLocationShown', value);
};

onMounted(() => {
    store.dispatch('services/fetchEquipmentCategories');
    store.dispatch('services/fetchEquipmentCategoriesCount');
});

</script>

<template>
    <DialogRoot v-model:open="model">
        <DialogPortal>
            <transition name="fade">
                <DialogOverlay class="fixed left-0 top-0 w-full h-full bg-black bg-opacity-60 z-[60]"/>
            </transition>
            <transition name="fade">
                <DialogContent
                    class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-h-[85vh] w-[90vw] max-w-[1000px] p-6 rounded bg-white overflow-y-auto z-[100]"
                >
                    <DialogTitle class="flex items-center justify-between font-semibold">
                        Выбрать оборудование

                        <DialogClose class="shrink-0 ml-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16.95 8.464a1 1 0 0 0-1.414-1.414L12 10.586L8.464 7.05A1 1 0 1 0 7.05 8.464L10.586 12L7.05 15.536a1 1 0 1 0 1.414 1.414L12 13.414l3.536 3.536a1 1 0 1 0 1.414-1.414L13.414 12z"/></svg>
                        </DialogClose>
                    </DialogTitle>

                    <nav class="mt-6 py-2.5 px-3 rounded-xl text-sm bg-my-gray">
                        <div class="relative">
                            <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                            <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                                <li
                                    :class="{ '!border-[#001D6C] text-[#001D6C]': selectedCategory === item.id }"
                                    class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                                    @click="selectCategory(item.id)" v-for="item in equipment_categories" :key="item.id"
                                >
                                    <button class="flex items-center justify-between">
                                        {{ item.name }}

                                        <span
                                            v-if="equipment_categories_counts[item.id - 1].count"
                                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text"
                                        >
                                            {{ equipment_categories_counts[item.id - 1].count }}
                                        </span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="relative mt-4">
                            <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                            <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                                <li
                                    :class="{ '!border-[#001D6C] text-[#001D6C]': selectedSize === item.id }"
                                    class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                                    @click="selectSize(item.id)" v-for="item in equipment_sizes" :key="item.id"
                                >
                                    <button class="flex items-center justify-between">
                                        {{ item.name }}
                                        <span
                                            v-if="equipment_sizes_counts[item.id]"
                                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text"
                                        >
                                            {{ equipment_sizes_counts[item.id] }}
                                        </span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </nav>

                    <div
                        v-if="equipment"
                        class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3"
                    >
                        <div class="min-w-[900px] text-xs">
                            <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                <div class="shrink-0 flex items-center w-[16%] py-2.5 px-2">Производитель</div>
                                <div class="shrink-0 flex items-center w-[15%] py-2.5 px-2">Cерия</div>
                                <div class="shrink-0 flex items-center w-[16%] py-2.5 px-2">Дата изготовления</div>
                                <div class="shrink-0 flex items-center w-[12%] py-2.5 px-2">Состояние</div>
                                <div class="shrink-0 flex items-center w-[12%] py-2.5 px-2">Стоимость</div>
                                <div class="shrink-0 flex items-center w-[calc(100%-16%-15%-16%-12%-12%)] py-2.5 px-2">Примечание</div>
                            </div>
                            <div
                                v-for="item in equipment.data"
                                class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 cursor-pointer hover:bg-slate-200"
                                @click="updateSelectedEquipment(item.id)"
                            >
                                <div class="shrink-0 flex items-center w-[16%] py-2.5 px-2">
                                    {{ item.manufactor }}
                                </div>
                                <div class="shrink-0 flex items-center w-[15%] py-2.5 px-2">
                                    {{ item.series }}
                                </div>
                                <div class="shrink-0 flex items-center w-[16%] py-2.5 px-2">
                                    {{ item.manufactor_date }}
                                </div>
                                <div class="shrink-0 flex items-center w-[12%] py-2.5 px-2">
                                    <span :class="statuses_colors[item.status]" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                    <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses[item.status] || '-' }}</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[12%] py-2.5 px-2">Стоимость</div>
                                <div class="shrink-0 flex items-center w-[calc(100%-16%-15%-16%-12%-12%)] py-2.5 px-2">Примечание</div>
                            </div>
                        </div>
                    </div>
                </DialogContent>
            </transition>
        </DialogPortal>
    </DialogRoot>
</template>
