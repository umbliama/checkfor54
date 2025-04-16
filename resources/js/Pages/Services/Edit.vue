<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SideMenu from "@/Layouts/SideMenu.vue";
import { MenuItem, MenuItems, Menu, MenuButton } from "@headlessui/vue";
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import { reactive, toRaw, watch, nextTick } from "vue";
import store from "../../../store/index";
import { computed, onMounted, ref } from "vue";
import ServiceModal from "@/Components/ServiceModal.vue";
import ServicesDialog from "@/Components/Services/ServicesDialog.vue";
import ServicesSubDialog from "@/Components/Services/ServicesSubDialog.vue";
import ServicesNav2 from "@/Components/Services/ServicesNav2.vue";
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
    ComboboxAnchor,
    ComboboxContent,
    ComboboxEmpty,
    ComboboxInput,
    ComboboxItem,
    ComboboxRoot,
    ComboboxPortal,
    PopoverContent,
    PopoverTrigger,
    PopoverRoot,
    PopoverAnchor,
    PopoverArrow,
    PopoverClose,
    PopoverPortal,
} from "radix-vue";
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";
import ServicesServicesDialog from "@/Components/Services/ServicesServicesDialog.vue";
import UiNotification from "@/Components/Ui/UiNotification.vue";

const props = defineProps({
    service: Object,
    equipment: Object,
    subservices: Object,
    contragents: Array,
    extraServices: Array,
    serviceEquip: Object,
});

const is_dialog_open = ref(false);
const is_dialog_open_sub = ref(false);
const is_agent_select_open = ref(false);

const subEquipment = computed(() => store.getters["services/getSubEquipment"]);
const selectedEquipment = computed(
    () => store.getters["services/getSelectedEquipment"]
);
const subEquipmentArray = computed(
    () => store.getters["services/getSubEquipmentArray"]
);
const selectedSubEquipmentArray = computed(
    () => store.getters["services/getSubSelectedEquipmentObjects"]
);
const selectedEquipmentService = computed(
    () => store.getters["services/getSelectedEquipmentService"]
);
const subRowsCount = computed(() => store.getters["services/getsubRowsCount"]);
const equipmentType = computed(
    () => store.getters["services/getEquipmentType"]
);
const chosenEquipment = computed(
    () => store.getters["services/getChosenEquipment"]
);
const getActiveEquipmentId = computed(
    () => store.getters["services/getActiveEquipmentId"]
);
const getActiveSubEquipmentId = computed(
    () => store.getters["services/getActiveSubEquipmentId"]
);
const modalShownServices = computed(
    () => store.getters["sale/getExtraServicesModal"]
);
const activeTab = ref("services");
const getSelectedServices = computed(
    () => store.getters["services/getSelectedServices"]
);
const additionalEquipment = ref([]);
const chosenEquipmentId = ref(null);
const selectedServices = ref([]);
const rows = ref(props.serviceEquip.length);
const incSubRow = async (value) => {
    store.dispatch("services/updateIncSubRowsCount");
    store.dispatch("services/updateEquipmentType", 1);
    store.dispatch("services/updateChosenEquipment", value);
    chosenEquipmentId.value = value;
    props.serviceEquip[value].subRows += 1;
    if (toRaw(selectedEquipmentService.value.length === 0)) {
        const response = await fetch(`/api/equipment/${value}`);
        const data = await response.json();
        await fillRequestObject(data.id);
    }
    store.dispatch("services/updateSubSelectedEquipmentObjects", {
        subequipment_id: "",
        id: "",
        shipping_date: "",
        period_start_date: "",
        return_date: "",
        period_end_date: "",
        store: "",
        operating: "",
        return_reason: "",
        commentary: "",
    });
};

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
        subEquipment: [],
    });

    await nextTick();
    console.log("Updated requestObject:", requestObject);
};

const chosenAgent = ref(null);
const chosenContracts = ref([]);
const setAgent = (id) => {
    chosenAgent.value = id;
};

const setContracts = (id) => {
    const agent = props.contragents.find((eq) => eq.id === id);
    agent.documents.forEach((doc) => {
        const fileName = doc.file_path.split("/").pop().split(".")[0];
        chosenContracts.value.push({ id: doc.id, name: toRaw(fileName) });
    });
};

watch(
    chosenAgent,
    async (newValue, oldValue) => {
        if (newValue) {
            try {
                setContracts(newValue);
            } catch (error) {
                console.error(error);
            }
        }
    },
    { deep: true }
);

const incSubRowMain = () => {
    store.dispatch("services/updateIncSubRowsCount");
    store.dispatch("services/updateEquipmentType", 0);
};

const addRows = () => {
    const equipment = selectedEquipment.value;
    store.dispatch("services/updateEquipmentType", 0);
    rows.value += 1;
};

