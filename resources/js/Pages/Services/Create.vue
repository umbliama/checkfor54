<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideMenu from '@/Layouts/SideMenu.vue';
import { MenuItem, MenuItems, Menu, MenuButton } from '@headlessui/vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive, toRaw, watch, nextTick } from 'vue';
import store from '../../../store/index';
import { computed, onMounted, ref } from 'vue';
import ServiceModal from '@/Components/ServiceModal.vue';
import ServicesNav2 from "@/Components/Services/ServicesNav2.vue";
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";
import ServicesDialog from "@/Components/Services/ServicesDialog.vue";
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
} from 'radix-vue';


const props = defineProps({
    contragents: Array,
    equipmentFormatted: Array,
})

const selectedEquipment = ref([]);

const is_dialog_open = ref(false);

const subEquipment = computed(() => store.getters['services/getSubEquipment']);
const rows = ref(selectedEquipment.value.length - 1)
const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedSubEquipmentArray = computed(() => store.getters['services/getSubSelectedEquipmentObjects']);
const selectedEquipmentService = computed(() => store.getters['services/getSelectedEquipmentService']);
const subRowsCount = computed(() => store.getters['services/getsubRowsCount']);
const equipmentType = computed(() => store.getters['services/getEquipmentType']);
const chosenEquipment = computed(() => store.getters['services/getChosenEquipment']);
const getActiveEquipmentId = computed(() => store.getters['services/getActiveEquipmentId']);
const getActiveSubEquipmentId = computed(() => store.getters['services/getActiveSubEquipmentId']);
const chosenEquipmentId = ref(null)


let requestObject = reactive([]);

const fillRequestObject = async (value) => {
    if (!Array.isArray(requestObject)) {
        requestObject = [];
    }

    requestObject.push({
        id: value,
        shipping_date: null,
        period_start_date: null,
        commentary: null,
        subEquipment: []
    });

    await nextTick();
    console.log("Updated requestObject:", requestObject);
};

const updateEquipmentByKey = (index, field, value) => {
    console.log("Before update:", requestObject);

    if (!requestObject || !Array.isArray(requestObject)) {
        console.error("requestObject is not initialized properly.");
        return;
    }

    if (index >= requestObject.length || requestObject[index] === undefined) {
        console.error(`Invalid index: ${index}. Array length: ${requestObject.length}`);
        return;
    }

    requestObject[index][field] = value;

    console.log("After update:", requestObject);
};
const incSubRow = (value) => {
    store.dispatch('services/updateIncSubRowsCount');
    store.dispatch('services/updateEquipmentType', 1)
    store.dispatch('services/updateChosenEquipment', value)
    const equipment = selectedEquipment.value[value];
    chosenEquipmentId.value = value
    console.log(value, equipment)
    if (equipment) {
        equipment.subRows += 1;
    }
    store.dispatch('services/updateSubSelectedEquipmentObjects', {
        subequipment_id: '',
        id: '',
        shipping_date: '',
        period_start_date: '',
        return_date: '',
        period_end_date: '',
        store: '',
        operating: "",
        return_reason: '',
        commentary: '',
    });
}


const updateSubEquipmentByIndex = (parentIndex, subIndex, data) => {
    if (!requestObject || !Array.isArray(requestObject)) {
        console.error("requestObject is not initialized properly.");
        return;
    }

    if (parentIndex >= requestObject.length || !requestObject[parentIndex]) {
        console.error(`Invalid parent index: ${parentIndex}`);
        return;
    }

    if (!Array.isArray(requestObject[parentIndex].subEquipment)) {
        requestObject[parentIndex].subEquipment = [];
    }

    if (subIndex !== null && subIndex < requestObject[parentIndex].subEquipment.length) {
        const updatedSubEquipment = [...requestObject[parentIndex].subEquipment];
        updatedSubEquipment[subIndex] = {
            ...updatedSubEquipment[subIndex],
            ...data
        };
        requestObject[parentIndex].subEquipment = updatedSubEquipment;
    } else {
        requestObject[parentIndex].subEquipment = [
            ...requestObject[parentIndex].subEquipment,
            data
        ];
    }

    console.log(`Updated requestObject[${parentIndex}].subEquipment:`, requestObject[parentIndex].subEquipment);
};

