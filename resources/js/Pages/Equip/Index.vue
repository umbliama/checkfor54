<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import store from '../../../store/index';
import { computed, onMounted, reactive, ref, toRaw } from 'vue';
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
} from 'radix-vue';
import { PopoverAnchor, PopoverArrow, PopoverClose, PopoverContent, PopoverPortal, PopoverRoot, PopoverTrigger } from 'radix-vue'
import Pagination from '@/Components/Pagination.vue';
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";
import EquipNav from '@/Components/Equip/EquipNav.vue';
import EquipFilter from '@/Components/Equip/EquipFilter.vue';


import { Menu, MenuItems, MenuItem, MenuButton } from '@headlessui/vue'
import { EquipMenuItems } from "../../../constants/index.js";
import UiCheckbox from "@/Components/Ui/UiCheckbox.vue";
import UiField from "@/Components/Ui/UiField.vue";
const locationId = computed(() => store.getters['equipment/getLocationActive']);
const inputHyperlinkShown = computed(() => store.getters['equipment/getInputHyperLinkShown']);
const selectedId = computed(() => store.getters['equipment/getSelectedID']);
const sortOrder = computed(() => store.getters['equipment/getSortOrder']);
const sortBy = computed(() => store.getters['equipment/getSortBy']);
const updateSortBy = (value) => store.dispatch("equipment/updateSortBy", value)
const updateSortOrder = (value) => store.dispatch("equipment/updateSortOrder", value)

const setLocation = (location) => {
    store.dispatch('equipment/updateLocationActive', location)
    updateUrl();
}

const toggleSortBy = (column) => {
    if (sortBy.value === column) {
        updateSortOrder(sortOrder.value === 'asc' ? 'desc' : 'asc');
    } else {
        updateSortBy(column);
        updateSortOrder('asc');
    }
};


const toggleInputLocationShown = (value, itemId) => {
    store.dispatch('equipment/updateSelectedId', itemId)
    store.dispatch('equipment/updateInputHyperLinkShown', value)
}
const sumLocs = () => {
    const sumValues = obj => Object.values(obj).reduce((a, b) => a + b, 0);
    return sumValues(toRaw(props.location_counts))
}

const sortedEquipment = computed(() => {
    // Clone the array to avoid mutating the original array
    return props.equipment.data.slice().sort((a, b) => {
        let result = 0;
        // Determine which property to sort by (e.g., 'name' or 'status')
        if (sortBy.value === 'series') {
            result = a.series.localeCompare(b.series);
        }

        // Apply sort order (ascending or descending)
        if (sortOrder.value === 'desc') {
            result = result * -1;
        }

        return result;
    });
});


const props = defineProps({
    equipment: Object,
    equipment_categories: Array,
    equipment_categories_counts: Object,
    equipment_sizes_counts: Object,
    equipment_sizes: Object,
    equipment_location: Array,
    location_counts: Object
});

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

const form = reactive({
    'hyperlink': null
})

const setCategoryId = (categoryId) => {
    if (selectedCategory.value) setSizeId(null);
    store.dispatch('equipment/updateCategory', categoryId);
    updateUrl();
}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    updateUrl();
}

const updateUrl = () => {
    const params = {};

    if (selectedCategory.value) params.category_id = selectedCategory.value;
    if (selectedSize.value) params.size_id = selectedSize.value;
    if (locationId.value) params.location_id = locationId.value;

    Object.keys(params).length && router.get(route('equip.index', params));
};

/*const updateUrl = () => {
    if (selectedCategory.value || selectedSize.value || locationId.value) {
        router.get(route('equip.index', { category_id: selectedCategory.value, size_id: selectedSize.value, location_id: locationId.value }));
    }
};*/

const isInputVisible = computed(() => store.getters['equipment/getInputLocationShown']);
const isDropdownVisible = computed(() => store.getters['equipment/getDropdownShown']);
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);

