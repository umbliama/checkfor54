<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
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



const props = defineProps({
    contragents: Object,
    contragents_count: Number,
    contragents_customer_count: Number,
    countries: Array,
    legalStatuses: Array,
    contragents_supplier_count: Number,
    contragents_suppliers: Object,
    contragents_customers: Object,
    legalStatuses: Object
});

const pagination_el = ref(null);

const selectAll     = ref(false);
const selectedItems = ref([]);

const currentPage = ref(props.contragents.current_page || 1);
const lastPage    = ref(props.contragents.last_page || 1);


const sortOrder = computed(() => store.getters['contragent/getSortOrder']);
const sortBy    = computed(() => store.getters['contragent/getSortBy']);

const sortedContragets = computed(() => {
    let target_contragents = null;

    if (getActiveMenuLink.value === 'all')       target_contragents = props.contragents;
    if (getActiveMenuLink.value === 'customers') target_contragents = props.contragents_customers;
    if (getActiveMenuLink.value === 'suppliers') target_contragents = props.contragents_suppliers;

    return target_contragents.data.slice().sort((a, b) => {
        let result = false;

        if (sortBy.value === 'name') {
            result = a.name.localeCompare(b.name);
        } else if (sortBy.value === 'status') {
            result = a.status - b.status; // Assuming 'status' is a number
        }

        if (sortOrder.value === 'desc') {
            result = !result;
        }

        return result;
    });

});

// Watch for changes to update the select all checkbox
watchEffect(() => {
    if (selectedItems.value.length === props.contragents.data.length) {
        selectAll.value = true;
    } else {
        selectAll.value = false;
    }
});

watch(() => store.getters['contragent/getActiveMenuLink'], () => pagination_el.value.resetPage());
watch(() => store.getters['pagination/perPage']          , () => pagination_el.value.resetPage());

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

// Toggle the select all functionality
const toggleSelectAll = () => {

    if (selectAll.value) {
        selectedItems.value = props.contragents.data.map((item) => item.id);  // Access directly without `.value`
    } else {
        selectedItems.value = [];
    }
};

const getActiveMenuLink = computed(() => store.getters['contragent/getActiveMenuLink']);

onMounted(() => {
    const { current_page, total } = props.contragents;

    // Dispatch the pagination state to Vuex store
    store.dispatch('pagination/updatePagination', {
        currentPage: current_page,
        totalPages: total,
    });

    currentPage.value = props.contragents.current_page;
    lastPage.value = props.contragents.last_page;
});

</script>