const incSubRowMain = () => {
    store.dispatch('services/updateIncSubRowsCount');
    store.dispatch('services/updateEquipmentType', 0)
}

const addSubEquipment = (id, data) => {
    const equipment = selectedEquipment.value[id];
    if (equipment) {
        equipment.subequipment.push(data)
    }
}

const updateByKey = (index, field, value) => {
    console.log(index, field, value)
    store.dispatch('services/updateSubSelectedEquipmentObjectsByKey', { index, field, value });
}


const addRows = () => {
    const equipment = selectedEquipment.value;
    store.dispatch('services/updateEquipmentType', 0);
    rows.value += 1
}
const showModal = (value) => {
    store.dispatch('services/updateModalShown', value)
}

function openDialog() {
    is_dialog_open.value = true;
}

const deleteMainEquipment = (id) => {
    const equipment = selectedEquipment.value[id];

    if(equipment) {
        equipment.slice(0,id);
    }
}

watch(getActiveEquipmentId, async (newValue, oldValue) => {
    if (newValue) {
        try {
            const response = await fetch(`/api/equipmentExtra/${newValue}`);
            const data = await response.json();
            const extendedEquipment = {
                ...data,
                shipping_date: null,
                period_start_date: null,
                commentary: null,
                subequipment: [],
                subRows: 0
            }
            selectedEquipment.value.push(extendedEquipment)
        } catch (error) {
            console.error(error);
        }
    }
}, { deep: true });

watch(getActiveSubEquipmentId, async (newValue, oldValue) => {
    if (newValue) {
        try {
            const response = await fetch(`/api/equipmentExtra/${newValue}`);
            const data = await response.json();
            const subequipment = {
                ...data,
                commentary: null,
                shipping_date: null,
                period_start_date: null,
            }
            addSubEquipment(chosenEquipmentId.value, subequipment)

        } catch (error) {
            console.error(error);
        }
    }
}, { deep: true });


const modalShown = computed(() => store.getters['services/getModalShown']);


const form = reactive({
    contragent_id: props.contragents[0].id,
    shipping_date: null,
    service_number: null,
    service_date: null,
    period_start_date: null,
    return_date: null,
    period_end_date: null,
    store: null,
    operating: null,
    return_reason: null,
    commentary: null,
    income: null,
    active: 0,
    contract: 0,
    equipment: null,

})

