<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideMenu from '@/Layouts/SideMenu.vue';
import { MenuItem, MenuItems, Menu, MenuButton } from '@headlessui/vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref, toRaw } from 'vue';
import store from '../../../store/index';
import SaleNav from "@/Components/Sale/SaleNav.vue";
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";
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
    DropdownMenuTrigger,
} from 'radix-vue';
import {
    PopoverArrow,
    PopoverClose,
    PopoverContent,
    PopoverPortal,
    PopoverRoot,
    PopoverTrigger
} from 'radix-vue'
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    sales: Object,
    contragents_names: Array,
    contragents: Array,
    formatted_data_months: Object
})

const page = usePage()

const user = computed(() => page.props.auth.user)


const minusYear = () => {
    store.dispatch('services/updateDecSelectedYear')
}
const plusYear = () => {
    store.dispatch('services/updateIncSelectedYear')
}

const selectActive = (value) => {
    store.dispatch('services/updateSelectedActive', value)
}


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
    return months[month] ?? null;

}

const updateMonth = (month) => {
    store.dispatch('services/updateSelectedMonth', month)
}
const showSubservices = ref({});

const toggleSubservice = (index) => {
    showSubservices.value[index] = !showSubservices.value[index];
};

const nameContragent = (id) => {
    return props.contragents_names[id].name
}

const getMonth = computed(() => store.getters['services/getSelectedMonth']);
const getYear = computed(() => store.getters['services/getSelectedYear']);
const selectedActive = computed(() => store.getters['services/getSelectedActive']);


</script>

