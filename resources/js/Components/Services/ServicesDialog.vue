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
import { computed, onMounted, ref, toRaw, watch } from 'vue';


const model = defineModel();

const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedEquipment = computed(() => store.getters['services/getSelectedEquipment']);
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const equipment_categories = computed(() => store.getters['services/getEquipmentCategories']);
const equipment_categories_counts = computed(() => store.getters['services/getEquipmentCategoriesCounts']);
const equipment_sizes = computed(() => store.getters['services/getEquipmentSizes']);
const equipment_sizes_counts = computed(() => store.getters['services/getEquipmentSizesCounts']);
const equipment = computed(() => store.getters['services/getEquipment']);
const equipmentType = computed(() => store.getters['services/getEquipmentType']);

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

const updateSubSelectedEquipment = (equipment_id,subEquipmentItem) => {
    store.dispatch('updateSubSelectedEquipment', {equipment_id,subEquipmentItem})
}


const updateSelectedEquipment = (value) => {
    if ( equipmentType.value === 0) {
        store.dispatch('services/updateSelectedEquipment', value);
    } else {
        store.dispatch('services/updateSubEquipmentArray', value);
        store.dispatch('services/updateSubSelectedEquipment', {
        equipment_id: toRaw(selectedEquipment.value[selectedEquipment.value.length - 1]),  
        subEquipmentItem: {
            subequipment_id: '',
            shipping_date: '',
            period_start_date: '',
            return_date: '',
            period_end_date: '',
            store: '',
            operating: false,
            return_reason: '',
            commentary: '',
    }
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



watch(model, () => {
    store.dispatch('equipment/updateCategory', null);
});

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
                    class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-h-[85vh] w-[90vw] max-w-[1300px] p-6 rounded bg-white overflow-y-auto z-[100]"
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

                    <template v-if="equipment">
                        <div v-if="selectedCategory == 1"
                             class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1144px] text-xs">
                                <div
                                    class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Производитель
                                    </div>
                                    <div class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer">
                                        Серия
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[5.15%] py-2.5 px-2">Заход-
                                        <br>ность
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[4.89%] py-2.5 px-2">Длина</div>
                                    <div class="shrink-0 flex items-center justify-center w-[5.15%] py-2.5 px-2">Длина <br>дс.
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">Статор
                                        <br>Ротор
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.11%] py-2.5 px-2">Нара- <br>ботка
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center text-center w-[7.08%] py-2.5 px-2">
                                        Наработка
                                        <br>дс.
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center text-center w-[8.56%] py-2.5 px-2">
                                        Дата
                                        <br>изготовления
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[8.04%] py-2.5 px-2">Стоимость</div>
                                    <div class="shrink-0 flex items-center justify-center w-[calc(8.04%+50px)] py-2.5 px-2">Примечание
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Состояние</div>
                                    <div class="shrink-0 flex items-center w-[50px] py-2.5 px-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                                  fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                                  fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                                  fill="#21272A"/>
                                            <path
                                                d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                                fill="#21272A"/>
                                        </svg>
                                    </div>
                                </div>
                                <template v-for="item in equipment.data">
                                    <div
                                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all cursor-pointer hover:bg-slate-200"
                                        @click="updateSelectedEquipment(item.id)"
                                    >
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                            <div class="mr-2">
                                                <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink"
                                                             endpoint="/equipment"/>
                                            </div>
                                            <span class="line-clamp-2">{{ item.manufactor }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series ?? '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[5.15%] py-2.5 px-2">{{
                                                item.zahodnost ?? '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[4.89%] py-2.5 px-2">{{ item.length ?? '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[5.15%] py-2.5 px-2">{{ item.dlina_ds ?? '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[7.08%] py-2.5 px-2">{{
                                                item.stator_rotor ??
                                                '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[6.11%] py-2.5 px-2">{{
                                                item.operating ?? '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[7.08%] py-2.5 px-2">{{
                                                item.narabotka_ds ??
                                                '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[8.56%] py-2.5 px-2">{{
                                                item.manufactor_date ??
                                                '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[8.04%] py-2.5 px-2">{{ item.price ?? '-' }} ₽
                                        </div>
                                        <div class="shrink-0 flex items-center w-[calc(8.04%+50px)] py-2.5 px-2">
                                            <span class="line-clamp-2">{{ item.notes ?? '-' }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                            <span :class="statuses_colors[item.status]"
                                                  class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                            <span class="text-nowrap text-ellipsis overflow-hidden">{{
                                                    statuses[item.status]
                                                }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[50px] py-2.5 px-2">
                                            <Link :href="route('directory.index', { type: 'equipment', id: item.id })"
                                                  class="mr-3.5">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                          fill="#21272A"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                          fill="#21272A"/>
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div v-else-if="selectedCategory == 2"
                             class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1144px] text-xs">
                                <div
                                    class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Производитель
                                    </div>
                                    <div class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer">
                                        Серия
                                    </div>
                                    <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Длина</div>
                                    <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Наработка</div>
                                    <div class="shrink-0 flex items-center justify-center text-center w-[10.48%] py-2.5 px-2">
                                        Дата
                                        <br>изготовления
                                    </div>
                                    <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Стоимость</div>
                                    <div
                                        class="shrink-0 flex items-center w-[calc(100%-9.96%-9.96%-10.48%-10.48%-10.48%-10.48%-10.48%-50px)] py-2.5 px-2">
                                        Примечание
                                    </div>
                                    <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Состояние</div>
                                    <div class="shrink-0 flex items-center w-[50px] py-2.5 px-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                                  fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                                  fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                                  fill="#21272A"/>
                                            <path
                                                d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                                fill="#21272A"/>
                                        </svg>
                                    </div>
                                </div>
                                <template v-for="item in equipment.data">
                                    <div
                                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all cursor-pointer hover:bg-slate-200"
                                        @click="updateSelectedEquipment(item.id)"
                                    >
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                            <div class="mr-2">
                                                <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink"
                                                             endpoint="/equipment"/>
                                            </div>
                                            <span class="line-clamp-2">{{ item.manufactor ?? '-' }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series ?? '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{ item.length ?? '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{
                                                item.operating ?? '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{
                                                item.manufactor_date
                                                ?? '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{ item.price ?? '-' }} ₽
                                        </div>
                                        <div
                                            class="shrink-0 flex items-center w-[calc(100%-9.96%-9.96%-10.48%-10.48%-10.48%-10.48%-10.48%-50px)] py-2.5 px-2">
                                            <span class="line-clamp-2">{{ item.notes ?? '-' }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">
                                            <span :class="statuses_colors[item.status]"
                                                  class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                            <span class="text-nowrap text-ellipsis overflow-hidden">{{
                                                    statuses[item.status]
                                                }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[50px] py-2.5 px-2">
                                            <Link :href="route('directory.index', { type: 'equipment', id: item.id })"
                                                  class="mr-3.5">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                          fill="#21272A"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                          fill="#21272A"/>
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div v-else class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1144px] text-xs">
                                <div
                                    class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Производитель
                                    </div>
                                    <div class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer">
                                        Серия
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.2%] py-2.5 px-2">Диаметр</div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.2%] py-2.5 px-2">Длина</div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.73%] py-2.5 px-2">Длина с
                                        <br>резьбой
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">Резьбы</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">Наработка</div>
                                    <div class="shrink-0 flex items-center justify-center text-center w-[8.56%] py-2.5 px-2">
                                        Дата
                                        <br>изготовления
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.6%] py-2.5 px-2">Стоимость</div>
                                    <div class="shrink-0 flex items-center justify-center w-[calc(9.96%+50px)] py-2.5 px-2">Примечание
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Состояние</div>
                                    <div class="shrink-0 flex items-center w-[50px] py-2.5 px-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                                  fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                                  fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                                  fill="#21272A"/>
                                            <path
                                                d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                                fill="#21272A"/>
                                        </svg>
                                    </div>
                                </div>
                                <template v-for="item in equipment.data">
                                    <div
                                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all cursor-pointer hover:bg-slate-200"
                                        @click="updateSelectedEquipment(item.id)"
                                    >
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                            <div class="mr-2">
                                                <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink"
                                                             endpoint="/equipment"/>
                                            </div>
                                            <span class="line-clamp-2">{{ item.manufactor ?? '-' }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series ?? '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[6.2%] py-2.5 px-2">{{
                                                item.diameter ?? '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[6.2%] py-2.5 px-2">{{
                                                item.length ?? '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[6.73%] py-2.5 px-2">{{
                                                item.length_rezba ?? '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">{{
                                                item.rezbi ?? '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">{{
                                                item.operating ?? '-'
                                            }}
                                        </div>
                                        <div
                                            class="shrink-0 flex items-center justify-center text-center w-[8.56%] py-2.5 px-2">
                                            {{
                                                item.manufactor_date ?? '-'
                                            }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[9.6%] py-2.5 px-2">{{ item.price ?? '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[calc(9.96%+50px)] py-2.5 px-2">
                                            <span class="line-clamp-2">{{ item.notes ?? '-' }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                            <span :class="statuses_colors[item.status]"
                                                  class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                            <span class="text-nowrap text-ellipsis overflow-hidden">{{
                                                    statuses[item.status]
                                                }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[50px] py-2.5 px-2">
                                            <Link :href="route('directory.index', { type: 'equipment', id: item.id })"
                                                  class="mr-3.5">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                          fill="#21272A"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                          fill="#21272A"/>
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

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
