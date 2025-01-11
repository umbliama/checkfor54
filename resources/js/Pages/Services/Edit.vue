<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideMenu from '@/Layouts/SideMenu.vue';
import {MenuItem, MenuItems, Menu, MenuButton} from '@headlessui/vue';
import {Link, router} from '@inertiajs/vue3';
import axios from 'axios';
import {reactive, watch} from 'vue';
import store from '../../../store/index';
import {computed, onMounted, ref} from 'vue';
import ServiceModal from '@/Components/ServiceModal.vue';
import ServicesDialog from "@/Components/Services/ServicesDialog.vue";
import ServicesNav2 from "@/Components/Services/ServicesNav2.vue";
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
} from 'radix-vue';
import {
    AccordionContent,
    AccordionHeader,
    AccordionItem,
    AccordionRoot,
    AccordionTrigger
} from "radix-vue";
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";

const props = defineProps({
    service: Object,
    equipment: Object,
    subservices: Object,
    contragents: Array
})

const is_dialog_open = ref(false);

const subEquipment = computed(() => store.getters['services/getSubEquipment']);
const selectedEquipment = computed(() => store.getters['services/getSelectedEquipment']);
const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedSubEquipmentArray = computed(() => store.getters['services/getSubSelectedEquipmentObjects']);
const selectedEquipmentService = computed(() => store.getters['services/getSelectedEquipmentService']);
const subRowsCount = computed(() => store.getters['services/getsubRowsCount']);

