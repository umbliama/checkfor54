<script setup>
import DashboardToolbar from '@/Components/Dashboard/DashboardToolbar.vue';
import Pagination from '@/Components/Pagination.vue';
import UiCheckbox from '@/Components/Ui/UiCheckbox.vue';
import UiField from '@/Components/Ui/UiField.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger
} from "radix-vue";
import {
    PopoverArrow,
    PopoverClose,
    PopoverContent,
    PopoverPortal,
    PopoverRoot,
    PopoverTrigger
} from 'radix-vue'
import { onMounted, ref,reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';



const props = defineProps({
    equipment: Object,
    equipment_categories: Array,
    equipment_categories_counts: Array,
    equipment_sizes: Array,
    equipment_sizes_counts: Array,
    equipment_location:Array
})
const filters = reactive({
    size_id: null,
    category_id: 1,
    location_id: 0,
});
const pagination = reactive({
    currentPage: 1,
    totalPages: 1,
});
const currentPage = ref(1);
const lastPage = ref(1);
const chosenCategory = ref(null);
const chosenSize = ref(null);
const chosenLocation = ref(null);

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

const updateUrl = () => {
    const params = {};

    if (filters.category_id) params.category_id = filters.category_id;
    if (filters.size_id) params.size_id = filters.size_id;
    if (filters.location_id) params.location_id = filters.location_id;

    Object.keys(params).length && router.replace(route('free', params));
};

const removeParams = () => {
    router.get(route('free'))
}

const setCategoryId = (categoryId) => {
    if (filters.category_id) setSizeId(null);
    filters.category_id = categoryId
    chosenCategory.value = categoryId
    updateUrl();
}

const setLocationId = (locationId) => {
    filters.location_id = locationId
    chosenLocation.value = locationId
    updateUrl();
}


const setSizeId = (sizeId) => {
    filters.size_id = sizeId
    chosenSize.value = sizeId
    updateUrl();
}
const updateFiltersAndFetchData = () => {
    const { current_page, total, last_page } = props.equipment;

    pagination.currentPage = current_page;
    pagination.totalPages = total;
    currentPage.value = current_page;
    lastPage.value = last_page;

    const url_params = new URLSearchParams(window.location.search);

    if (url_params.get('size_id')) {
        filters.size_id = +url_params.get('size_id');
    } else {
        filters.size_id = null;
    }

    if (url_params.get('category_id')) {
        filters.category_id = +url_params.get('category_id');
    } else {
        filters.category_id = 1;
    }
    if (url_params.get('location_id')) {
        filters.location_id = +url_params.get('location_id');
    } else {
        filters.location_id = 0;
    }

};


onMounted(() => {
    updateFiltersAndFetchData();
});

</script>

<template>
    <AuthenticatedLayout>
        <DashboardToolbar dashboard-page-type="free" />

        <div class="p-5">
            <nav class="py-2.5 px-6 rounded-xl text-sm bg-my-gray">
                <div class="relative">
                    <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                    <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                        <li v-for="category in equipment_categories" @click="setCategoryId(category.id)"
                            :class="{ '!border-[#001D6C] text-[#001D6C]': chosenCategory === category.id }"
                            class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                        >
                            <div class="flex items-center justify-between">
                                {{category.name}}
                                <span class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">6</span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="relative mt-4">
                    <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                    <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                        <li  v-for="size in equipment_sizes" @click="setSizeId(size.id)"
                            :class="{ '!border-[#001D6C] text-[#001D6C]': size.id === chosenSize }"
                            class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                        >
                            <div class="flex items-center justify-between">
                                {{ size.name }}
                                <span class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">6</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="relative mt-5 text-sm">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul
                    class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto lg:overflow-x-visible">
                    <li @click="setLocationId(0)"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': chosenLocation === 0 }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                    >
                        Все
                        <span
                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            12
                        </span>
                    </li>
                    <li @click="setLocationId(item.id)" v-for="item in equipment_location"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': chosenLocation === item.id }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                    >
                    {{ item.name }}
                        <span
                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            10
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
                                          stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </button>
                        </template>
                    </UiField>
                </form>

                <div class="flex mx-2 items-center">
                    <svg width="5" height="34" viewBox="0 0 3 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="path-1-inside-1_2101_84576" fill="white">
                            <path d="M0.5 0H2.5V28H0.5V0Z"/>
                        </mask>
                        <path d="M0.5 0H2.5V28H0.5V0Z" fill="white"/>
                        <path
                            d="M0 0V1H1V0H0ZM0 3V5H1V3H0ZM0 7V9H1V7H0ZM0 11V13H1V11H0ZM0 15V17H1V15H0ZM0 19V21H1V19H0ZM0 23V25H1V23H0ZM0 27V28H1V27H0ZM-0.5 0V1H1.5V0H-0.5ZM-0.5 3V5H1.5V3H-0.5ZM-0.5 7V9H1.5V7H-0.5ZM-0.5 11V13H1.5V11H-0.5ZM-0.5 15V17H1.5V15H-0.5ZM-0.5 19V21H1.5V19H-0.5ZM-0.5 23V25H1.5V23H-0.5ZM-0.5 27V28H1.5V27H-0.5Z"
                            fill="#C1C7CD" mask="url(#path-1-inside-1_2101_84576)"/>
                    </svg>
                </div>

                <PopoverRoot>
                    <PopoverTrigger class="flex items-center w-[calc(50%-10px)] px-4 bg-white">
                        <svg class="block mr-2" width="25" height="24" viewBox="0 0 25 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.58007 4L11.0621 12.101C11.3458 12.4558 11.5003 12.8967 11.5001 13.351V20L13.5001 18.5V13.35C13.5001 12.896 13.6545 12.4556 13.9381 12.101L20.4201 4H4.58107H4.58007ZM4.58007 2H20.4201C20.7969 2.00005 21.166 2.10652 21.4849 2.30717C21.8038 2.50781 22.0595 2.79445 22.2227 3.13409C22.3858 3.47372 22.4497 3.85253 22.4069 4.22687C22.3642 4.60122 22.2166 4.95588 21.9811 5.25L15.5001 13.35V18.5C15.5001 18.8105 15.4278 19.1167 15.2889 19.3944C15.1501 19.6721 14.9485 19.9137 14.7001 20.1L12.7001 21.6C12.4029 21.8229 12.0496 21.9586 11.6797 21.9919C11.3098 22.0253 10.9379 21.955 10.6056 21.7889C10.2734 21.6227 9.99404 21.3674 9.79877 21.0515C9.6035 20.7355 9.50007 20.3714 9.50007 20V13.35L3.01907 5.25C2.78359 4.95588 2.63597 4.60122 2.59323 4.22687C2.55049 3.85253 2.61437 3.47372 2.77749 3.13409C2.94062 2.79445 3.19637 2.50781 3.51528 2.30717C3.83419 2.10652 4.2033 2.00005 4.58007 2V2Z"
                                fill="#697077"/>
                        </svg>
                        Фильтры
                    </PopoverTrigger>
                    <PopoverPortal>
                        <PopoverContent
                            class="min-w-[var(--radix-popper-anchor-width)] py-4 px-3 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]">
                            <ul class="space-y-2.5">
                                <li>
                                    <UiCheckbox label="Фильтр 1"/>
                                </li>
                                <li>
                                    <UiCheckbox label="Фильтр 2"/>
                                </li>
                                <li>
                                    <UiCheckbox label="Фильтр 3"/>
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
                                    fill="#787878"/>
                            </svg>
                        </template>
                    </UiField>
                </form>
                <PopoverRoot>
                    <PopoverTrigger
                        class="flex items-center justify-center w-[103px] h-6 rounded text-[10px] border border-black border-opacity-15">
                        <svg class="block mr-2" width="13" height="9" viewBox="0 0 13 9" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <circle cx="2.60474" cy="6.50366" r="2" fill="white" stroke="#415476"/>
                            <line x1="4.10474" y1="6.50366" x2="12.1047" y2="6.50366" stroke="#415476"/>
                            <circle cx="9.89476" cy="2.8417" r="2" transform="rotate(-177.996 9.89476 2.8417)"
                                    fill="white" stroke="#415476"/>
                            <line x1="8.10474" y1="2.50366" x2="0.104735" y2="2.50366" stroke="#415476"/>
                        </svg>
                        Фильтры
                    </PopoverTrigger>
                    <PopoverPortal>
                        <PopoverContent
                            class="min-w-[137px] py-4 px-3 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]">
                            <ul class="space-y-2.5">
                                <li>
                                    <UiCheckbox label="Фильтр 1"/>
                                </li>
                                <li>
                                    <UiCheckbox label="Фильтр 2"/>
                                </li>
                                <li>
                                    <UiCheckbox label="Фильтр 3"/>
                                </li>
                            </ul>
                        </PopoverContent>
                    </PopoverPortal>
                </PopoverRoot>
            </div>

            <div class="mt-5">
                <div class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                    <div class="min-w-[1144px] text-xs">
                        <div
                            class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <div class="shrink-0 flex items-center justify-center w-[9.96%] py-2.5 px-2">Производитель
                            </div>
                            <div :class="{ 'bg-[#644DED] bg-opacity-10': true }"
                                 class="relative shrink-0 flex items-center justify-between w-[9.96%] py-2.5 px-2 cursor-pointer">
                                <span class="absolute left-0 top-0 w-full h-[1px] bg-[#644DED]"></span>
                                Серия
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08"/>
                                    <path d="M8.26783 12.1904L10.6249 9.83325L12.9821 12.1904" stroke="#644DED"
                                          stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10.6249 9.83329L10.6249 18.0832" stroke="#644DED" stroke-width="1.2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M19.5653 16.5594L17.2082 18.9165L14.8511 16.5594" stroke="#644DED"
                                          stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17.2082 10.6667L17.2082 18.9166" stroke="#644DED" stroke-width="1.2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
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
                        <div  v-for="item in equipment.data"
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                            <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                <div class="mr-2">
                                    <UiHyperlink endpoint="/equipment" />
                                </div>
                                <span class="line-clamp-2">{{item.manufactor}}</span>
                            </div>
                            <div :class="{ 'bg-[#644DED] bg-opacity-10': true }"
                                    class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">{{ item.series }}
                            </div>
                            <div class="shrink-0 flex items-center w-[5.15%] py-2.5 px-2">
                                {{item.zahodnost}}
                            </div>
                            <div class="shrink-0 flex items-center w-[4.89%] py-2.5 px-2">{{ item.length }}
                            </div>
                            <div class="shrink-0 flex items-center w-[5.15%] py-2.5 px-2">{{ item.dlina_ds }}
                            </div>
                            <div class="shrink-0 flex items-center w-[7.08%] py-2.5 px-2">
                                {{ item.stator_rotor }}
                            </div>
                            <div class="shrink-0 flex items-center w-[6.11%] py-2.5 px-2">
                                {{ item.operating }}
                            </div>
                            <div class="shrink-0 flex items-center w-[7.08%] py-2.5 px-2">
                                {{ item.narabotka_ds }}
                            </div>
                            <div class="shrink-0 flex items-center w-[8.56%] py-2.5 px-2">
                                {{ item.manufactor_date }}
                            </div>
                            <div class="shrink-0 flex items-center w-[8.04%] py-2.5 px-2">{{ item.price }}₽
                            </div>
                            <div class="shrink-0 flex items-center w-[8.04%] py-2.5 px-2">
                                <span class="line-clamp-2">{{item.notes}}</span>
                            </div>
                            <div class="shrink-0 flex items-center w-[9.96%] py-2.5 px-2">
                                <span :class="statuses_colors['new']"
                                        class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                <span class="text-nowrap text-ellipsis overflow-hidden">{{
                                    statuses[item.status]
                                    }}</span>
                            </div>
                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                <Link v-if="true" :href="'/directory/service/' + 2" class="mr-3.5">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                            fill="#21272A" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z"
                                            fill="#21272A" />
                                    </svg>
                                </Link>
                                <PopoverRoot>
                                    <PopoverTrigger class="mr-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                            <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                            <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                        </svg>
                                    </PopoverTrigger>
                                    <PopoverPortal>
                                        <PopoverContent side="bottom" align="end" class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                            <div>Комментарий:</div>
                                            <p class="mt-2.5 text-xs">Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Страна если бросил, он всемогущая запятых грамматики себя ipsum точках, несколько меня строчка маленькая страну предупреждал которой раз проектах. Ему выйти составитель дал то ...</p>
                                            <div class="mt-3 p-4 bg-bg1 text-xs">
                                                <div class="flex items-center max-w-full">
                                                    <span class="grow block mr-auto text-ellipsis overflow-hidden">Some file name</span>
                                                    <svg class="shrink-0 block ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                            fill="#697077"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                            fill="#697077"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                            fill="#697077"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                            fill="#697077"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <Link
                                                class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all"
                                            >
                                                Редактировать

                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                    <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                </svg>
                                            </Link>
                                        </PopoverContent>
                                    </PopoverPortal>
                                </PopoverRoot>

                                <DropdownMenuRoot>
                                    <DropdownMenuTrigger aria-label="Customise options">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                fill="#687182"/>
                                            <path
                                                d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                fill="#687182"/>
                                            <path
                                                d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                fill="#687182"/>
                                        </svg>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuPortal>
                                        <transition name="fade">
                                            <DropdownMenuContent
                                                class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                :side-offset="5" align="end">
                                                <DropdownMenuItem>
                                                    <Link :href="route('equip.edit', 1)"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                        Редактировать
                                                        <svg class="block ml-2" width="16" height="16"
                                                                viewBox="0 0 16 16" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                                fill="#464F60"/>
                                                            <path
                                                                d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                                fill="#464F60"/>
                                                        </svg>
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <Link
                                                        :href="route('equip.destroy', 1)"
                                                        method="DELETE"
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                        Удалить
                                                        <svg class="block ml-2" width="16" height="16"
                                                                viewBox="0 0 16 16" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                    fill="currentColor"/>
                                                        </svg>
                                                    </Link>
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </transition>
                                    </DropdownMenuPortal>
                                </DropdownMenuRoot>
                            </div>
                        </div>

                    </div>
                </div>
                
                <Pagination :links="[1,2,3]" current-page="1" total-pages="2" total-count="23" class="mt-6 bg-bg1" />
                <!--                <pagination class="lg:m-5" :links="equipment.links" />-->
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>