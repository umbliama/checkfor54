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
import ServicesServicesDialog from "@/Components/Services/ServicesServicesDialog.vue";
import UiNotification from '@/Components/Ui/UiNotification.vue';



const props = defineProps({
    service: Object,
    equipment: Object,
    subservices: Object,
    contragents: Array,
    extraServices: Array,
    serviceEquip: Object
})

const is_dialog_open = ref(false);

const subEquipment = computed(() => store.getters['services/getSubEquipment']);
const selectedEquipment = computed(() => store.getters['services/getSelectedEquipment']);
const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedSubEquipmentArray = computed(() => store.getters['services/getSubSelectedEquipmentObjects']);
const selectedEquipmentService = computed(() => store.getters['services/getSelectedEquipmentService']);
const subRowsCount = computed(() => store.getters['services/getsubRowsCount']);
const equipmentType = computed(() => store.getters['services/getEquipmentType']);
const chosenEquipment = computed(() => store.getters['services/getChosenEquipment']);
const getActiveEquipmentId = computed(() => store.getters['services/getActiveEquipmentId']);
const getActiveSubEquipmentId = computed(() => store.getters['services/getActiveSubEquipmentId']);
const modalShownServices = computed(() => store.getters['sale/getExtraServicesModal']);
const activeTab = ref('services')
const getSelectedServices = computed(() => store.getters['services/getSelectedServices']);
const additionalEquipment = ref([]);
const chosenEquipmentId = ref(null)
const selectedServices = ref([])
const rows = ref(props.serviceEquip.length)
const incSubRow = async (value) => {
    store.dispatch('services/updateIncSubRowsCount');
    store.dispatch('services/updateEquipmentType', 1)
    store.dispatch('services/updateChosenEquipment', value)
    chosenEquipmentId.value = value
    props.serviceEquip[value].subRows += 1
    if (toRaw(selectedEquipmentService.value.length === 0)) {
        const response = await fetch(`/api/equipment/${value}`);
        const data = await response.json();
        await fillRequestObject(data.id)


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

let requestObject = reactive([]);

const fillRequestObject = async (value) => {
    if (!Array.isArray(requestObject)) {
        requestObject = [];
    }

    requestObject.push({
        id: value,
        shipping_date: null,
        period_start_date: null,
        period_end_date: null,
        store: null,
        operating: null,
        return_date: null,
        subEquipment: []
    });

    await nextTick();
    console.log("Updated requestObject:", requestObject);
};

const chosenAgent = ref(null);
const chosenContracts = ref([]);
const setAgent = (id) => {
    chosenAgent.value = id
}

const setContracts = (id) => {
    const agent = props.contragents.find(eq => eq.id === id);
    agent.documents.forEach(doc => {

        const fileName = doc.file_path.split('/').pop().split('.')[0];
        chosenContracts.value.push({id:doc.id,name:toRaw(fileName)});
    });
}

watch(chosenAgent, async (newValue, oldValue) => {
    if (newValue) {
        try {
            setContracts(newValue)
        } catch (error) {
            console.error(error);
        }
    }
}, { deep: true });

const incSubRowMain = () => {
    store.dispatch('services/updateIncSubRowsCount');
    store.dispatch('services/updateEquipmentType', 0)
}

const addRows = () => {
    const equipment = selectedEquipment.value;
    store.dispatch('services/updateEquipmentType', 0);
    rows.value += 1
}


onMounted(() => {
    for (let i of props.serviceEquip) {
        toRaw(i.subRows = 0)
    }
    for (let subservice of props.subservices) {
        store.dispatch('services/updateSubSelectedEquipmentObjects', {
            id: subservice.id,
            subequipment_id: subservice.subequipment_id,
            shipping_date: '',
            period_start_date: '',
            return_date: '',
            period_end_date: '',
            store: 0,
            operating: 0,
            return_reason: '',
            commentary: '',

        });
    }
})

function openDialog() {
    is_dialog_open.value = true;
}
const addSubEquipment = (id, data) => {
    const equipment = props.serviceEquip[id];
    if (equipment) {
        equipment.service_subs.push(data)
    }
}
const updateActiveTab = (tab) => {
    activeTab.value = tab
}

watch(getSelectedServices, async (newValue, oldValue) => {
    if (newValue) {
        try {
            selectedServices.value.push({
                ...toRaw(newValue[0]),
                price: null,
                commentary: null,
                shipping_date: null
            })
        } catch (error) {
            console.error(error);
        }
    }
}, { deep: true });

watch(selectedEquipment, async (newValue, oldValue) => {
    if (newValue) {
        try {
            const response = await fetch(`/api/equipment/${toRaw(newValue[newValue.length - 1])}`);
            const data = await response.json();
            await fillRequestObject(data.id)
            store.dispatch('services/updateSelectedEquipmentService', data);
        } catch (error) {
            console.error(error);
        }
    }
}, { deep: true });

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
}, { deep: true });


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
                income: null,
                service_subs: [],
                subRows: 0
            }
            props.serviceEquip.push(extendedEquipment)

            console.log(newValue, props.serviceEquip)
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
                period_end_date: null,
                return_date: null,
                operating: null,
                income: null,
                store: null,
                return_reason:null,
                subequipment: {}
            }


            addSubEquipment(chosenEquipmentId.value, subequipment)

        } catch (error) {
            console.error(error);
        }
    }
}, { deep: true });



