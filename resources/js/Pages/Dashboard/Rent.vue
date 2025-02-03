<script setup>
import DashboardToolbar from '@/Components/Dashboard/DashboardToolbar.vue';
import Pagination from '@/Components/Pagination.vue';
import UiField from '@/Components/Ui/UiField.vue';
import UiFieldDate from '@/Components/Ui/UiFieldDate.vue';
import UiFieldSelect from '@/Components/Ui/UiFieldSelect.vue';
import UiHyperlink from '@/Components/Ui/UiHyperlink.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Link, router} from '@inertiajs/vue3';
import {
    AccordionContent,
    AccordionHeader,
    AccordionItem,
    AccordionRoot,
    AccordionTrigger
} from "radix-vue";
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
import { ref, reactive, onMounted } from 'vue';

const selected_series = ref();
const start_date = ref();
const end_date = ref();

const currentPage = ref(1);
const lastPage = ref(1);
const pagination = reactive({
    currentPage: 1,
    totalPages: 1,
});

const filters = reactive({
    size_id: null,
    category_id: 1,
});

// Props
const props = defineProps({
    equipment_categories: Array,
    equipment_categories_counts: Array,
    equipment_categories_counts_all: Number,
    contragents: Array,
    rented_equipment: Array,
    equipment: Object, 
    equipment_sizes:Array,
    equipment_sizes_counts: Array
});
const updateUrl = () => {
    const params = {};

    if (filters.category_id) params.category_id = filters.category_id;
    if (filters.size_id) params.size_id = filters.size_id;

    Object.keys(params).length && router.get(route('rent', params));
};

const setCategoryId = (categoryId) => {
    if (filters.category_id) setSizeId(null);
    filters.category_id = categoryId
    updateUrl();
}


const setSizeId = (sizeId) => {
    if (filters.size_id) 
    console.log(sizeId)
    filters.size_id = sizeId
    updateUrl();
}
const updateFiltersAndFetchData = () => {
    const { current_page, total, last_page } = props.rented_equipment;

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

};


onMounted(() => {
    updateFiltersAndFetchData();
});

