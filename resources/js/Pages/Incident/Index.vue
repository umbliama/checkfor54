<script setup>
import { MenuItem, MenuItems, Menu, MenuButton } from '@headlessui/vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, nextTick, onBeforeMount, onMounted, reactive, ref, toRaw, watch } from 'vue';
import ServiceModal from '@/Components/ServiceModal.vue';
import store from '../../../store/index';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Swiper, SwiperSlide } from 'swiper/vue';

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
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from 'radix-vue'
import UiField from '@/Components/Ui/UiField.vue';
import UiFieldSelect from '@/Components/Ui/UiFieldSelect.vue';
import UiUserAvatar from '@/Components/Ui/UiUserAvatar.vue';
import 'swiper/css';
import IncidentPagination from '@/Components/IncidentPagination.vue';

const page = usePage()

const user = computed(() => page.props.auth.user);

const selectedEquipment = computed(() => store.getters['services/getSelectedEquipment']);
const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedSubEquipmentArray = computed(() => store.getters['services/getSubSelectedEquipmentObjects']);
const selectedEquipmentService = computed(() => store.getters['services/getSelectedEquipmentService']);
const modalShown = computed(() => store.getters['services/getModalShown']);
const getMenuActiveItem = computed(() => store.getters['incident/getActiveMenuItem']);
const subEquipment = computed(() => store.getters['services/getSubEquipment'])
const props = defineProps({
    tasksColumns: Object,
    advColumns: Object,
    tasksColumnsArchived: Object,
    advColumnsArchived: Object,
    blocks: Array,
    contragents: Array,
    employees: Array,
    advColumnsArchivedCount: Number,
    tasksColumnsArchivedCount: Number,
    userAvatar: String
})


const showModal = (value) => {
    store.dispatch('services/updateModalShown', value)
}

const updateActiveMenuItem = (value) => {
    store.dispatch('incident/updateActiveMenuItem', value)
}



const form = reactive({
    mediaFile: null,
    mediaCaption: null,
    chosenAgent: null,
    equipmentCategory: null,
    equipmentSeries: null,
    equipmentSize: null,
    employee_id: null,
    equipmentId: selectedEquipment ?? null,
    subEquipmentIds: subEquipmentArray ?? null,
    files: null
});