const calculateResultForEquip = (equip) => {
    const { period_start_date, period_end_date, operating } = equip;
    if (!period_start_date || !period_end_date) {
        equip.store = 0;
        return;
    }

    const startDate = new Date(period_start_date);
    const endDate = new Date(period_end_date);

    if (isNaN(startDate) || isNaN(endDate)) {
        equip.store = 0;
        return;
    }

    const diffInDays = (endDate - startDate) / (1000 * 60 * 60 * 24);

    if (operating === 0) {
        equip.store = parseFloat((diffInDays + 1)).toFixed(2);
        console.log(diffInDays + 1)
    } else {
        equip.store = parseFloat(((diffInDays + 1) - (operating / 24))).toFixed(2);
    }
};
const calculateResultForSubEquip = (subEquip) => {
    const { period_start_date, period_end_date, operating } = subEquip;
    if (!period_start_date || !period_end_date) {
        subEquip.store = 0;
        return;
    }
    const startDate = new Date(period_start_date);
    const endDate = new Date(period_end_date);
    if (isNaN(startDate) || isNaN(endDate)) {
        subEquip.store = 0;
        return;
    }
    const diffInDays = (endDate - startDate) / (1000 * 60 * 60 * 24);

    if (operating === 0) {
        subEquip.store = parseFloat(diffInDays + 1).toFixed(2);
    } else {
        subEquip.store = parseFloat(((diffInDays + 1) - (operating / 24))).toFixed(2); 
    }
};

const calculateAllSubEquips = (equip) => {
    if (equip.service_subs && Array.isArray(equip.service_subs)) {
        equip.service_subs.forEach((subEquip) => {
            calculateResultForSubEquip(subEquip); 
        });
    }
};
const checkAndCalculate = (equip) => {
    if (equip.period_start_date && equip.period_end_date) {
        calculateResultForEquip(equip);
    }

    calculateAllSubEquips(equip);
};
const showModalServices = (value) => {
    store.dispatch('sale/showModal', value)
}
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
const isNewEquipmentAdded = computed(() => {
    return props.serviceEquip.some(equipment => !equipment.id);
});

const isNewServiceSubAdded = computed(() => {
    return props.serviceEquip.some(equipment => 
        equipment.service_subs && equipment.service_subs.some(sub => !sub.id)
    );
});
const method = computed(() => {
    return (isNewEquipmentAdded.value || isNewServiceSubAdded.value) ? 'post' : (props.service.id ? 'put' : 'post');
});
function submit() {
    const cleanedServices = selectedServices.value ? selectedServices.value.filter(sub => sub) : [];
    const allServices = props.extraServices ? cleanedServices.concat(props.extraServices) : cleanedServices;

    const formattedRequestObject = props.serviceEquip.map(equipment => ({
        equipment_id: equipment.equipment.id,
        shipping_date: equipment.shipping_date || null,
        period_start_date: equipment.period_start_date || null,
        store: equipment.store || null,
        operating: equipment.operating || null,
        period_end_date: equipment.period_end_date || null,
        return_date: equipment.return_date || null,
        return_reason: equipment.return_reason || null,
        subEquipment: (equipment.service_subs || []).filter(sub => sub
        ).map(sub => ({
            subequipment_id: sub.equipment.id,
            shipping_date: sub.shipping_date || null,
            period_start_date: sub.period_start_date || null,
            store: sub.store || null,
            operating: sub.operating || null,
            period_end_date: sub.period_end_date || null,
            return_date: sub.return_date || null,
            return_reason: sub.return_reason || null,

        }))
    })).filter(equipment => equipment.equipment_id);

    const requestData = {
        contragent_id: props.service.contragent_id,
        service_number: props.service.service_number,
        service_date: props.service.service_date,
        active: props.service.active,
        contract: props.service.contract,
        equipment: JSON.parse(JSON.stringify(formattedRequestObject))
    };

    if (cleanedServices.length > 0) {
        requestData.extraServices = cleanedServices;
    }

    if (props.extraServices) {
        requestData.extraServices = requestData.extraServices ? requestData.extraServices.concat(props.extraServices) : props.extraServices;
    }

    const method = props.service.id ? 'put' : 'post';
    const url = props.service.id ? `/services/${props.service.id}` : '/services';

    router[method](url, requestData);
}
</script>