<template>
    <AuthenticatedLayout>
        <SaleNav />

        <div class="p-6">
            <div class="relative text-sm">
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
                <div class="font-bold text-sm text-gray1">Список проданного оборудования:</div>

                <div class="flex space-x-4">
                    <div @click.prevent="minusYear" class="flex ">
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

                        <p class="mx-2">{{ getYear }}</p>

                        <div @click.prevent="plusYear" class="flex">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
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
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[1050px] text-xs">
                    <div
                        class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2"></div>
                        <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 bg-violet-full/10">
                            Заказчик
                            <button type="button" class="shrink-0 ml-auto rounded-full bg-violet-full/10">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.26795 12.1904L10.6251 9.83325L12.9822 12.1904" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.625 9.8333L10.625 18.0832" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M19.5654 16.5594L17.2083 18.9165L14.8512 16.5594" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.2083 10.6667L17.2083 18.9166" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">
                            Дата отгрузки
                            <button type="button" class="shrink-0 ml-auto rounded-full bg-violet-full/10">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.26795 12.1904L10.6251 9.83325L12.9822 12.1904" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.625 9.8333L10.625 18.0832" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M19.5654 16.5594L17.2083 18.9165L14.8512 16.5594" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.2083 10.6667L17.2083 18.9166" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div
                            class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                            Комментарий</div>
                        <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Цена продажи</div>
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
                    <!-- <AccordionRoot type="multiple" :collapsible="true">
                        <template v-for="(service, index) in sales.data" :key="service.id">
                            
                            <AccordionItem :value="service.id">
                                <AccordionHeader
                                    class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                    <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                        <UiHyperlink :item-id="service.id" :hyperlink="service.hyperlink"
                                            endpoint="/equipment" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 bg-violet-full/10">
                                        {{ nameContragent(index) }}
                                        <AccordionTrigger class="shrink-0 group ml-3">
                                            <svg class="group-data-[state=open]:hidden" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                    fill="#242533" />
                                            </svg>
                                            <svg class="group-data-[state=closed]:hidden" width="20" height="20"
                                                viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                    fill="#242533" />
                                            </svg>
                                        </AccordionTrigger>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">{{
                                        service.shipping_date ?? "Не задано" }}</div>
                                    <div
                                        class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                                        {{ service.commentary ?? "Не задано" }}</div>
                                    <div
                                        class="shrink-0 flex items-center justify-between w-[14.08%] py-2.5 px-2 font-bold">
                                        <span>Итого:</span>{{ service.price ?? "Не задано" }}
                                    </div>
                                    <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                        <Link :href="'/directory/equipment/' + service.id" class="mr-3">
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
                                                            <Link :href="route('sale.edit', service.id)">
                                                            <button type="button"
                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                Редактировать
                                                                <svg class="block ml-2" width="16" height="16"
                                                                    viewBox="0 0 16 16" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M10 3.5C10 3.36739 9.94732 3.24021 9.85355 3.14645C9.75979 3.05268 9.63261 3 9.5 3H1.5C1.36739 3 1.24021 3.05268 1.14645 3.14645C1.05268 3.24021 1 3.36739 1 3.5V12.5C1 12.6326 1.05268 12.7598 1.14645 12.8536C1.24021 12.9473 1.36739 13 1.5 13H9.5C9.63261 13 9.75979 12.9473 9.85355 12.8536C9.94732 12.7598 10 12.6326 10 12.5V10.5C10 10.3674 10.0527 10.2402 10.1464 10.1464C10.2402 10.0527 10.3674 10 10.5 10C10.6326 10 10.7598 10.0527 10.8536 10.1464C10.9473 10.2402 11 10.3674 11 10.5V12.5C11 12.8978 10.842 13.2794 10.5607 13.5607C10.2794 13.842 9.89782 14 9.5 14H1.5C1.10218 14 0.720644 13.842 0.43934 13.5607C0.158035 13.2794 0 12.8978 0 12.5L0 3.5C0 3.10218 0.158035 2.72064 0.43934 2.43934C0.720644 2.15804 1.10218 2 1.5 2H9.5C9.89782 2 10.2794 2.15804 10.5607 2.43934C10.842 2.72064 11 3.10218 11 3.5V5.5C11 5.63261 10.9473 5.75979 10.8536 5.85355C10.7598 5.94732 10.6326 6 10.5 6C10.3674 6 10.2402 5.94732 10.1464 5.85355C10.0527 5.75979 10 5.63261 10 5.5V3.5Z"
                                                                        fill="#21272A" />
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M4.14592 8.35402C4.09935 8.30758 4.06241 8.2524 4.0372 8.19165C4.012 8.13091 3.99902 8.06579 3.99902 8.00002C3.99902 7.93425 4.012 7.86913 4.0372 7.80839C4.06241 7.74764 4.09935 7.69247 4.14592 7.64602L7.14592 4.64602C7.2398 4.55213 7.36714 4.49939 7.49992 4.49939C7.63269 4.49939 7.76003 4.55213 7.85392 4.64602C7.9478 4.73991 8.00055 4.86725 8.00055 5.00002C8.00055 5.1328 7.9478 5.26013 7.85392 5.35402L5.70692 7.50002H14.4999C14.6325 7.50002 14.7597 7.5527 14.8535 7.64647C14.9472 7.74024 14.9999 7.86741 14.9999 8.00002C14.9999 8.13263 14.9472 8.25981 14.8535 8.35358C14.7597 8.44734 14.6325 8.50002 14.4999 8.50002H5.70692L7.85392 10.646C7.90041 10.6925 7.93728 10.7477 7.96244 10.8084C7.9876 10.8692 8.00055 10.9343 8.00055 11C8.00055 11.0658 7.9876 11.1309 7.96244 11.1916C7.93728 11.2523 7.90041 11.3075 7.85392 11.354C7.80743 11.4005 7.75224 11.4374 7.6915 11.4625C7.63076 11.4877 7.56566 11.5007 7.49992 11.5007C7.43417 11.5007 7.36907 11.4877 7.30833 11.4625C7.24759 11.4374 7.19241 11.4005 7.14592 11.354L4.14592 8.35402Z"
                                                                        fill="#21272A" />
                                                                </svg>
                                                            </button>
                                                            </Link>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <Link method="DELETE"
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
                                </AccordionHeader>
                                <AccordionContent
                                    class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden">
                                    <div class="">
                                        <div v-for="subservice in service" :key="subservice.id"
                                            class="flex border-b border-b-gray3 bg-white [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                            <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                                <UiHyperlink :item-id="subservice.id" :hyperlink="subservice.hyperlink"
                                                    endpoint="/equipment" />
                                            </div>
                                            <div
                                                class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                                Продажа №{{ subservice.sale_number }}
                                            </div>
                                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">{{
                                                subservice.sale_date ?? "Не задано" }}</div>
                                            <div
                                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                                                {{ subservice.commentary ?? "Не задано" }}</div>
                                            <div class="shrink-0 flex items-center justify-end w-[14.08%] py-2.5 px-2">
                                                {{ subservice.price ?? "Не задано" }}</div>
                                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                                <Link :href="'/directory/equipment/' + subservice.id" class="mr-3">
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
                                                                    <Link :href="route('sale.edit',subservice.id)"> 
 
                                                                    <button type="button"
                                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                        Редактировать
                                                                        <svg class="block ml-2" width="16" height="16"
                                                                            viewBox="0 0 16 16" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path fill-rule="evenodd"
                                                                                clip-rule="evenodd"
                                                                                d="M10 3.5C10 3.36739 9.94732 3.24021 9.85355 3.14645C9.75979 3.05268 9.63261 3 9.5 3H1.5C1.36739 3 1.24021 3.05268 1.14645 3.14645C1.05268 3.24021 1 3.36739 1 3.5V12.5C1 12.6326 1.05268 12.7598 1.14645 12.8536C1.24021 12.9473 1.36739 13 1.5 13H9.5C9.63261 13 9.75979 12.9473 9.85355 12.8536C9.94732 12.7598 10 12.6326 10 12.5V10.5C10 10.3674 10.0527 10.2402 10.1464 10.1464C10.2402 10.0527 10.3674 10 10.5 10C10.6326 10 10.7598 10.0527 10.8536 10.1464C10.9473 10.2402 11 10.3674 11 10.5V12.5C11 12.8978 10.842 13.2794 10.5607 13.5607C10.2794 13.842 9.89782 14 9.5 14H1.5C1.10218 14 0.720644 13.842 0.43934 13.5607C0.158035 13.2794 0 12.8978 0 12.5L0 3.5C0 3.10218 0.158035 2.72064 0.43934 2.43934C0.720644 2.15804 1.10218 2 1.5 2H9.5C9.89782 2 10.2794 2.15804 10.5607 2.43934C10.842 2.72064 11 3.10218 11 3.5V5.5C11 5.63261 10.9473 5.75979 10.8536 5.85355C10.7598 5.94732 10.6326 6 10.5 6C10.3674 6 10.2402 5.94732 10.1464 5.85355C10.0527 5.75979 10 5.63261 10 5.5V3.5Z"
                                                                                fill="#21272A" />
                                                                            <path fill-rule="evenodd"
                                                                                clip-rule="evenodd"
                                                                                d="M4.14592 8.35402C4.09935 8.30758 4.06241 8.2524 4.0372 8.19165C4.012 8.13091 3.99902 8.06579 3.99902 8.00002C3.99902 7.93425 4.012 7.86913 4.0372 7.80839C4.06241 7.74764 4.09935 7.69247 4.14592 7.64602L7.14592 4.64602C7.2398 4.55213 7.36714 4.49939 7.49992 4.49939C7.63269 4.49939 7.76003 4.55213 7.85392 4.64602C7.9478 4.73991 8.00055 4.86725 8.00055 5.00002C8.00055 5.1328 7.9478 5.26013 7.85392 5.35402L5.70692 7.50002H14.4999C14.6325 7.50002 14.7597 7.5527 14.8535 7.64647C14.9472 7.74024 14.9999 7.86741 14.9999 8.00002C14.9999 8.13263 14.9472 8.25981 14.8535 8.35358C14.7597 8.44734 14.6325 8.50002 14.4999 8.50002H5.70692L7.85392 10.646C7.90041 10.6925 7.93728 10.7477 7.96244 10.8084C7.9876 10.8692 8.00055 10.9343 8.00055 11C8.00055 11.0658 7.9876 11.1309 7.96244 11.1916C7.93728 11.2523 7.90041 11.3075 7.85392 11.354C7.80743 11.4005 7.75224 11.4374 7.6915 11.4625C7.63076 11.4877 7.56566 11.5007 7.49992 11.5007C7.43417 11.5007 7.36907 11.4877 7.30833 11.4625C7.24759 11.4374 7.19241 11.4005 7.14592 11.354L4.14592 8.35402Z"
                                                                                fill="#21272A" />
                                                                        </svg>
                                                                    </button>
                                                                    </Link>
                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem>
                                                                    <Link :href="`/sale/delete/${subservice.id}`"  method="DELETE"
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
                                    </div>

                                </AccordionContent>
                            </AccordionItem>
                        </template>
                    </AccordionRoot> -->

                    <!-- Для Макса -->

                    <!-- 1 уровень -->
                    <AccordionRoot type="multiple" :collapsible="true">
                        <AccordionItem v-for="(service, index) in sales.data" :value="'item-'+service.id">
                            <AccordionHeader
                                class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                <div class="shrink-0 flex items-center justify-center w-[44px] py-2.ы5 px-2">
                                    <UiHyperlink :item-id="2" :hyperlink="'some.ru'"
                                        endpoint="/equipment" />
                                </div>
                                <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 bg-violet-full/10">
                                    {{ nameContragent(index) }}
                                    <AccordionTrigger class="shrink-0 group ml-3">
                                        <svg class="group-data-[state=open]:hidden" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                fill="#242533" />
                                        </svg>
                                        <svg class="group-data-[state=closed]:hidden" width="20" height="20"
                                            viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                fill="#242533" />
                                        </svg>
                                    </AccordionTrigger>
                                </div>
                                <div class="shrink-0 flex items-center w-[14.08%]">
                                    <input value="2024-12-12" type="date" class="block w-full h-full px-2 bg-transparent"
                                                onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                    <input value="some comment" type="text" class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()" />
                                </div>
                                <div
                                    class="shrink-0 flex items-center justify-between w-[14.08%] py-2.5 px-2 font-bold">
                                    <span>Итого:</span> 12313
                                </div>
                                <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                    <PopoverRoot>
                                        <PopoverTrigger class="mr-2">
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
                                                        <Link method="DELETE"
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
                            </AccordionHeader>
                            <AccordionContent class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden">
                                <!-- 2 уровень -->
                                <AccordionRoot type="multiple" :collapsible="true">
                                    <AccordionItem v-for="subservice in service" :value="'item-'+subservice.id">
                                        <AccordionHeader class="flex border-b border-b-gray3 bg-white [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                            <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                                <UiHyperlink :item-id="2" :hyperlink="'some.sss'" endpoint="/equipment" />
                                            </div>
                                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                                Продажа № {{ subservice.sale_number }}

                                                <AccordionTrigger class="shrink-0 group ml-3">
                                                    <svg class="group-data-[state=open]:hidden" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                            fill="#242533" />
                                                    </svg>
                                                    <svg class="group-data-[state=closed]:hidden" width="20" height="20"
                                                        viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                            fill="#242533" />
                                                    </svg>
                                                </AccordionTrigger>
                                            </div>
                                            <div class="shrink-0 flex items-center w-[14.08%]">
                                                <input type="date" class="block w-full h-full px-2 bg-transparent" />
                                            </div>
                                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                                <input value="somee" type="text" class="block w-full h-full px-2 bg-transparent" />
                                            </div>
                                            <div class="shrink-0 flex items-center justify-end w-[14.08%]">
                                                <input value="1232" type="text" class="block w-full h-full px-2 bg-transparent" />
                                            </div>
                                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                                <PopoverRoot>
                                                    <PopoverTrigger class="mr-2">
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
                                                                    <Link :href="`/sale/delete/${2}`"  method="DELETE"
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
                                        </AccordionHeader>
                                        <AccordionContent class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden">
                                            <!-- 3 уровень -->
                                            <AccordionRoot type="multiple" :collapsible="true">
                                                <AccordionItem v-for="equip in subservice.sale_equipment" :value="'item-'+equip.id">
                                                    <AccordionHeader class="flex border-b border-b-gray3 bg-white [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                                            <UiHyperlink :item-id="2" :hyperlink="'some.sss'" endpoint="/equipment" />
                                                        </div>
                                                        <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                                            {{ equip.equipment.category.name }} {{ equip.equipment.size.name }} {{ equip.equipment.series }}

                                                            <AccordionTrigger class="shrink-0 group ml-3">
                                                                <svg class="group-data-[state=open]:hidden" width="24" height="24"
                                                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                                        fill="#242533" />
                                                                </svg>
                                                                <svg class="group-data-[state=closed]:hidden" width="20" height="20"
                                                                    viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                                        fill="#242533" />
                                                                </svg>
                                                            </AccordionTrigger>
                                                        </div>
                                                        <div class="shrink-0 flex items-center w-[14.08%]">
                                                            <input type="date" class="block w-full h-full px-2 bg-transparent" />
                                                        </div>
                                                        <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                                            <input value="somee" type="text" class="block w-full h-full px-2 bg-transparent" />
                                                        </div>
                                                        <div class="shrink-0 flex items-center justify-end w-[14.08%]">
                                                            <input value="1232" type="text" class="block w-full h-full px-2 bg-transparent" />
                                                        </div>
                                                        <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                                            <PopoverRoot>
                                                                <PopoverTrigger class="mr-2">
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
                                                                                <Link :href="`/sale/delete/${2}`"  method="DELETE"
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
                                                    </AccordionHeader>
                                                    <AccordionContent class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden">
                                                        <!-- 4 уровень -->
                                                        <div v-for="subequip in equip.subequipment":value="'item-'+subequip.id" class="flex border-b border-b-gray3 bg-white [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                                            <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                                                <UiHyperlink :item-id="2" :hyperlink="'some.sss'" endpoint="/equipment" />
                                                            </div>
                                                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                                                {{ subequip.equipment.category.name }} {{ subequip.equipment.size.name }} {{ subequip.equipment.series }}
                                                            </div>
                                                            <div class="shrink-0 flex items-center w-[14.08%]">
                                                                <input type="date" class="block w-full h-full px-2 bg-transparent" />
                                                            </div>
                                                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                                                <input value="somee" type="text" class="block w-full h-full px-2 bg-transparent" />
                                                            </div>
                                                            <div class="shrink-0 flex items-center justify-end w-[14.08%]">
                                                                <input value="1232" type="text" class="block w-full h-full px-2 bg-transparent" />
                                                            </div>
                                                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                                                <PopoverRoot>
                                                                    <PopoverTrigger class="mr-2">
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
                                                                                    <Link :href="`/sale/delete/${2}`"  method="DELETE"
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
                                                    </AccordionContent>
                                                </AccordionItem>
                                            </AccordionRoot>
                                        </AccordionContent>
                                    </AccordionItem>
                                </AccordionRoot>
                            </AccordionContent>
                        </AccordionItem>
                    </AccordionRoot>
                    <!--                    <template v-for="(service, index) in filterDatesActive()" :key="service.id">
                        <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                            <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                <UiHyperlink :item-id="service.id" :hyperlink="service.hyperlink" endpoint="/equipment" />
                            </div>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">
                                {{ service.equipment.category.name }} {{
                                        service.equipment.size.name
                                    }} {{ service.equipment.series }}
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
                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">{{ service.shipping_date ?? "Не задано" }}</div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">{{ service.commentary ?? "Не задано" }}</div>
                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">{{ service.price ?? "Не задано" }}</div>
                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                <Link :href="'/directory/equipment/'+service.id">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                        <div v-if="showSubservices[index]">
                            <div class="" v-if="service.subservices.length > 0">
                                <div v-for="subservice in service.subservices" :key="subservice.id" class="flex border-b border-b-gray3 bg-white [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                    <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                        <UiHyperlink :item-id="subservice.id" :hyperlink="subservice.hyperlink" endpoint="/equipment" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                        {{ subservice.equipment_info.category.name }}
                                        {{ subservice.equipment_info.size.name }} {{
                                            subservice.equipment_info.series
                                        }}
                                    </div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">{{ subservice.shipping_date ?? "Не задано" }}</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">{{ subservice.commentary ?? "Не задано" }}</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">{{ subservice.price ?? "Не задано" }}</div>
                                    <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                        <Link :href="'/directory/equipment/'+subservice.id">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="p-2 bg-red-50 text-red-500">
                                Нет добавленного оборудования
                            </div>
                        </div>
                    </template>-->
                </div>
            </div>

            <pagination :current-page="props.sales.current_page" :total-pages="props.sales.last_page"
                :total-count="props.sales.total" :next-page-url="props.sales.next_page_url" :links="props.sales.links"
                :prev-page-url="props.sales.prev_page_url" class="mt-5 bg-bg1" />
        </div>

        <div class="flex-1 2xl:w-[1184px] md:w-full sm:w-full">
            <!--            <div class="">
                <nav class="bg-my-gray ">
                    <div class="max-w-screen-xl px-4 py-3">
                        <div
                            class="lg:py-4 sm:py-0 lg:overflow-y-visible sm:whitespace-nowrap lg:overflow-x-visible sm:overflow-y-auto sm:overflow-x-auto  items-center">
                            <ul
                                class="flex flex border-b-2 items-center flex-row font-medium mt-0 space-x-4 gap-4 rtl:space-x-reverse text-sm">
                                <li>
                                    <div class="flex">
                                        <Link :href="route('sale.create')" class="text-lg  ">Новая продажа</Link>

                                    </div>
                                </li>
                                <li>
                                    <div class="flex">
                                        <Link :href="route('sale.create')" class="text-lg">История</Link>

                                    </div>
                                </li>
                                <li>
                                    <div class="flex">
                                        <Link v-if="user.isAdmin" :href="route('equip.tests')" class="text-lg">Admin
                                        </Link>
                                    </div>
                                </li>
                            </ul>
                        </div>

                    </div>
                </nav>
            </div>-->

            <!--            <nav class="lg:px-4 md:bg-white sm:bg-my-gray rounded-lg mt-4">
                <div class="max-w-screen-xl py-2">
                    <div class="mt-6 flex items-center">
                        <ul
                            class="flex overflow-x-auto sm:px-2 flex-row  border-b-2 border-gray-200 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">

                            <li :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'all' }"
                                class="text-my-nav-text text-md w-full" @click="updateMonth('all')">
                                Все
                            </li>
                            <li @click="updateMonth('jan')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'jan' }"
                                class="text-my-nav-text text-md w-full">
                                Январь
                            </li>
                            <li @click="updateMonth('feb')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'feb' }"
                                class="text-my-nav-text text-md w-full">
                                Февраль
                            </li>
                            <li @click="updateMonth('mar')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'mar' }"
                                class="text-my-nav-text text-md w-full">
                                Март
                            </li>
                            <li @click="updateMonth('apr')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'apr' }"
                                class="text-my-nav-text text-md w-full">
                                Апрель
                            </li>
                            <li @click="updateMonth('may')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'may' }"
                                class="text-my-nav-text text-md w-full">
                                Май
                            </li>
                            <li @click="updateMonth('jun')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'jun' }"
                                class="text-my-nav-text text-md w-full">
                                Июнь
                            </li>
                            <li @click="updateMonth('jul')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'jul' }"
                                class="text-my-nav-text text-md w-full">
                                Июль
                            </li>
                            <li @click="updateMonth('aug')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'aug' }"
                                class="text-my-nav-text text-md w-full">
                                Август
                            </li>
                            <li @click="updateMonth('sept')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'sept' }"
                                class="text-my-nav-text text-md w-full">
                                Сентябрь
                            </li>
                            <li @click="updateMonth('oct')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'oct' }"
                                class="text-my-nav-text text-md w-full">
                                Октябрь
                            </li>
                            <li @click="updateMonth('novb')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'novb' }"
                                class="text-my-nav-text text-md w-full">
                                Ноябрь
                            </li>
                            <li @click="updateMonth('dec')"
                                :class="{ 'pb-3 border-b-2 border-blue-600  ': getMonth === 'dec' }"
                                class="text-my-nav-text text-md w-full">
                                Декабрь
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>-->


            <!--            <div class="lg:hidden md:hidden flex bg-my-gray mt-4 p-4">

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

            <div class="flex my-4 lg:px-4 justify-between lg:pr-10">
                <h3>Список открытых аренд:</h3>

                <div class="flex space-x-4">
                    <div @click.prevent="minusYear" class="flex ">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_2051_7507)">
                                <rect x="24" width="24" height="24" rx="5" transform="rotate(90 24 0)" fill="#F7F9FC"/>
                                <path
                                    d="M10.686 11.929L15.636 16.879C15.8182 17.0676 15.919 17.3202 15.9167 17.5824C15.9144 17.8446 15.8092 18.0954 15.6238 18.2808C15.4384 18.4662 15.1876 18.5714 14.9254 18.5737C14.6632 18.576 14.4106 18.4752 14.222 18.293L8.565 12.636C8.37753 12.4485 8.27221 12.1942 8.27221 11.929C8.27221 11.6639 8.37753 11.4095 8.565 11.222L14.222 5.56502C14.4106 5.38286 14.6632 5.28207 14.9254 5.28434C15.1876 5.28662 15.4384 5.39179 15.6238 5.5772C15.8092 5.76261 15.9144 6.01342 15.9167 6.27562C15.919 6.53781 15.8182 6.79042 15.636 6.97902L10.686 11.929Z"
                                    fill="#697077"/>
                            </g>
                            <rect x="23.5" y="0.5" width="23" height="23" rx="4.5" transform="rotate(90 23.5 0.5)"
                                  stroke="#697077"/>
                            <defs>
                                <clipPath id="clip0_2051_7507">
                                    <rect x="24" width="24" height="24" rx="5" transform="rotate(90 24 0)"
                                          fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>


                        <p class="mx-2">{{ getYear }}</p>

                        <div @click.prevent="plusYear" class="flex">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_2051_7512)">
                                    <rect y="24" width="24" height="24" rx="5" transform="rotate(-90 0 24)"
                                          fill="#F7F9FC"/>
                                    <path
                                        d="M13.314 12.071L8.36399 7.12098C8.18184 6.93238 8.08104 6.67978 8.08332 6.41758C8.0856 6.15538 8.19077 5.90457 8.37618 5.71916C8.56158 5.53375 8.8124 5.42859 9.07459 5.42631C9.33679 5.42403 9.58939 5.52482 9.77799 5.70698L15.435 11.364C15.6225 11.5515 15.7278 11.8058 15.7278 12.071C15.7278 12.3361 15.6225 12.5905 15.435 12.778L9.77799 18.435C9.58939 18.6171 9.33679 18.7179 9.07459 18.7157C8.8124 18.7134 8.56158 18.6082 8.37618 18.4228C8.19077 18.2374 8.0856 17.9866 8.08332 17.7244C8.08104 17.4622 8.18184 17.2096 8.36399 17.021L13.314 12.071Z"
                                        fill="#697077"/>
                                </g>
                                <rect x="0.5" y="23.5" width="23" height="23" rx="4.5" transform="rotate(-90 0.5 23.5)"
                                      stroke="#697077"/>
                                <defs>
                                    <clipPath id="clip0_2051_7512">
                                        <rect y="24" width="24" height="24" rx="5" transform="rotate(-90 0 24)"
                                              fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>

                    </div>

                </div>
            </div>-->



            <!--            <div class="overflow-x-auto lg:px-4 pb-20">
                &lt;!&ndash; Table header &ndash;&gt;
                <div class="min-w-full whitespace-nowrap grid grid-cols-5 gap-52 bg-gray-100 font-bold p-2 rounded-md">
                    <div>Заказчик</div>
                    <div>Дата отгрузки</div>
                    <div>Комментарий</div>
                    <div>Цена</div>
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
                                <span>{{ service.equipment.category.name }} {{
                                        service.equipment.size.name
                                    }} {{ service.equipment.series }}</span>
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
                            <div>{{ service.price ?? "Не задано" }}</div>
                            &lt;!&ndash; Actions column (empty for now) &ndash;&gt;
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
                                                    <Link method="delete" :href="route('sale.destroy', service.id)"
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
                                    <div>{{ subservice.equipment_info.category.name }}
                                        {{ subservice.equipment_info.size.name }} {{
                                            subservice.equipment_info.series
                                        }}
                                    </div>
                                    <div>{{ subservice.shipping_date }}</div>
                                    <div>{{ subservice.commentary }}</div>
                                    <div>{{ subservice.price ?? "Не задано" }}</div>
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
                            </div>
                        </div>
                        <div v-if="showSubservices[index]" class="mt-2">
                            <div class="" v-if="service.subservices.length > 0">
                                <div v-for="subservice in service.subservices" :key="subservice.id"
                                     class="grid whitespace-nowrap grid-cols-11 gap-52 bg-gray-50 py-2 rounded-md">
                                    <div>{{ service.equipment_info.category.name }} {{
                                            service.equipment_info.size.name
                                        }} {{
                                            service.equipment_info.series
                                        }}
                                    </div>
                                    <div>{{ service.shipping_date ?? "Не задано" }}</div>
                                    <div>{{ service.commentary ?? "Не задано" }}</div>
                                    <div>{{ service.income ?? "Не задано" }}</div>
                                    <div></div>
                                </div>
                            </div>
                            <div v-else class="p-2 bg-red-50 text-red-500">
                                Нет добавленного оборудования
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->


        </div>

    </AuthenticatedLayout>

</template>
