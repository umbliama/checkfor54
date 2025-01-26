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
import ExtraServicesModal from '@/Components/ExtraServicesModal.vue';
import SaleNav from "@/Components/Sale/SaleNav.vue";
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger
} from "radix-vue";
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";
import SaleDialog from "@/Components/Sale/SaleDialog.vue";
import SaleServicesDialog from "@/Components/Sale/SaleServicesDialog.vue";

const props = defineProps({
    contragents: Array,
    equipmentFormatted: Array,
    saleStatuses: Object,
    extraServices: Object
})

const is_dialog_open = ref(false);

const subEquipment = computed(() => store.getters['services/getSubEquipment']);
const selectedEquipment = computed(() => store.getters['services/getSelectedEquipment']);
const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedSubEquipmentArray = computed(() => store.getters['services/getSubSelectedEquipmentObjects']);
const selectedEquipmentService = computed(() => store.getters['services/getSelectedEquipmentService']);
const subRowsCount = computed(() => store.getters['services/getsubRowsCount']);
const activeTab = computed(() => store.getters['sale/getActiveTab']);
const getExtraServices = computed(() => store.getters['sale/getExtraServices']);
const getCurrentIndex = computed(() => store.getters['sale/getCurrentIndex']);
const getSelectedServices = computed(() => store.getters['sale/getSelectedServices']);
const getRowsCount = computed(() => store.getters['sale/getRowsCount']);

const selectedServices = ref(new Array(subRowsCount.length).fill(null))

const addService = (service, subRowIndex) => {
    store.dispatch('sale/updateExtraServices', service, subRowIndex)
}
const removeExtraServiceAction = (index) => {
    console.log(index)
    store.dispatch('sale/removeExtraServiceAction', index)
}

