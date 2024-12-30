<script setup>
import EquipNav from '@/Components/Equip/EquipNav.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import store from '../../../store';
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
} from 'radix-vue';
import { ref, computed, onMounted, reactive, toRaw } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { EquipMenuItems } from "../../../constants/index.js";
import EquipFilter from "@/Components/Equip/EquipFilter.vue";
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";
import Pagination from "@/Components/Pagination.vue";
import EquipPriceDialog from "@/Components/Equip/EquipPriceDialog.vue";


const props = defineProps({
    equipment: Object,
    equipment_categories: Array,
    equipment_categories_counts: Object,
    equipment_sizes_counts: Object,
    equipment_sizes: Array,
    equipment_location: Array,
    contragents: Array,
    archivedPrices: Object,
    prices: Object,
    equipmentData: Array
})

const is_dialog_open = ref(false);
const dialog_purpose = ref('create');
const price_to_edit  = ref(null);

const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const seriesActive = computed(() => store.getters['equipment/getSeriesActive']);
const menuActive = computed(() => store.getters['equipment/getMenuActiveItem']);
const getTabActive = computed(() => store.getters['equipment/getPriceTabActive']);
const getPriceRowsCount = computed(() => store.getters['equipment/getPriceRowsCount']);


const incRows = () => store.dispatch('equipment/updatePriceRowsCountInc')
const decRows = () => store.dispatch('equipment/updatePriceRowsCountDec')

const sortOrder = computed(() => store.getters['equipment/getSortOrder']);
const sortBy = computed(() => store.getters['equipment/getSortBy']);

const updateSortBy = (value) => store.dispatch("equipment/updateSortBy", value)
const updateSortOrder = (value) => store.dispatch("equipment/updateSortOrder", value)
const toggleSortBy = (column) => {
    if (sortBy.value === column) {
        updateSortOrder(sortOrder.value === 'asc' ? 'desc' : 'asc');
    } else {
        updateSortBy(column);
        updateSortOrder('asc');
    }
};
const selectCategory = (category) => {
    store.dispatch('equipment/updateCategory', category);
    updateUrl()
};
const updateActiveTab = (tab) => {
    store.dispatch('equipment/updatePriceTabActive', tab);
};


const selectSize = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)


    updateUrl();
}

const setSeriesId = (seriesId) => {
    store.dispatch('equipment/updateSeriesId', seriesId)
    if (selectedCategory.value && selectedSize.value && seriesActive.value) {
        console.log(selectedSize.value, seriesActive.value)
        updateUrl()

        updateRepairTable(selectedCategory.value, selectedSize.value, seriesActive.value);
    }
}

const updateRepairTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentRepair', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}
const updateUrl = () => {
    if (selectedCategory.value && selectedSize.value) {
        router.get(route('equip.price', {
            category_id: selectedCategory.value,
            size_id: selectedSize.value,
            series: seriesActive.value
        }));
    }
};


const setCategoryId = (categoryId) => {
    store.dispatch('equipment/updateCategory', categoryId)
    updateUrl();
}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    updateUrl();
}

const findEquipmentByCategoryAndSize = (categoryId, sizeId) => {
    const rawData = toRaw(props.equipmentData)
    return rawData.filter((a) => console.log(a.category == categoryId && a.size == sizeId))
}

const sortedPrices = computed(() => {
    return props.prices.data
        .filter(price => !price.archive) // Filter non-archived
        .slice()
        .sort((a, b) => {
            let result = 0;

            // Sort by the selected property
            if (sortBy.value === 'date') {
                result = a.store_date.localeCompare(b.store_date);
            }

            // Apply sort order (ascending or descending)
            if (sortOrder.value === 'desc') {
                result = result * -1;
            }

            return result;
        });
});

const sortedPricesArchived = computed(() => {
    // Filter for archived prices and sort
    return props.archivedPrices.data
        .filter(price => price.archive) // Filter archived
        .slice()
        .sort((a, b) => {
            let result = 0;

            // Sort by the selected property
            if (sortBy.value === 'date') {
                result = a.store_date.localeCompare(b.store_date);
            }

            // Apply sort order (ascending or descending)
            if (sortOrder.value === 'desc') {
                result = result * -1;
            }

            return result;
        });
});


const form = reactive({
    'category_id': selectedCategory,
    'size_id': selectedSize,
    'contragent_id': null,
    'store_date': null,
    'notes': null,
    'store_price': null,
    'operation_price': null,
    'archive': 1
})