<template>
    <AuthenticatedLayout>
        <ServicesDialog v-model="is_dialog_open" />
        <ServicesServicesDialog v-if="modalShownServices" />
        <UiNotification v-model="$page.props.flash.message" :description="$page.props.flash.message" type="success" />
        <UiNotification v-model="$page.props.flash.error" :description="$page.props.flash.error" type="error" />
        <div class="p-5">
            <ServicesNav2 title="Редактор аренды" />

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
                                    <input v-model="service.service_number" type="text"
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
                                    <input v-model="service.service_date" type="date"
                                        class="block grow p-2 w-[177px] h-9 rounded-lg bg-inherit"
                                        onclick="this.showPicker()" />
                                </div>
                            </label>
                        </div>
                        <label class="flex items-center p-1 bg-bg1 lg:bg-transparent">
                            <span
                                class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-[52px] lg:p-0 lg:text-base lg:border-0">Заказчик:</span>
                            <span
                                class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"></span>
                            <div
                                class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:grow lg:text-base lg:w-auto lg:bg-[#F3F3F8]">
                                <select v-model="service.contragent_id"
                                    class="block grow p-2 w-full h-9 rounded-lg bg-inherit font-medium lg:w-[186px]">
                                    <option value="">Выберите</option>
                                    <option  @click="setAgent(agent.id)" v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
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
                                <select v-model="service.active"
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
                                <select v-model="service.contract"
                                    class="block grow p-2 w-[186px] h-9 rounded-lg bg-inherit font-medium">
                                    <option v-for="item in chosenContracts" :value="item.id">{{ item.name }}</option>
                                </select>
                            </div>
                        </label>
                    </div>
                </div>

                <label class="block items-center p-1 mt-4 bg-bg1 lg:flex lg:bg-transparent">
                    <span class="
                            block py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD]
                            lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0
                        ">Комментарий:</span>
                    <div class="
                            flex items-center mt-2 text-sm rounded-lg overflow-hidden bg-white
                            lg:w-[calc(50%-9px)] lg:mt-0 lg:text-basew-auto lg:bg-[#F3F3F8]
                        ">
                        <input v-model="form.commentary" type="text"
                            class="block grow p-2 w-[177px] h-9 rounded-lg bg-inherit" />
                    </div>
                </label>
            </div>

            <div class="relative text-sm">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul
                    class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto lg:overflow-x-visible">
                    <li :class="{ '!border-[#001D6C] text-[#001D6C]': activeTab === 'services' }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        @click="updateActiveTab('services')">
                        Аренда
                    </li>
                    <li :class="{ '!border-[#001D6C] text-[#001D6C]': activeTab === 'extra' }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        @click="updateActiveTab('extra')">
                        Доп. услуги
                    </li>
                </ul>
            </div>

            <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[930px] text-xs">
                    <template v-if="activeTab == 'services'">
                        <div
                            class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <button type="button"
                                class="shrink-0 flex items-center w-[44px] py-2.5 px-2 bg-violet-full/5"
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
                            <div class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2 cursor-pointer">Дата отгрузки
                            </div>
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


                        <div v-if="serviceEquip.length > 0" v-for="(equip, index) in serviceEquip">
                            <div
                                class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                    <UiHyperlink :hyperlink="'some.ru'" :item-id="12" endpoint="/services" />
                                </div>
                                <div class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2">
                                    {{ equip.equipment.category.name }} {{ equip.equipment.size.name }} {{
                                        equip.equipment.series }}
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input v-model="equip.shipping_date" type="date"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input @input="checkAndCalculate(equip)" v-model="equip.period_start_date"
                                        type="date" class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input v-model="equip.store" type="text"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input @input="checkAndCalculate(equip)" v-model="equip.operating" type="text"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input @input="checkAndCalculate(equip)" v-model="equip.period_end_date" type="date"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input v-model="equip.return_date" type="date"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()"  />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%] ">
                                    <select v-model="equip.return_reason" class="block w-full h-full py-2.5 px-2 bg-transparent">
                                        <option value="project">Проект</option>
                                        <option value="rejected">Отказ</option>
                                    </select>
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input v-model="equip.income" type="text"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent" />
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
                                                        <button type="button"
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
                                                        </button>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <button type="button" @click="incSubRow(index)"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                            Укомплектовать

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
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <Link method="POST"
                                                            :href="route('services.createIncident', service.id)">
                                                        <button type="button"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                            Инцидент
                                                            <svg class="block ml-2" width="16" height="16"
                                                                viewBox="0 0 16 16" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M9.82776 0.722047C9.89347 0.721929 9.95855 0.734763 10.0193 0.759815C10.08 0.784867 10.1352 0.821645 10.1818 0.868047L15.1318 5.81805C15.2255 5.91181 15.2782 6.03896 15.2782 6.17155C15.2782 6.30413 15.2255 6.43128 15.1318 6.52505C14.6518 7.00505 14.0598 7.11305 13.6288 7.11305C13.4518 7.11305 13.2938 7.09505 13.1688 7.07405L10.0348 10.208C10.1173 10.5405 10.1708 10.8794 10.1948 11.221C10.2408 11.923 10.1628 12.908 9.47476 13.596C9.381 13.6898 9.25384 13.7424 9.12126 13.7424C8.98868 13.7424 8.86152 13.6898 8.76776 13.596L5.93876 10.768L2.75676 13.95C2.56176 14.145 1.53776 14.852 1.34276 14.657C1.14776 14.462 1.85476 13.437 2.04976 13.243L5.23176 10.061L2.40376 7.23205C2.31002 7.13828 2.25737 7.01113 2.25737 6.87855C2.25737 6.74596 2.31002 6.61881 2.40376 6.52505C3.09176 5.83705 4.07676 5.75805 4.77876 5.80505C5.12042 5.82899 5.45936 5.88252 5.79176 5.96505L8.92576 2.83205C8.8996 2.67976 8.88622 2.52556 8.88576 2.37105C8.88576 1.94105 8.99376 1.34905 9.47476 0.868047C9.56847 0.774578 9.69541 0.722075 9.82776 0.722047Z"
                                                                    fill="#464F60" />
                                                            </svg>
                                                        </button>
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <Link method="DELETE"
                                                            :href="route('services.destroyServiceEquip', equip.id)">
                                                        <button type="button"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                            Удалить
                                                            <svg class="block ml-2" width="16" height="16"
                                                                viewBox="0 0 16 16" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M9.82776 0.722047C9.89347 0.721929 9.95855 0.734763 10.0193 0.759815C10.08 0.784867 10.1352 0.821645 10.1818 0.868047L15.1318 5.81805C15.2255 5.91181 15.2782 6.03896 15.2782 6.17155C15.2782 6.30413 15.2255 6.43128 15.1318 6.52505C14.6518 7.00505 14.0598 7.11305 13.6288 7.11305C13.4518 7.11305 13.2938 7.09505 13.1688 7.07405L10.0348 10.208C10.1173 10.5405 10.1708 10.8794 10.1948 11.221C10.2408 11.923 10.1628 12.908 9.47476 13.596C9.381 13.6898 9.25384 13.7424 9.12126 13.7424C8.98868 13.7424 8.86152 13.6898 8.76776 13.596L5.93876 10.768L2.75676 13.95C2.56176 14.145 1.53776 14.852 1.34276 14.657C1.14776 14.462 1.85476 13.437 2.04976 13.243L5.23176 10.061L2.40376 7.23205C2.31002 7.13828 2.25737 7.01113 2.25737 6.87855C2.25737 6.74596 2.31002 6.61881 2.40376 6.52505C3.09176 5.83705 4.07676 5.75805 4.77876 5.80505C5.12042 5.82899 5.45936 5.88252 5.79176 5.96505L8.92576 2.83205C8.8996 2.67976 8.88622 2.52556 8.88576 2.37105C8.88576 1.94105 8.99376 1.34905 9.47476 0.868047C9.56847 0.774578 9.69541 0.722075 9.82776 0.722047Z"
                                                                    fill="#464F60" />
                                                            </svg>
                                                        </button>
                                                        </Link>
                                                    </DropdownMenuItem>

                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
                                </div>
                            </div>
                            <div v-for="(equipment, index) in equip.service_subs">
                                <div
                                    class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                        <UiHyperlink :hyperlink="'some.ru'" :item-id="12" endpoint="/services" />
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                        {{ equipment.equipment.category.name }}
                                        {{ equipment.equipment.size.name }} {{ equipment.equipment.series }}
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input v-model="equipment.shipping_date" type="date"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                            onclick="this.showPicker()"  />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input v-model="equipment.period_start_date" type="date"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                            onclick="this.showPicker()"
                                            @change="checkAndCalculate(equipment)"
                                            />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input v-model="equipment.store" type="text"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input  @input="checkAndCalculate(equip)" v-model="equipment.operating" type="text"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input v-model="equipment.period_end_date" type="date"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                            @change="checkAndCalculate(equipment)"
                                            onclick="this.showPicker()" />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input v-model="equipment.return_date" type="date"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                            onclick="this.showPicker()"  />
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%] ">
                                        <select v-model="equipment.return_reason" class="block w-full h-full py-2.5 px-2 bg-transparent">
                                            <option value="project">Проект</option>
                                            <option value="rejected">Отказ</option>
                                        </select>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[8.97%]">
                                        <input v-model="equipment.income" type="text"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent" />
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
                                                            <button type="button"
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
                                                            </button>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <button @click="incSubRow(equipment.id)" type="button"
                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                Укомплектовать
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
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <button type="button"
                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                Инцидент
                                                                <svg class="block ml-2" width="16" height="16"
                                                                    viewBox="0 0 16 16" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M9.82776 0.722047C9.89347 0.721929 9.95855 0.734763 10.0193 0.759815C10.08 0.784867 10.1352 0.821645 10.1818 0.868047L15.1318 5.81805C15.2255 5.91181 15.2782 6.03896 15.2782 6.17155C15.2782 6.30413 15.2255 6.43128 15.1318 6.52505C14.6518 7.00505 14.0598 7.11305 13.6288 7.11305C13.4518 7.11305 13.2938 7.09505 13.1688 7.07405L10.0348 10.208C10.1173 10.5405 10.1708 10.8794 10.1948 11.221C10.2408 11.923 10.1628 12.908 9.47476 13.596C9.381 13.6898 9.25384 13.7424 9.12126 13.7424C8.98868 13.7424 8.86152 13.6898 8.76776 13.596L5.93876 10.768L2.75676 13.95C2.56176 14.145 1.53776 14.852 1.34276 14.657C1.14776 14.462 1.85476 13.437 2.04976 13.243L5.23176 10.061L2.40376 7.23205C2.31002 7.13828 2.25737 7.01113 2.25737 6.87855C2.25737 6.74596 2.31002 6.61881 2.40376 6.52505C3.09176 5.83705 4.07676 5.75805 4.77876 5.80505C5.12042 5.82899 5.45936 5.88252 5.79176 5.96505L8.92576 2.83205C8.8996 2.67976 8.88622 2.52556 8.88576 2.37105C8.88576 1.94105 8.99376 1.34905 9.47476 0.868047C9.56847 0.774578 9.69541 0.722075 9.82776 0.722047Z"
                                                                        fill="#464F60" />
                                                                </svg>
                                                            </button>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <Link
                                                                :href="route('services.destroyServiceEquip', equipment.id)"
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

                            </div>

                            <div v-if="equip.subRows > 0" v-for="item in equip.subRows - equip.service_subs.length"
                                class="flex border-b border-b-gray3 overflow-hidden [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                    <UiHyperlink :hyperlink="'some.ru'" :item-id="12" endpoint="/services" />
                                </div>
                                <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                    <button @click="openDialog" type="button" class="text-left">Нажмите чтобы выбрать
                                        оборудование</button>
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()" />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%] ">
                                    <select class="block w-full h-full py-2.5 px-2 bg-transparent">
                                        <option value="">Проект</option>
                                        <option value="">Отказ</option>
                                    </select>
                                </div>
                                <div class="shrink-0 flex items-center w-[8.97%]">
                                    <input readonly="readonly" type="text"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent" />
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
                                                        <button type="button"
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
                                                        </button>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <Link :href="'/'" method="DELETE"
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

                        <div v-for="item in rows - serviceEquip.length"
                            class="flex border-b border-b-gray3 overflow-hidden [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                <UiHyperlink :hyperlink="'some.ru'" :item-id="12" endpoint="/services" />
                            </div>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full">
                                <button @click="openDialog" type="button" class="text-left">Нажмите чтобы выбрать
                                    оборудование</button>
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input type="text" class="block w-full h-full py-2.5 px-2 bg-transparent" />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input type="date" class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%] ">
                                <select class="block w-full h-full py-2.5 px-2 bg-transparent">
                                    <option value="">Проект</option>
                                    <option value="">Отказ</option>
                                </select>
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input readonly="readonly" type="text"
                                    class="block w-full h-full py-2.5 px-2 bg-transparent" />
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
                                                    <button type="button"
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
                                                    </button>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <Link :href="'/'" method="DELETE"
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                    Удалить
                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <template v-if="activeTab == 'extra'">
                        <div
                            class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <button type="button"
                                class="shrink-0 flex items-center w-[44px] py-2.5 px-2 bg-violet-full/5"
                                @click.prevent="incServiceRow">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M14.9444 8.69444C14.9444 8.31091 14.6335 8 14.25 8C13.8665 8 13.5556 8.31091 13.5556 8.69444V13.5556H8.69444C8.31091 13.5556 8 13.8665 8 14.25C8 14.6335 8.31091 14.9444 8.69444 14.9444H13.5556V19.8056C13.5556 20.1891 13.8665 20.5 14.25 20.5C14.6335 20.5 14.9444 20.1891 14.9444 19.8056V14.9444H19.8056C20.1891 14.9444 20.5 14.6335 20.5 14.25C20.5 13.8665 20.1891 13.5556 19.8056 13.5556H14.9444V8.69444Z"
                                        fill="#484964" />
                                    <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08" />
                                </svg>
                            </button>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">Услуга</div>
                            <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Дата перевозки
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2">
                                Комментарий</div>
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
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                <UiHyperlink v-if="selectedEquipmentService"
                                    :hyperlink="selectedEquipmentService?.hyperlink"
                                    :item-id="selectedEquipmentService?.id" endpoint="/services" />
                            </div>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">
                                <button @click="showModalServices(true)" type="button">
                                    Нажмите чтобы выбрать услугу
                                </button>
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                                <input type="date" class="block w-full h-full px-2 bg-transparent"
                                    onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                <input type="text" class="block w-full h-full px-2 bg-transparent" />
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
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
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
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
                        <div v-for="(item, index) in selectedServices" :key="index"
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                <UiHyperlink v-if="selectedEquipmentService"
                                    :hyperlink="selectedEquipmentService?.hyperlink"
                                    :item-id="selectedEquipmentService?.id" endpoint="/services" />
                            </div>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">
                                {{ item.value }}
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                                <input v-model="item.shipping_date" type="date"
                                    class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                <input type="text" class="block w-full h-full px-2 bg-transparent"
                                    v-model="item.commentary" />
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
                                <input v-model="item.price" type="text"
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
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
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
                        <div v-if="extraServices.length > 0" v-for="(item, index) in extraServices" :key="index"
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                            <div class="shrink-0 flex items-center w-[44px] py-2.5 px-2">
                                <UiHyperlink v-if="selectedEquipmentService"
                                    :hyperlink="selectedEquipmentService?.hyperlink"
                                    :item-id="selectedEquipmentService?.id" endpoint="/services" />
                            </div>
                            <div class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2">
                                {{ item.value }}
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%] cursor-pointer">
                                <input v-model="item.shipping_date" type="date"
                                    class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()" />
                            </div>
                            <div class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]">
                                <input type="text" class="block w-full h-full px-2 bg-transparent"
                                    v-model="item.commentary" />
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
                                <input v-model="item.price" type="text"
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
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
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

                    </template>


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
