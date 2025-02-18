<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
} from 'radix-vue'
import { computed, onMounted, ref, toRaw, watch, watchEffect } from 'vue';
import store from '../../../store/';
import ContragentStatus from '@/Components/ContragentStatus.vue';
import Pagination from '@/Components/Pagination.vue';
import ContragentsToolbar from "@/Components/Contragents/ContragentsToolbar.vue";
import UiUserBadge from "@/Components/Ui/UiUserAvatar.vue";
import UiCheckbox from "@/Components/Ui/UiCheckbox.vue";
import UiHyperlink from '@/Components/Ui/UiHyperlink.vue';


const query = ref((new URLSearchParams(window.location.search)).get('query') || '');

const props = defineProps({
    contragents: Object,
    contragents_count: Number,
    contragents_customer_count: Number,
    countries: Object,
    legalStatuses: Object,
    contragents_supplier_count: Number,
    contragents_suppliers: Object,
    contragents_customers: Object,
});

const pagination_el = ref(null);

const selectAll = ref(false);
const selectedItems = ref([]);

const currentPage = ref(props.contragents.current_page || 1);
const lastPage = ref(props.contragents.last_page || 1);

const sortOrder = computed(() => store.getters['contragent/getSortOrder']);
const sortBy = computed(() => store.getters['contragent/getSortBy']);

const sortedContragets = computed(() => {
    let target_contragents = null;

    if (getActiveMenuLink.value === 'all') {
        target_contragents = props.contragents;
    } else if (getActiveMenuLink.value === 'customers') {
        target_contragents = props.contragents_customers;
    } else if (getActiveMenuLink.value === 'suppliers') {
        target_contragents = props.contragents_suppliers;
    }

    if (!target_contragents || !target_contragents.data) {
        return [];
    }

    const dataCopy = target_contragents.data.slice();

    return dataCopy.sort((a, b) => {
        let result = 0;

        if (sortBy.value === 'name') {
            result = a.name.localeCompare(b.name);
        } else if (sortBy.value === 'status') {
            result = a.status - b.status;
        }

        if (sortOrder.value === 'desc') {
            result *= -1; 
        }

        return result;
    });
});

watchEffect(() => {
    if (selectedItems.value.length === props.contragents.data.length) {
        selectAll.value = true;
    } else {
        selectAll.value = false;
    }
});

watch(() => store.getters['contragent/getActiveMenuLink'], () => pagination_el.value.resetPage());
watch(() => store.getters['pagination/perPage'], () => pagination_el.value.resetPage());

const translateStatus = (status) => {
    switch (status) {
        case 0:
            return '';

        case 1:
            return 'Активен';
    }

}
const updateSortBy = (value) => store.dispatch("contragent/updateSortBy", value)
const updateSortOrder = (value) => store.dispatch("contragent/updateSortOrder", value)

const toggleSortBy = (column) => {
    if (sortBy.value === column) {
        updateSortOrder(sortOrder.value === 'asc' ? 'desc' : 'asc');
    } else {
        updateSortBy(column);
        updateSortOrder('asc');
    }
};

const toggleSelectAll = () => {

    if (selectAll.value) {
        selectedItems.value = props.contragents.data.map((item) => item.id);
    } else {
        selectedItems.value = [];
    }
};

const getActiveMenuLink = computed(() => store.getters['contragent/getActiveMenuLink']);

onMounted(() => {
    const { current_page, total } = props.contragents;

    store.dispatch('pagination/updatePagination', {
        currentPage: current_page,
        totalPages: total,
    });

    currentPage.value = props.contragents.current_page;
    lastPage.value = props.contragents.last_page;
});

</script>