onMounted(() => {
    for (let i of props.serviceEquip) {
        toRaw((i.subRows = 0));
    }
    for (let subservice of props.subservices) {
        store.dispatch("services/updateSubSelectedEquipmentObjects", {
            id: subservice.id,
            subequipment_id: subservice.subequipment_id,
            shipping_date: "",
            period_start_date: "",
            return_date: "",
            period_end_date: "",
            store: 0,
            operating: 0,
            return_reason: "",
            commentary: "",
        });
    }
});

function openDialog() {
    is_dialog_open.value = true;
}

function openDialogSub() {
    is_dialog_open_sub.value = true;
}

const addSubEquipment = (id, data) => {
    const equipment = props.serviceEquip[id];
    if (equipment) {
        equipment.service_subs.push(data);
    }
};
const updateActiveTab = (tab) => {
    activeTab.value = tab;
};

watch(
    getSelectedServices,
    async (newValue, oldValue) => {
        if (newValue) {
            try {
                selectedServices.value.push({
                    ...toRaw(newValue[0]),
                    price: null,
                    commentary: null,
                    shipping_date: null,
                });
            } catch (error) {
                console.error(error);
            }
        }
    },
    { deep: true }
);

watch(
    selectedEquipment,
    async (newValue, oldValue) => {
        if (newValue) {
            try {
                const response = await fetch(
                    `/api/equipment/${toRaw(newValue[newValue.length - 1])}`
                );
                const data = await response.json();
                await fillRequestObject(data.id);
                store.dispatch("services/updateSelectedEquipmentService", data);
            } catch (error) {
                console.error(error);
            }
        }
    },
    { deep: true }
);

watch(
    subEquipmentArray,
    async (newValue, oldValue) => {
        if (newValue.length) {
            const lastValue = newValue[newValue.length - 1];
            try {
                const response = await fetch(`/api/equipment/${lastValue}`);
                const data = await response.json();
                store.dispatch("services/updateSubEquipment", data);
            } catch (error) {
                console.error(error);
            }
        }
    },
    { deep: true }
);

watch(
    getActiveEquipmentId,
    async (newValue, oldValue) => {
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
                    subRows: 0,
                };
                props.serviceEquip.push(extendedEquipment);

                console.log(newValue, props.serviceEquip);
            } catch (error) {
                console.error(error);
            }
        }
    },
    { deep: true }
);

watch(
    getActiveSubEquipmentId,
    async (newValue, oldValue) => {
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
                    return_reason: null,
                    subequipment: {},
                };

                addSubEquipment(chosenEquipmentId.value, subequipment);
            } catch (error) {
                console.error(error);
            }
        }
    },
    { deep: true }
);

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
        equip.store = parseFloat(diffInDays + 1).toFixed(2);
        console.log(diffInDays + 1);
    } else {
        equip.store = parseFloat(diffInDays + 1 - operating / 24).toFixed(2);
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
        subEquip.store = parseFloat(diffInDays + 1 - operating / 24).toFixed(2);
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
    store.dispatch("sale/showModal", value);
};
const modalShown = computed(() => store.getters["services/getModalShown"]);
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
});
const isNewEquipmentAdded = computed(() => {
    return props.serviceEquip.some((equipment) => !equipment.id);
});

const isNewServiceSubAdded = computed(() => {
    return props.serviceEquip.some(
        (equipment) =>
            equipment.service_subs &&
            equipment.service_subs.some((sub) => !sub.id)
    );
});
const method = computed(() => {
    return isNewEquipmentAdded.value || isNewServiceSubAdded.value
        ? "post"
        : props.service.id
        ? "put"
        : "post";
});
function submit() {
    const cleanedServices = selectedServices.value
        ? selectedServices.value.filter((sub) => sub)
        : [];
    const allServices = props.extraServices
        ? cleanedServices.concat(props.extraServices)
        : cleanedServices;

    const formattedRequestObject = props.serviceEquip
        .map((equipment) => ({
            equipment_id: equipment.equipment.id,
            shipping_date: equipment.shipping_date || null,
            period_start_date: equipment.period_start_date || null,
            store: equipment.store || null,
            operating: equipment.operating || null,
            period_end_date: equipment.period_end_date || null,
            return_date: equipment.return_date || null,
            return_reason: equipment.return_reason || null,
            subEquipment: (equipment.service_subs || [])
                .filter((sub) => sub)
                .map((sub) => ({
                    subequipment_id: sub.equipment.id,
                    shipping_date: sub.shipping_date || null,
                    period_start_date: sub.period_start_date || null,
                    store: sub.store || null,
                    operating: sub.operating || null,
                    period_end_date: sub.period_end_date || null,
                    return_date: sub.return_date || null,
                    return_reason: sub.return_reason || null,
                })),
        }))
        .filter((equipment) => equipment.equipment_id);

    const requestData = {
        contragent_id: props.service.contragent_id,
        service_number: props.service.service_number,
        service_date: props.service.service_date,
        active: props.service.active,
        contract: props.service.contract,
        equipment: JSON.parse(JSON.stringify(formattedRequestObject)),
    };

    if (cleanedServices.length > 0) {
        requestData.extraServices = cleanedServices;
    }

    if (props.extraServices) {
        requestData.extraServices = requestData.extraServices
            ? requestData.extraServices.concat(props.extraServices)
            : props.extraServices;
    }

    const method = props.service.id ? "put" : "post";
    const url = props.service.id
        ? `/services/${props.service.id}`
        : "/services";

    router[method](url, requestData);
}
</script>

