<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { MenuItem, MenuItems, Menu, MenuButton } from '@headlessui/vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, reactive, ref, toRaw, watch } from 'vue';
import store from '../../../store/index';
import ServicesNav from "@/Components/Services/ServicesNav.vue";
import UiField from "@/Components/Ui/UiField.vue";
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
} from 'radix-vue';
import { PopoverAnchor, PopoverArrow, PopoverClose, PopoverContent, PopoverPortal, PopoverRoot, PopoverTrigger } from 'radix-vue'
import Pagination from "@/Components/Pagination.vue";
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";

const props = defineProps({
    services: Object,
    activeServices: Object,
    contragents_names: Object,
    contragents: Array,
    count_services_active: Number,
    count_services_inactive: Number,
    formatted_data_months: Object,
    groupedServices: Object
});



const months = {
    all: 'Все',
    jan: 'Январь',
    feb: 'Февраль',
    mar: 'Март',
    apr: 'Апрель',
    may: 'Май',
    jun: 'Июнь',
    jul: 'Июль',
    aug: 'Август',
    sep: 'Сентябрь',
    oct: 'Октябрь',
    nov: 'Ноябрь',
    dec: 'Декабрь',
};

const form = reactive({
    'hyperlink': null
});

const start_date = ref(null);
const end_date = ref(null);
const date_sort = ref(null);

const minusYear = () => {
    store.dispatch('services/updateDecSelectedYear');
}
const plusYear = () => {
    store.dispatch('services/updateIncSelectedYear');
}

const filterDatesActive = computed(() => {
    const raw = toRaw(props.activeServices.data);

    return raw.sort((a, b) => {
            if (date_sort.value === 'asc') {
                return new Date(a.service_date) - new Date(b.service_date);
            } else {
                return new Date(b.service_date) - new Date(a.service_date);
            }
           }).filter(service => {
                const serviceDate = new Date(service.service_date);

                if (!!start_date.value || !!end_date.value) return service.service_date > start_date.value && service.service_date < end_date.value;

                if (!start_date.value && !end_date.value) {
                    const isYearMatch = serviceDate.getFullYear() == getYear.value;

                    const isMonthMatch = getMonth.value === 'all' || serviceDate.getMonth() === getMonthNumber(getMonth.value)

                    const isActive = service.active === 1;
                    return isYearMatch && isMonthMatch && isActive;
                }
            });
});

const filterDatesInActive = computed(() => {
    const raw = toRaw(props.services.data)

    return raw.sort((a, b) => {
            if (date_sort.value === 'desc') {
                return new Date(b.service_date) - new Date(a.service_date);
            } else {
                return new Date(a.service_date) - new Date(b.service_date);
            }
           }).filter(service => {
                const serviceDate = new Date(service.service_date);

                if (!!start_date.value && !!end_date.value) {
                    return service.service_date > start_date.value && service.service_date < end_date.value;
                } else {
                    const isYearMatch = serviceDate.getFullYear() == getYear.value;

                    const isMonthMatch = getMonth.value === 'all' || serviceDate.getMonth() === getMonthNumber(getMonth.value)

                    const isActive = service.active === 0;
                    return isYearMatch && isMonthMatch && isActive;
                }
            });
})

const getMonthNumber = (month) => {
    const months = {
        'jan': 0,
        'feb': 1,
        'mar': 2,
        'apr': 3,
        'may': 4,
        'jun': 5,
        'jul': 6,
        'aug': 7,
        'sept': 8,
        'oct': 9,
        'novb': 10,
        'dec': 11
    };
    return months[month] ?? null; // Return null if no match found
}

const updateMonth = (month) => {
    store.dispatch('services/updateSelectedMonth', month)
}
const showSubservices = ref({});
const subservicesEls = ref([]);
const subservicesHeights = ref({});

// Toggle subservices display
const toggleSubservice = (index) => {
    showSubservices.value[index] = !showSubservices.value[index];

    if (showSubservices.value[index]) {
        nextTick(() => {
            subservicesHeights.value[index] = subservicesEls.value[index].scrollHeight;

            subservicesEls.value[index].style.height = subservicesHeights.value[index] + 'px';
        });
    } else {
        subservicesEls.value[index].style.height = '0px';
    }
};

const nameContragent = (contragents_names, id) => {
    return contragents_names[id]
}

