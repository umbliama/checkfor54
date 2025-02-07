<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, reactive, ref, toRaw, watch } from 'vue';
import store from '../../../store/index';
import ServicesNav from "@/Components/Services/ServicesNav.vue";
import UiField from "@/Components/Ui/UiField.vue";
import UiFieldDate from "@/Components/Ui/UiFieldDate.vue";
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
import {
    DialogClose,
    DialogContent,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'radix-vue';
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

const edit_dialog_open = ref(false);
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

const calcFullIncome = () => {
    const result = {};
    const raw = toRaw(props.activeServices.data);

    for (const key in raw) {
        if (raw.hasOwnProperty(key)) {
            const servicesArray = raw[key];
            for (const service of servicesArray) {
                if (service && service.full_income) {
                    const contragentId = service.contragent_id;
                    if (!result[contragentId]) {
                        result[contragentId] = 0;
                    }
                    result[contragentId] += service.full_income;
                }
            }
        }
    }

    return result;
};

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


const nameContragent = (id) => {
    return props.contragents_names[id]
}

function openEditDialog() {
    edit_dialog_open.value = true;
}


</script>

<template>
    <AuthenticatedLayout>
        <ServicesNav v-model:start-date="start_date" v-model:end-date="end_date"
            :count-services-active="props.count_services_active"
            :count-services-inactive="props.count_services_inactive" />

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

            <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[1136px] text-xs">
                    <div
                        class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2"></div>
                        <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">Заказчик</div>
                        <div :class="{ 'bg-violet/5': !!date_sort }"
                            class="shrink-0 flex items-center justify-between w-[14.08%] py-2.5 px-2 cursor-pointer"
                            @click="date_sort === 'asc' ? date_sort = 'desc' : date_sort = 'asc'">
                            Дата отгрузки
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08" />
                                <path d="M8.26783 12.1904L10.6249 9.83325L12.9821 12.1904" stroke="#644DED"
                                    stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10.625 9.8333L10.625 18.0832" stroke="#644DED" stroke-width="1.2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M19.5654 16.5594L17.2083 18.9165L14.8511 16.5594" stroke="#644DED"
                                    stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M17.2082 10.6667L17.2082 18.9166" stroke="#644DED" stroke-width="1.2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div
                            class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                            Комментарий</div>
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
                    <!-- Для Макса -->

                    <!-- 1 уровень -->
                    <AccordionRoot type="multiple" :collapsible="true">
                        <AccordionItem v-for="(service, index) in selectedActive ? activeServices.data : services.data"
                            :value="'item-' + index">
                            <AccordionHeader
                                class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                <div class="shrink-0 flex items-center justify-center w-[44px] py-2.ы5 px-2">
                                    <UiHyperlink :item-id="2" :hyperlink="'some.ru'" endpoint="/equipment" />
                                </div>
                                <div
                                    class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2 bg-violet-full/10">
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
                                    <input value="2024-12-12" type="date"
                                        class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                    <input value="some comment" type="text"
                                        class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()" />
                                </div>
                                <div
                                    class="shrink-0 flex items-center justify-between w-[14.08%] py-2.5 px-2 font-bold">
                                    <span>Итого:</span> {{ calcFullIncome()[index] }}
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
                                    <PopoverRoot v-else>
                                        <PopoverTrigger class="mr-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                                <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                                <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                            </svg>
                                        </PopoverTrigger>
                                        <PopoverPortal>
                                            <PopoverContent side="bottom" align="end"
                                                class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                <div>Комментарий:</div>
                                                <p class="mt-2.5 text-xs">Далеко-далеко за словесными горами в стране
                                                    гласных и согласных
                                                    живут рыбные тексты. Страна если бросил, он всемогущая запятых
                                                    грамматики себя ipsum
                                                    точках, несколько меня строчка маленькая страну предупреждал которой
                                                    раз проектах. Ему
                                                    выйти составитель дал то ...</p>
                                                <div class="mt-3 p-4 bg-bg1 text-xs">
                                                    <div class="flex items-center max-w-full">
                                                        <span
                                                            class="grow block mr-auto text-ellipsis overflow-hidden">Some
                                                            file name</span>
                                                        <svg class="shrink-0 block ml-2" width="20" height="20"
                                                            viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                fill="#697077" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                fill="#697077" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                fill="#697077" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                fill="#697077" />
                                                        </svg>
                                                        <DropdownMenuRoot>
                                                            <DropdownMenuTrigger aria-label="Customise options">
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
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
                                                                            <Link :href="'/'"
                                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                Скачать
                                                                                <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                    <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                </svg>
                                                                            </Link>
                                                                        </DropdownMenuItem>
                                                                    </DropdownMenuContent>
                                                                </transition>
                                                            </DropdownMenuPortal>
                                                        </DropdownMenuRoot>
                                                    </div>
                                                </div>
                                                <Link :href="'/directory/service/' + 2"
                                                    class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                Редактировать

                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                        fill="#464F60" />
                                                    <path
                                                        d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                        fill="#464F60" />
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
                                                        <button
                                                            type="button"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                            @click="openEditDialog"
                                                        >
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
                                                        </button>
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
                                <!-- 2 уровень -->
                                <AccordionRoot type="multiple" :collapsible="false">
                                    <AccordionItem v-for="(service_item, index) in service"
                                        :value="'item-' + service_item.id">
                                        <AccordionHeader
                                            class="flex border-b border-b-gray3 bg-white [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                            <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                                <UiHyperlink :item-id="2" :hyperlink="'some.sss'"
                                                    endpoint="/equipment" />
                                            </div>
                                            <div
                                                class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                                Аренда №{{ service_item.service_number }}

                                                <AccordionTrigger class="shrink-0 group ml-3">
                                                    <svg class="group-data-[state=open]:hidden" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                            fill="#242533" />
                                                    </svg>
                                                    <svg class="group-data-[state=closed]:hidden" width="20" height="20"
                                                        viewBox="0 0 18 18" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                            fill="#242533" />
                                                    </svg>
                                                </AccordionTrigger>
                                            </div>
                                            <div class="shrink-0 flex items-center w-[14.08%]">
                                                <input v-model="service_item.service_date" type="date" class="block w-full h-full px-2 bg-transparent" />
                                            </div>
                                            <div
                                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                                <input value="somee" type="text"
                                                    class="block w-full h-full px-2 bg-transparent" />
                                            </div>
                                            <div class="shrink-0 flex items-center justify-end w-[14.08%]">
                                                <input v-model="service_item.full_income" type="text"
                                                    class="block w-full h-full px-2 bg-transparent" />

                                            </div>
                                            <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                                <Link v-if="service_item.directory === null"
                                                    :href="'/directory/service/' + service_item.id" class="mr-3.5">
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
                                                <PopoverRoot v-else>
                                                    <PopoverTrigger class="mr-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z"
                                                                fill="#21272A" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12.0068 11.8052L15.6263 9.07311C15.9569 8.82356 16.4272 8.88927 16.6767 9.21988C16.9263 9.55048 16.8606 10.0208 16.53 10.2703L12.4519 13.3486C12.1812 13.5529 11.8072 13.5502 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z"
                                                                fill="#21272A" />
                                                            <path
                                                                d="M20.2477 8.25001C21.489 8.25001 22.4953 7.2437 22.4953 6.00236C22.4953 4.76101 21.489 3.7547 20.2477 3.7547C19.0063 3.7547 18 4.76101 18 6.00236C18 7.2437 19.0063 8.25001 20.2477 8.25001Z"
                                                                fill="#21272A" />
                                                            <path
                                                                d="M20.25 9.00001C19.6571 9.00001 19.0776 8.8242 18.5846 8.49482C18.0916 8.16543 17.7074 7.69726 17.4805 7.14951C17.2536 6.60176 17.1943 5.99903 17.31 5.41754C17.4256 4.83606 17.7111 4.30192 18.1303 3.88269C18.5496 3.46346 19.0837 3.17797 19.6652 3.0623C20.2467 2.94663 20.8494 3.006 21.3972 3.23288C21.9449 3.45977 22.4131 3.84399 22.7425 4.33695C23.0719 4.82991 23.2477 5.40948 23.2477 6.00236C23.247 6.79719 22.931 7.5593 22.369 8.12134C21.807 8.68337 21.0448 8.99939 20.25 9.00001ZM20.25 4.5047C19.9532 4.50424 19.663 4.59182 19.416 4.75636C19.169 4.9209 18.9764 5.155 18.8625 5.42905C18.7486 5.7031 18.7186 6.00477 18.7762 6.2959C18.8338 6.58702 18.9765 6.85452 19.1862 7.06453C19.3959 7.27454 19.6631 7.41764 19.9542 7.47571C20.2452 7.53377 20.5469 7.50421 20.8211 7.39074C21.0954 7.27728 21.3298 7.08502 21.4947 6.8383C21.6596 6.59158 21.7477 6.30147 21.7477 6.0047C21.7477 5.60728 21.59 5.22611 21.3092 4.94487C21.0284 4.66363 20.6474 4.50532 20.25 4.5047Z"
                                                                fill="#21272A" />
                                                        </svg>
                                                    </PopoverTrigger>
                                                    <PopoverPortal>
                                                        <PopoverContent side="bottom" align="end"
                                                            class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                            <div>Комментарий:</div>
                                                            <p class="mt-2.5 text-xs">{{
                                                                service_item.directory.commentary }}</p>
                                                            <div v-for="file in service_item.directory.files"
                                                                class="mt-3 p-4 bg-bg1 text-xs">
                                                                <div class="flex items-center max-w-full">
                                                                    <span
                                                                        class="grow block mr-auto text-ellipsis overflow-hidden">
                                                                        {{ file }}</span>
                                                                    <svg class="shrink-0 block ml-2" width="20"
                                                                        height="20" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                            fill="#697077" />
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                            fill="#697077" />
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                            fill="#697077" />
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                            fill="#697077" />
                                                                    </svg>
                                                                    <DropdownMenuRoot>
                                                                        <DropdownMenuTrigger aria-label="Customise options">
                                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
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
                                                                                        <Link :href="'/'"
                                                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                            Скачать
                                                                                            <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                                <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                            </svg>
                                                                                        </Link>
                                                                                    </DropdownMenuItem>
                                                                                </DropdownMenuContent>
                                                                            </transition>
                                                                        </DropdownMenuPortal>
                                                                    </DropdownMenuRoot>
                                                                </div>
                                                            </div>
                                                            <Link :href="'/directory/service/' + service_item.id"
                                                                class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all">
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
                                                                    <Link :href="`/services/edit/${service_item.id}`"
                                                                        method="GET"
                                                                        class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Редактировать
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                    </Link>

                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem>
                                                                    <Link :href="`/services/delete/${service_item.id}`"
                                                                        method="DELETE"
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
                                            <!-- 3 уровень -->
                                            <AccordionRoot type="multiple" :collapsible="true">
                                                <AccordionItem v-for="(subservice, index) in service_item.main_services"
                                                    :value="'item-' + subservice.id">
                                                    <AccordionHeader
                                                        class="flex border-b border-b-gray3 bg-white [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                                        <div
                                                            class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                                            <UiHyperlink :item-id="2" :hyperlink="'some.sss'"
                                                                endpoint="/equipment" />
                                                        </div>
                                                        <div
                                                            class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                                            {{ subservice.equipment.category.name }}
                                                            {{ subservice.equipment.size.name }}
                                                            {{ subservice.equipment.series }}
                                                            <AccordionTrigger class="shrink-0 group ml-3">
                                                                <svg class="group-data-[state=open]:hidden" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                                        fill="#242533" />
                                                                </svg>
                                                                <svg class="group-data-[state=closed]:hidden" width="20"
                                                                    height="20" viewBox="0 0 18 18" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                                        fill="#242533" />
                                                                </svg>
                                                            </AccordionTrigger>
                                                        </div>
                                                        <div class="shrink-0 flex items-center w-[14.08%]">
                                                            <input v-model="subservice.shipping_date" type="date"
                                                                class="block w-full h-full px-2 bg-transparent" />
                                                        </div>
                                                        <div
                                                            class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                                            <input v-model="subservice.commentary" type="text"
                                                                class="block w-full h-full px-2 bg-transparent" />
                                                        </div>
                                                        <div class="shrink-0 flex items-center justify-end w-[14.08%]">
                                                            <input v-model="subservice.income" type="text"
                                                                class="block w-full h-full px-2 bg-transparent" />
                                                        </div>
                                                        <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                                            <Link v-if="true" :href="'/directory/service/' + 2"
                                                                class="mr-3.5">
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
                                                            <PopoverRoot v-else>
                                                                <PopoverTrigger class="mr-2">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                                                        <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                                                        <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                                                    </svg>
                                                                </PopoverTrigger>
                                                                <PopoverPortal>
                                                                    <PopoverContent side="bottom" align="end"
                                                                        class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                                        <div>Комментарий:</div>
                                                                        <p class="mt-2.5 text-xs">Далеко-далеко за
                                                                            словесными горами
                                                                            в стране гласных и
                                                                            согласных живут рыбные тексты. Страна если
                                                                            бросил, он
                                                                            всемогущая запятых
                                                                            грамматики себя ipsum точках, несколько меня
                                                                            строчка
                                                                            маленькая страну
                                                                            предупреждал которой раз проектах. Ему выйти
                                                                            составитель
                                                                            дал то ...</p>
                                                                        <div class="mt-3 p-4 bg-bg1 text-xs">
                                                                            <div class="flex items-center max-w-full">
                                                                                <span
                                                                                    class="grow block mr-auto text-ellipsis overflow-hidden">Some
                                                                                    file
                                                                                    name</span>
                                                                                <svg class="shrink-0 block ml-2"
                                                                                    width="20" height="20"
                                                                                    viewBox="0 0 24 24" fill="none"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <path fill-rule="evenodd"
                                                                                        clip-rule="evenodd"
                                                                                        d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                                        fill="#697077" />
                                                                                    <path fill-rule="evenodd"
                                                                                        clip-rule="evenodd"
                                                                                        d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                                        fill="#697077" />
                                                                                    <path fill-rule="evenodd"
                                                                                        clip-rule="evenodd"
                                                                                        d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                                        fill="#697077" />
                                                                                    <path fill-rule="evenodd"
                                                                                        clip-rule="evenodd"
                                                                                        d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                                        fill="#697077" />
                                                                                </svg>
                                                                                <DropdownMenuRoot>
                                                                                    <DropdownMenuTrigger aria-label="Customise options">
                                                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
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
                                                                                                    <Link :href="'/'"
                                                                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                                        Скачать
                                                                                                        <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                                            <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                                        </svg>
                                                                                                    </Link>
                                                                                                </DropdownMenuItem>
                                                                                            </DropdownMenuContent>
                                                                                        </transition>
                                                                                    </DropdownMenuPortal>
                                                                                </DropdownMenuRoot>
                                                                            </div>
                                                                        </div>
                                                                        <Link
                                                                            :href="'/directory/service/' + service_item.id"
                                                                            class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all">
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
                                                                    </PopoverContent>
                                                                </PopoverPortal>
                                                            </PopoverRoot>
                                                            <DropdownMenuRoot>
                                                                <DropdownMenuTrigger aria-label="Customise options">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                                                <Link :href="`/sale/delete/${2}`"
                                                                                    method="DELETE"
                                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                                Удалить
                                                                                <svg class="block ml-2" width="16"
                                                                                    height="16" viewBox="0 0 16 16"
                                                                                    fill="none"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <path fill-rule="evenodd"
                                                                                        clip-rule="evenodd"
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
                                                        <!-- 4 уровень -->
                                                        <div v-for="(sub, index) in subservice.service_subs"
                                                            :value="'item-' + service.id"
                                                            class="flex border-b border-b-gray3 bg-white [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                                            <div
                                                                class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                                                <UiHyperlink :item-id="2" :hyperlink="'some.sss'"
                                                                    endpoint="/equipment" />
                                                            </div>
                                                            <div
                                                                class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                                                {{ sub.equipment.category.name }} {{
                                                                    sub.equipment.size.name }} {{
                                                                    sub.equipment.series }}
                                                            </div>
                                                            <div class="shrink-0 flex items-center w-[14.08%]">
                                                                <input v-model="sub.shipping_date" type="date"
                                                                    class="block w-full h-full px-2 bg-transparent" />
                                                            </div>
                                                            <div
                                                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                                                <input v-model="sub.commentary" type="text"
                                                                    class="block w-full h-full px-2 bg-transparent" />
                                                            </div>
                                                            <div
                                                                class="shrink-0 flex items-center justify-end w-[14.08%]">
                                                                <input v-model="sub.income" type="text"
                                                                    class="block w-full h-full px-2 bg-transparent" />
                                                            </div>
                                                            <div
                                                                class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                                                <Link v-if="true" :href="'/directory/service/' + 2"
                                                                    class="mr-3.5">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                                            fill="#21272A" />
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z"
                                                                            fill="#21272A" />
                                                                    </svg>
                                                                </Link>
                                                                <PopoverRoot v-else>
                                                                    <PopoverTrigger class="mr-2">
                                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                                                            <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                                                            <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                                                        </svg>
                                                                    </PopoverTrigger>
                                                                    <PopoverPortal>
                                                                        <PopoverContent side="bottom" align="end"
                                                                            class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                                            <div>Комментарий:</div>
                                                                            <p class="mt-2.5 text-xs">Далеко-далеко за
                                                                                словесными горами
                                                                                в стране гласных и
                                                                                согласных живут рыбные тексты. Страна
                                                                                если бросил, он
                                                                                всемогущая запятых
                                                                                грамматики себя ipsum точках, несколько
                                                                                меня строчка
                                                                                маленькая страну
                                                                                предупреждал которой раз проектах. Ему
                                                                                выйти составитель
                                                                                дал то ...</p>
                                                                            <div class="mt-3 p-4 bg-bg1 text-xs">
                                                                                <div
                                                                                    class="flex items-center max-w-full">
                                                                                    <span
                                                                                        class="grow block mr-auto text-ellipsis overflow-hidden">Some
                                                                                        file
                                                                                        name</span>
                                                                                    <svg class="shrink-0 block ml-2"
                                                                                        width="20" height="20"
                                                                                        viewBox="0 0 24 24" fill="none"
                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                        <path fill-rule="evenodd"
                                                                                            clip-rule="evenodd"
                                                                                            d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                                            fill="#697077" />
                                                                                        <path fill-rule="evenodd"
                                                                                            clip-rule="evenodd"
                                                                                            d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                                            fill="#697077" />
                                                                                        <path fill-rule="evenodd"
                                                                                            clip-rule="evenodd"
                                                                                            d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                                            fill="#697077" />
                                                                                        <path fill-rule="evenodd"
                                                                                            clip-rule="evenodd"
                                                                                            d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                                            fill="#697077" />
                                                                                    </svg>
                                                                                    <DropdownMenuRoot>
                                                                                        <DropdownMenuTrigger aria-label="Customise options">
                                                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
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
                                                                                                        <Link :href="'/'"
                                                                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                                            Скачать
                                                                                                            <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                                                <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                                            </svg>
                                                                                                        </Link>
                                                                                                    </DropdownMenuItem>
                                                                                                </DropdownMenuContent>
                                                                                            </transition>
                                                                                        </DropdownMenuPortal>
                                                                                    </DropdownMenuRoot>
                                                                                </div>
                                                                            </div>
                                                                            <Link
                                                                                :href="'/directory/service/' + service_item.id"
                                                                                class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                            Редактировать

                                                                            <svg class="block ml-2" width="16"
                                                                                height="16" viewBox="0 0 16 16"
                                                                                fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path
                                                                                    d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                                                    fill="#464F60" />
                                                                                <path
                                                                                    d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                                                    fill="#464F60" />
                                                                            </svg>
                                                                            </Link>
                                                                        </PopoverContent>
                                                                    </PopoverPortal>
                                                                </PopoverRoot>
                                                                <DropdownMenuRoot>
                                                                    <DropdownMenuTrigger aria-label="Customise options">
                                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                                            fill="none"
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
                                                                                    <Link :href="`/sale/delete/${2}`"
                                                                                        method="DELETE"
                                                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                                    Удалить
                                                                                    <svg class="block ml-2" width="16"
                                                                                        height="16" viewBox="0 0 16 16"
                                                                                        fill="none"
                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                        <path fill-rule="evenodd"
                                                                                            clip-rule="evenodd"
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
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button class="inline-flex items-center justify-center py-3 px-7 font-medium tracking-wider bg-my-gray text-side-gray-text"> Сохранить </button>
            </div>

            <pagination ref="pagination_el"
                :current-page="selectedActive ? props.activeServices.current_page : props.services.current_page"
                :total-pages="selectedActive ? props.activeServices.last_page : props.services.last_page"
                :total-count="selectedActive ? count_services_active : count_services_inactive"
                :next-page-url="selectedActive ? props.activeServices.next_page_url : props.services.next_page_url"
                :links="selectedActive ? props.activeServices.links : props.services.links"
                :prev-page-url="selectedActive ? props.activeServices.prev_page_url : props.services.prev_page_url"
                class="mt-5 bg-bg1" />
        </div>


        <DialogRoot v-model:open="edit_dialog_open">
            <DialogPortal>
                <transition name="fade">
                    <DialogOverlay class="fixed left-0 top-0 w-full h-full bg-black bg-opacity-60 z-30"/>
                </transition>
                <transition name="fade">
                    <DialogContent
                        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-h-[85vh] w-[90vw] max-w-[450px] p-6 rounded bg-white z-[100]"
                    >
                        <DialogTitle class="flex items-center justify-between font-semibold">
                            Редактировать аренду

                            <DialogClose class="shrink-0 ml-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16.95 8.464a1 1 0 0 0-1.414-1.414L12 10.586L8.464 7.05A1 1 0 1 0 7.05 8.464L10.586 12L7.05 15.536a1 1 0 1 0 1.414 1.414L12 13.414l3.536 3.536a1 1 0 1 0 1.414-1.414L13.414 12z"/></svg>
                            </DialogClose>
                        </DialogTitle>
                        <form class="mt-8 space-y-4">
                            <UiFieldDate
                                v-model="date"
                                label="Дата отгрузки"
                                :inp-attrs="{ placeholder: 'Укажите дату отгрузки' }"
                            />
                            <UiField label="Комментарий" textarea />

                            <button
                                type="submit"
                                class="w-full mt-4 bg-my-gray text-side-gray-text font-bold px-6 py-3 disabled:opacity-40"
                            >Сохранить</button>
                        </form>
                    </DialogContent>
                </transition>
            </DialogPortal>
        </DialogRoot>
    </AuthenticatedLayout>

</template>