const currentPage = ref(props.equipment.current_page || 1);
const lastPage = ref(props.equipment.last_page || 1);

onMounted(() => {
    const { current_page, total } = props.equipment;

    store.dispatch('pagination/updatePagination', {
        currentPage: current_page,
        totalPages: total,
    });
    currentPage.value = props.equipment.current_page;
    lastPage.value = props.equipment.last_page;

    store.dispatch('fetchLocations')
    store.dispatch('equipment/updateEquipmentCategories', props.equipment_categories)
    store.dispatch('equipment/updateEquipmentSizesCounts', props.equipment_sizes_counts)
    store.dispatch('equipment/updateEquipmentCategoriesCounts', props.equipment_categories_counts)
    store.dispatch('equipment/updateEquipmentSizes', props.equipment_sizes)

    const url_params = new URLSearchParams(window.location.search);

    const fetch_by = {};

    if (url_params.get('size_id')) {
        fetch_by.size_id = +url_params.get('size_id');
        store.dispatch('equipment/updateSize', fetch_by.size_id);
    } else {
        store.dispatch('equipment/updateSize', null);
    }

    if (url_params.get('category_id')) {
        fetch_by.category_id = +url_params.get('category_id');
        store.dispatch('equipment/updateCategory', fetch_by.category_id);
    } else {
        fetch_by.category_id = 1;
        store.dispatch('equipment/updateCategory', 1);
    }

    store.dispatch('equipment/updateMenuItem', EquipMenuItems.EQUIPMENT);
});


</script>