<template>
    <AuthenticatedLayout class="bg-my-gray">
        <ContragentsToolbar
            :contragents-count="props.contragents_count"
            :contragents-customer-count="props.contragents_customer_count"
            :contragents-supplier-count="props.contragents_supplier_count"
        />

        <div class="px-4">
            <div class="overflow-x-auto border-2 border-gray lg:overflow-x-visible pb-30">
                <div class="w-[1136px] mt-2 break-all lg:w-auto">
                    <div class="border-b-2">
                        <div class="flex items-center min-h-12 py-2 font-medium text-sm">
                            <div class="flex items-center w-10 px-3">
                                <input
                                    class="w-4 h-4 border-my-black"
                                    type="checkbox" v-model="selectAll" @change="toggleSelectAll"
                                />
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
                            class="relative flex items-center py-2 text-sm"
                        >
                            <span v-if="selectedItems.includes(item.id)" class="absolute left-0 top-0 w-[3px] h-full bg-[#697077]"></span>

                            <div class="flex items-center justify-center w-10 px-3">
                                <input
                                    class="w-4 h-4 text-side-gray-text bg-gray-100 accent-[#697077] border-my-black"
                                    type="checkbox" v-model="selectedItems" :value="item.id"
                                />
                            </div>
                            <div class="w-[22.5%] flex px-3">
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
                            <div class="w-[14.08%] px-3">
                                {{ item.contact_person ? item.contact_person : 'Не задано' }}
                            </div>
                            <div class="w-[14.08%] px-3">
                                {{ item.contact_person_phone ? item.contact_person_phone : 'Не задано' }}
                            </div>
                            <div class="w-[14.08%] px-3">
                                {{ item.email ? item.email : 'Не задано' }}
                            </div>
                            <div class="w-[14.08%] px-3">{{ item.notes }}</div>
                            <div class="w-[14.08%] px-3">
                                <ContragentStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />
                            </div>
                            <div class="w-10 px-3">
                                <DropdownMenuRoot>
                                    <DropdownMenuTrigger
                                        aria-label="Customise options"
                                    >
                                        <svg class="block" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.33337 10C2.80294 10 2.29423 9.78929 1.91916 9.41421C1.54409 9.03914 1.33337 8.53043 1.33337 8C1.33337 7.46957 1.54409 6.96086 1.91916 6.58579C2.29423 6.21071 2.80294 6 3.33337 6C3.86381 6 4.37251 6.21071 4.74759 6.58579C5.12266 6.96086 5.33337 7.46957 5.33337 8C5.33337 8.53043 5.12266 9.03914 4.74759 9.41421C4.37251 9.78929 3.86381 10 3.33337 10ZM12.6667 10C12.1363 10 11.6276 9.78929 11.2525 9.41421C10.8774 9.03914 10.6667 8.53043 10.6667 8C10.6667 7.46957 10.8774 6.96086 11.2525 6.58579C11.6276 6.21071 12.1363 6 12.6667 6C13.1971 6 13.7058 6.21071 14.0809 6.58579C14.456 6.96086 14.6667 7.46957 14.6667 8C14.6667 8.53043 14.456 9.03914 14.0809 9.41421C13.7058 9.78929 13.1971 10 12.6667 10ZM8.00004 10C7.46961 10 6.9609 9.78929 6.58583 9.41421C6.21075 9.03914 6.00004 8.53043 6.00004 8C6.00004 7.46957 6.21075 6.96086 6.58583 6.58579C6.9609 6.21071 7.46961 6 8.00004 6C8.53047 6 9.03918 6.21071 9.41426 6.58579C9.78933 6.96086 10 7.46957 10 8C10 8.53043 9.78933 9.03914 9.41426 9.41421C9.03918 9.78929 8.53047 10 8.00004 10Z" fill="#697077"/>
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
                                                    <Link
                                                        :href="route('contragents.show', item.id)"
                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Посмотреть
                                                        <svg
                                                            class="block ml-2"
                                                            width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M12 6C12 9.31375 9.31384 12 6 12C2.68616 12 0 9.31375 0 6C0 2.68625 2.68616 0 6 0C9.31384 0 12 2.68625 12 6ZM4.70239 4.65747H6.82398V9.17688C6.82398 9.61435 6.46933 9.96899 6.03186 9.96899C5.59439 9.96899 5.23975 9.61435 5.23975 9.17688V6.06226H4.70239C4.31447 6.06226 4 5.74778 4 5.35986C4 4.97194 4.31447 4.65747 4.70239 4.65747ZM5.00977 2.93247C5.00977 2.80778 5.03192 2.69149 5.07587 2.58359C5.12345 2.47236 5.18773 2.37629 5.26909 2.29539C5.35044 2.21449 5.44524 2.15048 5.55383 2.10326C5.60323 2.0824 5.65408 2.06615 5.70674 2.0545C5.77357 2.03987 5.84294 2.03247 5.91485 2.03247C6.03688 2.03247 6.15202 2.05604 6.26061 2.10326C6.29003 2.11599 6.31836 2.12998 6.3456 2.14515C6.41933 2.18623 6.4858 2.23634 6.54536 2.29539C6.62671 2.37629 6.691 2.47236 6.73858 2.58359C6.76328 2.63984 6.7818 2.69835 6.79342 2.75912C6.80432 2.815 6.80977 2.87279 6.80977 2.93247C6.80977 2.99965 6.80287 3.06492 6.78906 3.12822C6.77744 3.18239 6.76037 3.23512 6.73858 3.2864C6.691 3.3943 6.62671 3.48865 6.54536 3.56955C6.50359 3.61099 6.45856 3.64801 6.40989 3.6806C6.3634 3.71157 6.31364 3.73866 6.26061 3.76168C6.15202 3.80891 6.03688 3.83247 5.91485 3.83247C5.85129 3.83247 5.78991 3.82678 5.73071 3.81532C5.66933 3.80358 5.61049 3.7857 5.55383 3.76168C5.44524 3.71446 5.35044 3.65045 5.26909 3.56955C5.18773 3.48865 5.12345 3.3943 5.07587 3.2864C5.03192 3.17517 5.00977 3.05716 5.00977 2.93247Z"
                                                                fill="#464F60" />
                                                        </svg>
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem>
                                                    <Link
                                                        :href="route('contragents.edit', item.id)"
                                                        class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Редактировать
                                                        <svg
                                                            class="block ml-2"
                                                            width="16" height="16" viewBox="0 0 16 16" fill="none"
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
                                                    <Link
                                                        :href="route('contragents.destroy', item.id)"
                                                        class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all"
                                                    >
                                                        Удалить
                                                        <svg
                                                            class="block ml-2"
                                                            width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                        >
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

            <pagination
                v-if="getActiveMenuLink === 'all'"
                ref="pagination_el"
                :current-page="props.contragents.current_page"
                :total-pages="props.contragents.last_page"
                :total-count="props.contragents.total"
                :next-page-url="props.contragents.next_page_url"
                :links="props.contragents.links"
                :prev-page-url="props.contragents.prev_page_url"
                class="mt-5"
            />
            <pagination
                v-else-if="getActiveMenuLink === 'customers'"
                ref="pagination_el"
                :current-page="props.contragents_customers.current_page"
                :total-count="props.contragents_customers.total"
                :total-pages="props.contragents_customers.last_page"
                :next-page-url="props.contragents_customers.next_page_url"
                :links="props.contragents_customers.links"
                :prev-page-url="props.contragents_customers.prev_page_url"
                class="mt-5"
            />
            <pagination
                v-else-if="getActiveMenuLink === 'suppliers'"
                ref="pagination_el"
                :current-page="props.contragents_suppliers.current_page"
                :total-count="props.contragents_suppliers.total"
                :total-pages="props.contragents_suppliers.last_page"
                :next-page-url="props.contragents_suppliers.next_page_url"
                :links="props.contragents_suppliers.links"
                :prev-page-url="props.contragents_suppliers.prev_page_url"
                class="mt-5"
            />
        </div>
