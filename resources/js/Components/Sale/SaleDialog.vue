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
const selectedEquipment = computed(() => store.getters['sale/getSelectedEquipment']);
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const equipment_categories = computed(() => store.getters['services/getEquipmentCategories']);
const equipment_categories_counts = computed(() => store.getters['services/getEquipmentCategoriesCounts']);
const equipment_sizes = computed(() => store.getters['services/getEquipmentSizes']);
const equipment_sizes_counts = computed(() => store.getters['services/getEquipmentSizesCounts']);
const activeMainEquipmentId = computed(() => store.getters['sale/getActiveMainEquipment']);
const activeSubEquipmentId = computed(() => store.getters['sale/getActiveSubEquipment']);
const equipment = computed(() => store.getters['services/getEquipment']);
const addingMainEquipment = computed(() => store.getters['sale/getAddingMainEquipment']);
const getEditorMode = computed(() => store.getters['sale/getEditorMode']);

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
    store.dispatch('services/fetchEquipmentSizesById', category)
}

const fetchEquipmentSizes = () => {
    store.dispatch('services/fetchEquipmentSizesCount')
}

const updateSelectedEquipment = (used, value) => {

    if (used) {
        return;
    } else {
        if (!getEditorMode.value) {
            if (addingMainEquipment.value) {
                store.dispatch('sale/updateActiveMainEquipment', value)
                store.dispatch('sale/updateActiveSubEquipment', null);
            } else {
                store.dispatch('sale/updateActiveSubEquipment', value);
            }
        } else {
            if (addingMainEquipment.value) {
                store.dispatch('sale/updateActiveMainEquipment', value)
                store.dispatch('sale/updateActiveSubEquipment', null);
                console.log('11')

            } else {
                store.dispatch('sale/updateActiveSubEquipment', value);

            }
        }

        model.value = false;
        selectCategory(null);
        selectSize(null);

    }
};


watch(activeMainEquipmentId, async (newValue) => {
    const equipment = selectedEquipment.value.find(eq => eq.id === newValue)
    if (equipment) {
        return
    } else {
        if (newValue) {
            try {
                const response = await fetch(`/api/equipment/${newValue}`);
                const data = await response.json();

                store.dispatch('sale/addMainEquipmentAction', data);

            } catch (error) {
                console.error("Ошибка загрузки оборудования:", error);
            }
        }
    }

}, { deep: true });


watch(activeSubEquipmentId, async (newValue) => {
    console.log("watch activeSubEquipmentId сработал, новое значение:", newValue);
    if (newValue) {
        try {
            const response = await fetch(`/api/equipment/${newValue}`);
            const data = await response.json();
            console.log("Загруженные данные дополнительного оборудования:", data);

            store.dispatch('sale/addExtraEquipmentAction', {
                mainEquipmentId: activeMainEquipmentId.value,
                subEquipment: data
            });

        } catch (error) {
            console.error("Ошибка загрузки оборудования:", error);
        }
    }
}, { deep: true });





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

watch(model, (newValue) => {
    if (newValue) {
        store.dispatch('services/fetchEquipmentCategories');
        store.dispatch('services/fetchEquipmentCategoriesCount');
    }
});
</script>