function submit() {
    router.post('/equip/price', {
        category_id: form.category_id,
        size_id: form.size_id,
        contragent_id: form.contragent_id,
        store_date: form.store_date,
        notes: form.notes,
        store_price: form.store_price,
        operation_price: form.operation_price,
        archive: 0,
    });

    decRows();

    form.contragent_id   = null;
    form.store_date      = null;
    form.notes           = null;
    form.store_price     = null;
    form.operation_price = null;
    form.archive         = 1;
}

function openDialog(purpose, price_id) {
    is_dialog_open.value = true;
    dialog_purpose.value = purpose;
    if (purpose === 'edit') price_to_edit.value = props.prices.data.find(p=>p.id==price_id);
}

const currentPage = ref(props.prices.current_page || 1);
const lastPage = ref(props.prices.last_page || 1);

onMounted(() => {
    const { current_page, total } = props.prices;

    store.dispatch('pagination/updatePagination', {
        currentPage: current_page,
        totalPages: total,
    });
    currentPage.value = props.prices.current_page;
    lastPage.value = props.prices.last_page;


    store.dispatch('fetchLocations')
    store.dispatch('equipment/updateEquipmentCategories', props.equipment_categories)
    store.dispatch('equipment/updateEquipmentSizesCounts', props.equipment_sizes_counts)
    store.dispatch('equipment/updateEquipmentCategoriesCounts', props.equipment_categories_counts)
    store.dispatch('equipment/updateEquipmentSizes', props.equipment_sizes)

    const url_params = new URLSearchParams(window.location.search);

    const fetch_by = {};

    if (url_params.get('size_id')) {
        fetch_by.size_id = +url_params.get('size_id');
        store.dispatch('equipment/updateSize', fetch_by.size_id);
    } else {
        fetch_by.size_id = 1;
        store.dispatch('equipment/updateSize', 1);
    }

    if (url_params.get('category_id')) {
        fetch_by.category_id = +url_params.get('category_id');
        store.dispatch('equipment/updateCategory', fetch_by.category_id);
    } else {
        fetch_by.category_id = 1;
        store.dispatch('equipment/updateCategory', 1);
    }

    store.dispatch('equipment/updateMenuItem', EquipMenuItems.PRICE);
});
</script>


