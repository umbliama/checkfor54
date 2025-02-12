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
                        <div v-for="(item, i) in sortedContragets" :key="item.id"
                            :class="{ 'bg-my-gray': selectedItems.includes(item.id), 'border-b': i < sortedContragets.length }"
                            class="relative flex items-center py-2 text-sm">
                            <span v-if="selectedItems.includes(item.id)"
                                class="absolute left-0 top-0 w-[3px] h-full bg-[#697077]"></span>

                            <div class="flex items-center justify-center w-10 px-3">
                                <UiCheckbox v-model="selectedItems" :inp-attrs="{ value: item.id }" />
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
                                {{ item.contact_person ?? '-' }}
                            </div>
                            <div class="w-[14.08%] px-3">
                                {{ item.contact_person_phone ?? '-' }}
                            </div>
                            <div class="w-[14.08%] px-3">
                                {{ item.contact_person_email ?? '-' }}
                            </div>
                            <div class="w-[14.08%] px-3">{{ item.contact_person_notes }}</div>
                            <div class="w-[14.08%] px-3">
                                <ContragentStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />
                            </div>
                            <div class="w-10 px-3">
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