<!--        <div v-if="getActiveMenuLink == 'customers'" class="p-4">
            <div class="lg:overflow-y-visible lg:overflow-x-visible sm:overflow-y-auto sm:overflow-x-auto pb-30">
                <table class="border-2 border-gray w-full mt-5 table-auto">
                    <thead class="border-b-2 whitespace-nowrap">
                        <tr class="py-3">
                            <th class="py-4">
                                <input
                                    class="w-5 h-5 text-side-gray-text bg-gray-100 border-my-black  dark:bg-gray-700 dark:border-my-black"
                                    type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
                            </th>
                            <th class="py-4 lg:text-md md:text-md sm:font-regular sm:text-left sm:text-sm">
                                <p class="text-left font-robotoBold">Наименование</p>
                            </th>
                            <th class="py-4 lg:text-md md:text-md lg:table-cell md:table-cell  sm:text-xs">
                                <p class="text-left font-robotoBold">Контактное лицо</p>
                            </th>
                            <th class="py-4 lg:text-md md:text-md lg:table-cell md:table-cell  sm:text-xs">
                                <p class="text-left font-robotoBold">Телефон</p>
                            </th>
                            <th class="py-4 lg:text-md md:text-md lg:table-cell md:table-cell  sm:text-xs">
                                <p class="text-left font-robotoBold">Эл. Почта</p>
                            </th>
                            <th class="py-4 lg:text-md md:text-md lg:table-cell md:table-cell  sm:text-xs">
                                <p class="text-left font-robotoBold">Примечание</p>
                            </th>
                            <th
                                class="py-4 lg:te justify-centerxt-md md:text-md lg:table-cell md:table-cell  sm:text-xs">
                                <p class="text-left font-robotoBold">Статус</p>
                            </th>
                            <th class="p-4 lg:table-cell md:table-cell ">
                            </th>
                        </tr>
                    </thead>
                    <tbody class=" bg-white">
                        <tr :class="{ 'bg-gray-200': selectedItems.includes(item.id) }" :key="item.id"
                            class=" sm:border-b-2" v-for="item in contragents_customers.data">
                            <td :class="{ 'border-l-4 border-side-gray-text': selectedItems.includes(item.id) }"
                                class="text-center p-2">
                                <input
                                    class="w-5 h-5 text-side-gray-text bg-gray-100 border-my-black focus:ring-side-gray-text dark:focus:ring-side-gray-text dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-my-black"
                                    type="checkbox" v-model="selectedItems" :value="item.id" />
                            </td>
                            <td class="text-center  p-2">
                                <div class="flex lg:justify-around md:justify-around sm:justify-start ">
                                    <div class="mr-1 sm:mr-2 sm:flex sm:justify-center sm:items-center">

                                        <img v-if="item.avatar" class="sm:max-w-10 sm:rounded-3xl" :src="item.avatar"
                                            alt="">
                                        <span v-else><svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect width="40" height="40" rx="20" fill="#F2F4F8" />
                                                <path
                                                    d="M16.534 20.07C16.6569 20.0175 16.7892 19.9903 16.9228 19.9898C17.0565 19.9893 17.1889 20.0156 17.3122 20.0672C17.4355 20.1188 17.5473 20.1945 17.6408 20.29C17.7344 20.3855 17.8078 20.4988 17.8568 20.6231C17.9058 20.7475 17.9294 20.8804 17.9262 21.014C17.9229 21.1477 17.8929 21.2793 17.8379 21.4011C17.7829 21.523 17.7041 21.6325 17.606 21.7234C17.508 21.8142 17.3927 21.8845 17.267 21.93C16.5985 22.1934 16.0248 22.6519 15.6205 23.2458C15.2162 23.8397 15 24.5415 15 25.26V27C15 27.2652 15.1054 27.5196 15.2929 27.7071C15.4804 27.8946 15.7348 28 16 28H24C24.2652 28 24.5196 27.8946 24.7071 27.7071C24.8946 27.5196 25 27.2652 25 27V25.353C25.0001 24.6115 24.7749 23.8874 24.3541 23.2768C23.9334 22.6662 23.337 22.1979 22.644 21.934C22.5175 21.8901 22.4012 21.8213 22.3018 21.7316C22.2024 21.6419 22.1221 21.5332 22.0655 21.4119C22.009 21.2905 21.9773 21.1591 21.9725 21.0253C21.9677 20.8916 21.9897 20.7582 22.0374 20.6331C22.0851 20.508 22.1574 20.3938 22.25 20.2972C22.3427 20.2006 22.4538 20.1235 22.5767 20.0706C22.6997 20.0177 22.832 19.9901 22.9659 19.9893C23.0998 19.9885 23.2324 20.0146 23.356 20.066C24.4276 20.4742 25.3499 21.1983 26.0006 22.1425C26.6514 23.0867 26.9999 24.2063 27 25.353V27C27 27.7956 26.6839 28.5587 26.1213 29.1213C25.5587 29.6839 24.7956 30 24 30H16C15.2044 30 14.4413 29.6839 13.8787 29.1213C13.3161 28.5587 13 27.7956 13 27V25.26C13.0001 24.1402 13.3373 23.0463 13.9676 22.1206C14.5978 21.195 15.4921 20.4805 16.534 20.07ZM20 10C21.0609 10 22.0783 10.4214 22.8284 11.1716C23.5786 11.9217 24 12.9391 24 14V16C24 17.0609 23.5786 18.0783 22.8284 18.8284C22.0783 19.5786 21.0609 20 20 20C18.9391 20 17.9217 19.5786 17.1716 18.8284C16.4214 18.0783 16 17.0609 16 16V14C16 12.9391 16.4214 11.9217 17.1716 11.1716C17.9217 10.4214 18.9391 10 20 10V10ZM20 12C19.4696 12 18.9609 12.2107 18.5858 12.5858C18.2107 12.9609 18 13.4696 18 14V16C18 16.5304 18.2107 17.0391 18.5858 17.4142C18.9609 17.7893 19.4696 18 20 18C20.5304 18 21.0391 17.7893 21.4142 17.4142C21.7893 17.0391 22 16.5304 22 16V14C22 13.4696 21.7893 12.9609 21.4142 12.5858C21.0391 12.2107 20.5304 12 20 12Z"
                                                    fill="#C1C7CD" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex flex-col justify-start ">
                                        <span
                                            class="text-left font-robotoBold text-black-700 font-bold flex justify-start items-start">
                                            {{
                                                item.name }}</span>
                                        <span
                                            class="font-roboto flex text-sm text-gray-500 justify-start items-start sm:text-xs">ИНН
                                            {{ item.inn
                                            }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell   ">{{
                                item.contact_person ?
                                    item.contact_person : 'Не задано' }}</td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell   ">{{
                                item.contact_person_phone ? item.contact_person_phone : 'Не задано' }}</td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell   ">{{
                                item.email ? item.email : 'Не задано' }}</td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell   ">{{ item.notes
                                }}</td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell  whitespace-nowrap ">
                                <ContragentStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />
                            </td>

                            <td
                                class="text-center font-roboto lg:table-cell md:table-cell   flex items-center justify-between  p-6">

                                <div>


                                    <Menu as="div" class="relative inline-block text-left">
                                        <div class="sm:overflow-x-visible">
                                            <MenuButton
                                                class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                                <svg width="14" height="4" viewBox="0 0 14 4" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2.33337 4C1.80294 4 1.29423 3.78929 0.91916 3.41421C0.544088 3.03914 0.333374 2.53043 0.333374 2C0.333374 1.46957 0.544088 0.960859 0.91916 0.585786C1.29423 0.210714 1.80294 0 2.33337 0C2.86381 0 3.37251 0.210714 3.74759 0.585786C4.12266 0.960859 4.33337 1.46957 4.33337 2C4.33337 2.53043 4.12266 3.03914 3.74759 3.41421C3.37251 3.78929 2.86381 4 2.33337 4ZM11.6667 4C11.1363 4 10.6276 3.78929 10.2525 3.41421C9.87742 3.03914 9.66671 2.53043 9.66671 2C9.66671 1.46957 9.87742 0.960859 10.2525 0.585786C10.6276 0.210714 11.1363 0 11.6667 0C12.1971 0 12.7058 0.210714 13.0809 0.585786C13.456 0.960859 13.6667 1.46957 13.6667 2C13.6667 2.53043 13.456 3.03914 13.0809 3.41421C12.7058 3.78929 12.1971 4 11.6667 4ZM7.00004 4C6.46961 4 5.9609 3.78929 5.58583 3.41421C5.21075 3.03914 5.00004 2.53043 5.00004 2C5.00004 1.46957 5.21075 0.960859 5.58583 0.585786C5.9609 0.210714 6.46961 0 7.00004 0C7.53047 0 8.03918 0.210714 8.41426 0.585786C8.78933 0.960859 9.00004 1.46957 9.00004 2C9.00004 2.53043 8.78933 3.03914 8.41426 3.41421C8.03918 3.78929 7.53047 4 7.00004 4Z"
                                                        fill="#697077" />
                                                </svg>

                                            </MenuButton>
                                        </div>

                                        <transition enter-active-class="transition ease-out duration-100"
                                            enter-from-class="transform opacity-0 scale-95"
                                            enter-to-class="transform opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform opacity-100 scale-100"
                                            leave-to-class="transform opacity-0 scale-95">
                                            <MenuItems style="z-index: 10;"
                                                class="absolute top-0  right-0 mt-10 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5  z-200 focus:outline-none">

                                                <div class="py-1">
                                                    <MenuItem class="">
                                                    <Link :href="route('contragents.edit', item.id)"
                                                        class="flex items-center justify-between"
                                                        :class="['block px-4 py-2 text-sm']">Редактировать<svg
                                                        width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                            fill="#464F60" />
                                                        <path
                                                            d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                            fill="#464F60" />
                                                    </svg>

                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link :href="route('contragents.show', item.id)"
                                                        class="flex items-center justify-between"
                                                        :class="['block px-4 py-2 text-sm']">Посмотреть<svg width="12"
                                                        height="12" viewBox="0 0 12 12" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12 6C12 9.31375 9.31384 12 6 12C2.68616 12 0 9.31375 0 6C0 2.68625 2.68616 0 6 0C9.31384 0 12 2.68625 12 6ZM4.70239 4.65747H6.82398V9.17688C6.82398 9.61435 6.46933 9.96899 6.03186 9.96899C5.59439 9.96899 5.23975 9.61435 5.23975 9.17688V6.06226H4.70239C4.31447 6.06226 4 5.74778 4 5.35986C4 4.97194 4.31447 4.65747 4.70239 4.65747ZM5.00977 2.93247C5.00977 2.80778 5.03192 2.69149 5.07587 2.58359C5.12345 2.47236 5.18773 2.37629 5.26909 2.29539C5.35044 2.21449 5.44524 2.15048 5.55383 2.10326C5.60323 2.0824 5.65408 2.06615 5.70674 2.0545C5.77357 2.03987 5.84294 2.03247 5.91485 2.03247C6.03688 2.03247 6.15202 2.05604 6.26061 2.10326C6.29003 2.11599 6.31836 2.12998 6.3456 2.14515C6.41933 2.18623 6.4858 2.23634 6.54536 2.29539C6.62671 2.37629 6.691 2.47236 6.73858 2.58359C6.76328 2.63984 6.7818 2.69835 6.79342 2.75912C6.80432 2.815 6.80977 2.87279 6.80977 2.93247C6.80977 2.99965 6.80287 3.06492 6.78906 3.12822C6.77744 3.18239 6.76037 3.23512 6.73858 3.2864C6.691 3.3943 6.62671 3.48865 6.54536 3.56955C6.50359 3.61099 6.45856 3.64801 6.40989 3.6806C6.3634 3.71157 6.31364 3.73866 6.26061 3.76168C6.15202 3.80891 6.03688 3.83247 5.91485 3.83247C5.85129 3.83247 5.78991 3.82678 5.73071 3.81532C5.66933 3.80358 5.61049 3.7857 5.55383 3.76168C5.44524 3.71446 5.35044 3.65045 5.26909 3.56955C5.18773 3.48865 5.12345 3.3943 5.07587 3.2864C5.03192 3.17517 5.00977 3.05716 5.00977 2.93247Z"
                                                            fill="#464F60" />
                                                    </svg>


                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link :href="route('contragents.destroy', item.id)"
                                                        class="flex items-center justify-between"
                                                        :class="['block px-4 py-2 text-sm']" method="delete">Удалить<svg
                                                        width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                            fill="#DA1E28" />
                                                    </svg>



                                                    </Link>
                                                    </MenuItem>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>


                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pagination :total-pages="contragents_customers.last_page" class="mt-5" :links="contragents_customers.links"
                :next-page-url="contragents_customers.next_page_url"
                :prev-page-url="contragents_customers.prev_page_url" />

        </div>
        <div v-if="getActiveMenuLink == 'suppliers'" class="p-4">
            <div class="lg:overflow-y-visible lg:overflow-x-visible sm:overflow-y-auto sm:overflow-x-auto pb-30">
                <table class="border-2 border-gray w-full mt-5 table-auto">
                    <thead class="border-b-2">
                        <tr class="py-3">
                            <th class="py-4">
                                <input
                                    class="w-5 h-5 text-side-gray-text bg-gray-100 border-my-black  focus:ring-2 dark:bg-gray-700 dark:border-my-blacks"
                                    type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
                            </th>
                            <th class="py-4 lg:text-md md:text-md sm:font-regular sm:text-left sm:text-sm">
                                <p class="text-left font-robotoBold">Наименование</p>
                            </th>
                            <th class="py-4 lg:text-md md:text-md lg:table-cell md:table-cell sm:whitespace-nowrap  sm:text-xs">
                                <p class="text-left font-robotoBold">Контактное лицо</p>
                            </th>
                            <th class="py-4 lg:text-md md:text-md lg:table-cell md:table-cell sm:whitespace-nowrap  sm:text-xs">
                                <p class="text-left font-robotoBold">Телефон</p>
                            </th>
                            <th class="py-4 lg:text-md md:text-md lg:table-cell md:table-cell sm:whitespace-nowrap  sm:text-xs">
                                <p class="text-left font-robotoBold">Эл. Почта</p>
                            </th>
                            <th class="py-4 lg:text-md md:text-md lg:table-cell md:table-cell sm:whitespace-nowrap  sm:text-xs">
                                <p class="text-left font-robotoBold">Примечание</p>
                            </th>
                            <th
                                class="py-4 lg:te justify-centerxt-md md:text-md lg:table-cell md:table-cell sm:whitespace-nowrap  sm:text-xs">
                                <p class="text-left font-robotoBold">Статус</p>
                            </th>
                            <th class="p-4 lg:table-cell md:table-cell sm:whitespace-nowrap ">
                            </th>
                        </tr>
                    </thead>
                    <tbody class=" bg-white">
                        <tr :class="{ 'bg-gray-200': selectedItems.includes(item.id) }" :key="item.id"
                            class=" sm:border-b-2" v-for="item in contragents_suppliers.data">
                            <td :class="{ 'border-l-4 border-side-gray-text': selectedItems.includes(item.id) }"
                                class="text-center p-2">
                                <input
                                    class="w-5 h-5 text-side-gray-text bg-gray-100 border-my-black  focus:ring-2 dark:bg-gray-700 dark:border-my-black"
                                    type="checkbox" v-model="selectedItems" :value="item.id" />
                            </td>
                            <td class="text-center  p-2">
                                <div class="flex lg:justify-around md:justify-around sm:justify-start ">
                                    <div class="mr-1 sm:mr-2 sm:flex sm:justify-center sm:items-center">

                                        <img v-if="item.avatar" class="sm:max-w-10 sm:rounded-3xl" :src="item.avatar"
                                            alt="">
                                        <span v-else><svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect width="40" height="40" rx="20" fill="#F2F4F8" />
                                                <path
                                                    d="M16.534 20.07C16.6569 20.0175 16.7892 19.9903 16.9228 19.9898C17.0565 19.9893 17.1889 20.0156 17.3122 20.0672C17.4355 20.1188 17.5473 20.1945 17.6408 20.29C17.7344 20.3855 17.8078 20.4988 17.8568 20.6231C17.9058 20.7475 17.9294 20.8804 17.9262 21.014C17.9229 21.1477 17.8929 21.2793 17.8379 21.4011C17.7829 21.523 17.7041 21.6325 17.606 21.7234C17.508 21.8142 17.3927 21.8845 17.267 21.93C16.5985 22.1934 16.0248 22.6519 15.6205 23.2458C15.2162 23.8397 15 24.5415 15 25.26V27C15 27.2652 15.1054 27.5196 15.2929 27.7071C15.4804 27.8946 15.7348 28 16 28H24C24.2652 28 24.5196 27.8946 24.7071 27.7071C24.8946 27.5196 25 27.2652 25 27V25.353C25.0001 24.6115 24.7749 23.8874 24.3541 23.2768C23.9334 22.6662 23.337 22.1979 22.644 21.934C22.5175 21.8901 22.4012 21.8213 22.3018 21.7316C22.2024 21.6419 22.1221 21.5332 22.0655 21.4119C22.009 21.2905 21.9773 21.1591 21.9725 21.0253C21.9677 20.8916 21.9897 20.7582 22.0374 20.6331C22.0851 20.508 22.1574 20.3938 22.25 20.2972C22.3427 20.2006 22.4538 20.1235 22.5767 20.0706C22.6997 20.0177 22.832 19.9901 22.9659 19.9893C23.0998 19.9885 23.2324 20.0146 23.356 20.066C24.4276 20.4742 25.3499 21.1983 26.0006 22.1425C26.6514 23.0867 26.9999 24.2063 27 25.353V27C27 27.7956 26.6839 28.5587 26.1213 29.1213C25.5587 29.6839 24.7956 30 24 30H16C15.2044 30 14.4413 29.6839 13.8787 29.1213C13.3161 28.5587 13 27.7956 13 27V25.26C13.0001 24.1402 13.3373 23.0463 13.9676 22.1206C14.5978 21.195 15.4921 20.4805 16.534 20.07ZM20 10C21.0609 10 22.0783 10.4214 22.8284 11.1716C23.5786 11.9217 24 12.9391 24 14V16C24 17.0609 23.5786 18.0783 22.8284 18.8284C22.0783 19.5786 21.0609 20 20 20C18.9391 20 17.9217 19.5786 17.1716 18.8284C16.4214 18.0783 16 17.0609 16 16V14C16 12.9391 16.4214 11.9217 17.1716 11.1716C17.9217 10.4214 18.9391 10 20 10V10ZM20 12C19.4696 12 18.9609 12.2107 18.5858 12.5858C18.2107 12.9609 18 13.4696 18 14V16C18 16.5304 18.2107 17.0391 18.5858 17.4142C18.9609 17.7893 19.4696 18 20 18C20.5304 18 21.0391 17.7893 21.4142 17.4142C21.7893 17.0391 22 16.5304 22 16V14C22 13.4696 21.7893 12.9609 21.4142 12.5858C21.0391 12.2107 20.5304 12 20 12Z"
                                                    fill="#C1C7CD" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex flex-col justify-start ">
                                        <span
                                            class="font-robotoBold text-black-700 font-bold flex justify-start items-start">
                                            {{
                                                item.name }}</span>
                                        <span
                                            class="font-roboto flex text-sm text-gray-500 justify-start items-start sm:text-xs">ИНН
                                            {{ item.inn
                                            }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell sm:whitespace-nowrap ">{{
                                item.contact_person ?
                                    item.contact_person : 'Не задано' }}</td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell sm:whitespace-nowrap ">{{
                                item.contact_person_phone ? item.contact_person_phone : 'Не задано' }}</td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell sm:whitespace-nowrap ">{{
                                item.email ? item.email : 'Не задано' }}</td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell sm:whitespace-nowrap ">{{ item.notes
                                }}</td>
                            <td class="font-roboto text-left lg:table-cell md:table-cell sm:whitespace-nowrap whitespace-nowrap">
                                <ContragentStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />
                            </td>

                            <td
                                class="font-roboto text-center lg:table-cell md:table-cell sm:whitespace-nowrap  flex items-center justify-between  p-6">

                                <div>


                                    <Menu as="div" class="relative inline-block text-left">
                                        <div class="sm:overflow-x-visible">
                                            <MenuButton
                                                class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                                <svg width="14" height="4" viewBox="0 0 14 4" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2.33337 4C1.80294 4 1.29423 3.78929 0.91916 3.41421C0.544088 3.03914 0.333374 2.53043 0.333374 2C0.333374 1.46957 0.544088 0.960859 0.91916 0.585786C1.29423 0.210714 1.80294 0 2.33337 0C2.86381 0 3.37251 0.210714 3.74759 0.585786C4.12266 0.960859 4.33337 1.46957 4.33337 2C4.33337 2.53043 4.12266 3.03914 3.74759 3.41421C3.37251 3.78929 2.86381 4 2.33337 4ZM11.6667 4C11.1363 4 10.6276 3.78929 10.2525 3.41421C9.87742 3.03914 9.66671 2.53043 9.66671 2C9.66671 1.46957 9.87742 0.960859 10.2525 0.585786C10.6276 0.210714 11.1363 0 11.6667 0C12.1971 0 12.7058 0.210714 13.0809 0.585786C13.456 0.960859 13.6667 1.46957 13.6667 2C13.6667 2.53043 13.456 3.03914 13.0809 3.41421C12.7058 3.78929 12.1971 4 11.6667 4ZM7.00004 4C6.46961 4 5.9609 3.78929 5.58583 3.41421C5.21075 3.03914 5.00004 2.53043 5.00004 2C5.00004 1.46957 5.21075 0.960859 5.58583 0.585786C5.9609 0.210714 6.46961 0 7.00004 0C7.53047 0 8.03918 0.210714 8.41426 0.585786C8.78933 0.960859 9.00004 1.46957 9.00004 2C9.00004 2.53043 8.78933 3.03914 8.41426 3.41421C8.03918 3.78929 7.53047 4 7.00004 4Z"
                                                        fill="#697077" />
                                                </svg>

                                            </MenuButton>
                                        </div>

                                        <transition enter-active-class="transition ease-out duration-100"
                                            enter-from-class="transform opacity-0 scale-95"
                                            enter-to-class="transform opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform opacity-100 scale-100"
                                            leave-to-class="transform opacity-0 scale-95">
                                            <MenuItems style="z-index: 10;"
                                                class="absolute top-0  right-0 mt-10 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5  z-200 focus:outline-none">

                                                <div class="py-1">
                                                    <MenuItem class="">
                                                    <Link :href="route('contragents.edit', item.id)"
                                                        class="flex items-center justify-between"
                                                        :class="['block px-4 py-2 text-sm']">Редактировать<svg
                                                        width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z"
                                                            fill="#464F60" />
                                                        <path
                                                            d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z"
                                                            fill="#464F60" />
                                                    </svg>

                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link :href="route('contragents.show', item.id)"
                                                        class="flex items-center justify-between"
                                                        :class="['block px-4 py-2 text-sm']">Посмотреть<svg width="12"
                                                        height="12" viewBox="0 0 12 12" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12 6C12 9.31375 9.31384 12 6 12C2.68616 12 0 9.31375 0 6C0 2.68625 2.68616 0 6 0C9.31384 0 12 2.68625 12 6ZM4.70239 4.65747H6.82398V9.17688C6.82398 9.61435 6.46933 9.96899 6.03186 9.96899C5.59439 9.96899 5.23975 9.61435 5.23975 9.17688V6.06226H4.70239C4.31447 6.06226 4 5.74778 4 5.35986C4 4.97194 4.31447 4.65747 4.70239 4.65747ZM5.00977 2.93247C5.00977 2.80778 5.03192 2.69149 5.07587 2.58359C5.12345 2.47236 5.18773 2.37629 5.26909 2.29539C5.35044 2.21449 5.44524 2.15048 5.55383 2.10326C5.60323 2.0824 5.65408 2.06615 5.70674 2.0545C5.77357 2.03987 5.84294 2.03247 5.91485 2.03247C6.03688 2.03247 6.15202 2.05604 6.26061 2.10326C6.29003 2.11599 6.31836 2.12998 6.3456 2.14515C6.41933 2.18623 6.4858 2.23634 6.54536 2.29539C6.62671 2.37629 6.691 2.47236 6.73858 2.58359C6.76328 2.63984 6.7818 2.69835 6.79342 2.75912C6.80432 2.815 6.80977 2.87279 6.80977 2.93247C6.80977 2.99965 6.80287 3.06492 6.78906 3.12822C6.77744 3.18239 6.76037 3.23512 6.73858 3.2864C6.691 3.3943 6.62671 3.48865 6.54536 3.56955C6.50359 3.61099 6.45856 3.64801 6.40989 3.6806C6.3634 3.71157 6.31364 3.73866 6.26061 3.76168C6.15202 3.80891 6.03688 3.83247 5.91485 3.83247C5.85129 3.83247 5.78991 3.82678 5.73071 3.81532C5.66933 3.80358 5.61049 3.7857 5.55383 3.76168C5.44524 3.71446 5.35044 3.65045 5.26909 3.56955C5.18773 3.48865 5.12345 3.3943 5.07587 3.2864C5.03192 3.17517 5.00977 3.05716 5.00977 2.93247Z"
                                                            fill="#464F60" />
                                                    </svg>


                                                    </Link>
                                                    </MenuItem>
                                                    <MenuItem>
                                                    <Link :href="route('contragents.destroy', item.id)"
                                                        class="flex items-center justify-between"
                                                        :class="['block px-4 py-2 text-sm']" method="delete">Удалить<svg
                                                        width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                            fill="#DA1E28" />
                                                    </svg>



                                                    </Link>
                                                    </MenuItem>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>


                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pagination :total-pages="contragents_suppliers.last_page" class="mt-5" :links="contragents_suppliers.links"
                :next-page-url="contragents_suppliers.next_page_url"
                :prev-page-url="contragents_suppliers.prev_page_url" />

        </div>-->
    </AuthenticatedLayout>
</template>