const incSubRow = () => {
    store.dispatch('services/updateIncSubRowsCount');
    store.dispatch('sale/incCurrentIndex')
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

const incServiceRow = () => {
    store.dispatch('sale/updateIncRowsCount');
}

const updateActiveTab = (tab) => {
    store.dispatch('sale/updateActiveTab', tab)
}

const updateByKeyServices = (index, field, value) => {
    store.dispatch('sale/updateSelectedServicesObject', {index, field, value});

}

const updateByKey = (index, field, value) => {
    store.dispatch('services/updateSubSelectedEquipmentObjectsByKey', {index, field, value});

}

const showModal = (value) => {
    store.dispatch('services/updateModalShown', value)
}
const showModalServices = (value) => {
    store.dispatch('sale/showModal', value)
}
const form = reactive({
    contragent_id: null,
    shipping_date: null,
    sale_number: null,
    sale_date: null,
    commentary: null,
    status: null,
    price: null,
    equipment_id: selectedEquipment,
})


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


const modalShown = computed(() => store.getters['services/getModalShown']);
const modalShownServices = computed(() => store.getters['sale/getExtraServicesModal']);

function submit() {
    const cleanedSubEquipment = selectedSubEquipmentArray.value.filter(sub => {
        return sub.subequipment_id && sub.shipping_date && sub.price && sub.commentary
    });
    const cleanedServices = getSelectedServices.value.filter(sub => {
        return sub.item.item && sub.shipping_date && sub.price && sub.commentary
    });
    console.log(selectedSubEquipmentArray.value)
    console.log(cleanedServices)
    router.post('/sales', {
        equipment_id: form.equipment_id,
        contragent_id: form.contragent_id,
        shipping_date: form.shipping_date,
        sale_number: form.sale_number,
        sale_date: form.sale_date,
        commentary: form.commentary,
        status: form.status,
        price: form.price,
        subEquipment: JSON.parse(JSON.stringify(cleanedSubEquipment)),
        extraServices: cleanedServices,
        onSuccess: () => {
            store.dispatch("services/clearSubEquipmentArray")
        }
    })
}

</script>

<template>
    <AuthenticatedLayout>
        <SaleDialog v-model="is_dialog_open" />
        <SaleNav />
        <SaleServicesDialog v-if="modalShownServices" />

        <div class="p-5">
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
                                        v-model="form.sale_number"
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
                                        v-model="form.sale_date"
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
                                    <option v-for="(key, value) in saleStatuses" :value="value">{{ key }}</option>
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

            <div class="relative text-sm">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto lg:overflow-x-visible">
                    <li
                        :class="{ '!border-[#001D6C] text-[#001D6C]': activeTab === 'sale' }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        @click="updateActiveTab('sale')"
                    >
                        Продажа
                    </li>
                    <li
                        :class="{ '!border-[#001D6C] text-[#001D6C]': activeTab === 'extra' }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        @click="updateActiveTab('extra')"
                    >
                        Доп. услуги
                    </li>
                </ul>
            </div>

            <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[1136px] text-xs">
                    <template v-if="activeTab == 'sale'">
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
                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Дата отгрузки</div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">Комментарий</div>
                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Цена без НДС</div>
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
                        <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                <UiHyperlink
                                    v-if="selectedEquipmentService"
                                    :hyperlink="selectedEquipmentService?.hyperlink"
                                    :item-id="selectedEquipmentService?.id"
                                    endpoint="/services"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">
                                <button
                                    v-if="!selectedEquipmentService"
                                    type="button"
                                    @click="is_dialog_open = true"
                                >Нажмите чтобы выбрать оборудование</button>
                                <template v-else>
                                    {{ selectedEquipmentService?.category?.name }}
                                    {{ selectedEquipmentService?.size?.name }} {{
                                        selectedEquipmentService?.series
                                    }}
                                </template>
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                                <input v-model="form.shipping_date" type="date"
                                            class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                <input v-model="form.commentary" type="text" class="block w-full h-full px-2 bg-transparent"/>
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
                                <input v-model="form.price" type="text" class="block w-full h-full px-2 bg-transparent" />
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
                                                    <Link :href="route('services.destroy', selectedEquipmentService.id)"
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
                        <div
                            v-for="(item, index) in subEquipment"
                            :key="index"
                            v-if="subEquipment.length > 0"
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                <UiHyperlink
                                    v-if="selectedEquipmentService"
                                    :hyperlink="selectedEquipmentService?.hyperlink"
                                    :item-id="selectedEquipmentService?.id"
                                    endpoint="/services"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[15.84%] !border-l-violet-full">
                                <button
                                    v-if="!subEquipment"
                                    class="block w-full h-full px-2 bg-transparent"
                                    @click="openDialog"
                                >
                                    Нажмите чтобы выбрать оборудование
                                </button>

                                <div
                                    v-else
                                    class="flex py-2.5 px-2"
                                >
                                    {{ item.category.name }} {{ item.size.name }} {{ item.series }}
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                                <input
                                    type="date"
                                    class="block w-full h-full px-2 bg-transparent"
                                    onclick="this.showPicker()"
                                    @change="updateByKey(index,'subequipment_id',item.id)"
                                    @input="updateByKey(index, 'shipping_date', $event.target.value)"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                <input
                                    v-model="item.commentary"
                                    @input="updateByKey(index, 'commentary', $event.target.value)"
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
                                <input
                                    v-model="item.price"
                                    @input="updateByKey(index, 'price', $event.target.value)"
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                />
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
                                                    <Link
                                                        :href="route('services.edit', service.id)"
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
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <Link :href="route('services.destroy', item.id)"
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
                        <div
                            v-if="subRowsCount > 0"
                            v-for="item in subRowsCount"
                            class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <button
                                class=" text-side-gray-text px-4 py-2 rounded"
                                @click="is_dialog_open = true"
                            >
                                Нажмите чтобы выбрать оборудование
                            </button>
                        </div>
                    </template>
                    <template v-else-if="activeTab == 'extra'">
                        <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <button
                                type="button"
                                class="shrink-0 flex items-center w-[44px] py-2.5 px-2 bg-violet-full/5"
                                @click.prevent="incServiceRow"
                            >
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.9444 8.69444C14.9444 8.31091 14.6335 8 14.25 8C13.8665 8 13.5556 8.31091 13.5556 8.69444V13.5556H8.69444C8.31091 13.5556 8 13.8665 8 14.25C8 14.6335 8.31091 14.9444 8.69444 14.9444H13.5556V19.8056C13.5556 20.1891 13.8665 20.5 14.25 20.5C14.6335 20.5 14.9444 20.1891 14.9444 19.8056V14.9444H19.8056C20.1891 14.9444 20.5 14.6335 20.5 14.25C20.5 13.8665 20.1891 13.5556 19.8056 13.5556H14.9444V8.69444Z" fill="#484964"/>
                                    <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08"/>
                                </svg>
                            </button>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">Оборудование</div>
                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Дата перевозки</div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">Комментарий</div>
                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Цена без НДС</div>
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
                        <div
                            v-if="getSelectedServices.length > 0"
                            v-for="(item, index) in getSelectedServices"
                            :key="index"
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                <UiHyperlink
                                    v-if="selectedEquipmentService"
                                    :hyperlink="selectedEquipmentService?.hyperlink"
                                    :item-id="selectedEquipmentService?.id"
                                    endpoint="/services"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">
                                <button
                                    v-if="!getSelectedServices"
                                    type="button"
                                    @click="showModalServices(true)"
                                >
                                    Нажмите чтобы выбрать оборудование
                                </button>
                                <template v-else>{{ item.item.value }}</template>
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                                <input
                                    v-model="item.shipping_date"
                                    type="date"
                                    class="block w-full h-full px-2 bg-transparent"
                                    onclick="this.showPicker()"
                                    @change="updateByKeyServices(index, 'shipping_date', $event.target.value)"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                <input
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                    v-model="item.commentary"
                                    @input="updateByKeyServices(index, 'commentary', $event.target.value)"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
                                <input
                                    v-model="item.price"
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                    @input="updateByKeyServices(index, 'price', $event.target.value)"
                                />
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
                                                    </button>
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </transition>
                                    </DropdownMenuPortal>
                                </DropdownMenuRoot>
                            </div>
                        </div>
                        <div
                            v-if="getRowsCount > 0"
                            v-for="item in getRowsCount"
                            class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <button
                                class=" text-side-gray-text px-4 py-2 rounded"
                                @click="showModalServices(true)"
                            >
                                Нажмите чтобы выбрать услугу
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-5 py-3 px-2 text-right font-semibold text-xs text-gray1 bg-bg1">
                Стоимость доп. услуг: 1 280 000 ₽
            </div>
            <div class="mt-5 space-y-5 py-3 px-2 text-right font-semibold text-xs text-gray1 bg-bg1">
                <div>Итого без НДС: 12 800 000 ₽</div>
                <div>В том числе НДС (20%): 2 560 000 ₽</div>
                <div>Всего к оплате: 15 360 000 ₽</div>
            </div>

            <div class="flex justify-end mt-6">
                <button
                    class="inline-flex items-center justify-center py-3 px-7 font-medium tracking-wider bg-my-gray text-side-gray-text"
                    @click="submit">
                    Сохранить
                </button>
            </div>
        </div>

<!--        <div class="mt-5 md:mx-auto sm:mx-auto">

            <div class="flex">
                <ServiceModal style="z-index: 1;" class="mt-14 absolute  bg-my-gray " v-if="modalShown"></ServiceModal>
&lt;!&ndash;                <ExtraServicesModal style="z-index: 1;" class="mt-14 absolute  bg-my-gray " v-if="modalShownServices">
                </ExtraServicesModal>&ndash;&gt;
                <div class="flex-1 sm:w-full">
                    <nav class="">
                        <div class=" sm:w-full lg:max-w-screen-xl px-4  mx-auto">
                            <div
                                class=" border-b-2 flex items-center lg:overflow-x-visible md:overflow-x-visible sm:overflow-y-hidden sm:whitespace-nowrap sm:overflow-x-auto ">
                                <ul class="flex items-center flex-row font-medium mt-0 space-x-8  rtl:space-x-reverse text-sm">
                                    <li class="flex  border-b-2  border-selected-blue pb-4 ">
                                        <Link class="text-lg sm:text-md  text-selected-blue">Новая продажа</Link>

                                    </li>
                                    <li class="flex  border-b-2  border-selected-blue pb-4 ">
                                        <Link :href="route('sale.index')"
                                              class="text-lg sm:text-md  text-selected-blue">История
                                        </Link>

                                    </li>


                                </ul>

                            </div>
                        </div>

                    </nav>


                    <div class="lg:block md:hidden sm:hidden grid grid-cols-2 gap-6 flex mt-4">
                        <div class="flex whitespace-nowrap  justify-between items-center overflow-x-auto ">
                            <div class="flex items-center flex-2 mx-3">
                                <label class="items-center mr-2" for="rent_num">Номер продажи:</label>

                                &lt;!&ndash; SVG Icon &ndash;&gt;
                                <input v-model="form.sale_number" id="rent_num"
                                       class="w-full bg-input-gray rounded-lg border-none border-gray-200"
                                       placeholder="№" type="text">


                                <div class="flex ml-2 items-center">
                                    <label class="" for="rent_date">от</label>
                                    <div class="flex mx-3">
                                        &lt;!&ndash; SVG Icon &ndash;&gt;
                                    </div>
                                    <input v-model="form.sale_date" id="rent_date"
                                           class="w-full  bg-input-gray border-none rounded-lg"
                                           type="date" placeholder="Дата">
                                </div>
                            </div>


                            <div class="flex items-center flex-4">
                                <label class="" for="status">Статус</label>
                                <div class="flex mx-3">
                                    &lt;!&ndash; SVG Icon &ndash;&gt;
                                </div>
                                <select v-model="form.status" id="status"
                                        class="w-full bg-input-gray  rounded-lg border-none">
                                    <option value="">Выберите</option>
                                    <option v-for="(key, value) in saleStatuses" :value="value">{{ key }}</option>
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
                                    &lt;!&ndash; SVG Icon &ndash;&gt;
                                </div>
                                <select v-model="form.status" id="status"
                                        class="w-full bg-input-gray  rounded-lg border-none">
                                    <option value="">Выберите</option>
                                    <option value="credit">Кредит</option>
                                    <option value="full">Полная</option>
                                    <option value="pred">Предоплата</option>


                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="lg:hidden md:flex sm:flex flex flex-col mt-4">
                        <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
                            <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_num">Номер продажи</label>
                            <div class="flex mx-3">
                                &lt;!&ndash; SVG Icon &ndash;&gt;
                            </div>
                            <input v-model="form.sale_number" id="rent_num"
                                   class="w-full border-r-0 border-t-0 border-l-0 border-b-2 border-gray-200"
                                   placeholder="№" type="text">
                        </div>

                        <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
                            <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_date">От</label>
                            <div class="flex mx-3">
                                &lt;!&ndash; SVG Icon &ndash;&gt;
                            </div>
                            <input v-model="form.sale_date" id="rent_date"
                                   class="w-full border-r-0 border-t-0 border-l-0 border-b-2 border-gray-200"
                                   type="date"
                                   placeholder="Дата">
                        </div>

                        <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
                            <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_customer">Заказчик</label>
                            <div class="flex mx-3">
                                &lt;!&ndash; SVG Icon &ndash;&gt;
                            </div>
                            <select v-model="form.contragent_id" id="rent_customer" class="w-full">
                                <option value="">Выберите</option>
                                <option v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
                            </select>
                        </div>
                        <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
                            <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_status">Статус</label>
                            <div class="flex mx-3">
                                &lt;!&ndash; SVG Icon &ndash;&gt;
                            </div>
                            <select v-model="form.status" id="status"
                                    class="w-full bg-input-gray  rounded-lg border-none">
                                <option value="">Выберите</option>
                                <option value="credit">Кредит</option>
                                <option value="full">Полная</option>
                                <option value="pred">Предоплата</option>


                            </select>
                        </div>
                    </div>

                    <div class="p-4">
                        <ul class="flex gap-4">
                            <li @click="updateActiveTab('sale')">Продажа</li>
                            <li @click="updateActiveTab('extra')">Доп.услуги</li>
                        </ul>
                    </div>


                    <div v-if="activeTab == 'sale'" class="p-4 overflow-x-auto whitespace-nowrap">
                        <table class="table-auto bg-table-gray w-full ">
                            &lt;!&ndash; Table Head &ndash;&gt;
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
                                <th class="p-4">Комментарий</th>
                                <th class="p-4">Цена</th>
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
                                    &lt;!&ndash; Show a button or link to trigger the modal to select equipment &ndash;&gt;
                                    <button v-if="!selectedEquipmentService" @click="showModal(true)"
                                            class=" text-side-gray-text px-4 py-2 rounded">
                                        Нажмите чтобы выбрать оборудование
                                    </button>

                                    &lt;!&ndash; When equipment is selected, display the details and pass the id to v-model &ndash;&gt;
                                    <div v-else class="p-4 bg-my-gray border whitespace-nowrap flex">
                                        <p> {{ selectedEquipmentService.category.name }}
                                            {{ selectedEquipmentService.size.name }} {{
                                                selectedEquipmentService.series
                                            }}</p>
                                        &lt;!&ndash; Hidden input to hold the selected equipment id for the form &ndash;&gt;
                                        <input type="hidden" v-model="form.equipment_id">
                                    </div>
                                </td>

                                <td><input v-model="form.shipping_date" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>

                                <td><input type="text" v-model="form.commentary"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td>
                                    <input
                                        class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"
                                        type="text" v-model="form.price">
                                </td>
                                <td>
                                    <div class="flex items-center justify-around">
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
                                                            <Link method="get"
                                                                  :href="route('services.edit', service.id)"
                                                                  class="flex items-center justify-around"
                                                                  :class="['block px-4 py-2 text-sm']">
                                                                Редактировать
                                                                <svg width="12" height="12" viewBox="0 0 12 12"
                                                                     fill="none"
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
                                                            <Link method="delete"
                                                                  :href="route('services.destroy', service.id)"
                                                                  class="flex items-center justify-around"
                                                                  :class="['block px-4 py-2 text-sm']">Удалить
                                                                <svg width="12" height="14" viewBox="0 0 12 14"
                                                                     fill="none"
                                                                     xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                          d="M2.75 2.75V3.25H0.75C0.335786 3.25 0 3.58579 0 4C0 4.41421 0.335786 4.75 0.75 4.75H1.51389L1.89504 11.6109C1.95392 12.6708 2.8305 13.5 3.89196 13.5H8.10802C9.16948 13.5 10.0461 12.6708 10.1049 11.6109L10.4861 4.75H11.25C11.6642 4.75 12 4.41421 12 4C12 3.58579 11.6642 3.25 11.25 3.25H9.25V2.75C9.25 1.50736 8.24264 0.5 7 0.5H5C3.75736 0.5 2.75 1.50736 2.75 2.75ZM5 2C4.58579 2 4.25 2.33579 4.25 2.75V3.25H7.75V2.75C7.75 2.33579 7.41421 2 7 2H5ZM5.25 6.75C5.25 6.33579 4.91421 6 4.5 6C4.08579 6 3.75 6.33579 3.75 6.75V11.25C3.75 11.6642 4.08579 12 4.5 12C4.91421 12 5.25 11.6642 5.25 11.25V6.75ZM8.25 6.75C8.25 6.33579 7.91421 6 7.5 6C7.08579 6 6.75 6.33579 6.75 6.75V11.25C6.75 11.6642 7.08579 12 7.5 12C7.91421 12 8.25 11.6642 8.25 11.25V6.75Z"
                                                                          fill="#DC4067"/>
                                                                </svg>


                                                            </Link>
                                                        </MenuItem>
                                                        <MenuItem>

                                                        </MenuItem>

                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
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
                                <td><input @change="updateByKey(index, 'subequipment_id', item.id)"
                                           @input="updateByKey(index, 'shipping_date', $event.target.value)" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input @input="updateByKey(index, 'commentary', $event.target.value)"
                                           v-model="item.commentary"
                                           type="text"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input @input="updateByKey(index, 'price', $event.target.value)"
                                           v-model="item.price" type="text"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>

                                <td>
                                    <div class="flex items-center justify-around">

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

                        &lt;!&ndash; Add New Row Button &ndash;&gt;

                    </div>
                    <div v-if="activeTab == 'extra'" class="p-4 overflow-x-auto whitespace-nowrap">
                        <table class="table-auto bg-table-gray w-full ">
                            &lt;!&ndash; Table Head &ndash;&gt;
                            <thead>
                            <tr class="bg-table-gray">
                                <th class="bg-violet p-4">
                                    <a @click.prevent="incServiceRow" class="flex justify-between" href="">

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
                                <th class="p-4">Услуги</th>
                                <th class="p-4">Дата отгрузки</th>
                                <th class="p-4">Комментарий</th>
                                <th class="p-4">Цена</th>
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

                            <tr class="border-l-2 border-violet-full" v-for="(item, index) in getSelectedServices "
                                :key="index"
                                v-if="getSelectedServices.length > 0">

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
                                    <button v-if="!getSelectedServices" @click="showModalServices(true)"
                                            class=" text-side-gray-text px-4 py-2 rounded">
                                        Нажмите чтобы выбрать оборудование
                                    </button>

                                    <div class="p-4 bg-my-gray border whitespace-nowrap flex" v-else>
                                        <p> {{ item.item.value }}</p>
                                    </div>

                                    <input type="text" v-model="form.subEquipment" placeholder="Sub-equipment name"
                                           class="input hidden border-r-0 border-t-0 border-b-0 border-l-2 border-violet-full bg-input-gray"/>
                                </td>
                                <td><input @change="updateByKeyServices(index, 'shipping_date', $event.target.value)"
                                           v-model="item.shipping_date" type="date"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input @input="updateByKeyServices(index, 'commentary', $event.target.value)"
                                           v-model="item.commentary"
                                           type="text"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>
                                <td><input @input="updateByKeyServices(index, 'price', $event.target.value)"
                                           v-model="item.price" type="text"
                                           class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"/>
                                </td>

                                <td>
                                    <div class="flex items-center justify-around">
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
                                                            <button @click="removeExtraServiceAction(index)"
                                                                    class="flex items-center justify-around"
                                                                    :class="['block px-4 py-2 text-sm']">
                                                                Удалить
                                                                <svg width="12" height="12" viewBox="0 0 12 12"
                                                                     fill="none"
                                                                     xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M11.0981 3.10827C10.6795 3.52693 10.4702 3.73626 10.2506 3.77677C10.0309 3.81728 9.88682 3.67315 9.59856 3.3849L8.61511 2.40144C8.32686 2.11319 8.18273 1.96906 8.22324 1.74946C8.26375 1.52985 8.47308 1.32052 8.89174 0.901864L9.11838 0.675215C9.53704 0.25656 9.74637 0.0472318 9.96598 0.00672082C10.1856 -0.0337902 10.3297 0.110336 10.618 0.398589L11.6014 1.38204C11.8897 1.6703 12.0338 1.81442 11.9933 2.03403C11.9528 2.25364 11.7434 2.46297 11.3248 2.88162L11.0981 3.10827Z"
                                                                        fill="#464F60"/>
                                                                    <path
                                                                        d="M0.954058 11.9107C0.454198 12.0029 0.204269 12.049 0.077628 11.9224C-0.0490128 11.7957 -0.00290857 11.5458 0.0893002 11.0459L0.311753 9.84003C0.35173 9.62332 0.371719 9.51497 0.43005 9.41009C0.488381 9.30521 0.579134 9.21446 0.76064 9.03295L6.31438 3.47921C6.73303 3.06056 6.94236 2.85123 7.16197 2.81072C7.38158 2.77021 7.5257 2.91433 7.81396 3.20259L8.79741 4.18604C9.08566 4.4743 9.22979 4.61842 9.18928 4.83803C9.14877 5.05764 8.93944 5.26697 8.52078 5.68562L2.96705 11.2394C2.78554 11.4209 2.69479 11.5116 2.58991 11.5699C2.48503 11.6283 2.37668 11.6483 2.15997 11.6882L0.954058 11.9107Z"
                                                                        fill="#464F60"/>
                                                                </svg>


                                                            </button>
                                                        </MenuItem>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>

                                    </div>
                                </td>

                            </tr>
                            <tr v-for="item in getRowsCount" v-if="getRowsCount > 0">

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
                                    <button @click="showModalServices(true)"
                                            class=" text-side-gray-text px-4 py-2 rounded">
                                        Нажмите чтобы выбрать услугу
                                    </button>
                                </td>

                            </tr>
                            </tbody>
                        </table>

                        &lt;!&ndash; Add New Row Button &ndash;&gt;

                    </div>

                    <p>Итого </p>

                    <button @click="submit">Сохранить</button>
                </div>

            </div>
        </div>-->

    </AuthenticatedLayout>

</template>