<template>
    <AuthenticatedLayout>
        <EquipNav/>

        <div class="p-5">
            <EquipFilter
                :selected-category="selectedCategory"
                :selected-size="selectedSize"
                :categories-counts="equipment_categories_counts"
                :sizes-counts="equipment_sizes_counts"
                :categories="equipment_categories"
                :sizes="equipment_sizes"
                required-size
                @category-click="cat_id => setCategoryId(cat_id)"
                @size-click="size_id => setSizeId(size_id)"
            />


            <div class="relative mt-5">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto text-nowrap text-gray1">
                    <li
                        :class="{ '!border-[#001D6C] text-[#001D6C]': getTabActive == 'price' }"
                        class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                        @click="updateActiveTab('price')"
                    >
                        Задать цену оборудования
                    </li>
                    <li
                        :class="{ '!border-[#001D6C] text-[#001D6C]': getTabActive == 'archive' }"
                        class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                        @click="updateActiveTab('archive')"
                    >
                        История цен
                    </li>
                </ul>
            </div>

            <div class="w-full max-w-full mt-5 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[1050px] text-xs">
                    <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div
                            :class="getTabActive === 'price' ? 'bg-violet-full/5' : 'bg-bg2'"
                            class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-"
                        >
                            <button v-if="getTabActive === 'price'" type="button" @click="incRows">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.9444 8.69444C14.9444 8.31091 14.6335 8 14.25 8C13.8665 8 13.5556 8.31091 13.5556 8.69444V13.5556H8.69444C8.31091 13.5556 8 13.8665 8 14.25C8 14.6335 8.31091 14.9444 8.69444 14.9444H13.5556V19.8056C13.5556 20.1891 13.8665 20.5 14.25 20.5C14.6335 20.5 14.9444 20.1891 14.9444 19.8056V14.9444H19.8056C20.1891 14.9444 20.5 14.6335 20.5 14.25C20.5 13.8665 20.1891 13.5556 19.8056 13.5556H14.9444V8.69444Z" fill="#484964"/>
                                    <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08"/>
                                </svg>
                            </button>
                        </div>
                        <div class="shrink-0 flex items-center w-[17.48%] py-2.5 px-2">Наименование</div>
                        <div
                            :class="{ 'bg-violet': sortBy === 'date' }"
                            class="shrink-0 flex items-center justify-between w-[13.98%] py-2.5 px-2 cursor-pointer"
                            @click="toggleSortBy('date')"
                        >
                            Дата
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08"/>
                                <path d="M8.26783 12.1904L10.6249 9.83331L12.9821 12.1904" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10.625 9.83354L10.625 18.0834" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19.5654 16.5596L17.2083 18.9167L14.8511 16.5596" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17.2082 10.667L17.2082 18.9169" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-44px-17.48%-13.98%-13.98%-13.98%-100px)] py-2.5 px-2">Описание</div>
                        <div class="shrink-0 flex items-center w-[13.98%] py-2.5 px-2">Цена хранения</div>
                        <div class="shrink-0 flex items-center w-[13.98%] py-2.5 px-2">Цена за наработку</div>
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
                        v-if="getPriceRowsCount > 0 && getTabActive === 'price'"
                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                    >
                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2"></div>
                        <div class="shrink-0 flex w-[17.48%]">
                            <select
                                class="w-full h-full border-none rounded px-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                v-model="form.contragent_id"
                            >
                                <option
                                    v-for="(agent, index) in contragents"
                                    :key="agent.id"
                                    :value="agent.id"
                                >{{ agent.name }}</option>
                            </select>
                        </div>
                        <div class="shrink-0 flex items-center w-[13.98%] py-2.5 px-2">
                            <input v-model="form.store_date" type="date"
                                        class="block w-full h-full px-2 bg-transparent" onclick="this.showPicker()" />
                        </div>
                        <div class="shrink-0 flex w-[calc(100%-44px-17.48%-13.98%-13.98%-13.98%-100px)]">
                            <input
                                v-model="form.notes"
                                type="text"
                                class="w-full h-full border-none rounded px-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div class="shrink-0 flex w-[13.98%]">
                            <input
                                v-model="form.store_price"
                                type="text"
                                class="w-full h-full border-none rounded px-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div class="shrink-0 flex w-[13.98%]">
                            <input
                                v-model="form.operation_price"
                                type="text"
                                class="w-full h-full border-none rounded px-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                            fill="#21272A" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z"
                                            fill="#21272A" />
                                    </svg>
                        </div>
                    </div>
                    <div
                        v-for="(price, index) in getTabActive === 'price' ? sortedPrices : sortedPricesArchived"
                        :key="index"
                        class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3"
                    >
                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                            <UiHyperlink :item-id="price.id" :hyperlink="price.hyperlink" endpoint="/equipment/price" />
                        </div>
                        <div class="shrink-0 flex items-center w-[17.48%] py-2.5 px-2">{{ price.contragent.name }}</div>
                        <div
                            :class="{ 'bg-violet': sortBy === 'date' }"
                            class="shrink-0 flex items-center w-[13.98%] py-2.5 px-2"
                        >
                            {{ price.store_date }}
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-44px-17.48%-13.98%-13.98%-13.98%-100px)] py-2.5 px-2">
                            {{ price.notes }}
                        </div>
                        <div class="shrink-0 flex items-center w-[13.98%] py-2.5 px-2">
                            {{ price.store_price }} ₽
                        </div>
                        <div class="shrink-0 flex items-center w-[13.98%] py-2.5 px-2">
                            {{ price.operation_price }} ₽
                        </div>
                        <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                            <Link :href="'/directory/price/' + price.id" class="mr-3.5">
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
                                                    @click="openDialog('edit', price.id)"
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
            </div>

            <div v-if="getTabActive === 'price'" class="mt-5 flex justify-end">
                <button
                    class="inline-flex items-center justify-center py-3 px-7 font-medium tracking-wider bg-my-gray text-side-gray-text"
                    @click="submit">
                    Сохранить
                </button>
            </div>
            <pagination
                ref="pagination_el"
                :current-page="props.prices.current_page"
                :total-pages="props.prices.last_page"
                :total-count="props.prices.total"
                :next-page-url="props.prices.next_page_url"
                :links="props.prices.links"
                :prev-page-url="props.prices.prev_page_url"
                class="mt-5 bg-bg1"
            />
        </div>

<!--        <EquipPriceDialog
            v-model="is_dialog_open"
            :purpose="dialog_purpose"
            :agents="contragents"
            :price="price_to_edit"
        />-->
    </AuthenticatedLayout>

</template>