const formatDate = (timestamp) => {
    const date = new Date(timestamp);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}.${month}.${year}`;
};
const swiper_refs = ref({});

const findEmployeeById = (employee_id) => {
    const raw = toRaw(props.employees)
    return raw.find(employee => employee.id == employee_id)
}


const createColumn = () => {
    axios.post('const')
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
}, { deep: true });


const createBlock = (columnId, type) => {
    router.post(`/constructor/column/${columnId}/block/${type}`)
}

const query = ref((new URLSearchParams(window.location.search)).get('query') || '');




const handleMediaFileUpload = async (event, columnId, blockId) => {
    const selectedFiles = Array.from(event.target.files);

    const rawBlocks = toRaw(props.tasksColumns.data.find(eq => eq.id === columnId));
    const blockIndex = rawBlocks.blocks.findIndex(block => block.id === blockId);

    if (blockIndex !== -1) {
        const existingFiles = rawBlocks.blocks[blockIndex].media_url || [];

        const updatedFiles = [...existingFiles, ...selectedFiles];

        rawBlocks.blocks[blockIndex].media_url = updatedFiles;

        await saveBlock(blockId, {
            media_file: selectedFiles,
            existing_media_urls: existingFiles,
        });
    } else {
        console.error(`Block with ID ${blockId} not found in column ${columnId}`);
    }
};

const handleFileUpload = async (event, columnId, blockId) => {
    const selectedFiles = Array.from(event.target.files);

    const rawBlocks = toRaw(props.tasksColumns.data.find(eq => eq.id === columnId));
    const blockIndex = rawBlocks.blocks.findIndex(block => block.id === blockId);

    if (blockIndex !== -1) {
        const existingFiles = rawBlocks.blocks[blockIndex].file_url || [];

        const updatedFiles = [...existingFiles, ...selectedFiles];

        rawBlocks.blocks[blockIndex].file_url = updatedFiles;

        await saveBlock(blockId, {
            files: selectedFiles,
            existing_file_urls: existingFiles,
        });
    } else {
        console.error(`Block with ID ${blockId} not found in column ${columnId}`);
    }
};




const saveBlock = async (blockId, blockData) => {
    try {
        const response = await router.post(`constructor/block/${blockId}/save`, blockData);
    } catch (error) {
        console.error('Error saving block:', error);
    }
}


onBeforeMount(() => {
    const url_params = new URLSearchParams(window.location.search);

    nextTick(() => {
        if (
            !url_params.get('perPage') ||
            (url_params.get('perPage') !== '1' && window.innerWidth <= 1024)
        ) {
            const route_params = { perPage: 1 };

            if (url_params.get('query')) route_params.query = url_params.get('query');

            router.get(route('incident.index'), route_params);
            return;
        }
        if (
            !url_params.get('perPage') ||
            (url_params.get('perPage') !== '3' && window.innerWidth <= 1536)
        ) {
            const route_params = { perPage: 3 };

            if (url_params.get('query')) route_params.query = url_params.get('query');

            router.get(route('incident.index'), route_params);
            return;
        }
        if (
            !url_params.get('perPage') ||
            (url_params.get('perPage') !== '4' && window.innerWidth > 1536)
        ) {
            const route_params = { perPage: 4 };

            if (url_params.get('query')) route_params.query = url_params.get('query');

            router.get(route('incident.index'), route_params);
            return;
        }
    });
});

</script>

<template>
    <AuthenticatedLayout>
        <div class="p-5">
            <div class="relative flex items-center justify-between flex-col lg:flex-row">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul
                    class="relative flex items-center w-full font-medium space-x-8 overflow-x-auto lg:overflow-x-visible">
                    <li :class="{ '!border-[#001D6C] text-[#001D6C]': getMenuActiveItem === 'tasks' }"
                        class="flex items-center border-b-2 border-transparent py-3 text-base lg:text-lg cursor-pointer"
                        @click="updateActiveMenuItem('tasks')">
                        Задачи
                    </li>
                    <li :class="{ '!border-[#001D6C] text-[#001D6C]': getMenuActiveItem === 'adv' }"
                        class="flex items-center border-b-2 border-transparent py-3 text-base lg:text-lg cursor-pointer"
                        @click="updateActiveMenuItem('adv')">
                        Рекламация
                    </li>
                    <li :class="{ '!border-[#001D6C] text-[#001D6C]': getMenuActiveItem === 'history' || getMenuActiveItem === 'history_tasks' || getMenuActiveItem === 'history_adv' }"
                        class="flex items-center border-b-2 border-transparent py-3 text-base lg:text-lg cursor-pointer"
                        @click="updateActiveMenuItem('history_tasks')">
                        История
                    </li>
                    <li>
                        <Link v-if="user.isAdmin" :href="route('equip.tests')"
                            class="flex items-center border-b-2 border-transparent py-3 text-base lg:text-lg">
                        Admin
                        </Link>
                    </li>
                    <li>
                        <DropdownMenuRoot>
                            <DropdownMenuTrigger aria-label="Customise options">
                                <svg class="block" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                        fill="#697077" />
                                </svg>
                            </DropdownMenuTrigger>

                            <DropdownMenuPortal>
                                <transition name="fade">
                                    <DropdownMenuContent
                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                        :side-offset="5" align="end">
                                        <DropdownMenuItem>
                                            <Link
                                                :href="route('constructor.columnCreate', { type: getMenuActiveItem, perPage: 3 })"
                                                as="button" method="POST"
                                                class="flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                            Добавить столбец

                                            <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.74999 2.75006C8.74999 2.33585 8.4142 2.00006 7.99999 2.00006C7.58578 2.00006 7.24999 2.33585 7.24999 2.75006V7.25003H2.75C2.33579 7.25003 2 7.58581 2 8.00003C2 8.41424 2.33579 8.75003 2.75 8.75003H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75003H13.25C13.6642 8.75003 14 8.41424 14 8.00003C14 7.58581 13.6642 7.25003 13.25 7.25003H8.74999V2.75006Z"
                                                    fill="#464F60" />
                                            </svg>
                                            </Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </transition>
                            </DropdownMenuPortal>
                        </DropdownMenuRoot>
                    </li>
                </ul>
                <form class="w-full mt-5 lg:w-56 lg:mt-0">
                    <UiField v-model="query" :inp-attrs="{ placeholder: 'Поиск...', name: 'query' }">
                        <template #prepend>
                            <button type="submit">
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </button>
                        </template>
                    </UiField>
                </form>
            </div>

            <div v-if="getMenuActiveItem === 'history' || getMenuActiveItem === 'history_tasks' || getMenuActiveItem === 'history_adv'"
                class="relative flex items-center justify-between flex-col mt-5 lg:flex-row">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul
                    class="relative flex items-center w-full font-medium space-x-8 overflow-x-auto lg:overflow-x-visible">
                    <li @click="updateActiveMenuItem('history_tasks')"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': getMenuActiveItem === 'history_tasks' }"
                        class="flex items-center border-b-2 border-transparent py-3 text-sm cursor-pointer">
                        Задачи
                        <span
                            class="flex items-center justify-center w-[25px] h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            {{ tasksColumnsArchivedCount }}
                        </span>
                    </li>
                    <li @click="updateActiveMenuItem('history_adv')"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': getMenuActiveItem === 'history_adv' }"
                        class="flex items-center border-b-2 border-transparent py-3 text-sm cursor-pointer">
                        Рекламация
                        <span
                            class="flex items-center justify-center w-auto h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            {{ advColumnsArchivedCount }}
                        </span>
                    </li>
                </ul>
            </div>

            <div class="grid grid-cols-1 items-start gap-5 mt-5 lg:grid-cols-3 3xl:grid-cols-4">
                <div v-if="getMenuActiveItem == 'tasks'" v-for="column in tasksColumns.data" class="p-2 bg-[#DDE1E6]">
                    <div class="flex items-center justify-between">
                        <div class="grow">Хронология</div>

                        <DropdownMenuRoot>
                            <DropdownMenuTrigger aria-label="Customise options" class="shrink-0 ml-2">
                                <svg class="block" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                        fill="#697077" />
                                </svg>
                            </DropdownMenuTrigger>

                            <DropdownMenuPortal>
                                <transition name="fade">
                                    <DropdownMenuContent
                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                        :side-offset="5" align="end">
                                        <!-- <DropdownMenuLabel
                                            class="flex items-center justify-between py-1 px-2.5 border-b border-b-gray1"
                                        >
                                            Добавить блок
                                            <svg class="shrink-0 ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.74999 2.75006C8.74999 2.33585 8.4142 2.00006 7.99999 2.00006C7.58578 2.00006 7.24999 2.33585 7.24999 2.75006V7.25003H2.75C2.33579 7.25003 2 7.58581 2 8.00003C2 8.41424 2.33579 8.75003 2.75 8.75003H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75003H13.25C13.6642 8.75003 14 8.41424 14 8.00003C14 7.58581 13.6642 7.25003 13.25 7.25003H8.74999V2.75006Z" fill="#464F60"/>
                                            </svg>
                                        </DropdownMenuLabel> -->
                                        <DropdownMenuLabel
                                            class="flex items-center justify-between py-0.5 px-2.5 opacity-70 font-normal text-xs">
                                            Добавить блок
                                        </DropdownMenuLabel>
                                        <DropdownMenuItem>
                                            <Link
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST" @click="createBlock(column.id, 'customer')"
                                                :href="route('constructor.createBlock', { column: column.id, type: 'customer' })">
                                            Заказчик
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'employee')"
                                                :href="route('constructor.createBlock', { column: column.id, type: 'employee' })"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Сотрудник
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'equipment')"
                                                :href="route('constructor.createBlock', { column: column.id, type: 'equipment' })"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Оборудование
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'commentary')"
                                                :href="route('constructor.createBlock', { column: column.id, type: 'commentary' })"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Комментарий
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'mediafiles')"
                                                :href="route('constructor.createBlock', { column: column.id, type: 'mediafiles' })"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Медиа
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'files')"
                                                :href="route('constructor.createBlock', { column: column.id, type: 'files' })"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Файлы
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuSeparator class="h-[1px] my-1.5 bg-gray1/30" />

                                        <DropdownMenuItem>
                                            <Link
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="PUT" :href="route('constructor.archiveColumn', column.id)">
                                            Архивировать колонку
                                            <svg class="shrink-0 block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_5001_1052)">
                                                    <path d="M14 5.33337V14H2V5.33337" stroke="#464F60" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M15.3333 2H0.666626V5.33333H15.3333V2Z" stroke="#464F60"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M6.66663 8H9.33329" stroke="#464F60" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_5001_1052">
                                                        <rect width="16" height="16" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link method="DELETE" :href="route('constructor.deleteColumn', column.id)"
                                                class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                            Удалить колонку
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
                    <div class="mt-2 space-y-2">
                        <div class="p-2 border rounded mb-2 bg-white">
                            <div class="mb-2 text-right">{{ formatDate(column.created_at) }}</div>
                            <div class="space-y-3">
                                <div v-for="block in column.blocks" :key="block.id">
                                    <template v-if="block.type == 'customer'">
                                        <UiFieldSelect :items="contragents.map(c => ({ title: c.name, value: c.id }))"
                                            label="Заказчик:" only-value v-model="block.contragent_id" />
                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>
                                                                <Link
                                                                    @click="saveBlock(block.id, { contragent_id: block.contragent_id });">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button></Link>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>
                                                                </Link>

                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                    <template v-if="block.type == 'employee'">
                                        <UiFieldSelect
                                            :items="employees.map(e => ({ title: e.lastname + e.name, value: e.id }))"
                                            label="Сотрудник:" only-value v-model="block.employee_id" />
                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>

                                                                <Link
                                                                    @click="saveBlock(block.id, { employee_id: block.employee_id });">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                                </Link>

                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>
                                                                </Link>
                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                    <template v-if="block.type == 'commentary'">
                                        <UiField label="Комментарий:" v-model="block.commentary" textarea />

                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>

                                                                <Link
                                                                    @click="saveBlock(block.id, { commentary: block.commentary });">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                                </Link>

                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>
                                                                </Link>

                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                    <template v-if="block.type == 'equipment'">
                                        <UiField label="Оборудование" v-model="block.equipment" textarea />
                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>
                                                                <Link
                                                                    @click="saveBlock(block.id, { equipment: block.equipment });">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                                </Link>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>

                                                                </Link>
                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                    <template v-if="block.type == 'mediafiles'">
                                        <label class="block mb-2">Медиа:</label>
                                        <!-- в ключ swiper_refs нужно передать уникальный id блока медиа -->
                                        <Swiper :slides-per-view="1" @swiper="swiper => swiper_refs['1'] = swiper"
                                            class="!z-[unset] [&>.swiper-wrapper]:z-[unset]">
                                            <SwiperSlide v-for="(n, i) in block.media_url" class=""
                                                style="height:240px">
                                                <img :src="'/storage/' + n"
                                                    class="block w-full h-full object-center object-cover" />
                                            </SwiperSlide>
                                        </Swiper>
                                        <div class="grid grid-cols-6 gap-2.5 mt-2 p-2 bg-bg1">
                                            <div class="relative" v-for="(n, i) in block.media_url">
                                                <button class="block w-full h-full" type="button"
                                                    @click="swiper_refs['1'].slideTo(i)" style="height: 40px">
                                                    <img :src="'/storage/' + n"
                                                        class="block w-full h-full object-center object-cover" />
                                                </button>

                                                <Link method="DELETE"
                                                    :href="route('constructor.deleteImage', [column.id, block.id, i])">

                                                <button type="button"
                                                    class="absolute left-full bottom-full translate-y-1/2 -translate-x-1/2 rounded-full bg-[#DDE1E6]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                        viewBox="0 0 24 24">
                                                        <path fill="currentColor"
                                                            d="m12 13.4l-4.9 4.9q-.275.275-.7.275t-.7-.275t-.275-.7t.275-.7l4.9-4.9l-4.9-4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l4.9 4.9l4.9-4.9q.275-.275.7-.275t.7.275t.275.7t-.275.7L13.4 12l4.9 4.9q.275.275.275.7t-.275.7t-.7.275t-.7-.275z" />
                                                    </svg>
                                                </button></Link>

                                            </div>
                                        </div>


                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>
                                                                <label
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all cursor-pointer">
                                                                    <input type="file" multiple hidden
                                                                        @change="handleMediaFileUpload($event, column.id, block.id)" />
                                                                    Добавить медиа
                                                                </label>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>
                                                                </Link>
                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                    <template v-if="block.type == 'files'">
                                        <label class="block mb-2">Файлы:</label>
                                        <ul class="py-1 px-3 space-y-3 text-xs bg-bg1">
                                            <div v-for="(file, i) in block.file_url"
                                                class="flex items-center max-w-full">
                                                <span class="grow block mr-auto text-ellipsis overflow-hidden">{{ file
                                                    }}</span>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.83 6H19C19.7956 6 20.5587 6.31607 21.1213 6.87868C21.6839 7.44129 22 8.20435 22 9V17C22 17.7956 21.6839 18.5587 21.1213 19.1213C20.5587 19.6839 19.7956 20 19 20H5C4.20435 20 3.44129 19.6839 2.87868 19.1213C2.31607 18.5587 2 17.7956 2 17V7C2 6.20435 2.31607 5.44129 2.87868 4.87868C3.44129 4.31607 4.20435 4 5 4H10C11.306 4 12.417 4.835 12.83 6ZM19 8H11.415L10.944 6.666C10.8748 6.47105 10.7468 6.30233 10.5778 6.18307C10.4087 6.06381 10.2069 5.99985 10 6H5C4.73478 6 4.48043 6.10536 4.29289 6.29289C4.10536 6.48043 4 6.73478 4 7V17C4 17.2652 4.10536 17.5196 4.29289 17.7071C4.48043 17.8946 4.73478 18 5 18H19C19.2652 18 19.5196 17.8946 19.7071 17.7071C19.8946 17.5196 20 17.2652 20 17V9C20 8.73478 19.8946 8.48043 19.7071 8.29289C19.5196 8.10536 19.2652 8 19 8ZM16 10H18V12H16V10ZM14 8H16V10H14V8ZM14 12H16V14H14V12ZM16 14H18V16H16V14ZM14 16H16V18H14V16Z"
                                                        fill="#697077" />
                                                </svg>
                                                <DropdownMenuRoot>
                                                    <DropdownMenuTrigger aria-label="Customise options">
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
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
                                                                    <a :href="'/' + file" download
                                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                        Скачать
                                                                        <svg class="block ml-2"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            width="16" height="16" viewBox="0 0 24 24">
                                                                            <path fill="none" stroke="#464F60"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12" />
                                                                        </svg>
                                                                    </a>
                                                                </DropdownMenuItem>
                                                                <DropdownMenuItem>
                                                                    <Link
                                                                        :href="route('constructor.deleteFile', [column.id, block.id, i])"
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
                                        </ul>

                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>
                                                                <label
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all cursor-pointer">
                                                                    <input type="file" hidden multiple
                                                                        @change="handleFileUpload($event, column.id, block.id)" />
                                                                    Добавить файл
                                                                </label>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>
                                                                </Link>
                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="getMenuActiveItem == 'adv'" v-for="column in advColumns.data" class="p-2 bg-[#DDE1E6]">
                    <div class="flex items-center justify-between">
                        <div class="grow">Хронология</div>

                        <DropdownMenuRoot>
                            <DropdownMenuTrigger aria-label="Customise options" class="shrink-0 ml-2">
                                <svg class="block" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                        fill="#697077" />
                                </svg>
                            </DropdownMenuTrigger>

                            <DropdownMenuPortal>
                                <transition name="fade">
                                    <DropdownMenuContent
                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                        :side-offset="5" align="end">
                                        <!-- <DropdownMenuLabel
                                            class="flex items-center justify-between py-1 px-2.5 border-b border-b-gray1"
                                        >
                                            Добавить блок
                                            <svg class="shrink-0 ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.74999 2.75006C8.74999 2.33585 8.4142 2.00006 7.99999 2.00006C7.58578 2.00006 7.24999 2.33585 7.24999 2.75006V7.25003H2.75C2.33579 7.25003 2 7.58581 2 8.00003C2 8.41424 2.33579 8.75003 2.75 8.75003H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75003H13.25C13.6642 8.75003 14 8.41424 14 8.00003C14 7.58581 13.6642 7.25003 13.25 7.25003H8.74999V2.75006Z" fill="#464F60"/>
                                            </svg>
                                        </DropdownMenuLabel> -->
                                        <DropdownMenuLabel
                                            class="flex items-center justify-between py-0.5 px-2.5 opacity-70 font-normal text-xs">
                                            Добавить блок
                                        </DropdownMenuLabel>
                                        <DropdownMenuItem>
                                            <Link
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST" @click="createBlock(column.id, 'customer')"
                                                :href="route('constructor.createBlock', column.id, 'customer')">
                                            Заказчик
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'employee')"
                                                :href="route('constructor.createBlock', column.id, 'employee')"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Сотрудник
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'equipment')"
                                                :href="route('constructor.createBlock', column.id, 'equipment')"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Оборудование
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'commentary')"
                                                :href="route('constructor.createBlock', column.id, 'commentary')"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Комментарий
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'mediafiles')"
                                                :href="route('constructor.createBlock', column.id, 'mediafiles')"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Медиа
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link @click="createBlock(column.id, 'files')"
                                                :href="route('constructor.createBlock', column.id, 'files')"
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="POST">
                                            Файлы
                                            </Link>
                                        </DropdownMenuItem>

                                        <DropdownMenuSeparator class="h-[1px] my-1.5 bg-gray1/30" />

                                        <DropdownMenuItem>
                                            <Link
                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                method="PUT" :href="route('constructor.archiveColumn', column.id)">
                                            Архивировать колонку
                                            <svg class="shrink-0 block ml-2" width="16" height="16" viewBox="0 0 16 16"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_5001_1052)">
                                                    <path d="M14 5.33337V14H2V5.33337" stroke="#464F60" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M15.3333 2H0.666626V5.33333H15.3333V2Z" stroke="#464F60"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M6.66663 8H9.33329" stroke="#464F60" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_5001_1052">
                                                        <rect width="16" height="16" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link method="DELETE" :href="route('constructor.deleteColumn', column.id)"
                                                class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                            Удалить колонку
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
                    <div class="mt-2 space-y-2">
                        <div class="p-2 border rounded mb-2 bg-white">
                            <div class="mb-2 text-right">{{ formatDate(column.created_at) }}</div>
                            <div class="space-y-3">
                                <div v-for="block in column.blocks" :key="block.id">
                                    <template v-if="block.type == 'customer'">
                                        <UiFieldSelect :items="contragents.map(c => ({ title: c.name, value: c.id }))"
                                            label="Заказчик:" only-value v-model="block.contragent_id" />
                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>
                                                                <Link
                                                                    @click="saveBlock(block.id, { contragent_id: block.contragent_id });">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button></Link>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>
                                                                </Link>

                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                    <template v-if="block.type == 'employee'">
                                        <UiFieldSelect
                                            :items="employees.map(e => ({ title: e.lastname + e.name, value: e.id }))"
                                            label="Сотрудник:" only-value v-model="block.employee_id" />
                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>

                                                                <Link
                                                                    @click="saveBlock(block.id, { employee_id: block.employee_id });">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                                </Link>

                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>
                                                                </Link>
                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                    <template v-if="block.type == 'commentary'">
                                        <UiField label="Комментарий:" v-model="block.commentary" textarea />

                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>

                                                                <Link
                                                                    @click="saveBlock(block.id, { commentary: block.commentary });">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                                </Link>

                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>
                                                                </Link>

                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                    <template v-if="block.type == 'equipment'">
                                        <UiField label="Оборудование" v-model="block.equipment" textarea />
                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>
                                                                <Link
                                                                    @click="saveBlock(block.id, { equipment: block.equipment });">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                                </Link>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <Link method="DELETE"
                                                                    :href="route('constructor.deleteBlock', block.id)">
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
                                                                    <svg class="block ml-2" width="16" height="16"
                                                                        viewBox="0 0 16 16" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </button>

                                                                </Link>
                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </transition>
                                                </DropdownMenuPortal>
                                            </DropdownMenuRoot>
                                        </div>
                                    </template>
                                    <template v-if="block.type == 'mediafiles'">
                                        <label class="block mb-2">Медиа:</label>
                                        <!-- в ключ swiper_refs нужно передать уникальный id блока медиа -->
                                        <Swiper :slides-per-view="1" @swiper="swiper => swiper_refs['1'] = swiper"
                                            class="!z-[unset] [&>.swiper-wrapper]:z-[unset]">
                                            <SwiperSlide v-for="(n, i) in block.media_url" class=""
                                                style="height:240px">
                                                <img :src="'/storage/' + n"
                                                    class="block w-full h-full object-center object-cover" />
                                            </SwiperSlide>
                                        </Swiper>
                                        <div class="grid grid-cols-6 gap-2.5 mt-2 p-2 bg-bg1">
                                            <div class="relative" v-for="(n, i) in block.media_url">
                                                <button class="block w-full h-full" type="button"
                                                    @click="swiper_refs['1'].slideTo(i)" style="height: 40px">
                                                    <img :src="'/storage/' + n"
                                                        class="block w-full h-full object-center object-cover" />
                                                </button>

                                                <Link method="DELETE"
                                                    :href="route('constructor.deleteImage', [column.id, block.id, i])">

                                                <button type="button"
                                                    class="absolute left-full bottom-full translate-y-1/2 -translate-x-1/2 rounded-full bg-[#DDE1E6]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                        viewBox="0 0 24 24">
                                                        <path fill="currentColor"
                                                            d="m12 13.4l-4.9 4.9q-.275.275-.7.275t-.7-.275t-.275-.7t.275-.7l4.9-4.9l-4.9-4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l4.9 4.9l4.9-4.9q.275-.275.7-.275t.7.275t.275.7t-.275.7L13.4 12l4.9 4.9q.275.275.275.7t-.275.7t-.7.275t-.7-.275z" />
                                                    </svg>
                                                </button></Link>

                                            </div>
                                        </div>


                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>
                                                                <label
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all cursor-pointer">
                                                                    <input type="file" multiple hidden
                                                                        @change="handleMediaFileUpload($event, column.id - 1, block.id)" />
                                                                    Добавить медиа
                                                                </label>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
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
                                    </template>
                                    <template v-if="block.type == 'files'">
                                        <label class="block mb-2">Файлы:</label>
                                        <ul class="py-1 px-3 space-y-3 text-xs bg-bg1">
                                            <div v-for="file in block.file_url" class="flex items-center max-w-full">
                                                <span class="grow block mr-auto text-ellipsis overflow-hidden">{{ file
                                                    }}</span>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.83 6H19C19.7956 6 20.5587 6.31607 21.1213 6.87868C21.6839 7.44129 22 8.20435 22 9V17C22 17.7956 21.6839 18.5587 21.1213 19.1213C20.5587 19.6839 19.7956 20 19 20H5C4.20435 20 3.44129 19.6839 2.87868 19.1213C2.31607 18.5587 2 17.7956 2 17V7C2 6.20435 2.31607 5.44129 2.87868 4.87868C3.44129 4.31607 4.20435 4 5 4H10C11.306 4 12.417 4.835 12.83 6ZM19 8H11.415L10.944 6.666C10.8748 6.47105 10.7468 6.30233 10.5778 6.18307C10.4087 6.06381 10.2069 5.99985 10 6H5C4.73478 6 4.48043 6.10536 4.29289 6.29289C4.10536 6.48043 4 6.73478 4 7V17C4 17.2652 4.10536 17.5196 4.29289 17.7071C4.48043 17.8946 4.73478 18 5 18H19C19.2652 18 19.5196 17.8946 19.7071 17.7071C19.8946 17.5196 20 17.2652 20 17V9C20 8.73478 19.8946 8.48043 19.7071 8.29289C19.5196 8.10536 19.2652 8 19 8ZM16 10H18V12H16V10ZM14 8H16V10H14V8ZM14 12H16V14H14V12ZM16 14H18V16H16V14ZM14 16H16V18H14V16Z"
                                                        fill="#697077" />
                                                </svg>
                                                <DropdownMenuRoot>
                                                    <DropdownMenuTrigger aria-label="Customise options">
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
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
                                                                    <a :href="'/' + file" download
                                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                        Скачать
                                                                        <svg class="block ml-2"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            width="16" height="16" viewBox="0 0 24 24">
                                                                            <path fill="none" stroke="#464F60"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12" />
                                                                        </svg>
                                                                    </a>
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
                                        </ul>

                                        <div class="flex items-center justify-between">
                                            <ul class="flex mt-2 -space-x-2">

                                                <li>
                                                    <UiUserAvatar :image="block.user.avatar" size="24px"
                                                        class="border border-[#DDE1E6]" />
                                                </li>
                                            </ul>

                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options"
                                                    class="shrink-0 ml-2">
                                                    <svg class="block" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                            fill="#697077" />
                                                    </svg>
                                                </DropdownMenuTrigger>

                                                <DropdownMenuPortal>
                                                    <transition name="fade">
                                                        <DropdownMenuContent
                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)] z-10"
                                                            :side-offset="5" align="end">
                                                            <DropdownMenuItem>
                                                                <label
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all cursor-pointer">
                                                                    <input type="file" hidden multiple
                                                                        @change="handleFileUpload($event, column.id, block.id)" />
                                                                    Добавить файл
                                                                </label>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                    Сохранить блок
                                                                </button>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>
                                                                <button type="button"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                                    Удалить блок
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
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="getMenuActiveItem == 'history_tasks'" v-for="(taskArray, key) in tasksColumnsArchived.data"
                    :key="key" class="p-2 bg-[#DDE1E6]">
                    <div class="flex items-center justify-between">
                        <div class="grow">Хронология</div>
                    </div>
                    <div class="mt-2 space-y-2">
                        <div class="p-2 border rounded mb-2 bg-white">
                            <div class="mb-2 text-right text-xs">{{ formatDate(taskArray[0].created_at) }}</div>
                            <AccordionRoot v-for="column in taskArray" type="multiple" :collapsible="true">
                                <AccordionItem value="some-id">
                                    <AccordionHeader class="flex items-center justify-between px-2.5 bg-bg1">
                                        <AccordionTrigger class="shrink-0 group mr-3">
                                            <svg class="group-data-[state=open]:hidden" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                    fill="#242533" />
                                            </svg>
                                            <svg class="group-data-[state=closed]:hidden" width="24" height="24"
                                                viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                    fill="#242533" />
                                            </svg>
                                        </AccordionTrigger>
                                        <span class="block w-1/3 text-center text-sm">{{
                                            formatDate(column.created_at)
                                        }}</span>
                                        <span class="block w-1/3 text-center text-sm">{{
                                            formatDate(column.archive_date)
                                        }}</span>
                                        <DropdownMenuRoot>
                                            <!-- DropdownMenuTrigger and content as before -->
                                        </DropdownMenuRoot>
                                    </AccordionHeader>

                                    <AccordionContent
                                        class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden">
                                        <div class="mt-3 mx-2 p-2 border border-bg1">
                                            <div v-for="block in column.blocks" :key="block.id" class="space-y-3">
                                                <div v-if="block.type == 'customer'" class="text-xs">
                                                    <label class="block">Заказчик</label>
                                                    <div>{{ block.contragent.name }}</div>
                                                </div>
                                                <div v-if="block.type == 'employee'" class="text-xs">
                                                    <label class="block">Сотрудник:</label>
                                                    <div>{{ block.employee_id }}</div>
                                                </div>
                                                <div v-if="block.type == 'commentary'" class="text-xs">
                                                    <label class="block">Комментарий:</label>
                                                    <div>{{ block.commentary }}</div>
                                                </div>
                                                <div v-if="block.type == 'equipment'" class="text-xs">
                                                    <label class="block">Оборудование:</label>
                                                    <div>{{ block.equipment }}</div>
                                                </div>
                                                <div v-if="block.type == 'mediafiles'">
                                                    <label class="block mb-2 text-xs">Медиа:</label>
                                                    <Swiper :slides-per-view="1"
                                                        class="!z-[unset] [&>.swiper-wrapper]:z-[unset]"
                                                        @swiper="swiper => swiper_refs['1'] = swiper">
                                                        <SwiperSlide v-for="(n, i) in block.media_url"
                                                            class="!z-[unset] [&>.swiper-wrapper]:z-[unset]"
                                                            style="height:240px" :key="i">
                                                            <img :src="'/storage/' + n"
                                                                class="block w-full h-full object-center object-cover" />
                                                        </SwiperSlide>
                                                    </Swiper>
                                                    <div class="grid grid-cols-5 gap-2.5 mt-2 p-2 bg-bg1">
                                                        <button v-for="(n, i) in block.media_url" :key="i" type="button"
                                                            @click="swiper_refs['1'].slideTo(i)">
                                                            <img :src="'storage/' + n" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </AccordionContent>
                                </AccordionItem>
                            </AccordionRoot>
                        </div>
                    </div>
                </div>
                <div v-if="getMenuActiveItem == 'history_adv'" v-for="(taskArray, key) in advColumnsArchived.data"
                    :key="key" class="p-2 bg-[#DDE1E6]">
                    <div class="flex items-center justify-between">
                        <div class="grow">Хронология</div>
                    </div>
                    <div class="mt-2 space-y-2">
                        <div class="p-2 border rounded mb-2 bg-white">
                            <div class="mb-2 text-right text-xs">{{ formatDate(taskArray[0].created_at) }}</div>
                            <AccordionRoot v-for="column in taskArray" type="multiple" :collapsible="true">
                                <AccordionItem value="some-id">
                                    <AccordionHeader class="flex items-center justify-between px-2.5 bg-bg1">
                                        <AccordionTrigger class="shrink-0 group mr-3">
                                            <svg class="group-data-[state=open]:hidden" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                    fill="#242533" />
                                            </svg>
                                            <svg class="group-data-[state=closed]:hidden" width="24" height="24"
                                                viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                    fill="#242533" />
                                            </svg>
                                        </AccordionTrigger>
                                        <span class="block w-1/3 text-center text-sm">{{
                                            formatDate(column.created_at)
                                        }}</span>
                                        <span class="block w-1/3 text-center text-sm">{{
                                            formatDate(column.archive_date)
                                        }}</span>
                                        <DropdownMenuRoot>
                                            <!-- DropdownMenuTrigger and content as before -->
                                        </DropdownMenuRoot>
                                    </AccordionHeader>

                                    <AccordionContent
                                        class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden">
                                        <div class="mt-3 mx-2 p-2 border border-bg1">
                                            <div v-for="block in column.blocks" :key="block.id" class="space-y-3">
                                                <div v-if="block.type == 'customer'" class="text-xs">
                                                    <label class="block">Заказчик</label>
                                                    <div>{{ block.contragent.name }}</div>
                                                </div>
                                                <div v-if="block.type == 'employee'" class="text-xs">
                                                    <label class="block">Сотрудник:</label>
                                                    <div>{{ block.employee_id }}</div>
                                                </div>
                                                <div v-if="block.type == 'commentary'" class="text-xs">
                                                    <label class="block">Комментарий:</label>
                                                    <div>{{ block.commentary }}</div>
                                                </div>
                                                <div v-if="block.type == 'equipment'" class="text-xs">
                                                    <label class="block">Оборудование:</label>
                                                    <div>{{ block.equipment }}</div>
                                                </div>
                                                <div v-if="block.type == 'mediafiles'">
                                                    <label class="block mb-2 text-xs">Медиа:</label>
                                                    <Swiper :slides-per-view="1"
                                                        class="!z-[unset] [&>.swiper-wrapper]:z-[unset]"
                                                        @swiper="swiper => swiper_refs['1'] = swiper">
                                                        <SwiperSlide v-for="(n, i) in block.media_url" class=""
                                                            style="height:240px" :key="i">
                                                            <img :src="'/storage/' + n"
                                                                class="block w-full h-full object-center object-cover" />
                                                        </SwiperSlide>
                                                    </Swiper>
                                                    <div class="grid grid-cols-5 gap-2.5 mt-2 p-2 bg-bg1">
                                                        <button v-for="(n, i) in block.media_url" :key="i" type="button"
                                                            @click="swiper_refs['1'].slideTo(i)">
                                                            <img :src="'storage/' + n" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </AccordionContent>
                                </AccordionItem>
                            </AccordionRoot>
                        </div>
                    </div>
                </div>


            </div>
            <IncidentPagination v-if="getMenuActiveItem === 'tasks'" v-model="tasksColumns.current_page"
                :total-pages="tasksColumns.last_page" class="mt-5" />
            <IncidentPagination v-if="getMenuActiveItem === 'adv'" v-model="advColumns.current_page"
                :total-pages="advColumns.last_page" class="mt-5" />
            <IncidentPagination v-if="getMenuActiveItem === 'history_tasks'" v-model="tasksColumnsArchived.current_page"
                :total-pages="tasksColumnsArchived.last_page" class="mt-5" />
            <IncidentPagination v-if="getMenuActiveItem === 'history_adv'" v-model="advColumnsArchived.current_page"
                :total-pages="advColumnsArchived.last_page" class="mt-5" />


        </div>
    </AuthenticatedLayout>

    <!-- <div class="flex">
        <div class="flex-1 p-4">
            <nav class="bg-my-gray ">
                <div class="max-w-screen-xl px-4 py-3">
                    <ul class="flex border-b-2 items-center flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                        <li @click="updateActiveMenuItem('tasks')" class="text-lg">Задачи</li>
                        <li @click="updateActiveMenuItem('adv')" class="text-lg"> Рекламация</li>
                        <li @click="updateActiveMenuItem('history')" class="text-lg"> История</li>

                        <li>
                            <Link v-if="user.isAdmin" :href="route('equip.tests')"
                                  class="text-lg text-side-gray-text">Admin
                            </Link>
                        </li>
                        <div>
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
                                                <Link method="POST" @click="createColumn"
                                                      :href="route('constructor.columnCreate', { type: getMenuActiveItem })"
                                                      class="flex items-center justify-around"
                                                      :class="['block px-4 py-2 text-sm']">Добавить столбец
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
                        </div>
                    </ul>
                </div>
            </nav>
            &lt;!&ndash; Cards Layout &ndash;&gt;
            <div class="grid grid-cols-3 mt-3 gap-4">
                <div v-if="getMenuActiveItem == 'tasks'" v-for="column in tasksColumns.data"
                     class="bg-white border-8 border-my-gray-500  rounded p-4">
                    <div class="flex items-center justify-between">
                        <h3>Хронология</h3>
                        <div @click="toggleDropdown">

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
                                        <div class="py-1 px-2">
                                            &lt;!&ndash; Heading &ndash;&gt;
                                            <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                Добавить блок</h3>
                                            &lt;!&ndash; List of Links &ndash;&gt;
                                            <ul class="px-2 py-1 space-y-1">
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'customer')"
                                                          :href="route('constructor.createBlock', column.id, 'customer')">
                                                        Заказчик
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'employee')"
                                                          :href="route('constructor.createBlock', column.id, 'employee')">
                                                        Сотрудник
                                                    </Link>
                                                </li>
                                                §
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'equipment')"
                                                          :href="route('constructor.createBlock', column.id, 'equipment')">
                                                        Оборудование
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'commentary')"
                                                          :href="route('constructor.createBlock', column.id, 'commentary')">
                                                        Комментарий
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'mediafiles')"
                                                          :href="route('constructor.createBlock', column.id, 'mediafiles')">
                                                        Медиа
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'files')"
                                                          :href="route('constructor.createBlock', column.id, 'files')">
                                                        Файлы
                                                    </Link>
                                                </li>
                                            </ul>
                                            <div class="border-b-2 "></div>
                                            <ul class="px-2 py-1 space-y-1">
                                                <li>
                                                    <Link method="PUT"
                                                          :href="route('constructor.archiveColumn', column.id)">
                                                        Архивировать колонку
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="DELETE"
                                                          :href="route('constructor.deleteColumn', column.id)">
                                                        Удалить колонку
                                                    </Link>
                                                </li>
                                            </ul>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </div>


                    </div>
                    <div class="mt-4">
                        <div v-for="block in column.blocks" :key="block.id" class="p-2 border rounded mb-2">
                            <div v-if="block.type == 'employee'">
                                <p>Сотрудник</p>
                                <select v-if="!block.employee_id" v-model="form.employee_id">
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                        {{ employee.lastname }}
                                        {{ employee.name }}
                                    </option>
                                </select>
                                <p v-else>{{ findEmployeeById(block.employee_id).name }}</p>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { employee_id: form.employee_id });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'customer'">
                                <p>Заказчик</p>

                                <select v-if="block.contragent == null" v-model="form.chosenAgent" name="" id="">
                                    <option v-for="agent in contragents" :key="agent.id" :value="agent.id">{{
                                            agent.name
                                        }}
                                    </option>
                                </select>
                                <p v-else>{{ block.contragent.name }}</p>
                                <div v-if="block.contragent && block.equipment"> {{ block.contragent.name }} {{
                                        block.equipment.category.name
                                    }} {{ block.equipment.size.name }} {{ block.equipment.series }}
                                </div>
                                <Link>

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
                                                    enter-from-class="transform opacity-0  scale-95"
                                                    enter-to-class="transform opacity-100 scale-100"
                                                    leave-active-class="transition ease-in duration-75"
                                                    leave-from-class="transform opacity-100 scale-100"
                                                    leave-to-class="transform opacity-0 scale-95">
                                            <MenuItems
                                                class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { contragent_id: form.chosenAgent });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'commentary'">
                                <div>
                                    <textarea name="commentary" v-model="block.commentary" id=""></textarea>
                                </div>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { text: block.commentary });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'mediafiles'">
                                <div>
                                    <input type="file" name="media_file[]" multiple @change="handleMediaFileUpload"/>

                                    <img v-for="item in block.media_url" :src="item" alt="">

                                </div>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { media_file: form.media_file });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'files'">
                                <div>
                                    <input type="file" name="files[]" multiple @change="handleFileUpload"/>

                                    <a v-for="item in block.file_url" :href="item" download alt="">Скачать</a>

                                </div>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link @click="saveBlock(block.id, { files: form.files });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'equipment'">
                                <div>
                                    <div v-if="block.equipment_id">
                                        {{ block.equipment.category.name }} {{ block.equipment.size.name }}
                                        {{ block.equipment.series }}
                                    </div>
                                    <div v-else>
                                        <button v-if="!selectedEquipmentService || block.equipment_id "
                                                @click="showModal(true)"
                                                class=" text-side-gray-text px-4 py-2 rounded">
                                            Нажмите чтобы выбрать оборудование
                                        </button>
                                        <div v-else class="p-4 bg-my-gray border whitespace-nowrap flex">
                                            <p> {{ selectedEquipmentService.category.name }}
                                                {{ selectedEquipmentService.size.name }} {{
                                                    selectedEquipmentService.series
                                                }}</p>
                                        </div>
                                    </div>


                                    <div v-if="block.subequipment.length > 0">
                                        <p v-for="item in block.subequipment">{{ item.category.name }} {{
                                                item.size.name
                                            }} {{ item.series }}</p>
                                    </div>
                                    <div v-else>
                                        <button @click="showModal(true)"
                                                class=" text-side-gray-text px-4 py-2 rounded">
                                            Нажмите чтобы выбрать оборудование
                                        </button>
                                        <div v-for="item in subEquipment"
                                             class="p-4 bg-my-gray border whitespace-nowrap flex">
                                            <p> {{ item.category.name }} {{ item.size.name }} {{ item.series }} </p>
                                        </div>

                                    </div>


                                </div>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { equipment_id: form.equipmentId, subEquipmentArray: form.subEquipmentIds });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                        </div>
                    </div>

                </div>
                <div v-if="getMenuActiveItem ==getMenuActiveItem == 'adv'" v-for="column in advColumns.data"
                     class="bg-white border-8 border-my-gray-500  rounded p-4">
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
                                        <div class="py-1 px-2">
                                            &lt;!&ndash; Heading &ndash;&gt;
                                            <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                Добавить блок</h3>
                                            &lt;!&ndash; List of Links &ndash;&gt;
                                            <ul class="px-2 py-1 space-y-1">
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'customer')"
                                                          :href="route('constructor.createBlock', column.id, 'customer')">
                                                        Заказчик
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'equipment')"
                                                          :href="route('constructor.createBlock', column.id, 'equipment')">
                                                        Оборудование
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'commentary')"
                                                          :href="route('constructor.createBlock', column.id, 'commentary')">
                                                        Комментарий
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'mediafiles')"
                                                          :href="route('constructor.createBlock', column.id, 'mediafiles')">
                                                        Медиа
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="POST" @click="createBlock(column.id, 'files')"
                                                          :href="route('constructor.createBlock', column.id, 'files')">
                                                        Файлы
                                                    </Link>
                                                </li>
                                            </ul>
                                            <div class="border-b-2 "></div>
                                            <ul class="px-2 py-1 space-y-1">
                                                <li>
                                                    <Link method="PUT"
                                                          :href="route('constructor.archiveColumn', column.id)">
                                                        Архивировать колонку
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link method="DELETE"
                                                          :href="route('constructor.deleteColumn', column.id)">
                                                        Удалить колонку
                                                    </Link>
                                                </li>
                                            </ul>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </Link>


                    </div>
                    <div class="mt-4">
                        <div v-for="block in column.blocks" :key="block.id" class="p-2 border rounded mb-2">
                            <div v-if="block.type == 'employee'">
                                <p>Сотрудник</p>
                                <select v-if="!block.employee_id" v-model="form.employee_id">
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                        {{ employee.lastname }}
                                        {{ employee.name }}
                                    </option>
                                </select>
                                <p v-else>{{ findEmployeeById(block.employee_id).name }}</p>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { employee_id: form.employee_id });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'customer'">
                                <p>Заказчик</p>

                                <select v-if="block.contragent == null" v-model="form.chosenAgent" name="" id="">
                                    <option v-for="agent in contragents" :key="agent.id" :value="agent.id">{{
                                            agent.name
                                        }}
                                    </option>
                                </select>
                                <p v-else>{{ block.contragent.name }}</p>
                                <div v-if="block.contragent && block.equipment"> {{ block.contragent.name }} {{
                                        block.equipment.category.name
                                    }} {{ block.equipment.size.name }} {{ block.equipment.series }}
                                </div>
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
                                                    enter-from-class="transform opacity-0  scale-95"
                                                    enter-to-class="transform opacity-100 scale-100"
                                                    leave-active-class="transition ease-in duration-75"
                                                    leave-from-class="transform opacity-100 scale-100"
                                                    leave-to-class="transform opacity-0 scale-95">
                                            <MenuItems
                                                class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { contragent_id: form.chosenAgent });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'commentary'">
                                <div>
                                    <textarea name="commentary" v-model="block.commentary" id=""></textarea>
                                </div>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { text: block.commentary });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'mediafiles'">
                                <div>
                                    <input type="file" name="media_file[]" multiple @change="handleMediaFileUpload"/>

                                    <img v-for="item in block.media_url" :src="item" alt="">

                                </div>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { media_file: form.media_file });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'files'">
                                <div>
                                    <input type="file" name="files[]" multiple @change="handleFileUpload"/>

                                    <a v-for="item in block.file_url" :href="item" download alt="">Скачать</a>

                                </div>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link @click="saveBlock(block.id, { files: form.files });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                            <div v-if="block.type == 'equipment'">
                                <div>
                                    <div v-if="block.equipment_id">
                                        {{ block.equipment.category.name }} {{ block.equipment.size.name }}
                                        {{ block.equipment.series }}
                                    </div>
                                    <div v-else>
                                        <button v-if="!selectedEquipmentService || block.equipment_id "
                                                @click="showModal(true)"
                                                class=" text-side-gray-text px-4 py-2 rounded">
                                            Нажмите чтобы выбрать оборудование
                                        </button>
                                        <div v-else class="p-4 bg-my-gray border whitespace-nowrap flex">
                                            <p> {{ selectedEquipmentService.category.name }}
                                                {{ selectedEquipmentService.size.name }} {{
                                                    selectedEquipmentService.series
                                                }}</p>
                                        </div>
                                    </div>


                                    <div v-if="block.subequipment.length > 0">
                                        <p v-for="item in block.subequipment">{{ item.category.name }} {{
                                                item.size.name
                                            }} {{ item.series }}</p>
                                    </div>
                                    <div v-else>
                                        <button @click="showModal(true)"
                                                class=" text-side-gray-text px-4 py-2 rounded">
                                            Нажмите чтобы выбрать оборудование
                                        </button>
                                        <div v-for="item in subEquipment"
                                             class="p-4 bg-my-gray border whitespace-nowrap flex">
                                            <p> {{ item.category.name }} {{ item.size.name }} {{ item.series }} </p>
                                        </div>

                                    </div>


                                </div>
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
                                                <div class="py-1 px-2">
                                                    &lt;!&ndash; Heading &ndash;&gt;
                                                    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                        Меню блока</h3>

                                                    <div class="border-b-2 "></div>
                                                    <ul class="px-2 py-1 space-y-1">
                                                        <li>
                                                            <Link
                                                                @click="saveBlock(block.id, { equipment_id: form.equipmentId, subEquipmentArray: form.subEquipmentIds });">
                                                                Сохранить блок
                                                            </Link>
                                                        </li>
                                                        <li>
                                                            <Link method="DELETE"
                                                                  :href="route('constructor.deleteBlock', block.id)">
                                                                Удалить блок
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                </Link>

                            </div>
                        </div>
                    </div>

                </div>
                <div
                    v-if="getMenuActiveItem ==getMenuActiveItem == 'history' || getMenuActiveItem == 'history_tasks' || getMenuActiveItem == 'history_adv'">
                    <ul>
                        <li @click="updateActiveMenuItem('history_tasks')">Задачи</li>
                        <li @click="updateActiveMenuItem('history_adv')">Рекламация</li>
                    </ul>
                    <div v-if="getMenuActiveItem == 'history_tasks'" v-for="column in tasksColumnsArchived.data"
                         class="bg-white border-8 border-my-gray-500  rounded p-4">
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
                                            <div class="py-1 px-2">
                                                &lt;!&ndash; Heading &ndash;&gt;
                                                <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                    Добавить блок</h3>
                                                &lt;!&ndash; List of Links &ndash;&gt;
                                                <ul class="px-2 py-1 space-y-1">
                                                    <li>
                                                        <Link method="POST" @click="createBlock(column.id, 'customer')"
                                                              :href="route('constructor.createBlock', column.id, 'customer')">
                                                            Заказчик
                                                        </Link>
                                                    </li>
                                                    <li>
                                                        <Link method="POST" @click="createBlock(column.id, 'equipment')"
                                                              :href="route('constructor.createBlock', column.id, 'equipment')">
                                                            Оборудование
                                                        </Link>
                                                    </li>
                                                    <li>
                                                        <Link method="POST"
                                                              @click="createBlock(column.id, 'commentary')"
                                                              :href="route('constructor.createBlock', column.id, 'commentary')">
                                                            Комментарий
                                                        </Link>
                                                    </li>
                                                    <li>
                                                        <Link method="POST"
                                                              @click="createBlock(column.id, 'mediafiles')"
                                                              :href="route('constructor.createBlock', column.id, 'mediafiles')">
                                                            Медиа
                                                        </Link>
                                                    </li>
                                                    <li>
                                                        <Link method="POST" @click="createBlock(column.id, 'files')"
                                                              :href="route('constructor.createBlock', column.id, 'files')">
                                                            Файлы
                                                        </Link>
                                                    </li>
                                                </ul>
                                                <div class="border-b-2 "></div>
                                                <ul class="px-2 py-1 space-y-1">
                                                    <li>
                                                        <Link method="DELETE"
                                                              :href="route('constructor.deleteColumn', column.id)">
                                                            Удалить колонку
                                                        </Link>
                                                    </li>
                                                </ul>
                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </Link>


                        </div>
                        <div class="mt-4">
                            <div v-for="block in column.blocks" :key="block.id" class="p-2 border rounded mb-2">
                                <div v-if="block.type == 'employee'">
                                    <p>Сотрудник</p>
                                    <select v-if="!block.employee_id" v-model="form.employee_id">
                                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                            {{ employee.lastname }}
                                            {{ employee.name }}
                                        </option>
                                    </select>
                                    <p v-else>{{ findEmployeeById(block.employee_id).name }}</p>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { employee_id: form.employee_id });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'customer'">
                                    <p>Заказчик</p>

                                    <select v-if="block.contragent == null" v-model="form.chosenAgent" name="" id="">
                                        <option v-for="agent in contragents" :key="agent.id" :value="agent.id">
                                            {{ agent.name }}
                                        </option>
                                    </select>
                                    <p v-else>{{ block.contragent.name }}</p>
                                    <div v-if="block.contragent && block.equipment"> {{ block.contragent.name }} {{
                                            block.equipment.category.name
                                        }} {{ block.equipment.size.name }} {{ block.equipment.series }}
                                    </div>
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
                                                        enter-from-class="transform opacity-0  scale-95"
                                                        enter-to-class="transform opacity-100 scale-100"
                                                        leave-active-class="transition ease-in duration-75"
                                                        leave-from-class="transform opacity-100 scale-100"
                                                        leave-to-class="transform opacity-0 scale-95">
                                                <MenuItems
                                                    class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { contragent_id: form.chosenAgent });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'commentary'">
                                    <div>
                                        <textarea name="commentary" v-model="block.commentary" id=""></textarea>
                                    </div>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { text: block.commentary });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'mediafiles'">
                                    <div>
                                        <input type="file" name="media_file[]" multiple
                                               @change="handleMediaFileUpload"/>

                                        <img v-for="item in block.media_url" :src="item" alt="">

                                    </div>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { media_file: form.media_file });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'files'">
                                    <div>
                                        <input type="file" name="files[]" multiple @change="handleFileUpload"/>

                                        <a v-for="item in block.file_url" :href="item" download alt="">Скачать</a>

                                    </div>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { files: form.files });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'equipment'">
                                    <div>
                                        <div v-if="block.equipment_id">
                                            {{ block.equipment.category.name }} {{ block.equipment.size.name }}
                                            {{ block.equipment.series }}
                                        </div>
                                        <div v-else>
                                            <button v-if="!selectedEquipmentService || block.equipment_id "
                                                    @click="showModal(true)"
                                                    class=" text-side-gray-text px-4 py-2 rounded">
                                                Нажмите чтобы выбрать оборудование
                                            </button>
                                            <div v-else class="p-4 bg-my-gray border whitespace-nowrap flex">
                                                <p> {{ selectedEquipmentService.category.name }}
                                                    {{ selectedEquipmentService.size.name }} {{
                                                        selectedEquipmentService.series
                                                    }}</p>
                                            </div>
                                        </div>


                                        <div v-if="block.subequipment">

                                            <div v-if="block.subequipment.length > 0">
                                                <p v-for="item in block.subequipment">{{ item.category.name }}
                                                    {{ item.size.name }} {{ item.series }}</p>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <button @click="showModal(true)"
                                                    class=" text-side-gray-text px-4 py-2 rounded">
                                                Нажмите чтобы выбрать оборудование
                                            </button>
                                            <div v-for="item in subEquipment"
                                                 class="p-4 bg-my-gray border whitespace-nowrap flex">
                                                <p> {{ item.category.name }} {{ item.size.name }} {{ item.series }} </p>
                                            </div>

                                        </div>


                                    </div>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { equipment_id: form.equipmentId, subEquipmentArray: form.subEquipmentIds });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div v-for="column in advColumnsArchived.data"
                         class="bg-white border-8 border-my-gray-500  rounded p-4">
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
                                            <div class="py-1 px-2">
                                                &lt;!&ndash; Heading &ndash;&gt;
                                                <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                    Добавить блок</h3>
                                                &lt;!&ndash; List of Links &ndash;&gt;
                                                <ul class="px-2 py-1 space-y-1">
                                                    <li>
                                                        <Link method="POST" @click="createBlock(column.id, 'customer')"
                                                              :href="route('constructor.createBlock', column.id, 'customer')">
                                                            Заказчик
                                                        </Link>
                                                    </li>
                                                    <li>
                                                        <Link method="POST" @click="createBlock(column.id, 'equipment')"
                                                              :href="route('constructor.createBlock', column.id, 'equipment')">
                                                            Оборудование
                                                        </Link>
                                                    </li>
                                                    <li>
                                                        <Link method="POST"
                                                              @click="createBlock(column.id, 'commentary')"
                                                              :href="route('constructor.createBlock', column.id, 'commentary')">
                                                            Комментарий
                                                        </Link>
                                                    </li>
                                                    <li>
                                                        <Link method="POST"
                                                              @click="createBlock(column.id, 'mediafiles')"
                                                              :href="route('constructor.createBlock', column.id, 'mediafiles')">
                                                            Медиа
                                                        </Link>
                                                    </li>
                                                    <li>
                                                        <Link method="POST" @click="createBlock(column.id, 'files')"
                                                              :href="route('constructor.createBlock', column.id, 'files')">
                                                            Файлы
                                                        </Link>
                                                    </li>
                                                </ul>
                                                <div class="border-b-2 "></div>
                                                <ul class="px-2 py-1 space-y-1">
                                                    <li>
                                                        <Link method="DELETE"
                                                              :href="route('constructor.deleteColumn', column.id)">
                                                            Удалить колонку
                                                        </Link>
                                                    </li>
                                                </ul>
                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </Link>


                        </div>
                        <div class="mt-4">
                            <div v-for="block in column.blocks" :key="block.id" class="p-2 border rounded mb-2">
                                <div v-if="block.type == 'employee'">
                                    <p>Сотрудник</p>
                                    <select v-if="!block.employee_id" v-model="form.employee_id">
                                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                            {{ employee.lastname }}
                                            {{ employee.name }}
                                        </option>
                                    </select>
                                    <p v-else>{{ findEmployeeById(block.employee_id).name }}</p>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { employee_id: form.employee_id });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'customer'">
                                    <p>Заказчик</p>

                                    <select v-if="block.contragent == null" v-model="form.chosenAgent" name="" id="">
                                        <option v-for="agent in contragents" :key="agent.id" :value="agent.id">
                                            {{ agent.name }}
                                        </option>
                                    </select>
                                    <p v-else>{{ block.contragent.name }}</p>
                                    <div v-if="block.contragent && block.equipment"> {{ block.contragent.name }} {{
                                            block.equipment.category.name
                                        }} {{ block.equipment.size.name }} {{ block.equipment.series }}
                                    </div>
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
                                                        enter-from-class="transform opacity-0  scale-95"
                                                        enter-to-class="transform opacity-100 scale-100"
                                                        leave-active-class="transition ease-in duration-75"
                                                        leave-from-class="transform opacity-100 scale-100"
                                                        leave-to-class="transform opacity-0 scale-95">
                                                <MenuItems
                                                    class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { contragent_id: form.chosenAgent });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'commentary'">
                                    <div>
                                        <textarea name="commentary" v-model="block.commentary" id=""></textarea>
                                    </div>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { text: block.commentary });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'mediafiles'">
                                    <div>
                                        <input type="file" name="media_file[]" multiple
                                               @change="handleMediaFileUpload"/>

                                        <img v-for="item in block.media_url" :src="item" alt="">

                                    </div>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { media_file: form.media_file });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'files'">
                                    <div>
                                        <input type="file" name="files[]" multiple @change="handleFileUpload"/>

                                        <a v-for="item in block.file_url" :href="item" download alt="">Скачать</a>

                                    </div>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { files: form.files });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                                <div v-if="block.type == 'equipment'">
                                    <div>
                                        <div v-if="block.equipment_id">
                                            {{ block.equipment.category.name }} {{ block.equipment.size.name }}
                                            {{ block.equipment.series }}
                                        </div>
                                        <div v-else>
                                            <button v-if="!selectedEquipmentService || block.equipment_id "
                                                    @click="showModal(true)"
                                                    class=" text-side-gray-text px-4 py-2 rounded">
                                                Нажмите чтобы выбрать оборудование
                                            </button>
                                            <div v-else class="p-4 bg-my-gray border whitespace-nowrap flex">
                                                <p> {{ selectedEquipmentService.category.name }}
                                                    {{ selectedEquipmentService.size.name }} {{
                                                        selectedEquipmentService.series
                                                    }}</p>
                                            </div>
                                        </div>


                                        <div v-if="block.subequipment.length > 0">
                                            <p v-for="item in block.subequipment">{{ item.category.name }}
                                                {{ item.size.name }} {{ item.series }}</p>
                                        </div>
                                        <div v-else>
                                            <button @click="showModal(true)"
                                                    class=" text-side-gray-text px-4 py-2 rounded">
                                                Нажмите чтобы выбрать оборудование
                                            </button>
                                            <div v-for="item in subEquipment"
                                                 class="p-4 bg-my-gray border whitespace-nowrap flex">
                                                <p> {{ item.category.name }} {{ item.size.name }} {{ item.series }} </p>
                                            </div>

                                        </div>


                                    </div>
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
                                                    <div class="py-1 px-2">
                                                        &lt;!&ndash; Heading &ndash;&gt;
                                                        <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">
                                                            Меню блока</h3>

                                                        <div class="border-b-2 "></div>
                                                        <ul class="px-2 py-1 space-y-1">
                                                            <li>
                                                                <Link
                                                                    @click="saveBlock(block.id, { equipment_id: form.equipmentId, subEquipmentArray: form.subEquipmentIds });">
                                                                    Сохранить блок
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <Link method="DELETE"
                                                                      :href="route('constructor.deleteBlock', block.id)">
                                                                    Удалить блок
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </Link>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
        <ServiceModal style="z-index: 1;" class="mt-14 absolute  bg-my-gray " v-if="modalShown">
        </ServiceModal>
    </div> -->
</template>