const getMonth = computed(() => store.getters['services/getSelectedMonth']);
const getYear = computed(() => store.getters['services/getSelectedYear']);
const selectedActive = computed(() => store.getters['services/getSelectedActive']);

function submitHyperlink(id, data) {
    router.post(`/services/${id}/hyperlink`, {
        'hyperlink': data
    });
}

watch(() => props.services.current_page, () => {
    subservicesEls.value = [];
    subservicesHeights.value = {};
});

</script>

<template>
    <AuthenticatedLayout>
        <ServicesNav
            v-model:start-date="start_date"
            v-model:end-date="end_date"
            :count-services-active="props.count_services_active"
            :count-services-inactive="props.count_services_inactive"
        />

        <div class="p-5">
            <div class="relative mt-5 text-sm">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul
                    class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto lg:overflow-x-visible">
                    <li v-for="(value, key) in months" :class="{ '!border-[#001D6C] text-[#001D6C]': getMonth === key }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        @click="updateMonth(key)">
                        {{ value }}
                    </li>
                </ul>
            </div>

            <div class="flex items-center justify-between mt-6">
                <h3 class="font-bold text-gray1">Список {{ selectedActive ? 'открытых' : 'закрытых' }} аренд:</h3>

                <div class="flex">
                    <button type="button" class="flex" @click="minusYear">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_2051_7507)">
                                <rect x="24" width="24" height="24" rx="5" transform="rotate(90 24 0)" fill="#F7F9FC" />
                                <path
                                    d="M10.686 11.929L15.636 16.879C15.8182 17.0676 15.919 17.3202 15.9167 17.5824C15.9144 17.8446 15.8092 18.0954 15.6238 18.2808C15.4384 18.4662 15.1876 18.5714 14.9254 18.5737C14.6632 18.576 14.4106 18.4752 14.222 18.293L8.565 12.636C8.37753 12.4485 8.27221 12.1942 8.27221 11.929C8.27221 11.6639 8.37753 11.4095 8.565 11.222L14.222 5.56502C14.4106 5.38286 14.6632 5.28207 14.9254 5.28434C15.1876 5.28662 15.4384 5.39179 15.6238 5.5772C15.8092 5.76261 15.9144 6.01342 15.9167 6.27562C15.919 6.53781 15.8182 6.79042 15.636 6.97902L10.686 11.929Z"
                                    fill="#697077" />
                            </g>
                            <rect x="23.5" y="0.5" width="23" height="23" rx="4.5" transform="rotate(90 23.5 0.5)"
                                stroke="#697077" />
                            <defs>
                                <clipPath id="clip0_2051_7507">
                                    <rect x="24" width="24" height="24" rx="5" transform="rotate(90 24 0)"
                                        fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                    </button>
                    <p class="mx-2.5">{{ getYear }}</p>
                    <button type="button" class="flex" @click="plusYear">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_2051_7512)">
                                <rect y="24" width="24" height="24" rx="5" transform="rotate(-90 0 24)"
                                    fill="#F7F9FC" />
                                <path
                                    d="M13.314 12.071L8.36399 7.12098C8.18184 6.93238 8.08104 6.67978 8.08332 6.41758C8.0856 6.15538 8.19077 5.90457 8.37618 5.71916C8.56158 5.53375 8.8124 5.42859 9.07459 5.42631C9.33679 5.42403 9.58939 5.52482 9.77799 5.70698L15.435 11.364C15.6225 11.5515 15.7278 11.8058 15.7278 12.071C15.7278 12.3361 15.6225 12.5905 15.435 12.778L9.77799 18.435C9.58939 18.6171 9.33679 18.7179 9.07459 18.7157C8.8124 18.7134 8.56158 18.6082 8.37618 18.4228C8.19077 18.2374 8.0856 17.9866 8.08332 17.7244C8.08104 17.4622 8.18184 17.2096 8.36399 17.021L13.314 12.071Z"
                                    fill="#697077" />
                            </g>
                            <rect x="0.5" y="23.5" width="23" height="23" rx="4.5" transform="rotate(-90 0.5 23.5)"
                                stroke="#697077" />
                            <defs>
                                <clipPath id="clip0_2051_7512">
                                    <rect y="24" width="24" height="24" rx="5" transform="rotate(-90 0 24)"
                                        fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                    </button>
                </div>
            </div>

            <template v-if="selectedActive ? filterDatesActive.length : filterDatesInActive.length">
                <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                    <div class="min-w-[1136px] text-xs">
                        <div
                            class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2"></div>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">Заказчик</div>
                            <div
                                :class="{ 'bg-violet/5': !!date_sort }"
                                class="shrink-0 flex items-center justify-between w-[14.08%] py-2.5 px-2 cursor-pointer"
                                @click="date_sort === 'asc' ? date_sort = 'desc' : date_sort = 'asc'"
                            >
                                Дата отгрузки
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08"/>
                                    <path d="M8.26783 12.1904L10.6249 9.83325L12.9821 12.1904" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10.625 9.8333L10.625 18.0832" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M19.5654 16.5594L17.2083 18.9165L14.8511 16.5594" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17.2082 10.6667L17.2082 18.9166" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
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
                        <template v-for="(service, index) in selectedActive ? filterDatesActive : filterDatesInActive" :key="service.id">
                            <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                    <UiHyperlink :hyperlink="service.hyperlink" :item-id="service.id" endpoint="/services" />
                                </div>
                                <div class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2">
                                    {{ props.contragents_names[service.contragent_id] ?? '-' }}
                                    <button v-if="service.subservices.length" type="button"
                                        class="flex items-center py-1 px-2 ml-2 rounded-full border border-[#AD9FFF]"
                                        @click="toggleSubservice(index)">
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
                                        <svg :class="{ '-rotate-90': showSubservices[index] }"
                                            class="block ml-1 transition-all" width="18" height="18" viewBox="0 0 18 18"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.5 12L7.31802 8.81802L10.5 5.63604" stroke="#644DED"
                                                stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                                <div
                                    :class="{ 'bg-violet/5': !!date_sort }"
                                    class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2"
                                >{{ service.shipping_date ?? '-' }}</div>
                                <div
                                    class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                                    {{ service.commentary
                                        ?? '-' }}</div>
                                <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">{{ service.income ?? '-' }} ₽
                                </div>
                                <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                    <Link :href="route('directory.index', { type: 'services', id: service.id })"
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
                                                        <button type="button"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                            @click="toggleSubservice(index)">
                                                            {{ showSubservices[index] ? 'Скрыть' : 'Отобразить' }}
                                                            оборудование
                                                            <svg class="block ml-2" width="16" height="16"
                                                                viewBox="0 0 16 16" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z"
                                                                    fill="#464F60" />
                                                            </svg>
                                                        </button>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <Link :href="route('services.edit', service.id)"
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
                                                        <Link :href="route('services.destroy', service.id)" method="delete"
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

                            <div v-if="service.subservices.length" ref="subservicesEls" style="transition: .3s;"
                                class="h-0 overflow-hidden">
                                <div
                                    class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                    <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                        <PopoverRoot>
                                            <PopoverTrigger>
                                                <svg v-if="service.hyperlink" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333"
                                                        stroke="#644DED" stroke-width="1.2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M14 6H18V9.99997" stroke="#644DED" stroke-width="1.2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M10.6667 13.3333L18 6" stroke="#644DED" stroke-width="1.2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333"
                                                        stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </PopoverTrigger>
                                            <PopoverPortal>
                                                <PopoverContent align="start"
                                                    class="min-w-[137px] py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]">
                                                    <template v-if="service.hyperlink">
                                                        <ul>
                                                            <li>
                                                                <Link :href="service.hyperlink"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                Открыть
                                                                <svg class="block ml-2" width="16" height="16"
                                                                    viewBox="0 0 16 16" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z"
                                                                        fill="#464F60" />
                                                                </svg>
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <PopoverRoot>
                                                                    <PopoverTrigger
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
                                                                    </PopoverTrigger>
                                                                    <PopoverPortal>
                                                                        <PopoverContent align="start"
                                                                            class="min-w-[137px] py-4 px-3 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]">
                                                                            <UiField v-model="form.hyperlink"
                                                                                :inp-attrs="{ placeholder: 'www.site.ru' }"
                                                                                size="sm" />
                                                                            <PopoverClose>
                                                                                <button
                                                                                    class="inline-block mt-2 font-bold text-xs text-violet-full"
                                                                                    @click="submitHyperlink(service.id, form.hyperlink)">Сохранить</button>
                                                                            </PopoverClose>
                                                                        </PopoverContent>
                                                                    </PopoverPortal>
                                                                </PopoverRoot>
                                                            </li>
                                                            <li>
                                                                <Link :href="item.hyperlink" method="delete"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                Удалить
                                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                        fill="#DC4067" />
                                                                </svg>
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </template>
                                                    <template v-else>
                                                        <UiField v-model="form.hyperlink"
                                                            :inp-attrs="{ placeholder: 'www.site.ru' }" size="sm" />
                                                        <PopoverClose>
                                                            <button
                                                                class="inline-block mt-2 font-bold text-xs text-violet-full"
                                                                @click="submitHyperlink(item.id, form.hyperlink)">Сохранить</button>
                                                        </PopoverClose>
                                                    </template>
                                                </PopoverContent>
                                            </PopoverPortal>
                                        </PopoverRoot>
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                        Аренда №{{ service.service_number ?? '-' }}
                                    </div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">{{
                                        service.shipping_date ?? '-' }}</div>
                                    <div
                                        class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                                        {{
                                            service.commentary ?? '-' }}</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">{{ service.income ??
                                        '-' }} ₽</div>
                                    <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                        <Link :href="route('directory.index', { type: 'services', id: service.id })"
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
                                                            <Link :href="route('services.edit', service.id)"
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
                                                            <Link :href="route('services.destroy', service.id)"
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
                            </div>
                        </template>
                    </div>
                </div>

                <pagination
                    ref="pagination_el"
                    :current-page="selectedActive ? props.activeServices.current_page : props.services.current_page"
                    :total-pages="selectedActive ? props.activeServices.last_page : props.services.last_page"
                    :total-count="selectedActive ? count_services_active : count_services_inactive"
                    :next-page-url="selectedActive ? props.activeServices.next_page_url : props.services.next_page_url"
                    :links="selectedActive ? props.activeServices.links : props.services.links"
                    :prev-page-url="selectedActive ? props.activeServices.prev_page_url : props.services.prev_page_url"
                    class="mt-5 bg-bg1"
                />
            </template>
        </div>

        <!--        <div class="flex-1  sm:w-full">
            <div class="lg:hidden md:hidden flex bg-my-gray mt-4 p-4">

                <select v-model="seriesActive" class="text-gray-500 border-gray-200" name="" id="">
                    <option value="">Номер</option>
                    <option v-for="series in equipment_series" @click="setSeriesId(series)" value="">{{ series }}
                    </option>
                </select>
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
                <form action="" method="GET" class="max-w-md">
                    <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" name="search" id="default-search"
                               class="block w-full ps-10 text-sm text-gray-900 border-b-2 border-gray-200 border-t-0 border-l-0 border-r-0 bg-white-50 py-4"
                               placeholder="Поиск">
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto lg:px-4 pb-20">
                <div class="min-w-full whitespace-nowrap grid grid-cols-5 gap-52 bg-gray-100 font-bold p-2 rounded-md">
                    <div>Заказчик</div>
                    <div>Дата отгрузки</div>
                    <div>Комментарий</div>
                    <div>Доход</div>
                    <div>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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

                &lt;!&ndash; Table body &ndash;&gt;
                <div v-if="filterDatesActive().length && selectedActive" class="">
                    <div v-for="(service, index) in filterDatesActive()" :key="service.id"
                         class=" border-b border-gray-200 p-2">
                        &lt;!&ndash; Main service row &ndash;&gt;
                        <div class="min-w-full grid grid-cols-5 gap-52 items-center whitespace-nowrap">
                            &lt;!&ndash; Service Number with toggle button using SVG &ndash;&gt;
                            <div class="flex items-center space-x-2">
                <span>{{ service.equipment_info.category.name }} {{ service.equipment_info.size.name }} {{
                        service.equipment_info.series
                    }}</span>
                                <button @click="toggleSubservice(index)">
                                    <svg v-if="!showSubservices[index]" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                              fill="#242533"/>
                                    </svg>

                                    <svg v-if="showSubservices[index]" width="18" height="18" viewBox="0 0 18 18"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M2.27174 9.29127C2.27174 8.9599 2.54037 8.69127 2.87174 8.69127H14.6283C14.9596 8.69127 15.2283 8.9599 15.2283 9.29127C15.2283 9.62265 14.9596 9.89127 14.6283 9.89127H2.87174C2.54037 9.89127 2.27174 9.62265 2.27174 9.29127Z"
                                              fill="#242533"/>
                                    </svg>
                                </button>
                            </div>
                            <div>{{ service.shipping_date ?? "Не задано" }}</div>
                            <div>{{ service.commentary ?? "Не задано" }}</div>
                            <div>{{ service.income ?? "Не задано" }}</div>
                            &lt;!&ndash; Actions column (empty for now) &ndash;&gt;
                            <div class="">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                          fill="#21272A"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                          fill="#21272A"/>
                                </svg>

                                <Menu as="div" class="relative inline-block text-left">
                                    <div class="sm:overflow-x-visible">
                                        <MenuButton
                                            class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                            <svg width="4" height="16" viewBox="0 0 4 16" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M3.5 2C3.5 2.82843 2.82843 3.5 2 3.5C1.17157 3.5 0.5 2.82843 0.5 2C0.5 1.17157 1.17157 0.5 2 0.5C2.82843 0.5 3.5 1.17157 3.5 2Z"
                                                    fill="#687182"/>
                                                <path
                                                    d="M3.5 8C3.5 8.82843 2.82843 9.5 2 9.5C1.17157 9.5 0.5 8.82843 0.5 8C0.5 7.17157 1.17157 6.5 2 6.5C2.82843 6.5 3.5 7.17157 3.5 8Z"
                                                    fill="#687182"/>
                                                <path
                                                    d="M3.5 14C3.5 14.8284 2.82843 15.5 2 15.5C1.17157 15.5 0.5 14.8284 0.5 14C0.5 13.1716 1.17157 12.5 2 12.5C2.82843 12.5 3.5 13.1716 3.5 14Z"
                                                    fill="#687182"/>
                                            </svg>
                                        </MenuButton>
                                    </div>

                                    <transition enter-active-class="transition ease-out duration-100"
                                                enter-from-class="transform opacity-0 scale-95"
                                                enter-to-class="transform opacity-100 scale-100"
                                                leave-active-class="transition ease-in duration-75"
                                                leave-from-class="transform opacity-100 scale-100"
                                                leave-to-class="transform opacity-0 scale-95">
                                        <MenuItems
                                            class="absolute top-6 z-200 right-2 justify-between w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                            <div class="py-1">


                                                <MenuItem>
                                                    <Link method="get" :href="route('services.edit', service.id)"
                                                          class="flex items-center justify-around"
                                                          :class="['block px-4 py-2 text-sm']">Редактировать
                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11.0981 3.10827C10.6795 3.52693 10.4702 3.73626 10.2506 3.77677C10.0309 3.81728 9.88682 3.67315 9.59856 3.3849L8.61511 2.40144C8.32686 2.11319 8.18273 1.96906 8.22324 1.74946C8.26375 1.52985 8.47308 1.32052 8.89174 0.901864L9.11838 0.675215C9.53704 0.25656 9.74637 0.0472318 9.96598 0.00672082C10.1856 -0.0337902 10.3297 0.110336 10.618 0.398589L11.6014 1.38204C11.8897 1.6703 12.0338 1.81442 11.9933 2.03403C11.9528 2.25364 11.7434 2.46297 11.3248 2.88162L11.0981 3.10827Z"
                                                                fill="#464F60"/>
                                                            <path
                                                                d="M0.954058 11.9107C0.454198 12.0029 0.204269 12.049 0.077628 11.9224C-0.0490128 11.7957 -0.00290857 11.5458 0.0893002 11.0459L0.311753 9.84003C0.35173 9.62332 0.371719 9.51497 0.43005 9.41009C0.488381 9.30521 0.579134 9.21446 0.76064 9.03295L6.31438 3.47921C6.73303 3.06056 6.94236 2.85123 7.16197 2.81072C7.38158 2.77021 7.5257 2.91433 7.81396 3.20259L8.79741 4.18604C9.08566 4.4743 9.22979 4.61842 9.18928 4.83803C9.14877 5.05764 8.93944 5.26697 8.52078 5.68562L2.96705 11.2394C2.78554 11.4209 2.69479 11.5116 2.58991 11.5699C2.48503 11.6283 2.37668 11.6483 2.15997 11.6882L0.954058 11.9107Z"
                                                                fill="#464F60"/>
                                                        </svg>


                                                    </Link>
                                                </MenuItem>

                                                <MenuItem>
                                                    <Link method="delete" :href="route('services.destroy', service.id)"
                                                          class="flex items-center justify-around"
                                                          :class="['block px-4 py-2 text-sm']">Удалить
                                                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                  d="M2.75 2.75V3.25H0.75C0.335786 3.25 0 3.58579 0 4C0 4.41421 0.335786 4.75 0.75 4.75H1.51389L1.89504 11.6109C1.95392 12.6708 2.8305 13.5 3.89196 13.5H8.10802C9.16948 13.5 10.0461 12.6708 10.1049 11.6109L10.4861 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H9.25V2.75C9.25 1.50736 8.24264 0.5 7 0.5H5C3.75736 0.5 2.75 1.50736 2.75 2.75ZM5 2C4.58579 2 4.25 2.33579 4.25 2.75V3.25H7.75V2.75C7.75 2.33579 7.41421 2 7 2H5ZM5.25 6.75C5.25 6.33579 4.91421 6 4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V11.25C3.75 11.6642 4.08579 12 4.5 12C4.91421 12 5.25 11.6642 5.25 11.25V6.75ZM8.25 6.75C8.25 6.33579 7.91421 6 7.5 6C7.08579 6 6.75 6.33579 6.75 6.75V11.25C6.75 11.6642 7.08579 12 7.5 12C7.91421 12 8.25 11.6642 8.25 11.25V6.75Z"
                                                                  fill="#DC4067"/>
                                                        </svg>


                                                    </Link>
                                                </MenuItem>
                                                <MenuItem>
                                                    <Link method="post"
                                                          :href="route('services.createIncident', service.id)"
                                                          class="flex items-center justify-around"
                                                          :class="['block px-4 py-2 text-sm']">Инциндент
                                                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                  d="M2.75 2.75V3.25H0.75C0.335786 3.25 0 3.58579 0 4C0 4.41421 0.335786 4.75 0.75 4.75H1.51389L1.89504 11.6109C1.95392 12.6708 2.8305 13.5 3.89196 13.5H8.10802C9.16948 13.5 10.0461 12.6708 10.1049 11.6109L10.4861 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H9.25V2.75C9.25 1.50736 8.24264 0.5 7 0.5H5C3.75736 0.5 2.75 1.50736 2.75 2.75ZM5 2C4.58579 2 4.25 2.33579 4.25 2.75V3.25H7.75V2.75C7.75 2.33579 7.41421 2 7 2H5ZM5.25 6.75C5.25 6.33579 4.91421 6 4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V11.25C3.75 11.6642 4.08579 12 4.5 12C4.91421 12 5.25 11.6642 5.25 11.25V6.75ZM8.25 6.75C8.25 6.33579 7.91421 6 7.5 6C7.08579 6 6.75 6.33579 6.75 6.75V11.25C6.75 11.6642 7.08579 12 7.5 12C7.91421 12 8.25 11.6642 8.25 11.25V6.75Z"
                                                                  fill="#DC4067"/>
                                                        </svg>


                                                    </Link>
                                                </MenuItem>

                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>

                            </div>
                        </div>

                        &lt;!&ndash; Subservices (shown when expanded) &ndash;&gt;
                        <div v-if="showSubservices[index]" class="mt-2">
                            <div class="" v-if="service.subservices.length > 0">
                                <div v-for="subservice in service.subservices" :key="subservice.id"
                                     class="grid whitespace-nowrap grid-cols-5 gap-52 bg-gray-50 py-2 rounded-md">
                                    <div>{{ subservice.equipment.category.name }} {{ subservice.equipment.size.name }}
                                        {{
                                            subservice.equipment.series
                                        }}
                                    </div>
                                    <div>{{ subservice.shipping_date }}</div>
                                    <div>{{ subservice.commentary ?? "Не задано" }}</div>
                                    <div>{{ subservice.income ?? "Не задано" }}</div>
                                    <div></div>
                                </div>
                            </div>
                            <div v-else class="p-2 bg-red-50 text-red-500">
                                Нет добавленного оборудования
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="filterDatesInActive().length && !selectedActive" class="">
                    <div v-for="(service, index) in filterDatesInActive()" :key="service.id"
                         class=" border-b border-gray-200 p-2">
                        <div class="min-w-full grid grid-cols-5 gap-52 items-center whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                <span>{{ service.equipment_info.category.name }} {{ service.equipment_info.size.name }} {{
                        service.equipment_info.series
                    }}</span>
                                <button @click="toggleSubservice(index)">
                                    <svg v-if="!showSubservices[index]" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                              fill="#242533"/>
                                    </svg>

                                    <svg v-if="showSubservices[index]" width="18" height="18" viewBox="0 0 18 18"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M2.27174 9.29127C2.27174 8.9599 2.54037 8.69127 2.87174 8.69127H14.6283C14.9596 8.69127 15.2283 8.9599 15.2283 9.29127C15.2283 9.62265 14.9596 9.89127 14.6283 9.89127H2.87174C2.54037 9.89127 2.27174 9.62265 2.27174 9.29127Z"
                                              fill="#242533"/>
                                    </svg>
                                </button>
                            </div>
                            <div>{{ service.shipping_date }}</div>
                            <div>{{ service.commentary }}</div>
                            <div>{{ service.income ?? "Не задано" }}</div>
                            <div class="flex justify-center items-center space-x-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                          fill="#21272A"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M4.65799 7.03952C4.91229 6.71256 5.3835 6.65366 5.71046 6.90796L12 11.7998L18.2895 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5963 7.36648 19.5374 7.83769 19.2105 8.09199L12.4605 13.342C12.1896 13.5526 11.8104 13.5526 11.5395 13.342L4.78955 8.09199C4.46259 7.83769 4.40369 7.36648 4.65799 7.03952Z"
                                          fill="#21272A"/>
                                </svg>
                                <Menu as="div" class="relative inline-block text-left">
                                    <div class="sm:overflow-x-visible">
                                        <MenuButton
                                            class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                            <svg width="4" height="16" viewBox="0 0 4 16" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M3.5 2C3.5 2.82843 2.82843 3.5 2 3.5C1.17157 3.5 0.5 2.82843 0.5 2C0.5 1.17157 1.17157 0.5 2 0.5C2.82843 0.5 3.5 1.17157 3.5 2Z"
                                                    fill="#687182"/>
                                                <path
                                                    d="M3.5 8C3.5 8.82843 2.82843 9.5 2 9.5C1.17157 9.5 0.5 8.82843 0.5 8C0.5 7.17157 1.17157 6.5 2 6.5C2.82843 6.5 3.5 7.17157 3.5 8Z"
                                                    fill="#687182"/>
                                                <path
                                                    d="M3.5 14C3.5 14.8284 2.82843 15.5 2 15.5C1.17157 15.5 0.5 14.8284 0.5 14C0.5 13.1716 1.17157 12.5 2 12.5C2.82843 12.5 3.5 13.1716 3.5 14Z"
                                                    fill="#687182"/>
                                            </svg>
                                        </MenuButton>
                                    </div>

                                    <transition enter-active-class="transition ease-out duration-100"
                                                enter-from-class="transform opacity-0 scale-95"
                                                enter-to-class="transform opacity-100 scale-100"
                                                leave-active-class="transition ease-in duration-75"
                                                leave-from-class="transform opacity-100 scale-100"
                                                leave-to-class="transform opacity-0 scale-95">
                                        <MenuItems
                                            class="absolute top-6 z-200 right-2 justify-between w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                            <div class="py-1">


                                                <MenuItem>
                                                    <Link method="delete" :href="route('services.edit', service.id)"
                                                          class="flex items-center justify-around"
                                                          :class="['block px-4 py-2 text-sm']">Редактировать
                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11.0981 3.10827C10.6795 3.52693 10.4702 3.73626 10.2506 3.77677C10.0309 3.81728 9.88682 3.67315 9.59856 3.3849L8.61511 2.40144C8.32686 2.11319 8.18273 1.96906 8.22324 1.74946C8.26375 1.52985 8.47308 1.32052 8.89174 0.901864L9.11838 0.675215C9.53704 0.25656 9.74637 0.0472318 9.96598 0.00672082C10.1856 -0.0337902 10.3297 0.110336 10.618 0.398589L11.6014 1.38204C11.8897 1.6703 12.0338 1.81442 11.9933 2.03403C11.9528 2.25364 11.7434 2.46297 11.3248 2.88162L11.0981 3.10827Z"
                                                                fill="#464F60"/>
                                                            <path
                                                                d="M0.954058 11.9107C0.454198 12.0029 0.204269 12.049 0.077628 11.9224C-0.0490128 11.7957 -0.00290857 11.5458 0.0893002 11.0459L0.311753 9.84003C0.35173 9.62332 0.371719 9.51497 0.43005 9.41009C0.488381 9.30521 0.579134 9.21446 0.76064 9.03295L6.31438 3.47921C6.73303 3.06056 6.94236 2.85123 7.16197 2.81072C7.38158 2.77021 7.5257 2.91433 7.81396 3.20259L8.79741 4.18604C9.08566 4.4743 9.22979 4.61842 9.18928 4.83803C9.14877 5.05764 8.93944 5.26697 8.52078 5.68562L2.96705 11.2394C2.78554 11.4209 2.69479 11.5116 2.58991 11.5699C2.48503 11.6283 2.37668 11.6483 2.15997 11.6882L0.954058 11.9107Z"
                                                                fill="#464F60"/>
                                                        </svg>


                                                    </Link>
                                                </MenuItem>

                                                <MenuItem>
                                                    <Link method="delete" :href="route('services.destroy', service.id)"
                                                          class="flex items-center justify-around"
                                                          :class="['block px-4 py-2 text-sm']">Удалить
                                                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                  d="M2.75 2.75V3.25H0.75C0.335786 3.25 0 3.58579 0 4C0 4.41421 0.335786 4.75 0.75 4.75H1.51389L1.89504 11.6109C1.95392 12.6708 2.8305 13.5 3.89196 13.5H8.10802C9.16948 13.5 10.0461 12.6708 10.1049 11.6109L10.4861 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H9.25V2.75C9.25 1.50736 8.24264 0.5 7 0.5H5C3.75736 0.5 2.75 1.50736 2.75 2.75ZM5 2C4.58579 2 4.25 2.33579 4.25 2.75V3.25H7.75V2.75C7.75 2.33579 7.41421 2 7 2H5ZM5.25 6.75C5.25 6.33579 4.91421 6 4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V11.25C3.75 11.6642 4.08579 12 4.5 12C4.91421 12 5.25 11.6642 5.25 11.25V6.75ZM8.25 6.75C8.25 6.33579 7.91421 6 7.5 6C7.08579 6 6.75 6.33579 6.75 6.75V11.25C6.75 11.6642 7.08579 12 7.5 12C7.91421 12 8.25 11.6642 8.25 11.25V6.75Z"
                                                                  fill="#DC4067"/>
                                                        </svg>


                                                    </Link>
                                                </MenuItem>

                                                <MenuItem>
                                                    <Link method="post"
                                                          :href="route('services.createIncident', service.id)"
                                                          class="flex items-center justify-around"
                                                          :class="['block px-4 py-2 text-sm']">Инциндент
                                                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                  d="M2.75 2.75V3.25H0.75C0.335786 3.25 0 3.58579 0 4C0 4.41421 0.335786 4.75 0.75 4.75H1.51389L1.89504 11.6109C1.95392 12.6708 2.8305 13.5 3.89196 13.5H8.10802C9.16948 13.5 10.0461 12.6708 10.1049 11.6109L10.4861 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H9.25V2.75C9.25 1.50736 8.24264 0.5 7 0.5H5C3.75736 0.5 2.75 1.50736 2.75 2.75ZM5 2C4.58579 2 4.25 2.33579 4.25 2.75V3.25H7.75V2.75C7.75 2.33579 7.41421 2 7 2H5ZM5.25 6.75C5.25 6.33579 4.91421 6 4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V11.25C3.75 11.6642 4.08579 12 4.5 12C4.91421 12 5.25 11.6642 5.25 11.25V6.75ZM8.25 6.75C8.25 6.33579 7.91421 6 7.5 6C7.08579 6 6.75 6.33579 6.75 6.75V11.25C6.75 11.6642 7.08579 12 7.5 12C7.91421 12 8.25 11.6642 8.25 11.25V6.75Z"
                                                                  fill="#DC4067"/>
                                                        </svg>


                                                    </Link>
                                                </MenuItem>

                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </div>
                        </div>
                        <div v-if="showSubservices[index]" class="mt-2">
                            <div class="" v-if="service.subservices.length > 0">
                                <div v-for="subservice in service.subservices" :key="subservice.id"
                                     class="grid whitespace-nowrap grid-cols-11 gap-52 bg-gray-50 py-2 rounded-md">

                                    <div>{{ subservice.equipment.category.name }} {{ subservice.equipment.size.name }}
                                        {{
                                            subservice.equipment.series
                                        }}
                                    </div>
                                    <div>{{ subservice.shipping_date ?? "Не задано" }}</div>
                                    <div>{{ subservice.commentary ?? "Не задано" }}</div>
                                    <div>{{ subservice.income ?? "Не задано" }}</div>
                                    <div></div>
                                </div>
                            </div>
                            <div v-else class="p-2 bg-red-50 text-red-500">
                                Нет добавленного оборудования
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>-->

    </AuthenticatedLayout>

</template>