<template>

    <AuthenticatedLayout>
        <EquipNav />

        <div class="p-5">
            <EquipFilter :selected-category="selectedCategory" :selected-size="selectedSize"
                :categories-counts="equipment_categories_counts" :sizes-counts="equipment_sizes_counts"
                :categories="equipment_categories" :sizes="equipment_sizes"
                @category-click="cat_id => setCategoryId(cat_id)" @size-click="size_id => setSizeId(size_id)" />

            <div class="relative mt-5 text-sm">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul
                    class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto lg:overflow-x-visible">
                    <li :class="{ '!border-[#001D6C] text-[#001D6C]': locationId === 0 }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        @click="setLocation(0)">
                        Все
                        <span
                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            {{ sumLocs() }}
                        </span>
                    </li>
                    <li class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': locationId === location.id }"
                        @click="setLocation(location.id)" v-for="location in store.getters.cities" :key="location.id">
                        {{ location.name }}
                        <span
                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            {{ location_counts[location.id] }}
                        </span>
                    </li>
                </ul>
            </div>

            <div class="flex justify-between bg-my-gray mt-4 p-1 lg:hidden">
                <form action="" method="GET" class="w-[calc(50%-10px)]">
                    <UiField :inp-attrs="{ placeholder: 'Поиск', class: 'bg-white' }" class="w-full">
                        <template #prepend>
                            <button type="submit">
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </button>
                        </template>
                    </UiField>
                </form>

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

                <PopoverRoot>
                    <PopoverTrigger class="flex items-center w-[calc(50%-10px)] px-4 bg-white">
                        <svg class="block mr-2" width="25" height="24" viewBox="0 0 25 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.58007 4L11.0621 12.101C11.3458 12.4558 11.5003 12.8967 11.5001 13.351V20L13.5001 18.5V13.35C13.5001 12.896 13.6545 12.4556 13.9381 12.101L20.4201 4H4.58107H4.58007ZM4.58007 2H20.4201C20.7969 2.00005 21.166 2.10652 21.4849 2.30717C21.8038 2.50781 22.0595 2.79445 22.2227 3.13409C22.3858 3.47372 22.4497 3.85253 22.4069 4.22687C22.3642 4.60122 22.2166 4.95588 21.9811 5.25L15.5001 13.35V18.5C15.5001 18.8105 15.4278 19.1167 15.2889 19.3944C15.1501 19.6721 14.9485 19.9137 14.7001 20.1L12.7001 21.6C12.4029 21.8229 12.0496 21.9586 11.6797 21.9919C11.3098 22.0253 10.9379 21.955 10.6056 21.7889C10.2734 21.6227 9.99404 21.3674 9.79877 21.0515C9.6035 20.7355 9.50007 20.3714 9.50007 20V13.35L3.01907 5.25C2.78359 4.95588 2.63597 4.60122 2.59323 4.22687C2.55049 3.85253 2.61437 3.47372 2.77749 3.13409C2.94062 2.79445 3.19637 2.50781 3.51528 2.30717C3.83419 2.10652 4.2033 2.00005 4.58007 2V2Z"
                                fill="#697077" />
                        </svg>
                        Фильтры
                    </PopoverTrigger>
                    <PopoverPortal>
                        <PopoverContent
                            class="min-w-[var(--radix-popper-anchor-width)] py-4 px-3 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]">
                            <ul class="space-y-2.5">
                                <li>
                                    <UiCheckbox label="Фильтр 1" />
                                </li>
                                <li>
                                    <UiCheckbox label="Фильтр 2" />
                                </li>
                                <li>
                                    <UiCheckbox label="Фильтр 3" />
                                </li>
                            </ul>
                        </PopoverContent>
                    </PopoverPortal>
                </PopoverRoot>
            </div>

            <div class="hidden items-center mt-5 lg:flex">
                <form action="" method="GET" class="w-[200px] mr-4">
                    <UiField size="sm" :inp-attrs="{ placeholder: 'Поиск' }">
                        <template #prepend>
                            <svg width="14" height="16" viewBox="0 0 14 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13.8424 14.558L10.4759 10.9851C11.4517 9.74161 11.9382 8.14666 11.8344 6.53206C11.7305 4.91746 11.0443 3.40751 9.91838 2.31632C8.79248 1.22514 7.31363 0.63673 5.78946 0.6735C4.26529 0.710271 2.81315 1.36939 1.73512 2.51374C0.657088 3.6581 0.0361702 5.19958 0.00153095 6.81752C-0.0331083 8.43547 0.521198 10.0053 1.54914 11.2005C2.57708 12.3956 3.99952 13.1241 5.52054 13.2343C7.04156 13.3446 8.54407 12.8281 9.7155 11.7923L13.0813 15.3659C13.1312 15.4189 13.1906 15.461 13.2559 15.4897C13.3212 15.5184 13.3911 15.5332 13.4618 15.5332C13.5325 15.5332 13.6025 15.5184 13.6678 15.4897C13.7331 15.461 13.7924 15.4189 13.8424 15.3659C13.8923 15.3128 13.932 15.2499 13.959 15.1805C13.9861 15.1112 14 15.0369 14 14.9619C14 14.8869 13.9861 14.8126 13.959 14.7433C13.932 14.674 13.8923 14.611 13.8424 14.558ZM1.09062 6.96833C1.09062 5.95198 1.37453 4.95846 1.90646 4.1134C2.43838 3.26834 3.19443 2.60969 4.07899 2.22075C4.96355 1.83182 5.93689 1.73005 6.87594 1.92833C7.81498 2.12661 8.67755 2.61603 9.35456 3.33469C10.0316 4.05336 10.4926 4.96899 10.6794 5.96581C10.8662 6.96263 10.7703 7.99585 10.4039 8.93484C10.0375 9.87382 9.41707 10.6764 8.62099 11.241C7.8249 11.8057 6.88896 12.1071 5.93152 12.1071C4.64807 12.1056 3.4176 11.5637 2.51006 10.6003C1.60252 9.63692 1.09204 8.33074 1.09062 6.96833Z"
                                    fill="#787878" />
                            </svg>
                        </template>
                    </UiField>
                </form>
                <PopoverRoot>
                    <PopoverTrigger
                        class="flex items-center justify-center w-[103px] h-6 rounded text-[10px] border border-black border-opacity-15">
                        <svg class="block mr-2" width="13" height="9" viewBox="0 0 13 9" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="2.60474" cy="6.50366" r="2" fill="white" stroke="#415476" />
                            <line x1="4.10474" y1="6.50366" x2="12.1047" y2="6.50366" stroke="#415476" />
                            <circle cx="9.89476" cy="2.8417" r="2" transform="rotate(-177.996 9.89476 2.8417)"
                                fill="white" stroke="#415476" />
                            <line x1="8.10474" y1="2.50366" x2="0.104735" y2="2.50366" stroke="#415476" />
                        </svg>
                        Фильтры
                    </PopoverTrigger>
                    <PopoverPortal>
                        <PopoverContent
                            class="min-w-[137px] py-4 px-3 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]">
                            <ul class="space-y-2.5">
                                <li>
                                    <UiCheckbox label="Фильтр 1" />
                                </li>
                                <li>
                                    <UiCheckbox label="Фильтр 2" />
                                </li>
                                <li>
                                    <UiCheckbox label="Фильтр 3" />
                                </li>
                            </ul>
                        </PopoverContent>
                    </PopoverPortal>
                </PopoverRoot>
            </div>

            <div v-if="selectedCategory" class="mt-5">
                <div v-if="selectedCategory == 1"
                    class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                    <div class="min-w-[1144px] text-xs">
                        <div
                            class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Производитель
                            </div>
                            <div :class="{ 'bg-[#644DED] bg-opacity-10': sortBy === 'series' }"
                                class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer"
                                @click="toggleSortBy('series')">
                                <span class="absolute left-0 top-0 w-full h-[1px] bg-[#644DED]"></span>
                                Серия
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08" />
                                    <path d="M8.26783 12.1904L10.6249 9.83325L12.9821 12.1904" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.6249 9.83329L10.6249 18.0832" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M19.5653 16.5594L17.2082 18.9165L14.8511 16.5594" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.2082 10.6667L17.2082 18.9166" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
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
                            <div class="shrink-0 flex items-center justify-center w-[8.04%] py-2.5 px-2">Примечание
                            </div>
                            <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Состояние</div>
                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
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
                        <template v-for="item in sortedEquipment">
                            <div
                                class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                    <div class="mr-2">
                                        <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink" endpoint="/equipment" />
                                    </div>
                                    <span class="line-clamp-2">{{ item.manufactor }}</span>
                                </div>
                                <div :class="{ 'bg-[#644DED] bg-opacity-10': sortBy === 'series' }"
                                    class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series ?? '-' }}
                                </div>
                                <div class="shrink-0 flex items-center w-[5.15%] py-2.5 px-2">{{ item.zahodnost ?? '-'
                                    }}</div>
                                <div class="shrink-0 flex items-center w-[4.89%] py-2.5 px-2">{{ item.length ?? '-' }}
                                </div>
                                <div class="shrink-0 flex items-center w-[5.15%] py-2.5 px-2">{{ item.dlina_ds ?? '-' }}
                                </div>
                                <div class="shrink-0 flex items-center w-[7.08%] py-2.5 px-2">{{ item.stator_rotor ??
                                    '-' }}</div>
                                <div class="shrink-0 flex items-center w-[6.11%] py-2.5 px-2">{{ item.operating ?? '-'
                                    }}</div>
                                <div class="shrink-0 flex items-center w-[7.08%] py-2.5 px-2">{{ item.narabotka_ds ??
                                    '-' }}</div>
                                <div class="shrink-0 flex items-center w-[8.56%] py-2.5 px-2">{{ item.manufactor_date ??
                                    '-' }}</div>
                                <div class="shrink-0 flex items-center w-[8.04%] py-2.5 px-2">{{ item.price ?? '-' }} ₽
                                </div>
                                <div class="shrink-0 flex items-center w-[8.04%] py-2.5 px-2">
                                    <span class="line-clamp-2">{{ item.notes ?? '-' }}</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                    <span :class="statuses_colors[item.status]"
                                        class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                    <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses[item.status]
                                        }}</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
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

                                    <DropdownMenuRoot>
                                        <DropdownMenuTrigger aria-label="Customise options">
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
                                        </DropdownMenuTrigger>

                                        <DropdownMenuPortal>
                                            <transition name="fade">
                                                <DropdownMenuContent
                                                    class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                    :side-offset="5" align="end">
                                                    <DropdownMenuItem>
                                                        <Link :href="route('equip.edit', item.id)"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                        Редактировать
                                                        <svg class="block ml-2" width="16" height="16"
                                                            viewBox="0 0 16 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                                fill="#464F60" />
                                                            <path
                                                                d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                                fill="#464F60" />
                                                        </svg>
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <Link :href="route('equip.destroy', item.id)"
                                                            class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                        Удалить
                                                        <svg class="block ml-2" width="16" height="16"
                                                            viewBox="0 0 16 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                        </Link>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
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
                            <div :class="{ 'bg-[#644DED] bg-opacity-10': sortBy === 'series' }"
                                class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer"
                                @click="toggleSortBy('series')">
                                <span class="absolute left-0 top-0 w-full h-[1px] bg-[#644DED]"></span>
                                Серия
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08" />
                                    <path d="M8.26783 12.1904L10.6249 9.83325L12.9821 12.1904" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.6249 9.83329L10.6249 18.0832" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M19.5653 16.5594L17.2082 18.9165L14.8511 16.5594" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.2082 10.6667L17.2082 18.9166" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Длина</div>
                            <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Наработка</div>
                            <div class="shrink-0 flex items-center justify-center text-center w-[10.48%] py-2.5 px-2">
                                Дата
                                <br>изготовления
                            </div>
                            <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Стоимость</div>
                            <div
                                class="shrink-0 flex items-center w-[calc(100%-9.96%-9.96%-10.48%-10.48%-10.48%-10.48%-10.48%-100px)] py-2.5 px-2">
                                Примечание</div>
                            <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">Состояние</div>
                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
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
                        <template v-for="item in sortedEquipment">
                            <div
                                class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                    <div class="mr-2">
                                        <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink" endpoint="/equipment" />
                                    </div>
                                    <span class="line-clamp-2">{{ item.manufactor ?? '-' }}</span>
                                </div>
                                <div :class="{ 'bg-[#644DED] bg-opacity-10': sortBy === 'series' }"
                                    class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series ?? '-' }}
                                </div>
                                <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{ item.length ?? '-' }}
                                </div>
                                <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{ item.operating ?? '-'
                                    }}</div>
                                <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{ item.manufactor_date
                                    ?? '-' }}</div>
                                <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">{{ item.price ?? '-' }} ₽
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[calc(100%-9.96%-9.96%-10.48%-10.48%-10.48%-10.48%-10.48%-100px)] py-2.5 px-2">
                                    <span class="line-clamp-2">{{ item.notes ?? '-' }}</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[10.48%] py-2.5 px-2">
                                    <span :class="statuses_colors[item.status]"
                                        class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                    <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses[item.status]
                                        }}</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
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

                                    <DropdownMenuRoot>
                                        <DropdownMenuTrigger aria-label="Customise options">
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
                                        </DropdownMenuTrigger>

                                        <DropdownMenuPortal>
                                            <transition name="fade">
                                                <DropdownMenuContent
                                                    class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                    :side-offset="5" align="end">
                                                    <DropdownMenuItem>
                                                        <Link :href="route('equip.edit', item.id)"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                        Редактировать
                                                        <svg class="block ml-2" width="16" height="16"
                                                            viewBox="0 0 16 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                                fill="#464F60" />
                                                            <path
                                                                d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                                fill="#464F60" />
                                                        </svg>
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <Link :href="route('equip.destroy', item.id)"
                                                            class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                        Удалить
                                                        <svg class="block ml-2" width="16" height="16"
                                                            viewBox="0 0 16 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                        </Link>

                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
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
                            <div :class="{ 'bg-[#644DED] bg-opacity-10': sortBy === 'series' }"
                                class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer"
                                @click="toggleSortBy('series')">
                                <span class="absolute left-0 top-0 w-full h-[1px] bg-[#644DED]"></span>
                                Серия
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08" />
                                    <path d="M8.26783 12.1904L10.6249 9.83325L12.9821 12.1904" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.6249 9.83329L10.6249 18.0832" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M19.5653 16.5594L17.2082 18.9165L14.8511 16.5594" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.2082 10.6667L17.2082 18.9166" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
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
                            <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Примечание
                            </div>
                            <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Состояние</div>
                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
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
                        <template v-for="item in sortedEquipment">
                            <div
                                class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                    <div class="mr-2">
                                        <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink" endpoint="/equipment" />
                                    </div>
                                    <span class="line-clamp-2">{{ item.manufactor ?? '-' }}</span>
                                </div>
                                <div :class="{ 'bg-[#644DED] bg-opacity-10': sortBy === 'series' }"
                                    class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series ?? '-' }}
                                </div>
                                <div class="shrink-0 flex items-center justify-center w-[6.2%] py-2.5 px-2">{{
                                    item.diameter ?? '-' }}</div>
                                <div class="shrink-0 flex items-center justify-center w-[6.2%] py-2.5 px-2">{{
                                    item.length ?? '-' }}</div>
                                <div class="shrink-0 flex items-center justify-center w-[6.73%] py-2.5 px-2">{{
                                    item.length_rezba ?? '-' }}</div>
                                <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">{{
                                    item.rezbi ?? '-' }}</div>
                                <div class="shrink-0 flex items-center justify-center w-[7.08%] py-2.5 px-2">{{
                                    item.operating ?? '-' }}</div>
                                <div
                                    class="shrink-0 flex items-center justify-center text-center w-[8.56%] py-2.5 px-2">
                                    {{ item.manufactor_date ?? '-'
                                    }}</div>
                                <div class="shrink-0 flex items-center w-[9.6%] py-2.5 px-2">{{ item.price ?? '-' }}
                                </div>
                                <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                    <span class="line-clamp-2">{{ item.notes ?? '-' }}</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                    <span :class="statuses_colors[item.status]"
                                        class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                    <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses[item.status]
                                        }}</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
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

                                    <DropdownMenuRoot>
                                        <DropdownMenuTrigger aria-label="Customise options">
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
                                        </DropdownMenuTrigger>

                                        <DropdownMenuPortal>
                                            <transition name="fade">
                                                <DropdownMenuContent
                                                    class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                    :side-offset="5" align="end">
                                                    <DropdownMenuItem>
                                                        <Link :href="route('equip.edit', item.id)"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                        Редактировать
                                                        <svg class="block ml-2" width="16" height="16"
                                                            viewBox="0 0 16 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                                fill="#464F60" />
                                                            <path
                                                                d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                                fill="#464F60" />
                                                        </svg>
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <Link :href="route('equip.destroy', item.id)"
                                                            class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                        Удалить
                                                        <svg class="block ml-2" width="16" height="16"
                                                            viewBox="0 0 16 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                        </Link>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <pagination
                    :current-page="props.equipment.current_page"
                    :total-pages="props.equipment.last_page"
                    :total-count="props.equipment.total"
                    :next-page-url="props.equipment.next_page_url"
                    :links="props.equipment.links"
                    :prev-page-url="props.equipment.prev_page_url"
                    class="mt-5 bg-bg1"
                />
                <!--                <pagination class="lg:m-5" :links="equipment.links" />-->
            </div>
        </div>
    </AuthenticatedLayout>
</template>