function submit() {
    console.log(selectedEquipment.value)
    const formattedRequestObject = selectedEquipment.value.map(equipment => ({
        equipment_id: equipment.equipment.id,
        shipping_date: equipment.shipping_date || null,
        period_start_date: equipment.period_start_date || null,
        commentary: equipment.commentary || null,
        subEquipment: (equipment.subequipment || []).filter(sub => sub
        ).map(sub => ({
            subequipment_id: sub.equipment_id,
            shipping_date: sub.shipping_date || null,
            period_start_date: sub.period_start_date || null,
            commentary: sub.commentary || null,
        }))
    })).filter(equipment => equipment.equipment_id);

    console.log("Formatted Request Object:", formattedRequestObject);

    router.post('/services', {
        contragent_id: form.contragent_id,
        service_number: form.service_number,
        service_date: form.service_date,
        active: form.active,
        equipment: JSON.parse(JSON.stringify(formattedRequestObject))
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <ServicesDialog v-model="is_dialog_open" />

        <div class="p-5">
            <ServicesNav2 title="Новая аренда" />

            <div class="mt-9 text-nowrap">
                <div class="justify-between lg:flex">
                    <div class="space-y-2 lg:space-y-4">
                        <div class="items-center space-y-2 lg:flex lg:space-y-0">
                            <label class="flex items-center p-1 bg-bg1 lg:bg-transparent">
                                <span
                                    class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0">Номер
                                    аренды:</span>
                                <span
                                    class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                                <div
                                    class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg overflow-hidden bg-white lg: lg:text-basew-auto lg:bg-[#F3F3F8]">
                                    <span class="inline-block pl-2 font-medium">№</span>
                                    <input v-model="form.service_number" type="text"
                                        class="block grow p-2 w-[177px] h-9 rounded-lg bg-inherit" />
                                </div>
                            </label>
                            <label class="flex items-center p-1 bg-bg1 lg:ml-2 lg:bg-transparent">
                                <span
                                    class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0">от</span>
                                <span
                                    class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                                <div
                                    class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:w-auto lg:text-base lg:bg-[#F3F3F8]">
                                    <span class="hidden pl-2 font-medium lg:inline-block ">Дата</span>
                                    <input v-model="form.service_date" type="date"
                                        class="block grow p-2 w-[177px] h-9 rounded-lg bg-inherit"
                                        onclick="this.showPicker()" />
                                </div>
                            </label>
                        </div>

                        <label class="flex items-center p-1 bg-bg1 lg:bg-transparent">
                            <span
                                class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-[52px] lg:p-0 lg:text-base lg:border-0">Заказч
                                к:</span>
                            <span
                                class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                            <div
                                class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:grow lg:text-base lg:w-auto lg:bg-[#F3F3F8]">
                                <select v-model="form.contragent_id"
                                    class="block grow p-2 w-full h-9 rounded-lg bg-inherit font-medium lg:w-[186px]">
                                    <option value="">Выберите</option>
                                    <option v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
                                </select>
                            </div>
                        </label>
                    </div>


                    <div class="mt-2 space-y-2 lg:space-y-4 lg:mt-0">
                        <label class="flex items-center p-1 bg-bg1 lg:justify-between lg:bg-transparent">
                            <span
                                class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0">Статус:</span>
                            <span
                                class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                            <div
                                class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:w-auto lg:text-base lg:bg-[#F3F3F8]">
                                <select v-model="form.active"
                                    class="block grow p-2 w-full h-9 rounded-lg bg-inherit font-medium lg:w-[186px]">
                                    <option value="0" selected>Аренда закрыта</option>
                                    <option value="1">Аренда открыта</option>
                                </select>
                            </div>
                        </label>
                        <label class="flex items-center p-1 bg-bg1 lg:justify-between lg:bg-transparent">
                            <span
                                class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0">Договор:</span>
                            <span
                                class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                            <div
                                class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:w-auto lg:text-base lg:bg-[#F3F3F8]">
                                <select v-model="form.contract"
                                    class="block grow p-2 w-[186px] h-9 rounded-lg bg-inherit font-medium">
                                    <option value="0" selected>Договор 0</option>
                                    <option value="1">Договор 1</option>
                                </select>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[1136px] text-xs">
                    <div
                        class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <button type="button" class="shrink-0 flex items-center w-[44px] py-2.5 px-2 bg-violet-full/5"
                            @click.prevent="addRows">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M14.9444 8.69444C14.9444 8.31091 14.6335 8 14.25 8C13.8665 8 13.5556 8.31091 13.5556 8.69444V13.5556H8.69444C8.31091 13.5556 8 13.8665 8 14.25C8 14.6335 8.31091 14.9444 8.69444 14.9444H13.5556V19.8056C13.5556 20.1891 13.8665 20.5 14.25 20.5C14.6335 20.5 14.9444 20.1891 14.9444 19.8056V14.9444H19.8056C20.1891 14.9444 20.5 14.6335 20.5 14.25C20.5 13.8665 20.1891 13.5556 19.8056 13.5556H14.9444V8.69444Z"
                                    fill="#484964" />
                                <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08" />
                            </svg>
                        </button>
                        <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">Оборудование</div>
                        <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Дата отгрузки
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Начало периода</div>
                        <div
                            class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                            Комментарий</div>
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
                    <!-- <div
                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                            <UiHyperlink v-if="selectedEquipmentService"
                                :hyperlink="selectedEquipmentService?.hyperlink" :item-id="selectedEquipmentService?.id"
                                endpoint="/services" />
                        </div>
                        <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">
                            <button v-if="!selectedEquipmentService.length" type="button" @click="openDialog">Нажмите
                                чтобы выбрать оборудование</button>
                            <template v-for="item in selectedEquipmentService">

                                {{ item.category.name }}
                                {{ item.size.name }} {{
                                    item.series
                                }}
                            </template>
</div>
<div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
    <input v-model="form.shipping_date" type="date" class="block w-full h-full px-2 bg-transparent"
        onclick="this.showPicker()" />
</div>
<div class="shrink-0 flex items-center w-[14.08%]">
    <input v-model="form.period_start_date" type="date" class="block w-full h-full px-2 bg-transparent"
        onclick="this.showPicker()" />
</div>
<div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
    <input v-model="form.commentary" type="text" class="block w-full h-full px-2 bg-transparent" />
</div>
<div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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

    <DropdownMenuRoot v-if="selectedEquipmentService">
        <DropdownMenuTrigger aria-label="Customise options">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                        <button @click="incSubRow" type="button"
                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                            Укомплектовать
                            <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none"
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

                </DropdownMenuContent>
            </transition>
        </DropdownMenuPortal>
    </DropdownMenuRoot>
</div>
</div> -->
                    <!-- <div v-for="(item, index) in subEquipment" :key="index" v-if="subEquipment.length > 0"
                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                            <UiHyperlink v-if="selectedEquipmentService"
                                :hyperlink="selectedEquipmentService?.hyperlink" :item-id="selectedEquipmentService?.id"
                                endpoint="/services" />
                        </div>
                        <div class="shrink-0 flex items-center w-[15.84%] !border-l-violet-full">
                            <button v-if="!subEquipment" class="block w-full h-full px-2 bg-transparent"
                                @click="openDialog">
                                Нажмите чтобы выбрать оборудование
                            </button>

                            <div v-else class="flex py-2.5 px-2">
                                {{ item.category.name }} {{ item.size.name }} {{ item.series }}
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                            <input type="date" class="block w-full h-full px-2 bg-transparent"
                                onclick="this.showPicker()" @change="updateByKey(index, 'subequipment_id', item.id)"
                                @input="updateByKey(index, 'shipping_date', $event.target.value)" />
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%]">
                            <input v-model="item.period_start_date" type="date"
                                class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()"
                                @input="updateByKey(index, 'period_start_date', $event.target.value)" />
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                            <input v-model="item.commentary"
                                @input="updateByKey(index, 'commentary', $event.target.value)" type="text"
                                class="block w-full h-full px-2 bg-transparent" />
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
                                                <button @click="incSubRow" type="button"
                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                    Укомплектовать
                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                            fill="#464F60" />
                                                        <path
                                                            d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                            fill="#464F60" />
                                                    </svg>
                                                </button>
                                            </DropdownMenuItem>

                                        </DropdownMenuContent>
                                    </transition>
                                </DropdownMenuPortal>
                            </DropdownMenuRoot>
                        </div>
                    </div> -->
                    <!-- <div v-if="subRowsCount > 0" v-for="item in subRowsCount - subEquipment.length"
                        class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <button class=" text-side-gray-text px-4 py-2 rounded" @click="openDialog">
                            Нажмите чтобы выбрать оборудование
                        </button>
                    </div> -->
                    <!-- <br>
                    <br>
                    <br> -->

                    <!-- для Макса -->

                    <div v-if="selectedEquipment.length === 0"
                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                            <UiHyperlink v-if="selectedEquipmentService"
                                :hyperlink="selectedEquipmentService?.hyperlink" :item-id="selectedEquipmentService?.id"
                                endpoint="/services" />
                        </div>
                        <div :class="{ '!border-l-violet-full': equipmentType === 1 }"
                            class="shrink-0 flex items-center w-[15.84%] ">
                            <button class="block w-full h-full px-2 bg-transparent text-left" @click="openDialog">
                                Нажмите чтобы выбрать оборудование 
                            </button>
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                            <input type="date" class="block w-full h-full px-2 bg-transparent"
                                onclick="this.showPicker()" />
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%]">
                            <input type="date" class="block w-full h-full px-2 bg-transparent"
                                onclick="this.showPicker()" />
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                            <input type="text" class="block w-full h-full px-2 bg-transparent" />
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
                                                <button type="button"
                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                    Укомплектовать
                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                            fill="#464F60" />
                                                        <path
                                                            d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                            fill="#464F60" />
                                                    </svg>
                                                </button>
                                            </DropdownMenuItem>

                                        </DropdownMenuContent>
                                    </transition>
                                </DropdownMenuPortal>
                            </DropdownMenuRoot>
                        </div>
                    </div>
                    <div v-if="rows === selectedEquipment.length"
                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                            <UiHyperlink v-if="selectedEquipmentService"
                                :hyperlink="selectedEquipmentService?.hyperlink" :item-id="selectedEquipmentService?.id"
                                endpoint="/services" />
                        </div>
                        <div :class="{ '!border-l-violet-full': equipmentType === 1 }"
                            class="shrink-0 flex items-center w-[15.84%] ">
                            <button class="block w-full h-full px-2 bg-transparent text-left" @click="openDialog">
                                Нажмите чтобы выбрать оборудование {{ selectedEquipmentService.length +
                                subEquipment.length + 2 }}
                            </button>
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                            <input type="date" class="block w-full h-full px-2 bg-transparent"
                                onclick="this.showPicker()" />
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%]">
                            <input type="date" class="block w-full h-full px-2 bg-transparent"
                                onclick="this.showPicker()" />
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                            <input type="text" class="block w-full h-full px-2 bg-transparent" />
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
                                                <button type="button"
                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                    Укомплектовать
                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                            fill="#464F60" />
                                                        <path
                                                            d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                            fill="#464F60" />
                                                    </svg>
                                                </button>
                                            </DropdownMenuItem>

                                        </DropdownMenuContent>
                                    </transition>
                                </DropdownMenuPortal>
                            </DropdownMenuRoot>
                        </div>
                    </div>

                    <div v-for="(equipment, index) in selectedEquipment">
                        <div
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                <UiHyperlink v-if="selectedEquipmentService"
                                    :hyperlink="selectedEquipmentService?.hyperlink"
                                    :item-id="selectedEquipmentService?.id" endpoint="/services" />
                            </div>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">{{ equipment.equipment.category.name }}
                                {{ equipment.equipment.size.name }} {{ equipment.equipment.series }}</div>
                            <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                                <input v-model="equipment.shipping_date" type="date"
                                    class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
                                <input v-model="equipment.period_start_date" type="date"
                                    class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                <input v-model="equipment.commentary" type="text"
                                    class="block w-full h-full px-2 bg-transparent" />
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

                                <DropdownMenuRoot v-if="selectedEquipmentService">
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
                                                    <button @click="incSubRow(index)" type="button"
                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                        Укомплектовать
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

                                            </DropdownMenuContent>
                                        </transition>
                                    </DropdownMenuPortal>
                                </DropdownMenuRoot>
                            </div>
                        </div>
                        <div>
                            <div v-for="(subEquipment, subindex) in equipment.subequipment"
                                class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                    <UiHyperlink v-if="selectedEquipmentService"
                                        :hyperlink="selectedEquipmentService?.hyperlink"
                                        :item-id="selectedEquipmentService?.id" endpoint="/services" />
                                </div>
                                <div class="shrink-0 flex items-center w-[15.84%] !border-l-violet-full">
                                    <div class="flex py-2.5 px-2">
                                        {{ subEquipment.equipment.category.name }} {{ subEquipment.equipment.size.name }} {{
                                            subEquipment.equipment.series }}
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                                    <input
                                        v-model="subEquipment.shipping_date"
                                        type="date" class="block w-full h-full px-2 bg-transparent"
                                        onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[14.08%]">
                                    <input
                                        v-model="subEquipment.period_start_date"
                                        type="date" class="block w-full h-full px-2 bg-transparent"
                                        onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                    <input type="text"
                                        v-model="subEquipment.commentary"
                                        class="block w-full h-full px-2 bg-transparent" />
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
                                                        <button type="button"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                            Укомплектовать
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

                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
                                </div>
                            </div>
                            <div  v-for="item in equipment.subRows - equipment.subequipment.length"
                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                            <UiHyperlink v-if="selectedEquipmentService"
                                :hyperlink="selectedEquipmentService?.hyperlink" :item-id="selectedEquipmentService?.id"
                                endpoint="/services" />
                        </div>
                        <div :class="{ '!border-l-violet-full': equipmentType === 1 }"
                            class="shrink-0 flex items-center w-[15.84%] ">
                            <button class="block w-full h-full px-2 bg-transparent text-left" @click="openDialog">
                                Нажмите чтобы выбрать оборудование 
                            </button>
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                            <input type="date" class="block w-full h-full px-2 bg-transparent"
                                onclick="this.showPicker()" />
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%]">
                            <input type="date" class="block w-full h-full px-2 bg-transparent"
                                onclick="this.showPicker()" />
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                            <input type="text" class="block w-full h-full px-2 bg-transparent" />
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
                                                <button type="button"
                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                    Укомплектовать
                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                            fill="#464F60" />
                                                        <path
                                                            d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                            fill="#464F60" />
                                                    </svg>
                                                </button>
                                            </DropdownMenuItem>

                                        </DropdownMenuContent>
                                    </transition>
                                </DropdownMenuPortal>
                            </DropdownMenuRoot>
                        </div>
                    </div>

                        </div>
                    </div>

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


    </AuthenticatedLayout>

</template>

<style scoped>
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    -o-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='19' height='18' viewBox='0 0 19 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12.9707 7.5L9.78872 10.682L6.60674 7.5' stroke='%23242533' stroke-width='1.2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
    background-position: calc(100% - 10px) center;
    padding-right: 30px;
    background-repeat: no-repeat;
}

input[type="date"]::-webkit-inner-spin-button,
input[type="date"]::-webkit-calendar-picker-indicator {
    display: none;
    -webkit-appearance: none;
}

input[type="date"] {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20 10V8C20 7.73478 19.8946 7.48043 19.7071 7.29289C19.5196 7.10536 19.2652 7 19 7H18V8C18 8.26522 17.8946 8.51957 17.7071 8.70711C17.5196 8.89464 17.2652 9 17 9C16.7348 9 16.4804 8.89464 16.2929 8.70711C16.1054 8.51957 16 8.26522 16 8V7H8V8C8 8.26522 7.89464 8.51957 7.70711 8.70711C7.51957 8.89464 7.26522 9 7 9C6.73478 9 6.48043 8.89464 6.29289 8.70711C6.10536 8.51957 6 8.26522 6 8V7H5C4.73478 7 4.48043 7.10536 4.29289 7.29289C4.10536 7.48043 4 7.73478 4 8V10H20ZM20 12H4V18C4 18.2652 4.10536 18.5196 4.29289 18.7071C4.48043 18.8946 4.73478 19 5 19H19C19.2652 19 19.5196 18.8946 19.7071 18.7071C19.8946 18.5196 20 18.2652 20 18V12ZM18 5H19C19.7956 5 20.5587 5.31607 21.1213 5.87868C21.6839 6.44129 22 7.20435 22 8V18C22 18.7956 21.6839 19.5587 21.1213 20.1213C20.5587 20.6839 19.7956 21 19 21H5C4.20435 21 3.44129 20.6839 2.87868 20.1213C2.31607 19.5587 2 18.7956 2 18V8C2 7.20435 2.31607 6.44129 2.87868 5.87868C3.44129 5.31607 4.20435 5 5 5H6V4C6 3.73478 6.10536 3.48043 6.29289 3.29289C6.48043 3.10536 6.73478 3 7 3C7.26522 3 7.51957 3.10536 7.70711 3.29289C7.89464 3.48043 8 3.73478 8 4V5H16V4C16 3.73478 16.1054 3.48043 16.2929 3.29289C16.4804 3.10536 16.7348 3 17 3C17.2652 3 17.5196 3.10536 17.7071 3.29289C17.8946 3.48043 18 3.73478 18 4V5Z' fill='%23697077'/%3E%3C/svg%3E%0A");
    background-position: calc(100% - 16px) center;
    background-repeat: no-repeat;
}
</style>