</script>
<template>
    <AuthenticatedLayout>
        <DashboardToolbar dashboard-page-type="rent" filter />

        <div class="p-5">
            <nav class="py-2.5 px-6 rounded-xl text-sm bg-my-gray">
                <div class="relative">
                    <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                    <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                        <li
                            :class="{ '!border-[#001D6C] text-[#001D6C]': false }"
                            class="relative flex items-center border-b-2 border-transparent py-3 pr-6 cursor-pointer"
                        >
                            <span class="absolute right-0 top-2.5 w-px h-[calc(100%-20px)] bg-gray1"></span>
                            <div class="flex items-center justify-between">
                                Все
                                <span class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">{{equipment_categories_counts_all}}</span>
                            </div>
                        </li>
                        
                        
                        <li v-for="category in equipment_categories"
                            :class="{ '!border-[#001D6C] text-[#001D6C]': true }"
                            class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"  @click="setCategoryId(category.id)"
                        >
                            <div class="flex items-center justify-between">
                                {{ category.name }}
                                <span class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">{{ equipment_categories_counts[category.id] }}</span>
                            </div>
                        </li>
                  
                    </ul>
                </div>
                <div class="relative mt-4">
                    <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                    <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                        <li
                            :class="{ '!border-[#001D6C] text-[#001D6C]': false }"
                            class="relative flex items-center border-b-2 border-transparent py-3 pr-6 cursor-pointer"
                        >
                            <span class="absolute right-0 top-2.5 w-px h-[calc(100%-20px)] bg-gray1"></span>
                            <div class="flex items-center justify-between">
                                Все
                                <span class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">10</span>
                            </div>
                        </li>
                        
                        
                        <li v-for="size in equipment_sizes"
                            :class="{ '!border-[#001D6C] text-[#001D6C]': true }"
                            class="flex items-center border-b-2 border-transparent py-3 cursor-pointer" @click="setSizeId(size.id)"
                        >
                            <div class="flex items-center justify-between">
                                {{ size.name }}
                                <span class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">{{ equipment_sizes_counts[size.id]}}</span>
                            </div>
                        </li>

                    </ul>
                </div>
            </nav>

            <div class="block mt-4 lg:hidden">
                <div class="flex justify-between bg-my-gray p-1 text-sm lg:hidden">
                    <UiField :inp-attrs="{ class: 'bg-white text-sm', placeholder: 'Поиск...' }" class="w-[calc(50%-10px)]">
                        <template #prepend>
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.3424 13.5813L10.9759 10.2155C11.9517 9.04407 12.4382 7.54156 12.3344 6.02054C12.2305 4.49952 11.5443 3.07708 10.4184 2.04914C9.29248 1.0212 7.81363 0.466892 6.28946 0.501531C4.76529 0.53617 3.31315 1.15709 2.23512 2.23512C1.15709 3.31315 0.53617 4.76529 0.501531 6.28946C0.466892 7.81363 1.0212 9.29248 2.04914 10.4184C3.07708 11.5443 4.49952 12.2305 6.02054 12.3344C7.54156 12.4382 9.04407 11.9517 10.2155 10.9759L13.5813 14.3424C13.6312 14.3923 13.6906 14.432 13.7559 14.459C13.8212 14.4861 13.8911 14.5 13.9618 14.5C14.0325 14.5 14.1025 14.4861 14.1678 14.459C14.2331 14.432 14.2924 14.3923 14.3424 14.3424C14.3923 14.2924 14.432 14.2331 14.459 14.1678C14.4861 14.1025 14.5 14.0325 14.5 13.9618C14.5 13.8911 14.4861 13.8212 14.459 13.7559C14.432 13.6906 14.3923 13.6312 14.3424 13.5813ZM1.59062 6.43152C1.59062 5.47408 1.87453 4.53814 2.40646 3.74206C2.93838 2.94598 3.69443 2.32551 4.57899 1.95911C5.46355 1.59271 6.43689 1.49685 7.37594 1.68363C8.31498 1.87042 9.17755 2.33147 9.85456 3.00849C10.5316 3.6855 10.9926 4.54807 11.1794 5.48711C11.3662 6.42615 11.2703 7.3995 10.9039 8.28406C10.5375 9.16862 9.91707 9.92466 9.12099 10.4566C8.3249 10.9885 7.38896 11.2724 6.43152 11.2724C5.14807 11.271 3.9176 10.7605 3.01006 9.85299C2.10252 8.94545 1.59204 7.71497 1.59062 6.43152Z" fill="#787878" />
                            </svg>
                        </template>
                    </UiField>

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
                        <PopoverTrigger
                            class="flex items-center w-[calc(50%-10px)] py-2 px-3 text-gray1 bg-white lg:py-3 lg:px-4"
                        >
                            <svg class="block mr-2" width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.57995 4L11.062 12.101C11.3457 12.4558 11.5002 12.8967 11.5 13.351V20L13.5 18.5V13.35C13.5 12.896 13.6544 12.4556 13.938 12.101L20.42 4H4.58095H4.57995ZM4.57995 2H20.42C20.7967 2.00005 21.1658 2.10652 21.4847 2.30717C21.8037 2.50781 22.0594 2.79445 22.2225 3.13409C22.3857 3.47372 22.4495 3.85253 22.4068 4.22687C22.3641 4.60122 22.2164 4.95588 21.981 5.25L15.5 13.35V18.5C15.5 18.8105 15.4277 19.1167 15.2888 19.3944C15.15 19.6721 14.9483 19.9137 14.7 20.1L12.7 21.6C12.4028 21.8229 12.0495 21.9586 11.6796 21.9919C11.3096 22.0253 10.9377 21.955 10.6055 21.7889C10.2733 21.6227 9.99392 21.3674 9.79865 21.0515C9.60338 20.7355 9.49995 20.3714 9.49995 20V13.35L3.01895 5.25C2.78347 4.95588 2.63585 4.60122 2.59311 4.22687C2.55037 3.85253 2.61424 3.47372 2.77737 3.13409C2.9405 2.79445 3.19625 2.50781 3.51516 2.30717C3.83407 2.10652 4.20317 2.00005 4.57995 2V2Z" fill="#697077"/>
                            </svg>
                            Фильтры
                        </PopoverTrigger>
                        <PopoverPortal>
                            <PopoverContent
                                side="bottom"
                                align="end"
                                :side-offset="5"
                                class="rounded p-3 w-[360px] bg-white shadow-[0_10px_38px_-10px_hsla(206,22%,7%,.35),0_10px_20px_-15px_hsla(206,22%,7%,.2)] focus:shadow-[0_10px_38px_-10px_hsla(206,22%,7%,.35),0_10px_20px_-15px_hsla(206,22%,7%,.2),0_0_0_2px_theme(colors.green7)] will-change-[transform,opacity] data-[state=open]:data-[side=top]:animate-slideDownAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade lg:p-5"
                            >
                            <div class="flex w-full text-sm lg:w-auto lg:text-base lg:mt-0">
                                <UiFieldDate v-model="start_date" :inp-attrs="{ placeholder: 'Начало', class: 'bg-white' }" class="w-[calc(100%/2-12px)] lg:w-[178px]" />
                                <button
                                    class="flex items-center justify-center w-6 h-12 bg-[#DDE1E6] text-gray1 lg:w-12"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.75 10.75h-3m12.5-2c0 3-2.798 5.5-6.25 5.5c-3.75 0-6.25-3.5-6.25-3.5v3.5m9.5-9h3m-12.5 2c0-3 2.798-5.5 6.25-5.5c3.75 0 6.25 3.5 6.25 3.5v-3.5"/></svg>
                                </button>
                                <UiFieldDate v-model="end_date" :inp-attrs="{ placeholder: 'Завершение', class: 'bg-white' }" class="w-[calc(100%/2-12px)] lg:w-[178px]" />
                            </div>
                            </PopoverContent>
                        </PopoverPortal>
                    </PopoverRoot>
                    
                </div>
                <div class="flex justify-between bg-my-gray mt-3 p-1 text-sm lg:hidden">
                    <UiFieldSelect
                        v-model="selected_series"
                        :items="[ { title: 'ООО Азалмеган', value: 1 }, { title: 'ООО Бабила баъ', value: 2 }, { title: 'ООО Гаршаган', value: 4 } ]"
                        :trigger-attrs="{ class: 'bg-white' }"
                        placeholder="Заказчики"
                        class="w-[calc(50%-10px)]"
                        only-value
                    />

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
                    <form action="" method="GET" class="w-[calc(50%-10px)]">
                        <button class="flex items-center justify-between w-full py-2 px-3 text-gray1 bg-white lg:py-3 lg:px-4" type="button">
                            Весь список
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 6H20H9Z" fill="#697077"/>
                                <path d="M9 6H20" stroke="#697077" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.80005 5.79998L4.60005 6.59997L6.60004 4.59998" stroke="#697077" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.80005 11.8L4.60005 12.6L6.60004 10.6" stroke="#697077" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.80005 17.8L4.60005 18.6L6.60004 16.6" stroke="#697077" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12H20" stroke="#697077" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 18H20" stroke="#697077" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="flex w-full mt-5">
                <ul class="hidden w-[168px] mr-3.5 lg:block">
                    <li v-for="agent in contragents"
                        :class="{ 'pointer-events-none bg-my-gray': true }"
                        class="py-3 px-2 font-medium text-sm cursor-pointer border-b border-b-my-gray"
                    >
                        {{ agent.name }}
                    </li>
                </ul>
                
                <div class="w-full lg:w-[calc(100%-168px-14px)]">
                    <form class="hidden items-center justify-between py-2 px-2.5 bg-bg1 lg:flex">
                        <UiField :inp-attrs="{ class: 'bg-white', placeholder: 'Поиск...' }" size="sm">
                            <template #prepend>
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.3424 13.5813L10.9759 10.2155C11.9517 9.04407 12.4382 7.54156 12.3344 6.02054C12.2305 4.49952 11.5443 3.07708 10.4184 2.04914C9.29248 1.0212 7.81363 0.466892 6.28946 0.501531C4.76529 0.53617 3.31315 1.15709 2.23512 2.23512C1.15709 3.31315 0.53617 4.76529 0.501531 6.28946C0.466892 7.81363 1.0212 9.29248 2.04914 10.4184C3.07708 11.5443 4.49952 12.2305 6.02054 12.3344C7.54156 12.4382 9.04407 11.9517 10.2155 10.9759L13.5813 14.3424C13.6312 14.3923 13.6906 14.432 13.7559 14.459C13.8212 14.4861 13.8911 14.5 13.9618 14.5C14.0325 14.5 14.1025 14.4861 14.1678 14.459C14.2331 14.432 14.2924 14.3923 14.3424 14.3424C14.3923 14.2924 14.432 14.2331 14.459 14.1678C14.4861 14.1025 14.5 14.0325 14.5 13.9618C14.5 13.8911 14.4861 13.8212 14.459 13.7559C14.432 13.6906 14.3923 13.6312 14.3424 13.5813ZM1.59062 6.43152C1.59062 5.47408 1.87453 4.53814 2.40646 3.74206C2.93838 2.94598 3.69443 2.32551 4.57899 1.95911C5.46355 1.59271 6.43689 1.49685 7.37594 1.68363C8.31498 1.87042 9.17755 2.33147 9.85456 3.00849C10.5316 3.6855 10.9926 4.54807 11.1794 5.48711C11.3662 6.42615 11.2703 7.3995 10.9039 8.28406C10.5375 9.16862 9.91707 9.92466 9.12099 10.4566C8.3249 10.9885 7.38896 11.2724 6.43152 11.2724C5.14807 11.271 3.9176 10.7605 3.01006 9.85299C2.10252 8.94545 1.59204 7.71497 1.59062 6.43152Z" fill="#787878" />
                                </svg>
                            </template>
                        </UiField>
                        <div class="font-medium text-[#484964]">
                            Доход за весь период: 1 280 000 ₽
                        </div>
                    </form>

                    <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                        <div class="min-w-[1136px] text-xs">
                            <div
                                class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2"></div>
                                <div
                                    :class="{ 'bg-violet/5': true }"
                                    class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2  cursor-pointer"
                                >
                                    Оборудование
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08"/>
                                        <path d="M8.26783 12.1904L10.6249 9.83325L12.9821 12.1904" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10.625 9.8333L10.625 18.0832" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M19.5654 16.5594L17.2083 18.9165L14.8511 16.5594" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17.2082 10.6667L17.2082 18.9166" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2"
                                >
                                    Дата отгрузки
                                </div>
                                <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">Комментарий</div>
                                <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Доход</div>
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
                            <AccordionRoot type="multiple" :collapsible="true">
                                <AccordionItem v-for="equipment in rented_equipment.data" value="item-1">
                                    <AccordionHeader>
                                        <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                                <UiHyperlink endpoint="/services" />
                                            </div>
                                            <div
                                                :class="{ 'bg-violet/5': true }"
                                                class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2"
                                            >
                                                {{ equipment.id }}
                                                <AccordionTrigger class="flex items-center py-1 px-2 ml-2 rounded-full border border-[#AD9FFF]">
                                                    <svg class="block" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M3.375 6C5.625 10.875 12.375 10.875 14.625 6" stroke="#644DED"
                                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12.6122 8.4881L14.625 11.25" stroke="#644DED" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M9 9.65625V12.375" stroke="#644DED" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M5.38787 8.4881L3.375 11.25" stroke="#644DED" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <svg  class="block ml-1 transition-all" width="18" height="18" viewBox="0 0 18 18"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.5 12L7.31802 8.81802L10.5 5.63604" stroke="#644DED"
                                                            stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </AccordionTrigger>
                                            </div>
                                            <div
                                                class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2"
                                            >12.12.2024</div>
                                            <div
                                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                                                Somesome</div>
                                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">12332 ₽
                                            </div>
                                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                                <PopoverRoot>
                                                    <PopoverTrigger class="shrink-0 flex items-center justify-center mr-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
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
                                                <!-- <Link 
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
                                                </Link> -->

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
                                                                    <Link :href="route('services.edit', 12)"
                                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Редактировать
                                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M13.0982 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.031 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1828 3.96906 10.2233 3.74946C10.2638 3.52985 10.4731 3.32052 10.8918 2.90186L11.1184 2.67522C11.5371 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7435 4.46297 13.3248 4.88162L13.0982 5.10827Z"
                                                                            fill="#464F60" />
                                                                        <path
                                                                            d="M2.95409 13.9107C2.45423 14.0029 2.2043 14.049 2.07766 13.9224C1.95102 13.7957 1.99712 13.5458 2.08933 13.0459L2.31178 11.84C2.35176 11.6233 2.37175 11.515 2.43008 11.4101C2.48841 11.3052 2.57916 11.2145 2.76067 11.033L8.31441 5.47921C8.73306 5.06056 8.94239 4.85123 9.162 4.81072C9.38161 4.77021 9.52573 4.91433 9.81399 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9395 7.26697 10.5208 7.68562L4.96708 13.2394C4.78557 13.4209 4.69482 13.5116 4.58994 13.5699C4.48507 13.6283 4.37671 13.6483 4.16 13.6882L2.95409 13.9107Z"
                                                                            fill="#464F60" />
                                                                    </svg>
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem>
                                                                    <Link :href="route('services.destroy', 12)" method="delete"
                                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить
                                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="#DA1E28" />
                                                                    </svg>
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                            </DropdownMenuContent>
                                                        </transition>
                                                    </DropdownMenuPortal>
                                                </DropdownMenuRoot>
                                            </div>
                                        </div>
                                    </AccordionHeader>
                                    <AccordionContent class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden">
                                        <div
                                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                                <UiHyperlink />
                                            </div>
                                            <div
                                                :class="{ 'bg-violet/5': true }"
                                                class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2 !border-l-violet-full"
                                            >
                                                Аренда №123
                                            </div>
                                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">20.12.2024</div>
                                            <div
                                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                                                asdasdas</div>
                                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">12333 ₽</div>
                                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                                <PopoverRoot>
                                                    <PopoverTrigger class="shrink-0 flex items-center justify-center mr-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
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
                                                                    <Link :href="route('services.edit', 2)"
                                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Редактировать
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M13.0982 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.031 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1828 3.96906 10.2233 3.74946C10.2638 3.52985 10.4731 3.32052 10.8918 2.90186L11.1184 2.67522C11.5371 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7435 4.46297 13.3248 4.88162L13.0982 5.10827Z"
                                                                            fill="#464F60" />
                                                                        <path
                                                                            d="M2.95409 13.9107C2.45423 14.0029 2.2043 14.049 2.07766 13.9224C1.95102 13.7957 1.99712 13.5458 2.08933 13.0459L2.31178 11.84C2.35176 11.6233 2.37175 11.515 2.43008 11.4101C2.48841 11.3052 2.57916 11.2145 2.76067 11.033L8.31441 5.47921C8.73306 5.06056 8.94239 4.85123 9.162 4.81072C9.38161 4.77021 9.52573 4.91433 9.81399 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9395 7.26697 10.5208 7.68562L4.96708 13.2394C4.78557 13.4209 4.69482 13.5116 4.58994 13.5699C4.48507 13.6283 4.37671 13.6483 4.16 13.6882L2.95409 13.9107Z"
                                                                            fill="#464F60" />
                                                                    </svg>
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem>
                                                                    <Link :href="route('services.destroy', 2)"
                                                                        methods="delete"
                                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="#DA1E28" />
                                                                    </svg>
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                            </DropdownMenuContent>
                                                        </transition>
                                                    </DropdownMenuPortal>
                                                </DropdownMenuRoot>
                                            </div>
                                        </div>
                                    </AccordionContent>
                                </AccordionItem>

                            </AccordionRoot>
                        </div>
                    </div>

                    <Pagination :links="[1,2,3]" current-page="1" total-pages="2" total-count="23" class="mt-6 bg-bg1" />
                </div>
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>