<template>
    <AuthenticatedLayout bg="gray">
        <ContragentsToolbar v-model:query="query" :contragents-count="props.contragents_count"
            :contragents-customer-count="props.contragents_customer_count"
            :contragents-supplier-count="props.contragents_supplier_count" />

        <div class="px-4">
            <div class="overflow-x-auto border-2 border-gray lg:overflow-x-visible pb-30">
                <div class="w-[1136px] mt-2 break-all lg:w-auto">
                    <div class="border-b-2">
                        <div class="flex items-center min-h-12 py-2 font-medium text-sm">
                            <div class="flex items-center w-10 px-3">
                                <UiCheckbox v-model="selectAll" @change="toggleSelectAll" />
                            </div>
                            <div class="w-[22.5%] px-3">
                                <button type="button" class="flex items-center" @click="toggleSortBy('name')">
                                    <p>Наименование</p>
                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        :class="sortBy === 'name' && sortOrder === 'asc' ? 'rotate-180' : ''">
                                        <path
                                            d="M8.16663 12L12.1666 8M8.16663 3.66666V12V3.66666ZM8.16663 12L4.16663 8L8.16663 12Z"
                                            stroke="#21272A" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                            <div class="w-[14.08%] px-3">
                                <p class="text-left font-semibold">Контактное лицо</p>
                            </div>
                            <div class="w-[14.08%] px-3">
                                <p class="text-left font-semibold">Телефон</p>
                            </div>
                            <div class="w-[14.08%] px-3">
                                <p class="text-left font-semibold">Эл. Почта</p>
                            </div>
                            <div class="w-[14.08%] px-3">
                                <p class="text-left font-semibold">Примечание</p>
                            </div>
                            <div class="w-[14.08%] px-3">
                                <button type="button" class="flex items-center" @click="toggleSortBy('status')">
                                    <p class="text-left font-semibold">Статус</p>
                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        :class="sortBy === 'status' && sortOrder === 'asc' ? 'rotate-180' : ''">
                                        <path
                                            d="M8.16663 12L12.1666 8M8.16663 3.66666V12V3.66666ZM8.16663 12L4.16663 8L8.16663 12Z"
                                            stroke="#21272A" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                            <div class="w-10 px-4"></div>
                        </div>
                    </div>
                    <div class="bg-white">
                        <div
                            v-for="(item, i) in sortedContragets"
                            :key="item.id"
                            :class="{ 'bg-my-gray': selectedItems.includes(item.id), 'border-b': i < sortedContragets.length }"
                            class="relative flex items-center text-sm"
                        >
                            <span v-if="selectedItems.includes(item.id)" class="absolute left-0 top-0 w-[3px] h-full py-2 bg-[#697077]"></span>

                            <div class="flex items-center justify-center w-10 py-2 px-3">
                                <UiCheckbox v-model="selectedItems" :inp-attrs="{ value: item.id }" />
                            </div>
                            <div class="self-stretch flex items-center justify-center w-[44px] py-2 border-x border-l-[#644DED] border-r-[#DDE1E6]">
                                <UiHyperlink :item-id="1" :hyperlink="'somoe.ru'" endpoint="/equipment" />
                            </div>
                            <div class="w-[calc(22.5%-44px)] flex py-2 px-3">
                                <UiUserBadge :image="item.avatar" size="40px" class="shrink-0 mr-2" />
                                <div>
                                    <span class="flex justify-start items-start font-medium">
                                        {{ item.name }}
                                    </span>
                                    <span class="flex text-gray-500 items-start text-xs">
                                        ИНН {{ item.inn }}
                                    </span>
                                </div>
                            </div>
                            <div class="w-[14.08%] py-2 px-3">
                                {{ item.contact_person ?? '-' }}
                            </div>
                            <div class="w-[14.08%] py-2 px-3">
                                {{ item.contact_person_phone ?? '-' }}
                            </div>
                            <div class="w-[14.08%] py-2 px-3">
                                {{ item.contact_person_email ?? '-' }}
                            </div>
                            <div class="w-[14.08%] py-2 px-3">{{ item.contact_person_notes }}</div>
                            <div class="w-[14.08%] py-2 px-3">
                                <ContragentStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />
                            </div>
                            <div class="flex w-20 py-2 px-3">
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
                                                                        <Link :href="'/'"
                                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
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
                                        <svg class="block" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M3.33337 10C2.80294 10 2.29423 9.78929 1.91916 9.41421C1.54409 9.03914 1.33337 8.53043 1.33337 8C1.33337 7.46957 1.54409 6.96086 1.91916 6.58579C2.29423 6.21071 2.80294 6 3.33337 6C3.86381 6 4.37251 6.21071 4.74759 6.58579C5.12266 6.96086 5.33337 7.46957 5.33337 8C5.33337 8.53043 5.12266 9.03914 4.74759 9.41421C4.37251 9.78929 3.86381 10 3.33337 10ZM12.6667 10C12.1363 10 11.6276 9.78929 11.2525 9.41421C10.8774 9.03914 10.6667 8.53043 10.6667 8C10.6667 7.46957 10.8774 6.96086 11.2525 6.58579C11.6276 6.21071 12.1363 6 12.6667 6C13.1971 6 13.7058 6.21071 14.0809 6.58579C14.456 6.96086 14.6667 7.46957 14.6667 8C14.6667 8.53043 14.456 9.03914 14.0809 9.41421C13.7058 9.78929 13.1971 10 12.6667 10ZM8.00004 10C7.46961 10 6.9609 9.78929 6.58583 9.41421C6.21075 9.03914 6.00004 8.53043 6.00004 8C6.00004 7.46957 6.21075 6.96086 6.58583 6.58579C6.9609 6.21071 7.46961 6 8.00004 6C8.53047 6 9.03918 6.21071 9.41426 6.58579C9.78933 6.96086 10 7.46957 10 8C10 8.53043 9.78933 9.03914 9.41426 9.41421C9.03918 9.78929 8.53047 10 8.00004 10Z"
                                                fill="#697077" />
                                        </svg>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuPortal>
                                        <transition name="fade">
                                            <DropdownMenuContent
                                                class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                :side-offset="5" align="end">
                                                <DropdownMenuItem>
                                                    <Link :href="route('contragents.show', item.id)"
                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                    Посмотреть
                                                    <svg class="block ml-2" width="12" height="12" viewBox="0 0 12 12"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12 6C12 9.31375 9.31384 12 6 12C2.68616 12 0 9.31375 0 6C0 2.68625 2.68616 0 6 0C9.31384 0 12 2.68625 12 6ZM4.70239 4.65747H6.82398V9.17688C6.82398 9.61435 6.46933 9.96899 6.03186 9.96899C5.59439 9.96899 5.23975 9.61435 5.23975 9.17688V6.06226H4.70239C4.31447 6.06226 4 5.74778 4 5.35986C4 4.97194 4.31447 4.65747 4.70239 4.65747ZM5.00977 2.93247C5.00977 2.80778 5.03192 2.69149 5.07587 2.58359C5.12345 2.47236 5.18773 2.37629 5.26909 2.29539C5.35044 2.21449 5.44524 2.15048 5.55383 2.10326C5.60323 2.0824 5.65408 2.06615 5.70674 2.0545C5.77357 2.03987 5.84294 2.03247 5.91485 2.03247C6.03688 2.03247 6.15202 2.05604 6.26061 2.10326C6.29003 2.11599 6.31836 2.12998 6.3456 2.14515C6.41933 2.18623 6.4858 2.23634 6.54536 2.29539C6.62671 2.37629 6.691 2.47236 6.73858 2.58359C6.76328 2.63984 6.7818 2.69835 6.79342 2.75912C6.80432 2.815 6.80977 2.87279 6.80977 2.93247C6.80977 2.99965 6.80287 3.06492 6.78906 3.12822C6.77744 3.18239 6.76037 3.23512 6.73858 3.2864C6.691 3.3943 6.62671 3.48865 6.54536 3.56955C6.50359 3.61099 6.45856 3.64801 6.40989 3.6806C6.3634 3.71157 6.31364 3.73866 6.26061 3.76168C6.15202 3.80891 6.03688 3.83247 5.91485 3.83247C5.85129 3.83247 5.78991 3.82678 5.73071 3.81532C5.66933 3.80358 5.61049 3.7857 5.55383 3.76168C5.44524 3.71446 5.35044 3.65045 5.26909 3.56955C5.18773 3.48865 5.12345 3.3943 5.07587 3.2864C5.03192 3.17517 5.00977 3.05716 5.00977 2.93247Z"
                                                            fill="#464F60" />
                                                    </svg>
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <Link :href="route('contragents.edit', item.id)"
                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
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
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <Link :href="route('contragents.destroy', item.id)"
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
                    </div>
                </div>
            </div>

            <pagination v-if="getActiveMenuLink === 'all'" ref="pagination_el"
                :current-page="props.contragents.current_page" :total-pages="props.contragents.last_page"
                :total-count="props.contragents.total" :next-page-url="props.contragents.next_page_url"
                :links="props.contragents.links" :prev-page-url="props.contragents.prev_page_url" :query="query"
                class="mt-5" />
            <pagination v-else-if="getActiveMenuLink === 'customers'" ref="pagination_el"
                :current-page="props.contragents_customers.current_page"
                :total-count="props.contragents_customers.total" :total-pages="props.contragents_customers.last_page"
                :next-page-url="props.contragents_customers.next_page_url" :links="props.contragents_customers.links"
                :prev-page-url="props.contragents_customers.prev_page_url" class="mt-5" />
            <pagination v-else-if="getActiveMenuLink === 'suppliers'" ref="pagination_el"
                :current-page="props.contragents_suppliers.current_page"
                :total-count="props.contragents_suppliers.total" :total-pages="props.contragents_suppliers.last_page"
                :next-page-url="props.contragents_suppliers.next_page_url" :links="props.contragents_suppliers.links"
                :prev-page-url="props.contragents_suppliers.prev_page_url" class="mt-5" />
        </div>
    </AuthenticatedLayout>
</template>