<template>
    <AuthenticatedLayout>
        <ServicesDialog v-model="is_dialog_open" />
        <ServicesSubDialog v-model="is_dialog_open_sub" />
        <ServicesServicesDialog v-if="modalShownServices" />
        <UiNotification
            v-model="$page.props.flash.message"
            :description="$page.props.flash.message"
            type="success"
        />
        <UiNotification
            v-model="$page.props.flash.error"
            :description="$page.props.flash.error"
            type="error"
        />
        <div class="p-5">
            <ServicesNav2 title="Редактор аренды" />

            <div class="mt-9 text-nowrap">
                <div class="justify-between lg:flex">
                    <div class="space-y-2 lg:space-y-4">
                        <div
                            class="items-center space-y-2 lg:flex lg:space-y-0"
                        >
                            <label
                                class="flex items-center p-1 bg-bg1 lg:bg-transparent"
                            >
                                <span
                                    class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0"
                                    >Номер аренды:</span
                                >
                                <span
                                    class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"
                                ></span>
                                <div
                                    class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg overflow-hidden bg-white lg: lg:text-basew-auto lg:bg-[#F3F3F8]"
                                >
                                    <span class="inline-block pl-2 font-medium"
                                        >№</span
                                    >
                                    <input
                                        v-model="service.service_number"
                                        type="text"
                                        class="block grow p-2 w-[177px] h-9 rounded-lg bg-inherit"
                                    />
                                </div>
                            </label>
                            <label
                                class="flex items-center p-1 bg-bg1 lg:ml-2 lg:bg-transparent"
                            >
                                <span
                                    class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0"
                                    >от</span
                                >
                                <span
                                    class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"
                                ></span>
                                <div
                                    class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:w-auto lg:text-base lg:bg-[#F3F3F8]"
                                >
                                    <span
                                        class="hidden pl-2 font-medium lg:inline-block"
                                        >Дата</span
                                    >
                                    <input
                                        v-model="service.service_date"
                                        type="date"
                                        class="block grow p-2 w-[177px] h-9 rounded-lg bg-inherit"
                                        onclick="this.showPicker()"
                                    />
                                </div>
                            </label>
                        </div>
                        <label
                            class="flex items-center p-1 bg-bg1 lg:bg-transparent"
                        >
                            <span
                                class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-[52px] lg:p-0 lg:text-base lg:border-0"
                                >Заказчик:</span
                            >
                            <span
                                class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"
                            ></span>
                            <div
                                class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:grow lg:text-base lg:w-auto lg:bg-[#F3F3F8]"
                            >
                                <ComboboxRoot
                                    :model-value="service.contragent_id"
                                    v-model:open="is_agent_select_open"
                                    :filter-function="
                                        (list, v) =>
                                            list.filter((item) =>
                                                item.name
                                                    .toLowerCase()
                                                    .includes(v.toLowerCase())
                                            )
                                    "
                                    :display-value="
                                        (v) =>
                                            contragents.find((c) => c.id === v)
                                                ?.name
                                    "
                                    class="w-full"
                                    @update:model-value="
                                        (v) => (service.contragent_id = v.id)
                                    "
                                >
                                    <ComboboxAnchor class="relative w-full">
                                        <ComboboxInput
                                            placeholder="Выберите"
                                            @focus="is_agent_select_open = true"
                                            class="block grow p-2 w-full h-9 rounded-lg bg-inherit font-medium lg:w-[186px]"
                                        />
                                        <svg
                                            class="absolute right-2 top-1/2 -translate-y-1/2"
                                            width="18"
                                            height="18"
                                            viewBox="0 0 18 18"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M12 7.5L8.81802 10.682L5.63604 7.5"
                                                stroke="#242533"
                                                stroke-width="1.2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </ComboboxAnchor>
                                    <ComboboxPortal>
                                        <ComboboxContent
                                            align="start"
                                            position="popper"
                                            class="max-h-[300px] py-2 px-1.5 rounded-md font-medium text-sm overflow-y-auto bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                        >
                                            <ComboboxEmpty>
                                                <div
                                                    class="p-3 text-sm text-gray-500"
                                                >
                                                    Ничего не найдено
                                                </div>
                                            </ComboboxEmpty>
                                            <ComboboxItem
                                                v-for="agent in contragents"
                                                :key="agent.id"
                                                :value="agent"
                                                class="flex items-center py-1 px-2 rounded cursor-pointer hover:bg-my-gray data-[state=checked]:bg-my-gray data-[highlighted]:bg-my-gray"
                                            >
                                                {{ agent.name }}
                                            </ComboboxItem>
                                        </ComboboxContent>
                                    </ComboboxPortal>
                                </ComboboxRoot>
                                <!-- <select v-model="service.contragent_id"
                                    class="block grow p-2 w-full h-9 rounded-lg bg-inherit font-medium lg:w-[186px]">
                                    <option value="">Выберите</option>
                                    <option  @click="setAgent(agent.id)" v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
                                </select> -->
                            </div>
                        </label>
                    </div>

                    <div class="mt-2 space-y-2 lg:space-y-4 lg:mt-0">
                        <label
                            class="flex items-center p-1 bg-bg1 lg:justify-between lg:bg-transparent"
                        >
                            <span
                                class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0"
                                >Статус:</span
                            >
                            <span
                                class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"
                            ></span>
                            <div
                                class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:w-auto lg:text-base lg:bg-[#F3F3F8]"
                            >
                                <select
                                    v-model="service.active"
                                    class="block grow p-2 w-full h-9 rounded-lg bg-inherit font-medium lg:w-[186px]"
                                >
                                    <option value="0" selected>
                                        Аренда закрыта
                                    </option>
                                    <option value="1">Аренда открыта</option>
                                </select>
                            </div>
                        </label>
                        <label
                            class="flex items-center p-1 bg-bg1 lg:justify-between lg:bg-transparent"
                        >
                            <span
                                class="block w-[calc(50%-9px)] py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0"
                                >Договор:</span
                            >
                            <span
                                class="block self-stretch w-0.5 my-2 mx-auto border-l border-dashed border-l-[#C1C7CD] bg-white lg:hidden"
                            ></span>
                            <div
                                class="flex items-center w-[calc(50%-9px)] text-sm rounded-lg bg-white lg:w-auto lg:text-base lg:bg-[#F3F3F8]"
                            >
                                <select
                                    v-model="service.contract"
                                    class="block grow p-2 w-[186px] h-9 rounded-lg bg-inherit font-medium"
                                >
                                    <option
                                        v-for="item in chosenContracts"
                                        :value="item.id"
                                    >
                                        {{ item.name }}
                                    </option>
                                </select>
                            </div>
                        </label>
                    </div>
                </div>

                <label
                    class="block items-center p-1 mt-4 bg-bg1 lg:flex lg:bg-transparent"
                >
                    <span
                        class="block py-1.5 px-3.5 text-sm font-medium border-b border-b-[#C1C7CD] lg:w-auto lg:mr-2 lg:p-0 lg:text-base lg:border-0"
                        >Комментарий:</span
                    >
                    <div
                        class="flex items-center mt-2 text-sm rounded-lg overflow-hidden bg-white lg:w-[calc(50%-9px)] lg:mt-0 lg:text-basew-auto lg:bg-[#F3F3F8]"
                    >
                        <input
                            v-model="form.commentary"
                            type="text"
                            class="block grow p-2 w-[177px] h-9 rounded-lg bg-inherit"
                        />
                    </div>
                </label>
            </div>

            <div class="relative text-sm">
                <span
                    class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"
                ></span>
                <ul
                    class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto lg:overflow-x-visible"
                >
                    <li
                        :class="{
                            '!border-[#001D6C] text-[#001D6C]':
                                activeTab === 'services',
                        }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        @click="updateActiveTab('services')"
                    >
                        Аренда
                    </li>
                    <li
                        :class="{
                            '!border-[#001D6C] text-[#001D6C]':
                                activeTab === 'extra',
                        }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        @click="updateActiveTab('extra')"
                    >
                        Доп. услуги
                    </li>
                </ul>
            </div>

            <div
                class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3"
            >
                <div class="min-w-[930px] text-xs">
                    <template v-if="activeTab == 'services'">
                        <div
                            class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <button
                                type="button"
                                class="shrink-0 flex items-center w-[44px] py-2.5 px-2 bg-violet-full/5"
                                @click.prevent="addRows"
                            >
                                <svg
                                    width="28"
                                    height="28"
                                    viewBox="0 0 28 28"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M14.9444 8.69444C14.9444 8.31091 14.6335 8 14.25 8C13.8665 8 13.5556 8.31091 13.5556 8.69444V13.5556H8.69444C8.31091 13.5556 8 13.8665 8 14.25C8 14.6335 8.31091 14.9444 8.69444 14.9444H13.5556V19.8056C13.5556 20.1891 13.8665 20.5 14.25 20.5C14.6335 20.5 14.9444 20.1891 14.9444 19.8056V14.9444H19.8056C20.1891 14.9444 20.5 14.6335 20.5 14.25C20.5 13.8665 20.1891 13.5556 19.8056 13.5556H14.9444V8.69444Z"
                                        fill="#484964"
                                    />
                                    <rect
                                        width="28"
                                        height="28"
                                        rx="14"
                                        fill="#644DED"
                                        fill-opacity="0.08"
                                    />
                                </svg>
                            </button>
                            <div
                                class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2"
                            >
                                Оборудование
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2 cursor-pointer"
                            >
                                Дата отгрузки
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2"
                            >
                                Начало периода
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2"
                            >
                                Хранение
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2"
                            >
                                Наработка
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2"
                            >
                                Окончание периода
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2"
                            >
                                Дата возврата
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2"
                            >
                                Причина возврата
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[8.97%] py-2.5 px-2"
                            >
                                Доход
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[100px] py-2.5 px-2"
                            >
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                        fill="#21272A"
                                    />
                                </svg>
                            </div>
                        </div>

                        <div
                            v-if="serviceEquip.length > 0"
                            v-for="(equip, index) in serviceEquip"
                        >
                            <div
                                class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                            >
                                <div
                                    class="shrink-0 flex items-center w-[44px] py-2.5 px-2"
                                >
                                    <UiHyperlink
                                        :hyperlink="'some.ru'"
                                        :item-id="12"
                                        endpoint="/services"
                                    />
                                </div>
                                <div
                                    class="shrink-0 flex items-center justify-between w-[15.84%] py-2.5 px-2"
                                >
                                    {{ equip.equipment.category.name }}
                                    {{ equip.equipment.size.name }}
                                    {{ equip.equipment.series }}
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[8.97%]"
                                >
                                    <input
                                        v-model="equip.shipping_date"
                                        type="date"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()"
                                    />
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[8.97%]"
                                >
                                    <input
                                        @input="checkAndCalculate(equip)"
                                        v-model="equip.period_start_date"
                                        type="date"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()"
                                    />
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[8.97%]"
                                >
                                    <input
                                        v-model="equip.store"
                                        type="text"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    />
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[8.97%]"
                                >
                                    <input
                                        @input="checkAndCalculate(equip)"
                                        v-model="equip.operating"
                                        type="text"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    />
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[8.97%]"
                                >
                                    <input
                                        @input="checkAndCalculate(equip)"
                                        v-model="equip.period_end_date"
                                        type="date"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()"
                                    />
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[8.97%]"
                                >
                                    <input
                                        v-model="equip.return_date"
                                        type="date"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        onclick="this.showPicker()"
                                    />
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[8.97%]"
                                >
                                    <select
                                        v-model="equip.return_reason"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    >
                                        <option value="project">Проект</option>
                                        <option value="rejected">Отказ</option>
                                    </select>
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[8.97%]"
                                >
                                    <input
                                        v-model="equip.income"
                                        type="text"
                                        class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    />
                                    <span class="shrink-0 inline-block mr-2"
                                        >₽</span
                                    >
                                </div>
                                <div
                                    class="shrink-0 flex items-center w-[100px] py-2.5 px-2"
                                >
                                    <Link v-if="equip.equipment.directory === null" :href="'/directory/equipment/' + equip.equipment.id" class="mr-3.5">
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
                                    <PopoverRoot v-else>
                                        <PopoverTrigger class="mr-3.5">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z"
                                                    fill="#21272A" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z"
                                                    fill="#21272A" />
                                                <path
                                                    d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z"
                                                    fill="#21272A" />
                                                <path
                                                    d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z"
                                                    fill="#21272A" />
                                            </svg>
                                        </PopoverTrigger>
                                        <PopoverPortal>
                                            <PopoverContent side="bottom" align="end"
                                                class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                <div>Комментарий:</div>
                                                <p class="mt-2.5 text-xs">{{equip.equipment.directory.commentary}}</p>
                                                <div v-for="file in equip.equipment.directory.files"
                                                    class="mt-3 p-4 bg-bg1 text-xs">
                                                    <div class="flex items-center max-w-full">
                                                        <span
                                                            class="grow block mr-auto text-ellipsis overflow-hidden">{{
                                                                file.file_name }}</span>
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
                                                                <svg width="20" height="20" viewBox="0 0 24 24"
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
                                                                            <a download :href="'/' + file.file_path"
                                                                                class= "inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                            Скачать 
                                                                            <svg class="block ml-2"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                width="16" height="16"
                                                                                viewBox="0 0 24 24">
                                                                                <path fill="none" stroke="#464F60"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12" />
                                                                            </svg>
                                                                            </a>
                                                                        </DropdownMenuItem>
                                                                    </DropdownMenuContent>
                                                                </transition>
                                                            </DropdownMenuPortal>
                                                        </DropdownMenuRoot>
                                                    </div>
                                                </div>
                                                <Link :href="'/directory/equipment/' + equip.equipment.id"
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
                                </div>
                            </div>
                            <div
                                v-for="(equipment, index) in equip.service_subs"
                            >
                                <div
                                    class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                                >
                                    <div
                                        class="shrink-0 flex items-center w-[44px] py-2.5 px-2"
                                    >
                                        <UiHyperlink
                                            :hyperlink="'some.ru'"
                                            :item-id="12"
                                            endpoint="/services"
                                        />
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full"
                                    >
                                        {{ equipment.equipment.category.name }}
                                        {{ equipment.equipment.size.name }}
                                        {{ equipment.equipment.series }}
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[8.97%]"
                                    >
                                        <input
                                            v-model="equipment.shipping_date"
                                            type="date"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                            onclick="this.showPicker()"
                                        />
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[8.97%]"
                                    >
                                        <input
                                            v-model="
                                                equipment.period_start_date
                                            "
                                            type="date"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                            onclick="this.showPicker()"
                                            @change="
                                                checkAndCalculate(equipment)
                                            "
                                        />
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[8.97%]"
                                    >
                                        <input
                                            v-model="equipment.store"
                                            type="text"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        />
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[8.97%]"
                                    >
                                        <input
                                            @input="checkAndCalculate(equip)"
                                            v-model="equipment.operating"
                                            type="text"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        />
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[8.97%]"
                                    >
                                        <input
                                            v-model="equipment.period_end_date"
                                            type="date"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                            @change="
                                                checkAndCalculate(equipment)
                                            "
                                            onclick="this.showPicker()"
                                        />
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[8.97%]"
                                    >
                                        <input
                                            v-model="equipment.return_date"
                                            type="date"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                            onclick="this.showPicker()"
                                        />
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[8.97%]"
                                    >
                                        <select
                                            v-model="equipment.return_reason"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        >
                                            <option value="project">
                                                Проект
                                            </option>
                                            <option value="rejected">
                                                Отказ
                                            </option>
                                        </select>
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[8.97%]"
                                    >
                                        <input
                                            v-model="equipment.income"
                                            type="text"
                                            class="block w-full h-full py-2.5 px-2 bg-transparent"
                                        />
                                        <span class="shrink-0 inline-block mr-2"
                                            >₽</span
                                        >
                                    </div>
                                    <div
                                        class="shrink-0 flex items-center w-[100px] py-2.5 px-2"
                                    >
                                        <Link v-if="equipment.equipment.directory === null" :href="'/directory/equipment/' + equipment.equipment.id" class="mr-3.5">
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
                                        <PopoverRoot v-else>
                                            <PopoverTrigger class="mr-3.5">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z"
                                                        fill="#21272A" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z"
                                                        fill="#21272A" />
                                                    <path
                                                        d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z"
                                                        fill="#21272A" />
                                                    <path
                                                        d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z"
                                                        fill="#21272A" />
                                            </svg>
                                        </PopoverTrigger>
                                        <PopoverPortal>
                                            <PopoverContent side="bottom" align="end"
                                                class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                <div>Комментарий:</div>
                                                <p class="mt-2.5 text-xs">{{ equipment.equipment.directory.commentary }}</p>
                                                <div v-for="file in equipment.equipment.directory.files"
                                                    class="mt-3 p-4 bg-bg1 text-xs">
                                                    <div class="flex items-center max-w-full">
                                                        <span
                                                            class="grow block mr-auto text-ellipsis overflow-hidden">{{
                                                                file.file_name }}</span>
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
                                                                <svg width="20" height="20" viewBox="0 0 24 24"
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
                                                                            <a download :href="'/' + file.file_path"
                                                                                class= "inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                            Скачать 
                                                                            <svg class="block ml-2"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                width="16" height="16"
                                                                                viewBox="0 0 24 24">
                                                                                <path fill="none" stroke="#464F60"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12" />
                                                                            </svg>
                                                                            </a>
                                                                        </DropdownMenuItem>
                                                                    </DropdownMenuContent>
                                                                </transition>
                                                            </DropdownMenuPortal>
                                                        </DropdownMenuRoot>
                                                    </div>
                                                </div>
                                                <Link :href="'/directory/equipment/' + equipment.equipment.id"
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
                                </div>
                            </div>
                        </div>

                        <div
                            v-for="item in rows - serviceEquip.length"
                            class="flex border-b border-b-gray3 overflow-hidden [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <div
                                class="shrink-0 flex items-center w-[44px] py-2.5 px-2"
                            >
                                <UiHyperlink
                                    :hyperlink="'some.ru'"
                                    :item-id="12"
                                    endpoint="/services"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2 !border-l-violet-full"
                            >
                                <button
                                    @click="openDialog"
                                    type="button"
                                    class="text-left"
                                >
                                    Нажмите чтобы выбрать оборудование
                                </button>
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input
                                    type="date"
                                    class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    onclick="this.showPicker()"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input
                                    type="date"
                                    class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    onclick="this.showPicker()"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input
                                    type="text"
                                    class="block w-full h-full py-2.5 px-2 bg-transparent"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input
                                    type="text"
                                    class="block w-full h-full py-2.5 px-2 bg-transparent"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input
                                    type="date"
                                    class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    onclick="this.showPicker()"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input
                                    type="date"
                                    class="block w-full h-full py-2.5 px-2 bg-transparent"
                                    onclick="this.showPicker()"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <select
                                    class="block w-full h-full py-2.5 px-2 bg-transparent"
                                >
                                    <option value="">Проект</option>
                                    <option value="">Отказ</option>
                                </select>
                            </div>
                            <div class="shrink-0 flex items-center w-[8.97%]">
                                <input
                                    readonly="readonly"
                                    type="text"
                                    class="block w-full h-full py-2.5 px-2 bg-transparent"
                                />
                                <span class="shrink-0 inline-block mr-2"
                                    >₽</span
                                >
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[100px] py-2.5 px-2"
                            >
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                        fill="#21272A"
                                    />
                                </svg>

                                <DropdownMenuRoot>
                                    <DropdownMenuTrigger
                                        aria-label="Customise options"
                                    >
                                        <svg
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                fill="#687182"
                                            />
                                            <path
                                                d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                fill="#687182"
                                            />
                                            <path
                                                d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                fill="#687182"
                                            />
                                        </svg>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuPortal>
                                        <transition name="fade">
                                            <DropdownMenuContent
                                                class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                :side-offset="5"
                                                align="end"
                                            >
                                                <DropdownMenuItem>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Редактировать
                                                        <svg
                                                            class="block ml-2"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 16 16"
                                                            fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path
                                                                d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                                fill="#464F60"
                                                            />
                                                            <path
                                                                d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                                fill="#464F60"
                                                            />
                                                        </svg>
                                                    </button>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <button
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all"
                                                    >
                                                        Удалить
                                                        <svg
                                                            class="block ml-2"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 16 16"
                                                            fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                clip-rule="evenodd"
                                                                d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                fill="currentColor"
                                                            />
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
                    <template v-if="activeTab == 'extra'">
                        <div
                            class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <button
                                type="button"
                                class="shrink-0 flex items-center w-[44px] py-2.5 px-2 bg-violet-full/5"
                                @click.prevent="incServiceRow"
                            >
                                <svg
                                    width="28"
                                    height="28"
                                    viewBox="0 0 28 28"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M14.9444 8.69444C14.9444 8.31091 14.6335 8 14.25 8C13.8665 8 13.5556 8.31091 13.5556 8.69444V13.5556H8.69444C8.31091 13.5556 8 13.8665 8 14.25C8 14.6335 8.31091 14.9444 8.69444 14.9444H13.5556V19.8056C13.5556 20.1891 13.8665 20.5 14.25 20.5C14.6335 20.5 14.9444 20.1891 14.9444 19.8056V14.9444H19.8056C20.1891 14.9444 20.5 14.6335 20.5 14.25C20.5 13.8665 20.1891 13.5556 19.8056 13.5556H14.9444V8.69444Z"
                                        fill="#484964"
                                    />
                                    <rect
                                        width="28"
                                        height="28"
                                        rx="14"
                                        fill="#644DED"
                                        fill-opacity="0.08"
                                    />
                                </svg>
                            </button>
                            <div
                                class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2"
                            >
                                Услуга
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer"
                            >
                                Дата перевозки
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)] py-2.5 px-2"
                            >
                                Комментарий
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2"
                            >
                                Цена без НДС
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[100px] py-2.5 px-2"
                            >
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                        fill="#21272A"
                                    />
                                </svg>
                            </div>
                        </div>

                        <div
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <div
                                class="shrink-0 flex items-center w-[44px] py-2.5 px-2"
                            >
                                <UiHyperlink
                                    v-if="selectedEquipmentService"
                                    :hyperlink="
                                        selectedEquipmentService?.hyperlink
                                    "
                                    :item-id="selectedEquipmentService?.id"
                                    endpoint="/services"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2"
                            >
                                <button
                                    @click="showModalServices(true)"
                                    type="button"
                                >
                                    Нажмите чтобы выбрать услугу
                                </button>
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[14.08%] cursor-pointer"
                            >
                                <input
                                    type="date"
                                    class="block w-full h-full px-2 bg-transparent"
                                    onclick="this.showPicker()"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]"
                            >
                                <input
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
                                <input
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[100px] py-2.5 px-2"
                            >
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                        fill="#21272A"
                                    />
                                </svg>

                                <DropdownMenuRoot>
                                    <DropdownMenuTrigger
                                        aria-label="Customise options"
                                    >
                                        <svg
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                fill="#687182"
                                            />
                                            <path
                                                d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                fill="#687182"
                                            />
                                            <path
                                                d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                fill="#687182"
                                            />
                                        </svg>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuPortal>
                                        <transition name="fade">
                                            <DropdownMenuContent
                                                class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                :side-offset="5"
                                                align="end"
                                            >
                                                <DropdownMenuItem>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Редактировать
                                                        <svg
                                                            class="block ml-2"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 16 16"
                                                            fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path
                                                                d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                                fill="#464F60"
                                                            />
                                                            <path
                                                                d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                                fill="#464F60"
                                                            />
                                                        </svg>
                                                    </button>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <button
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all"
                                                    >
                                                        Удалить
                                                        <svg
                                                            class="block ml-2"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 16 16"
                                                            fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                clip-rule="evenodd"
                                                                d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                fill="currentColor"
                                                            />
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
                            v-for="(item, index) in selectedServices"
                            :key="index"
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <div
                                class="shrink-0 flex items-center w-[44px] py-2.5 px-2"
                            >
                                <UiHyperlink
                                    v-if="selectedEquipmentService"
                                    :hyperlink="
                                        selectedEquipmentService?.hyperlink
                                    "
                                    :item-id="selectedEquipmentService?.id"
                                    endpoint="/services"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2"
                            >
                                {{ item.value }}
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[14.08%] cursor-pointer"
                            >
                                <input
                                    v-model="item.shipping_date"
                                    type="date"
                                    class="block w-full h-full px-2 bg-transparent"
                                    onclick="this.showPicker()"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]"
                            >
                                <input
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                    v-model="item.commentary"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
                                <input
                                    v-model="item.price"
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[100px] py-2.5 px-2"
                            >
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                        fill="#21272A"
                                    />
                                </svg>

                                <DropdownMenuRoot>
                                    <DropdownMenuTrigger
                                        aria-label="Customise options"
                                    >
                                        <svg
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                fill="#687182"
                                            />
                                            <path
                                                d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                fill="#687182"
                                            />
                                            <path
                                                d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                fill="#687182"
                                            />
                                        </svg>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuPortal>
                                        <transition name="fade">
                                            <DropdownMenuContent
                                                class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                :side-offset="5"
                                                align="end"
                                            >
                                                <DropdownMenuItem>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Редактировать
                                                        <svg
                                                            class="block ml-2"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 16 16"
                                                            fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path
                                                                d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                                fill="#464F60"
                                                            />
                                                            <path
                                                                d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                                fill="#464F60"
                                                            />
                                                        </svg>
                                                    </button>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <button
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all"
                                                    >
                                                        Удалить
                                                        <svg
                                                            class="block ml-2"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 16 16"
                                                            fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                clip-rule="evenodd"
                                                                d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                fill="currentColor"
                                                            />
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
                            v-if="extraServices.length > 0"
                            v-for="(item, index) in extraServices"
                            :key="index"
                            class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                        >
                            <div
                                class="shrink-0 flex items-center w-[44px] py-2.5 px-2"
                            >
                                <UiHyperlink
                                    v-if="selectedEquipmentService"
                                    :hyperlink="
                                        selectedEquipmentService?.hyperlink
                                    "
                                    :item-id="selectedEquipmentService?.id"
                                    endpoint="/services"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[15.84%] py-2.5 px-2"
                            >
                                {{ item.value }}
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[14.08%] cursor-pointer"
                            >
                                <input
                                    v-model="item.shipping_date"
                                    type="date"
                                    class="block w-full h-full px-2 bg-transparent"
                                    onclick="this.showPicker()"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[calc(100%-44px-15.84%-14.08%-14.08%-100px)]"
                            >
                                <input
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                    v-model="item.commentary"
                                />
                            </div>
                            <div class="shrink-0 flex items-center w-[14.08%]">
                                <input
                                    v-model="item.price"
                                    type="text"
                                    class="block w-full h-full px-2 bg-transparent"
                                />
                            </div>
                            <div
                                class="shrink-0 flex items-center w-[100px] py-2.5 px-2"
                            >
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                        fill="#21272A"
                                    />
                                    <path
                                        d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                        fill="#21272A"
                                    />
                                </svg>

                                <DropdownMenuRoot>
                                    <DropdownMenuTrigger
                                        aria-label="Customise options"
                                    >
                                        <svg
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                fill="#687182"
                                            />
                                            <path
                                                d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                fill="#687182"
                                            />
                                            <path
                                                d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                fill="#687182"
                                            />
                                        </svg>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuPortal>
                                        <transition name="fade">
                                            <DropdownMenuContent
                                                class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                :side-offset="5"
                                                align="end"
                                            >
                                                <DropdownMenuItem>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Редактировать
                                                        <svg
                                                            class="block ml-2"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 16 16"
                                                            fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path
                                                                d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                                fill="#464F60"
                                                            />
                                                            <path
                                                                d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                                fill="#464F60"
                                                            />
                                                        </svg>
                                                    </button>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <button
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all"
                                                    >
                                                        Удалить
                                                        <svg
                                                            class="block ml-2"
                                                            width="16"
                                                            height="16"
                                                            viewBox="0 0 16 16"
                                                            fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                clip-rule="evenodd"
                                                                d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                fill="currentColor"
                                                            />
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
                    @click="submit"
                >
                    Сохранить
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