const incSubRow = () => {
    store.dispatch('services/updateIncSubRowsCount');
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

const updateByKey = (index, field, value) => {
    store.dispatch('services/updateSubSelectedEquipmentObjectsByKey', {index, field, value});

}

const showModal = (value) => {
    store.dispatch('services/updateModalShown', value)
}

watch(selectedEquipment, async (newValue, oldValue) => {
    if (newValue) {
        try {
            const response = await fetch(`/api/equipment/${newValue}`);
            const data = await response.json();
            store.dispatch('services/updateSelectedEquipmentService', data);
        } catch (error) {
            console.error(error);
        }
    }
});

watch(subEquipmentArray, async (newValue, oldValue) => {
    if (newValue.length) {
        const lastValue = newValue[newValue.length - 1];
        try {
            const response = await fetch(`/api/equipment/${lastValue}`);
            const data = await response.json();
            store.dispatch('services/updateSubEquipment', data);
        } catch (error) {
            console.error(error);
        }
    }
}, {deep: true});

let dateResult = ref(0)


const calculateResult = () => {
    const {period_start_date, period_end_date, operating} = form;

    // Проверка на валидность дат
    if (!period_start_date || !period_end_date) {
        dateResult = 0;
        return;
    }

    // Преобразование дат в объекты
    const startDate = new Date(period_start_date);
    const endDate = new Date(period_end_date);

    // Проверка на корректность дат
    if (isNaN(startDate) || isNaN(endDate)) {
        dateResult = 0;
        return;
    }
    const diffInDays = (endDate - startDate) / (1000 * 60 * 60 * 24);

    if (operating === 0) {
        dateResult = diffInDays + 1
        console.log(diffInDays)
    } else {
        dateResult = (diffInDays) + 1 - (operating / 24);
        console.log(dateResult)

    }

    form.store = dateResult.toFixed(2)
}

const modalShown = computed(() => store.getters['services/getModalShown']);


const form = reactive({
    contragent_id: props.service.contragent_id ?? null,
    shipping_date: props.service.shipping_date?.split(' ')[0] ?? null,
    service_number: props.service.service_number ?? null,
    service_date: props.service.service_date?.split(' ')[0] ?? null,
    period_start_date: null,
    return_date: props.service.return_date?.split(' ')[0] ?? null,
    period_end_date: props.service.preiod_end_date?.split(' ')[0] ?? null,
    store: null,
    operating: 0,
    contract: 0,
    return_reason: null,
    active: props.service.active ?? null,
    income: null,
    equipment_id: props.service.equipment_id ?? null,

})

function submit() {
    const cleanedSubEquipment = selectedSubEquipmentArray.value.filter(sub => {
        return sub.subequipment_id && sub.shipping_date && sub.period_start_date;
    });
    router.put(`/services/${props.service.id}`, {
        equipment_id: form.equipment_id,
        contragent_id: form.contragent_id,
        shipping_date: form.shipping_date,
        service_number: form.service_number,
        service_date: form.service_date,
        period_start_date: form.period_start_date,
        return_date: form.return_date,
        period_end_date: form.period_end_date,
        store: form.store ? dateResult : 0,
        operating: form.operating,
        return_reason: form.return_reason,
        active: form.active,
        income: form.income,
        subEquipment: JSON.parse(JSON.stringify(cleanedSubEquipment))
    }, {
        onError: (error) => {
            console.log(error)
        }
    })
}

const test_rows = ref([
    {
        id: 2,
        subrows: [{id:1}, {id:4}, {id:10}]
    },
    {
        id: 4,
        subrows: [{id:22}]
    }
]);

// const subrows_els = ref([]);
//
// function toggleSubrows(row_index) {
//     console.log(subrows_els.value[row_index]);
// }
</script>

<template>
    <AuthenticatedLayout>
        <ServicesDialog v-model="is_dialog_open" />

        <div class="p-5">
            <ServicesNav2 title="Редактор аренды" />

            <div class="mt-9 text-nowrap">
                <div class="justify-between lg:flex">
                    <div class="space-y-2 lg:space-y-4">
                        <div class="items-center space-y-2 lg:flex lg:space-y-0">
                            <label class="flex items-center p-1 bg-bg1 lg:bg-transparent">
                                <span class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0">Номер аренды:</span>
                                <span class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                                <div class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg overflow-hidden bg-white lg: lg:text-basew-auto lg:bg-[#F3F3F8]">
                                    <span class="inline-block pl-2 font-medium">№</span>
                                    <input
                                        v-model="form.service_number"
                                        type="text"
                                        class="block grow p-2 w-[177px] h-9 rounded-lg bg-inherit"
                                    />
                                </div>
                            </label>
                            <label class="flex items-center p-1 bg-bg1 lg:ml-2 lg:bg-transparent">
                                <span class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0">от</span>
                                <span class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                                <div class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:w-auto lg:text-base lg:bg-[#F3F3F8]">
                                    <span class="hidden pl-2 font-medium lg:inline-block ">Дата</span>
                                    <input
                                        v-model="form.service_date"
                                        type="date"
                                        class="block grow p-2 w-[177px] h-9 rounded-lg bg-inherit"
                                        onclick="this.showPicker()"
                                    />
                                </div>
                            </label>
                        </div>
                        <label class="flex items-center p-1 bg-bg1 lg:bg-transparent">
                            <span class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-[52px] lg:p-0 lg:text-base lg:border-0">Заказчик:</span>
                            <span class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                            <div class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:grow lg:text-base lg:w-auto lg:bg-[#F3F3F8]">
                                <select
                                    v-model="form.contragent_id"
                                    class="block grow p-2 w-full h-9 rounded-lg bg-inherit font-medium lg:w-[186px]"
                                >
                                    <option value="">Выберите</option>
                                    <option v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
                                </select>
                            </div>
                        </label>
                    </div>


                    <div class="mt-2 space-y-2 lg:space-y-4 lg:mt-0">
                        <label class="flex items-center p-1 bg-bg1 lg:justify-between lg:bg-transparent">
                            <span class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0">Статус:</span>
                            <span class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                            <div class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:w-auto lg:text-base lg:bg-[#F3F3F8]">
                                <select
                                    v-model="form.active"
                                    class="block grow p-2 w-full h-9 rounded-lg bg-inherit font-medium lg:w-[186px]"
                                >
                                    <option value="0" selected>Аренда закрыта</option>
                                    <option value="1">Аренда открыта</option>
                                </select>
                            </div>
                        </label>
                        <label class="flex items-center p-1 bg-bg1 lg:justify-between lg:bg-transparent">
                            <span class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0">Договор:</span>
                            <span class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                            <div class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:w-auto lg:text-base lg:bg-[#F3F3F8]">
                                <select
                                    v-model="form.contract"
                                    class="block grow p-2 w-[186px] h-9 rounded-lg bg-inherit font-medium"
                                >
                                    <option value="0" selected>Договор 0</option>
                                    <option value="1">Договор 1</option>
                                </select>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-start justify-between w-full max-w-full overflow-x-hidden my-6 lg:flex-row lg:items-center">
                <div class="relative flex items-center flex-col w-full px-8 overflow-x-auto bg-bg1 lg:flex-row lg:w-auto">
                    <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                    <ul class="relative flex items-center w-full font-medium space-x-8 overflow-x-auto">
                        <li>
                            <button
                                type="button"
                                class="flex items-center border-b-2 border-transparent py-3 text-sm text-[#A2A9B0]"
                            >Январь</button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center border-b-2 border-transparent py-3 text-sm text-[#A2A9B0]"
                            >Февраль</button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent !border-[#001D6C] text-[#001D6C]"
                            >
                                Март
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent"
                            >
                                Апрель
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent"
                            >
                                Май
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent"
                            >
                                Июнь
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent"
                            >
                                Июль
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent"
                            >
                                Август
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent"
                            >
                                Сентябрь
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent"
                            >
                                Октябрь
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent"
                            >
                                Ноябрь
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="flex items-center py-3 text-sm border-b-2 border-transparent"
                            >
                                Декабрь
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="flex items-center mt-4 ml-0 lg:ml-4 lg:mt-0">
                    <button type="button" class="flex">
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
                    <p class="mx-2.5">2024</p>
                    <button type="button" class="flex">
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

            <div class="relative flex items-center flex-col lg:flex-row">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul class="relative flex items-center w-full font-medium space-x-8 overflow-x-auto">
                    <li>
                        <button
                            type="button"
                            class="flex items-center py-3 text-sm border-b-2 border-transparent !border-[#001D6C] text-[#001D6C]"
                        >
                            Аренда
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="flex items-center py-3 text-sm border-b-2 border-transparent"
                        >
                            Доп. услуги
                        </button>
                    </li>
                </ul>
            </div>

            <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[930px] text-xs">
                    <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <button
                            type="button"
                            class="shrink-0 flex items-center w-[44px] py-2.5 px-2 bg-violet-full/5"
                            @click.prevent="incSubRow"
                        >
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.9444 8.69444C14.9444 8.31091 14.6335 8 14.25 8C13.8665 8 13.5556 8.31091 13.5556 8.69444V13.5556H8.69444C8.31091 13.5556 8 13.8665 8 14.25C8 14.6335 8.31091 14.9444 8.69444 14.9444H13.5556V19.8056C13.5556 20.1891 13.8665 20.5 14.25 20.5C14.6335 20.5 14.9444 20.1891 14.9444 19.8056V14.9444H19.8056C20.1891 14.9444 20.5 14.6335 20.5 14.25C20.5 13.8665 20.1891 13.5556 19.8056 13.5556H14.9444V8.69444Z" fill="#484964"/>
                                <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08"/>
                            </svg>
                        </button>
                        <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">Оборудование</div>
                        <div class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2 cursor-pointer">Дата отгрузки</div>
                        <div class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2">Начало периода</div>
                        <div class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2">Хранение</div>
                        <div class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2">Наработка</div>
                        <div class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2">Окончание периода</div>
                        <div class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2">Дата возврата</div>
                        <div class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2">Причина возврата</div>
                        <div class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2">Доход</div>
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
                    <AccordionRoot type="multiple">
                        <AccordionItem v-for="(test_row, i) in test_rows" :key="test_row.id" :value="'item-'+test_row.id">
                            <AccordionHeader>
                                <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                        <UiHyperlink
                                            :hyperlink="'some.ru'"
                                            :item-id="12"
                                            endpoint="/services"
                                        />
                                    </div>
                                    <div class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2">
                                        <button
                                            v-if="!equipment"
                                            type="button"
                                            @click="openDialog"
                                        >Нажмите чтобы выбрать оборудование</button>
                                        <template v-else>
                                            {{ equipment.category.name }}
                                            {{ equipment.size.name }} {{
                                                equipment.series
                                            }}
                                            <AccordionTrigger class="group shrink-0 ml-2">
                                                <svg class="hidden group-data-[state='closed']:block" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z" fill="#242533"/>
                                                </svg>
                                                <svg class="block group-data-[state='closed']:hidden" width="24" height="24" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.27174 9.29127C2.27174 8.9599 2.54037 8.69127 2.87174 8.69127H14.6283C14.9596 8.69127 15.2283 8.9599 15.2283 9.29127C15.2283 9.62265 14.9596 9.89127 14.6283 9.89127H2.87174C2.54037 9.89127 2.27174 9.62265 2.27174 9.29127Z" fill="#242533"/>
                                                </svg>
                                            </AccordionTrigger>

                                            <input type="hidden" v-model="equipment.equipment_id" />
                                        </template>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent" onclick="this.showPicker()" value="2024-04-11" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent" onclick="this.showPicker()" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" value="17"/>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" value="0"/>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent" onclick="this.showPicker()" value="2024-04-11" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent" onclick="this.showPicker()" value="2024-04-11" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%] ">
                                        <select class="block w-full h-full py-2.5 px-2 bg-transparent">
                                            <option value="">Проект</option>
                                            <option value="">Отказ</option>
                                        </select>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" value="1 280 000"/>
                                        <span class="shrink-0 inline-block mr-2">₽</span>
                                    </div>
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
                                                            <button
                                                                type="button"
                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                            >
                                                                Укомплектовать
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.5C10 3.36739 9.94732 3.24021 9.85355 3.14645C9.75979 3.05268 9.63261 3 9.5 3H1.5C1.36739 3 1.24021 3.05268 1.14645 3.14645C1.05268 3.24021 1 3.36739 1 3.5V12.5C1 12.6326 1.05268 12.7598 1.14645 12.8536C1.24021 12.9473 1.36739 13 1.5 13H9.5C9.63261 13 9.75979 12.9473 9.85355 12.8536C9.94732 12.7598 10 12.6326 10 12.5V10.5C10 10.3674 10.0527 10.2402 10.1464 10.1464C10.2402 10.0527 10.3674 10 10.5 10C10.6326 10 10.7598 10.0527 10.8536 10.1464C10.9473 10.2402 11 10.3674 11 10.5V12.5C11 12.8978 10.842 13.2794 10.5607 13.5607C10.2794 13.842 9.89782 14 9.5 14H1.5C1.10218 14 0.720644 13.842 0.43934 13.5607C0.158035 13.2794 0 12.8978 0 12.5L0 3.5C0 3.10218 0.158035 2.72064 0.43934 2.43934C0.720644 2.15804 1.10218 2 1.5 2H9.5C9.89782 2 10.2794 2.15804 10.5607 2.43934C10.842 2.72064 11 3.10218 11 3.5V5.5C11 5.63261 10.9473 5.75979 10.8536 5.85355C10.7598 5.94732 10.6326 6 10.5 6C10.3674 6 10.2402 5.94732 10.1464 5.85355C10.0527 5.75979 10 5.63261 10 5.5V3.5Z" fill="#21272A"/>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.14592 8.35402C4.09935 8.30758 4.06241 8.2524 4.0372 8.19165C4.012 8.13091 3.99902 8.06579 3.99902 8.00002C3.99902 7.93425 4.012 7.86913 4.0372 7.80839C4.06241 7.74764 4.09935 7.69247 4.14592 7.64602L7.14592 4.64602C7.2398 4.55213 7.36714 4.49939 7.49992 4.49939C7.63269 4.49939 7.76003 4.55213 7.85392 4.64602C7.9478 4.73991 8.00055 4.86725 8.00055 5.00002C8.00055 5.1328 7.9478 5.26013 7.85392 5.35402L5.70692 7.50002H14.4999C14.6325 7.50002 14.7597 7.5527 14.8535 7.64647C14.9472 7.74024 14.9999 7.86741 14.9999 8.00002C14.9999 8.13263 14.9472 8.25981 14.8535 8.35358C14.7597 8.44734 14.6325 8.50002 14.4999 8.50002H5.70692L7.85392 10.646C7.90041 10.6925 7.93728 10.7477 7.96244 10.8084C7.9876 10.8692 8.00055 10.9343 8.00055 11C8.00055 11.0658 7.9876 11.1309 7.96244 11.1916C7.93728 11.2523 7.90041 11.3075 7.85392 11.354C7.80743 11.4005 7.75224 11.4374 7.6915 11.4625C7.63076 11.4877 7.56566 11.5007 7.49992 11.5007C7.43417 11.5007 7.36907 11.4877 7.30833 11.4625C7.24759 11.4374 7.19241 11.4005 7.14592 11.354L4.14592 8.35402Z" fill="#21272A"/>
                                                                </svg>
                                                            </button>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <button
                                                                type="button"
                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                            >
                                                                Инцидент
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M9.82776 0.722047C9.89347 0.721929 9.95855 0.734763 10.0193 0.759815C10.08 0.784867 10.1352 0.821645 10.1818 0.868047L15.1318 5.81805C15.2255 5.91181 15.2782 6.03896 15.2782 6.17155C15.2782 6.30413 15.2255 6.43128 15.1318 6.52505C14.6518 7.00505 14.0598 7.11305 13.6288 7.11305C13.4518 7.11305 13.2938 7.09505 13.1688 7.07405L10.0348 10.208C10.1173 10.5405 10.1708 10.8794 10.1948 11.221C10.2408 11.923 10.1628 12.908 9.47476 13.596C9.381 13.6898 9.25384 13.7424 9.12126 13.7424C8.98868 13.7424 8.86152 13.6898 8.76776 13.596L5.93876 10.768L2.75676 13.95C2.56176 14.145 1.53776 14.852 1.34276 14.657C1.14776 14.462 1.85476 13.437 2.04976 13.243L5.23176 10.061L2.40376 7.23205C2.31002 7.13828 2.25737 7.01113 2.25737 6.87855C2.25737 6.74596 2.31002 6.61881 2.40376 6.52505C3.09176 5.83705 4.07676 5.75805 4.77876 5.80505C5.12042 5.82899 5.45936 5.88252 5.79176 5.96505L8.92576 2.83205C8.8996 2.67976 8.88622 2.52556 8.88576 2.37105C8.88576 1.94105 8.99376 1.34905 9.47476 0.868047C9.56847 0.774578 9.69541 0.722075 9.82776 0.722047Z" fill="#464F60"/>
                                                                </svg>
                                                            </button>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <Link
                                                                :href="'/'"
                                                                method="DELETE"
                                                                class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all"
                                                            >
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
                            </AccordionHeader>
                            <AccordionContent
                                class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden"
                            >
                                <div
                                    v-for="(test_subrow, i2) in test_row.subrows"
                                    class="flex border-b border-b-gray3 overflow-hidden [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                                >
                                    <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                        <UiHyperlink
                                            :hyperlink="'some.ru'"
                                            :item-id="12"
                                            endpoint="/services"
                                        />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                        <button
                                            v-if="!equipment"
                                            type="button"
                                            @click="openDialog"
                                        >Нажмите чтобы выбрать оборудование</button>
                                        <template v-else>
                                            {{ equipment.category.name }}
                                            {{ equipment.size.name }} {{
                                                equipment.series
                                            }}
                                            <input type="hidden" v-model="equipment.equipment_id" />
                                        </template>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent" onclick="this.showPicker()" value="2024-04-11" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent" onclick="this.showPicker()" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" value="17"/>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" value="0"/>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent" onclick="this.showPicker()" value="2024-04-11" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent" onclick="this.showPicker()" value="2024-04-11" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%] ">
                                        <select class="block w-full h-full py-2.5 px-2 bg-transparent">
                                            <option value="">Проект</option>
                                            <option value="">Отказ</option>
                                        </select>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" value="1 280 000"/>
                                        <span class="shrink-0 inline-block mr-2">₽</span>
                                    </div>
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
                                                            <Link :href="'/'"
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
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    </AccordionRoot>

                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button
                    class="inline-flex items-center justify-center py-3 px-7 font-medium tracking-wider bg-my-gray text-side-gray-text"
                    @click="submit">
                    Сохранить
                </button>
            </div>
        </div>

        <div class="mt-5 md:mx-auto sm:mx-auto">

            <div class="flex">
                <ServiceModal style="z-index: 1;" class="mt-14 absolute  bg-my-gray " v-if="modalShown"></ServiceModal>
                <div class="flex-1 sm:w-full">
                    <nav class="">
                        <div class=" sm:w-full lg:max-w-screen-xl px-4  mx-auto">
                            <div
                                class=" border-b-2 flex items-center lg:overflow-x-visible md:overflow-x-visible sm:overflow-y-hidden sm:whitespace-nowrap sm:overflow-x-auto ">
                                <ul class="flex items-center flex-row font-medium mt-0 space-x-8  rtl:space-x-reverse text-sm">
                                    <li class="flex  border-b-2  border-selected-blue pb-4 "
                                        @click="updateMenuLink('all')">
                                        <Link class="text-lg sm:text-md  text-selected-blue">Редактор аренды</Link>

                                    </li>
                                    <li class="flex pb-4">
                                        <Menu as="div" class="relative inline-block text-left">
                                            <div class="sm:overflow-x-visible">
                                                <MenuButton
                                                    class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                                    <svg width="14" height="4" viewBox="0 0 14 4" fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.33337 4C1.80294 4 1.29423 3.78929 0.91916 3.41421C0.544088 3.03914 0.333374 2.53043 0.333374 2C0.333374 1.46957 0.544088 0.960859 0.91916 0.585786C1.29423 0.210714 1.80294 0 2.33337 0C2.86381 0 3.37251 0.210714 3.74759 0.585786C4.12266 0.960859 4.33337 1.46957 4.33337 2C4.33337 2.53043 4.12266 3.03914 3.74759 3.41421C3.37251 3.78929 2.86381 4 2.33337 4ZM11.6667 4C11.1363 4 10.6276 3.78929 10.2525 3.41421C9.87742 3.03914 9.66671 2.53043 9.66671 2C9.66671 1.46957 9.87742 0.960859 10.2525 0.585786C10.6276 0.210714 11.1363 0 11.6667 0C12.1971 0 12.7058 0.210714 13.0809 0.585786C13.456 0.960859 13.6667 1.46957 13.6667 2C13.6667 2.53043 13.456 3.03914 13.0809 3.41421C12.7058 3.78929 12.1971 4 11.6667 4ZM7.00004 4C6.46961 4 5.9609 3.78929 5.58583 3.41421C5.21075 3.03914 5.00004 2.53043 5.00004 2C5.00004 1.46957 5.21075 0.960859 5.58583 0.585786C5.9609 0.210714 6.46961 0 7.00004 0C7.53047 0 8.03918 0.210714 8.41426 0.585786C8.78933 0.960859 9.00004 1.46957 9.00004 2C9.00004 2.53043 8.78933 3.03914 8.41426 3.41421C8.03918 3.78929 7.53047 4 7.00004 4Z"
                                                            fill="#697077"/>
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
                                                    class="absolute top-6 z-200 right-2  w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                    <div class="py-1">
                                                        <MenuItem>
                                                            <Link :href="route('services.create')"
                                                                  class="flex items-center justify-around"
                                                                  :class="['block px-4 py-2 text-sm']">Создать аренду
                                                                <svg width="16" height="16" viewBox="0 0 16 16"
                                                                     fill="none"
                                                                     xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M8.74999 2.75004C8.74999 2.33582 8.4142 2.00004 7.99999 2.00004C7.58578 2.00004 7.24999 2.33582 7.24999 2.75004V7.25H2.75C2.33579 7.25 2 7.58579 2 8C2 8.41422 2.33579 8.75 2.75 8.75H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75H13.25C13.6642 8.75 14 8.41422 14 8C14 7.58579 13.6642 7.25 13.25 7.25H8.74999V2.75004Z"
                                                                        fill="#464F60"/>
                                                                </svg>


                                                            </Link>
                                                        </MenuItem>

                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>

                                    </li>

                                </ul>

                            </div>
                        </div>

                    </nav>


                    <div class="lg:block md:hidden sm:hidden grid grid-cols-2 gap-6 flex mt-4">
                        <div class="flex whitespace-nowrap  justify-between items-center overflow-x-auto ">
                            <div class="flex items-center flex-2 mx-3">
                                <label class="items-center mr-2" for="rent_num">Номер аренды:</label>

                                <!-- SVG Icon -->
                                <input v-model="form.service_number" id="rent_num"
                                       class="w-full bg-input-gray rounded-lg border-none border-gray-200"
                                       placeholder="№" type="text">


                                <div class="flex ml-2 items-center">
                                    <label class="" for="rent_date">от</label>
                                    <div class="flex mx-3">
                                        <!-- SVG Icon -->
                                    </div>
                                    <input v-model="form.service_date" id="rent_date"
                                           class="w-full  bg-input-gray border-none rounded-lg"
                                           type="date" placeholder="Дата">
                                </div>
                            </div>


                            <div class="flex items-center flex-4">
                                <label class="" for="rent_status">Статус</label>
                                <div class="flex mx-3">
                                    <!-- SVG Icon -->
                                </div>
                                <select v-model="form.active" id="rent_status"
                                        class="w-full bg-input-gray  rounded-lg border-none">
                                    <option value="">Выберите</option>
                                    <option value="0">Не активна</option>
                                    <option value="1">Активна</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <div
                                class="flex flex-1 max-w-[600px] mt-2 flex-grow whitespace-nowrap items-center overflow-x-auto  px-4 py-2">
                                <label class="" for="rent_customer">Заказчик</label>

                                <select v-model="form.contragent_id" id="rent_customer"
                                        class="border-none bg-input-gray  rounded-lg ml-12 w-full">
                                    <option value="">Выберите</option>
                                    <option v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
                                </select>
                            </div>
                            <div class="flex items-center flex-4">
                                <label class="" for="rent_status">Договор</label>
                                <div class="flex mx-3">
                                    <!-- SVG Icon -->
                                </div>
                                <select v-model="form.active" id="rent_status"
                                        class="w-full bg-input-gray  rounded-lg border-none">
                                    <option value="">Выберите</option>
                                    <option value="0">Не активна</option>
                                    <option value="1">Активна</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="lg:hidden md:flex sm:flex flex flex-col mt-4">
                        <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
                            <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_num">Номер аренды</label>
                            <div class="flex mx-3">
                                <!-- SVG Icon -->
                            </div>
                            <input v-model="form.service_number" id="rent_num"
                                   class="w-full border-r-0 border-t-0 border-l-0 border-b-2 border-gray-200"
                                   placeholder="№" type="text">
                        </div>

                        <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
                            <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_date">От</label>
                            <div class="flex mx-3">
                                <!-- SVG Icon -->
                            </div>
                            <input v-model="form.service_date" id="rent_date"
                                   class="w-full border-r-0 border-t-0 border-l-0 border-b-2 border-gray-200"
                                   type="date" placeholder="Дата">
                        </div>

                        <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
                            <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_customer">Заказчик</label>
                            <div class="flex mx-3">
                                <!-- SVG Icon -->
                            </div>
                            <select v-model="form.contragent_id" id="rent_customer" class="w-full">
                                <option value="">Выберите</option>
                                <option v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
                            </select>
                        </div>
                        <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
                            <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_status">Статус</label>
                            <div class="flex mx-3">
                                <!-- SVG Icon -->
                            </div>
                            <select v-model="form.active" id="rent_status" class="w-full">
                                <option value="">Выберите</option>
                                <option value="0">Не активна</option>
                                <option value="1">Активна</option>
                            </select>
                        </div>
                    </div>


                    <div class="p-4 overflow-x-auto whitespace-nowrap">
                        <table class="table-auto bg-table-gray w-full ">
                            <!-- Table Head -->
                            <thead>
                            <tr class="bg-table-gray">
                                <th class="bg-violet p-4">
                                    <a @click.prevent="incSubRow" class="flex justify-between" href="">

                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08"/>
                                            <path
                                                d="M15 6.5C15 5.94772 14.5523 5.5 14 5.5V5.5C13.4477 5.5 13 5.94772 13 6.5V21.5C13 22.0523 13.4477 22.5 14 22.5V22.5C14.5523 22.5 15 22.0523 15 21.5V6.5Z"
                                                fill="#4A496C"/>
                                            <rect width="2" height="17" rx="1" transform="matrix(0 -1 -1 0 22 15)"
                                                  fill="#4A496C"/>
                                        </svg>
                                    </a>
                                </th>
                                <th class="p-4">Оборудование</th>
                                <th class="p-4">Дата отгрузки</th>
                                <th class="p-4">Начало периода</th>
                                <th class="p-4">Хранение</th>
                                <th class="p-4">Наработка</th>
                                <th class="p-4">Окончание периода</th>
                                <th class="p-4">Дата возврата</th>
                                <th class="p-4">
                                    <div>
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

                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr>
                                <td class="flex justify-center items-center">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15.9999 12.6671V16.667C15.9999 17.0206 15.8594 17.3598 15.6094 17.6098C15.3594 17.8599 15.0202 18.0003 14.6666 18.0003H7.33332C6.9797 18.0003 6.64057 17.8599 6.39052 17.6098C6.14047 17.3598 6 17.0206 6 16.667V9.33375C6 8.98013 6.14047 8.641 6.39052 8.39095C6.64057 8.1409 6.9797 8.00043 7.33332 8.00043H11.3333"
                                            stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round"/>
                                        <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2"
                                              stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                        <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2"
                                              stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>


                                </td>
                                <td>
                                    <!-- Show a button or link to trigger the modal to select equipment -->
                                    <button v-if="!equipment" @click="showModal(true)"
                                            class=" text-side-gray-text px-4 py-2 rounded">
                                        Нажмите чтобы выбрать оборудование
                                    </button>

                                    <!-- When equipment is selected, display the details and pass the id to v-model -->
                                    <div v-else class="p-4 bg-my-gray border whitespace-nowrap flex">
                                        <p> {{ equipment.category.name }} {{ equipment.size.name }} {{
                                                equipment.series
                                            }}</p>
                                        <!-- Hidden input to hold the selected equipment id for the form -->
                                        <input type="hidden" v-model="form.equipment_id">
                                    </div>
                                </td>

                                <td><input v-model="form.shipping_date" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input v-model="form.period_start_date" @input="calculateResult" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input v-model="form.store" type="number"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input v-model="form.operating" type="number"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input v-model="form.period_end_date" @input="calculateResult" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input v-model="form.return_date" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input type="text"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input type="text"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td>
                                    <div class="flex items-center justify-around">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                  fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                  fill="#21272A"/>
                                        </svg>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.5 2C9.5 2.82843 8.82843 3.5 8 3.5C7.17157 3.5 6.5 2.82843 6.5 2C6.5 1.17157 7.17157 0.5 8 0.5C8.82843 0.5 9.5 1.17157 9.5 2Z"
                                                fill="#687182"/>
                                            <path
                                                d="M9.5 8C9.5 8.82843 8.82843 9.5 8 9.5C7.17157 9.5 6.5 8.82843 6.5 8C6.5 7.17157 7.17157 6.5 8 6.5C8.82843 6.5 9.5 7.17157 9.5 8Z"
                                                fill="#687182"/>
                                            <path
                                                d="M9.5 14C9.5 14.8284 8.82843 15.5 8 15.5C7.17157 15.5 6.5 14.8284 6.5 14C6.5 13.1716 7.17157 12.5 8 12.5C8.82843 12.5 9.5 13.1716 9.5 14Z"
                                                fill="#687182"/>
                                        </svg>

                                    </div>
                                </td>
                            </tr>
                            <tr class="border-l-2 border-violet-full" v-for="(item, index) in subEquipment "
                                :key="index"
                                v-if="subEquipment.length > 0">

                                <td class="flex justify-center items-center">
                                    <svg width="40" height="40" viewBox="0 0 24 24"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15.9999 12.6671V16.667C15.9999 17.0206 15.8594 17.3598 15.6094 17.6098C15.3594 17.8599 15.0202 18.0003 14.6666 18.0003H7.33332C6.9797 18.0003 6.64057 17.8599 6.39052 17.6098C6.14047 17.3598 6 17.0206 6 16.667V9.33375C6 8.98013 6.14047 8.641 6.39052 8.39095C6.64057 8.1409 6.9797 8.00043 7.33332 8.00043H11.3333"
                                            stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round"/>
                                        <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2"
                                              stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                        <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2"
                                              stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>
                                </td>
                                <td colspan="1">
                                    <button v-if="!subEquipment" @click="showModal(true)"
                                            class=" text-side-gray-text px-4 py-2 rounded">
                                        Нажмите чтобы выбрать оборудование
                                    </button>

                                    <div class="p-4 bg-my-gray border whitespace-nowrap flex" v-else>
                                        <p> {{ item.category.name }} {{ item.size.name }} {{
                                                item.series
                                            }}</p>
                                    </div>

                                    <input type="text" v-model="form.subEquipment" placeholder="Sub-equipment name"
                                           class="input hidden border-r-0 border-t-0 border-b-0 border-l-2 border-violet-full bg-input-gray"/>
                                </td>
                                <td><input @change="updateByKey(index,'subequipment_id',item.id)"
                                           @input="updateByKey(index, 'shipping_date', $event.target.value)" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>

                                <td><input @input="updateByKey(index, 'period_start_date', $event.target.value)"
                                           v-model="item.period_start_date" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input v-model="form.store" type="number"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input v-model="form.operating" type="number"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input v-model="form.period_end_date" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input v-model="form.return_date" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input @input="updateByKey(index, 'commentary', $event.target.value)"
                                           v-model="item.commentary"
                                           type="text"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>

                                <td>
                                    <div class="flex items-center justify-around">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                  fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                  fill="#21272A"/>
                                        </svg>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.5 2C9.5 2.82843 8.82843 3.5 8 3.5C7.17157 3.5 6.5 2.82843 6.5 2C6.5 1.17157 7.17157 0.5 8 0.5C8.82843 0.5 9.5 1.17157 9.5 2Z"
                                                fill="#687182"/>
                                            <path
                                                d="M9.5 8C9.5 8.82843 8.82843 9.5 8 9.5C7.17157 9.5 6.5 8.82843 6.5 8C6.5 7.17157 7.17157 6.5 8 6.5C8.82843 6.5 9.5 7.17157 9.5 8Z"
                                                fill="#687182"/>
                                            <path
                                                d="M9.5 14C9.5 14.8284 8.82843 15.5 8 15.5C7.17157 15.5 6.5 14.8284 6.5 14C6.5 13.1716 7.17157 12.5 8 12.5C8.82843 12.5 9.5 13.1716 9.5 14Z"
                                                fill="#687182"/>
                                        </svg>

                                    </div>
                                </td>

                            </tr>
                            <tr v-for="item in subRowsCount" v-if="subRowsCount > 0">

                                <td class="flex justify-center items-center">
                                    <svg width="40" height="40" viewBox="0 0 24 24"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15.9999 12.6671V16.667C15.9999 17.0206 15.8594 17.3598 15.6094 17.6098C15.3594 17.8599 15.0202 18.0003 14.6666 18.0003H7.33332C6.9797 18.0003 6.64057 17.8599 6.39052 17.6098C6.14047 17.3598 6 17.0206 6 16.667V9.33375C6 8.98013 6.14047 8.641 6.39052 8.39095C6.64057 8.1409 6.9797 8.00043 7.33332 8.00043H11.3333"
                                            stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round"/>
                                        <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2"
                                              stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                        <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2"
                                              stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>
                                </td>
                                <td colspan="5">
                                    <input type="text" placeholder="Sub-equipment name"
                                           class="input hidden border-r-0 border-t-0 border-b-0 border-l-2 border-violet-full bg-input-gray"/>
                                    <button @click="showModal(true)" class=" text-side-gray-text px-4 py-2 rounded">
                                        Нажмите чтобы выбрать оборудование
                                    </button>
                                </td>

                            </tr>
                            </tbody>
                        </table>

                        <!-- Add New Row Button -->

                    </div>

                    <!-- Cards Layout -->
                    <div class="grid grid-cols-3 mt-3 gap-4">
                        <div v-for="column in columns" class="bg-white border-2 border-my-gray-500 shadow rounded p-4">
                            <div class="flex items-center justify-between">
                                <h3>Хронология</h3>
                                <Link @click="toggleDropdown">

                                    <Menu as="div" class="relative inline-block text-left">
                                        <div>
                                            <MenuButton
                                                class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                                <svg width="20" height="6" viewBox="0 0 20 6" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M3 4C3.26522 4 3.51957 3.89464 3.70711 3.70711C3.89464 3.51957 4 3.26522 4 3C4 2.73478 3.89464 2.48043 3.70711 2.29289C3.51957 2.10536 3.26522 2 3 2C2.73478 2 2.48043 2.10536 2.29289 2.29289C2.10536 2.48043 2 2.73478 2 3C2 3.26522 2.10536 3.51957 2.29289 3.70711C2.48043 3.89464 2.73478 4 3 4ZM3 6C2.20435 6 1.44129 5.68393 0.87868 5.12132C0.31607 4.55871 0 3.79565 0 3C0 2.20435 0.31607 1.44129 0.87868 0.87868C1.44129 0.31607 2.20435 0 3 0C3.79565 0 4.55871 0.31607 5.12132 0.87868C5.68393 1.44129 6 2.20435 6 3C6 3.79565 5.68393 4.55871 5.12132 5.12132C4.55871 5.68393 3.79565 6 3 6ZM17 6C16.2044 6 15.4413 5.68393 14.8787 5.12132C14.3161 4.55871 14 3.79565 14 3C14 2.20435 14.3161 1.44129 14.8787 0.87868C15.4413 0.31607 16.2044 0 17 0C17.7956 0 18.5587 0.31607 19.1213 0.87868C19.6839 1.44129 20 2.20435 20 3C20 3.79565 19.6839 4.55871 19.1213 5.12132C18.5587 5.68393 17.7956 6 17 6ZM17 4C17.2652 4 17.5196 3.89464 17.7071 3.70711C17.8946 3.51957 18 3.26522 18 3C18 2.73478 17.8946 2.48043 17.7071 2.29289C17.5196 2.10536 17.2652 2 17 2C16.7348 2 16.4804 2.10536 16.2929 2.29289C16.1054 2.48043 16 2.73478 16 3C16 3.26522 16.1054 3.51957 16.2929 3.70711C16.4804 3.89464 16.7348 4 17 4ZM10 6C9.20435 6 8.44129 5.68393 7.87868 5.12132C7.31607 4.55871 7 3.79565 7 3C7 2.20435 7.31607 1.44129 7.87868 0.87868C8.44129 0.31607 9.20435 0 10 0C10.7956 0 11.5587 0.31607 12.1213 0.87868C12.6839 1.44129 13 2.20435 13 3C13 3.79565 12.6839 4.55871 12.1213 5.12132C11.5587 5.68393 10.7956 6 10 6ZM10 4C10.2652 4 10.5196 3.89464 10.7071 3.70711C10.8946 3.51957 11 3.26522 11 3C11 2.73478 10.8946 2.48043 10.7071 2.29289C10.5196 2.10536 10.2652 2 10 2C9.73478 2 9.48043 2.10536 9.29289 2.29289C9.10536 2.48043 9 2.73478 9 3C9 3.26522 9.10536 3.51957 9.29289 3.70711C9.48043 3.89464 9.73478 4 10 4Z"
                                                        fill="#697077"/>
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
                                                class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                <div class="py-1">
                                                    <MenuItem v-slot="{ active }">
                                                        <Link class="flex items-center justify-around">Добавить блок
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M8.74999 2.75003C8.74999 2.33582 8.4142 2.00003 7.99999 2.00003C7.58578 2.00003 7.24999 2.33582 7.24999 2.75003V7.25H2.75C2.33579 7.25 2 7.58578 2 8C2 8.41421 2.33579 8.75 2.75 8.75H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75H13.25C13.6642 8.75 14 8.41421 14 8C14 7.58578 13.6642 7.25 13.25 7.25H8.74999V2.75003Z"
                                                                    fill="#464F60"/>
                                                            </svg>
                                                        </Link>
                                                    </MenuItem>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>


                            </div>
                        </div>

                    </div>

                    <button @click="submit">Сохранить</button>
                </div>

            </div>
        </div>

    </AuthenticatedLayout>

</template>