<template>
    <DialogRoot v-model:open="model">
        <DialogPortal>
            <transition name="fade">
                <DialogOverlay class="fixed left-0 top-0 w-full h-full bg-black bg-opacity-60 z-[60]" />
            </transition>
            <transition name="fade">
                <DialogContent
                    class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-h-[85vh] w-[90vw] max-w-[1300px] p-6 rounded bg-white overflow-y-auto z-[100]">
                    <DialogTitle class="flex items-center justify-between font-semibold">
                        Выбрать оборудование

                        <DialogClose class="shrink-0 ml-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M16.95 8.464a1 1 0 0 0-1.414-1.414L12 10.586L8.464 7.05A1 1 0 1 0 7.05 8.464L10.586 12L7.05 15.536a1 1 0 1 0 1.414 1.414L12 13.414l3.536 3.536a1 1 0 1 0 1.414-1.414L13.414 12z" />
                            </svg>
                        </DialogClose>
                    </DialogTitle>

                    <nav class="mt-6 py-2.5 px-3 rounded-xl text-sm bg-my-gray">
                        <div class="relative">
                            <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                            <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                                <li :class="{ '!border-[#001D6C] text-[#001D6C]': selectedCategory === item.id }"
                                    class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                                    @click="selectCategory(item.id)" v-for="item in equipment_categories"
                                    :key="item.id">
                                    <button class="flex items-center justify-between">
                                        {{ item.name }}

                                        <span v-if="equipment_categories_counts[item.id - 1].count"
                                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                                            {{ equipment_categories_counts[item.id - 1].count }}
                                        </span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="relative mt-4">
                            <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                            <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                                <li :class="{ '!border-[#001D6C] text-[#001D6C]': selectedSize === item.id }"
                                    class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                                    @click="selectSize(item.id)" v-for="item in equipment_sizes" :key="item.id">
                                    <button class="flex items-center justify-between">
                                        {{ item.name }}
                                        <span v-if="equipment_sizes_counts[item.id]"
                                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
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
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">
                                        Производитель
                                    </div>
                                    <div
                                        class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer">
                                        Серия
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[5.15%] py-2.5 px-2">Заход-
                                        <br>ность
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[4.89%] py-2.5 px-2">Длина
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[5.15%] py-2.5 px-2">Длина
                                        <br>дс.
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">Статор
                                        <br>Ротор
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.11%] py-2.5 px-2">Нара-
                                        <br>ботка
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center justify-center text-center w-[7.08%] py-2.5 px-2">
                                        Наработка
                                        <br>дс.
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center justify-center text-center w-[8.56%] py-2.5 px-2">
                                        Дата
                                        <br>изготовления
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[8.04%] py-2.5 px-2">
                                        Стоимость</div>
                                    <div
                                        class="shrink-0 flex items-center justify-center w-[calc(8.04%+50px)] py-2.5 px-2">
                                        Примечание
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">
                                        Состояние</div>
                                    <div class="shrink-0 flex items-center w-[50px] py-2.5 px-2">
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
                                <template v-for="item in equipment.data">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all cursor-pointer hover:bg-slate-200"
                                        @click="updateSelectedEquipment(item.used, item.id)">
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                            <div class="mr-2">
                                                <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink"
                                                    endpoint="/equipment" />
                                            </div>
                                            <span class="line-clamp-2">{{ item.manufactor }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series ??
                                            '-' }}
                                            <svg v-if="item.used" class="shrink-0 block ml-2" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M4.66671 9.33334V13.3333H11.3334V9.33334H4.66671ZM11.3334 8.00001C11.687 8.00001 12.0261 8.14049 12.2762 8.39053C12.5262 8.64058 12.6667 8.97972 12.6667 9.33334V13.3333C12.6667 13.687 12.5262 14.0261 12.2762 14.2762C12.0261 14.5262 11.687 14.6667 11.3334 14.6667H4.66671C4.31309 14.6667 3.97395 14.5262 3.7239 14.2762C3.47385 14.0261 3.33337 13.687 3.33337 13.3333V9.33334C3.33337 8.97972 3.47385 8.64058 3.7239 8.39053C3.97395 8.14049 4.31309 8.00001 4.66671 8.00001V4.66668C4.66671 3.78262 5.0179 2.93478 5.64302 2.30965C6.26814 1.68453 7.11599 1.33334 8.00004 1.33334C8.8841 1.33334 9.73194 1.68453 10.3571 2.30965C10.9822 2.93478 11.3334 3.78262 11.3334 4.66668V8.00001ZM10 8.00001V4.66668C10 4.40403 9.94831 4.14396 9.8478 3.90131C9.74729 3.65866 9.59997 3.43818 9.41425 3.25246C9.22854 3.06675 9.00806 2.91943 8.76541 2.81892C8.52276 2.71841 8.26268 2.66668 8.00004 2.66668C7.7374 2.66668 7.47732 2.71841 7.23467 2.81892C6.99202 2.91943 6.77154 3.06675 6.58583 3.25246C6.40011 3.43818 6.25279 3.65866 6.15228 3.90131C6.05177 4.14396 6.00004 4.40403 6.00004 4.66668V8.00001H10ZM8.00004 12.6667C7.64642 12.6667 7.30728 12.5262 7.05723 12.2762C6.80718 12.0261 6.66671 11.687 6.66671 11.3333C6.66671 10.9797 6.80718 10.6406 7.05723 10.3905C7.30728 10.1405 7.64642 10 8.00004 10C8.35366 10 8.6928 10.1405 8.94285 10.3905C9.1929 10.6406 9.33337 10.9797 9.33337 11.3333C9.33337 11.687 9.1929 12.0261 8.94285 12.2762C8.6928 12.5262 8.35366 12.6667 8.00004 12.6667Z"
                                                    fill="#484964" />
                                            </svg>
                                        </div>
                                        <!-- <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.used ??
                                            '-' }}
                                        </div> -->
                                        <div class="shrink-0 flex items-center w-[5.15%] py-2.5 px-2">{{
                                            item.zahodnost ?? '-'
                                        }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[4.89%] py-2.5 px-2">{{ item.length ??
                                            '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center w-[5.15%] py-2.5 px-2">{{ item.dlina_ds
                                            ?? '-' }}
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
                                        <div class="shrink-0 flex items-center w-[8.04%] py-2.5 px-2">{{ item.price ??
                                            '-' }} ₽
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
                                                    fill="#21272A" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                    fill="#21272A" />
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
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">
                                        Производитель
                                    </div>
                                    <div
                                        class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer">
                                        Серия
                                    </div>
                                    <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Длина</div>
                                    <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Наработка</div>
                                    <div
                                        class="shrink-0 flex items-center justify-center text-center w-[10.48%] py-2.5 px-2">
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
                                <template v-for="item in equipment.data">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all cursor-pointer hover:bg-slate-200"
                                        @click="updateSelectedEquipment(item.id)">
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                            <div class="mr-2">
                                                <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink"
                                                    endpoint="/equipment" />
                                            </div>
                                            <span class="line-clamp-2">{{ item.manufactor ?? '-' }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series ??
                                            '-' }}
                                            <svg v-if="item.used" class="shrink-0 block ml-2" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M4.66671 9.33334V13.3333H11.3334V9.33334H4.66671ZM11.3334 8.00001C11.687 8.00001 12.0261 8.14049 12.2762 8.39053C12.5262 8.64058 12.6667 8.97972 12.6667 9.33334V13.3333C12.6667 13.687 12.5262 14.0261 12.2762 14.2762C12.0261 14.5262 11.687 14.6667 11.3334 14.6667H4.66671C4.31309 14.6667 3.97395 14.5262 3.7239 14.2762C3.47385 14.0261 3.33337 13.687 3.33337 13.3333V9.33334C3.33337 8.97972 3.47385 8.64058 3.7239 8.39053C3.97395 8.14049 4.31309 8.00001 4.66671 8.00001V4.66668C4.66671 3.78262 5.0179 2.93478 5.64302 2.30965C6.26814 1.68453 7.11599 1.33334 8.00004 1.33334C8.8841 1.33334 9.73194 1.68453 10.3571 2.30965C10.9822 2.93478 11.3334 3.78262 11.3334 4.66668V8.00001ZM10 8.00001V4.66668C10 4.40403 9.94831 4.14396 9.8478 3.90131C9.74729 3.65866 9.59997 3.43818 9.41425 3.25246C9.22854 3.06675 9.00806 2.91943 8.76541 2.81892C8.52276 2.71841 8.26268 2.66668 8.00004 2.66668C7.7374 2.66668 7.47732 2.71841 7.23467 2.81892C6.99202 2.91943 6.77154 3.06675 6.58583 3.25246C6.40011 3.43818 6.25279 3.65866 6.15228 3.90131C6.05177 4.14396 6.00004 4.40403 6.00004 4.66668V8.00001H10ZM8.00004 12.6667C7.64642 12.6667 7.30728 12.5262 7.05723 12.2762C6.80718 12.0261 6.66671 11.687 6.66671 11.3333C6.66671 10.9797 6.80718 10.6406 7.05723 10.3905C7.30728 10.1405 7.64642 10 8.00004 10C8.35366 10 8.6928 10.1405 8.94285 10.3905C9.1929 10.6406 9.33337 10.9797 9.33337 11.3333C9.33337 11.687 9.1929 12.0261 8.94285 12.2762C8.6928 12.5262 8.35366 12.6667 8.00004 12.6667Z"
                                                    fill="#484964" />
                                            </svg>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{ item.length ??
                                            '-' }}
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
                                        <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{ item.price ??
                                            '-' }} ₽
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
                                                    fill="#21272A" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                    fill="#21272A" />
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
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">
                                        Производитель
                                    </div>
                                    <div
                                        class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer">
                                        Серия
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.2%] py-2.5 px-2">Диаметр
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.2%] py-2.5 px-2">Длина
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.73%] py-2.5 px-2">Длина с
                                        <br>резьбой
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">Резьбы
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">
                                        Наработка</div>
                                    <div
                                        class="shrink-0 flex items-center justify-center text-center w-[8.56%] py-2.5 px-2">
                                        Дата
                                        <br>изготовления
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.6%] py-2.5 px-2">
                                        Стоимость</div>
                                    <div
                                        class="shrink-0 flex items-center justify-center w-[calc(9.96%+50px)] py-2.5 px-2">
                                        Примечание
                                    </div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">
                                        Состояние</div>
                                    <div class="shrink-0 flex items-center w-[50px] py-2.5 px-2">
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
                                <template v-for="item in equipment.data">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all cursor-pointer hover:bg-slate-200"
                                        @click="updateSelectedEquipment(item.used, item.id)">
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                            <div class="mr-2">
                                                <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink"
                                                    endpoint="/equipment" />
                                            </div>
                                            <span class="line-clamp-2">{{ item.manufactor ?? '-' }}</span>
                                        </div>
                                        <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series ??
                                            '-' }}
                                            <svg v-if="item.used" class="shrink-0 block ml-2" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M4.66671 9.33334V13.3333H11.3334V9.33334H4.66671ZM11.3334 8.00001C11.687 8.00001 12.0261 8.14049 12.2762 8.39053C12.5262 8.64058 12.6667 8.97972 12.6667 9.33334V13.3333C12.6667 13.687 12.5262 14.0261 12.2762 14.2762C12.0261 14.5262 11.687 14.6667 11.3334 14.6667H4.66671C4.31309 14.6667 3.97395 14.5262 3.7239 14.2762C3.47385 14.0261 3.33337 13.687 3.33337 13.3333V9.33334C3.33337 8.97972 3.47385 8.64058 3.7239 8.39053C3.97395 8.14049 4.31309 8.00001 4.66671 8.00001V4.66668C4.66671 3.78262 5.0179 2.93478 5.64302 2.30965C6.26814 1.68453 7.11599 1.33334 8.00004 1.33334C8.8841 1.33334 9.73194 1.68453 10.3571 2.30965C10.9822 2.93478 11.3334 3.78262 11.3334 4.66668V8.00001ZM10 8.00001V4.66668C10 4.40403 9.94831 4.14396 9.8478 3.90131C9.74729 3.65866 9.59997 3.43818 9.41425 3.25246C9.22854 3.06675 9.00806 2.91943 8.76541 2.81892C8.52276 2.71841 8.26268 2.66668 8.00004 2.66668C7.7374 2.66668 7.47732 2.71841 7.23467 2.81892C6.99202 2.91943 6.77154 3.06675 6.58583 3.25246C6.40011 3.43818 6.25279 3.65866 6.15228 3.90131C6.05177 4.14396 6.00004 4.40403 6.00004 4.66668V8.00001H10ZM8.00004 12.6667C7.64642 12.6667 7.30728 12.5262 7.05723 12.2762C6.80718 12.0261 6.66671 11.687 6.66671 11.3333C6.66671 10.9797 6.80718 10.6406 7.05723 10.3905C7.30728 10.1405 7.64642 10 8.00004 10C8.35366 10 8.6928 10.1405 8.94285 10.3905C9.1929 10.6406 9.33337 10.9797 9.33337 11.3333C9.33337 11.687 9.1929 12.0261 8.94285 12.2762C8.6928 12.5262 8.35366 12.6667 8.00004 12.6667Z"
                                                    fill="#484964" />
                                            </svg>
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
                                        <div class="shrink-0 flex items-center w-[9.6%] py-2.5 px-2">{{ item.price ??
                                            '-' }}
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
                                                    fill="#21272A" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                    fill="#21272A" />
                                            </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                </DialogContent>
            </transition>
        </DialogPortal>
    </DialogRoot>
</template>
