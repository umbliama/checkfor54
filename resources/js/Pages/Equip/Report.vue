<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { EquipMenuItems } from '../../../constants/index';
import { Link, router } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref, watch } from 'vue';


import store from '../../../store/index';
import EquipNav from '@/Components/EquipNav.vue';
import { MenuButton, MenuItems, MenuItem, Menu } from '@headlessui/vue';
import EquipStatus from '@/Components/EquipStatus.vue';
import UiFieldSelect from '@/Components/Ui/UiFieldSelect.vue';
import UiField from '@/Components/Ui/UiField.vue';

const seriesActive = computed(() => store.getters['equipment/getSeriesActive'])

const repairs = computed(() => store.getters['equipment/getEquipmentRepairs'])
const tests   = computed(() => store.getters['equipment/getEquipmentTests'])
const history = computed(() => store.getters['equipment/getEquipmentHistory'])

const props = defineProps({
    equipment: Array,
    equipment_categories: Array,
    equipment_categories_counts: Object,
    equipment_sizes_counts: Object,
    equipment_sizes: Array,
    equipment_location: Array,
    equipment_series: Array,
    equipment_repairs: Object,
    equipment_tests: Object
})

const get_equipment_series = computed(() => {
    return props.equipment_series.map(s => ({ title: s, value: s }));
});

const setCategoryId = (categoryId) => {
    store.dispatch('equipment/updateCategory', categoryId);
    setSizeId(null);
    updateUrl();
}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    setSeriesId(null);
    updateUrl();
}

const setSeriesId = (seriesId) => {
    store.dispatch('equipment/updateSeriesId', seriesId)
    updateUrl();

    if (!selectedCategory.value || !selectedSize.value || !seriesActive.value) return;

    updateReportTable (selectedCategory.value, selectedSize.value, seriesActive.value);
    updateRepairTable (selectedCategory.value, selectedSize.value, seriesActive.value);
    updateTestsTable  (selectedCategory.value, selectedSize.value, seriesActive.value);
    updateHistoryTable(selectedCategory.value, selectedSize.value, seriesActive.value);
}


const updateRepairTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentRepair', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}

const updateReportTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentReport', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}

const updateHistoryTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentHistory', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}

const getLocationName = (id) => {
    const location = props.equipment_location.find(loc => loc.id === id);
    return location.name || 'Location not found';
}

const updateTestsTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentTest', {
        category_id: selectedCategory,
        size_id: selectedSize,
        series: seriesActive
    })
}


const updateUrl = () => {
    const params = {};

    if (selectedCategory.value) params.category_id = selectedCategory.value;
    if (selectedSize.value)     params.size_id     = selectedSize.value;
    if (seriesActive.value)     params.series      = seriesActive.value;

    Object.keys(params).length && router.get(route('equip.report', params));
};

const setMenuItem = (item) => {
    store.dispatch('equipment/updateMenuItem', item)
}

const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const menuActive = computed(() => store.getters['equipment/getMenuActiveItem']);
const equipment_categories = computed(() => store.getters['equipment/getEquipmentCategories'])
const equipment_sizes = computed(() => store.getters['equipment/getEquipmentSizes'])


const form = reactive({});
const statuses = {
    'new'         : 'Новое',
    'good'        : 'Хорошее',
    'satisfactory': 'Удовлетворительно',
    'bad'         : 'Плохое',
    'off'         : 'Списано',
    'unknown'     : 'Неизвестный статус'
};

const localSeriesActive = ref({
    title: seriesActive,
    value: seriesActive,
});

watch(localSeriesActive, ({ value: seriesId }) => {
    store.dispatch('equipment/updateSeriesId', seriesId)
    updateUrl();
    if (!selectedCategory.value || !selectedSize.value || !seriesActive.value) return;

    updateReportTable (selectedCategory.value, selectedSize.value, seriesActive.value);
    updateRepairTable (selectedCategory.value, selectedSize.value, seriesActive.value)
    updateTestsTable  (selectedCategory.value, selectedSize.value, seriesActive.value)
    updateHistoryTable(selectedCategory.value, selectedSize.value, seriesActive.value)
});

onMounted(() => {
    store.dispatch('fetchLocations')
    store.dispatch('equipment/updateEquipmentCategories', props.equipment_categories)
    store.dispatch('equipment/updateEquipmentCategoriesCounts', props.equipment_categories_counts)
    store.dispatch('equipment/updateEquipmentSizes', props.equipment_sizes)
    store.dispatch('equipment/updateEquipmentSizesCounts', props.equipment_sizes_counts)

    const url_params = new URLSearchParams(window.location.search);

    if (url_params.get('category_id') && +url_params.get('category_id') !== selectedCategory.value) setCategoryId(+url_params.get('category_id'));
    if (url_params.get('size_id')     && +url_params.get('size_id')     !== selectedSize.value)     setSizeId(+url_params.get('size_id'));
    if (url_params.get('series')      && url_params.get('series')       !== seriesActive.value)     setSeriesId(url_params.get('series'));
});



</script>

<template>
    <AuthenticatedLayout bg="white">
        <EquipNav />

        <div class="p-4 lg:p-5">
            <nav class="py-2.5 px-6 rounded-xl text-sm bg-my-gray">
                <div class="relative">
                    <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>

                    <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                        <li
                            :class="{ '!border-[#001D6C] text-[#001D6C]': selectedCategory === item.id }"
                            class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                            @click="setCategoryId(item.id)" v-for="item in equipment_categories" :key="item.id"
                        >
                            <Link
                                :href="route('equip.report', { category_id: item.id })"
                                class="flex items-center justify-between"
                            >
                                {{ item.name }}
                                <span
                                    class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text"
                                >
                                    {{ equipment_categories_counts[item.id] }}
                                </span>
                            </Link>
                        </li>
                    </ul>
                </div>
                <div class="relative mt-4">
                    <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                    <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                        <li
                            :class="{ '!border-[#001D6C] text-[#001D6C]': selectedSize === item.id }"
                            class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                            @click="setSizeId(item.id)" v-for="item in equipment_sizes" :key="item.id"
                        >
                            <Link class="flex items-center justify-between" :href="route('equip.report', { size_id: item.id })">
                                {{ item.name }}
                                <span
                                    class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text"
                                >
                                    {{ equipment_sizes_counts[item.id] }}
                                </span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="flex justify-between bg-my-gray mt-4 p-1 lg:hidden">
                <UiFieldSelect v-model="localSeriesActive" :items="get_equipment_series" :trigger-attrs="{ class: 'bg-white' }" class="w-[calc(50%-10px)]" />

                <div class="flex mx-2 items-center">
                    <svg width="5" height="34" viewBox="0 0 3 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="path-1-inside-1_2101_84576" fill="white">
                            <path d="M0.5 0H2.5V28H0.5V0Z" />
                        </mask>
                        <path d="M0.5 0H2.5V28H0.5V0Z" fill="white" />
                        <path
                            d="M0 0V1H1V0H0ZM0 3V5H1V3H0ZM0 7V9H1V7H0ZM0 11V13H1V11H0ZM0 15V17H1V15H0ZM0 19V21H1V19H0ZM0 23V25H1V23H0ZM0 27V28H1V27H0ZM-0.5 0V1H1.5V0H-0.5ZM-0.5 3V5H1.5V3H-0.5ZM-0.5 7V9H1.5V7H-0.5ZM-0.5 11V13H1.5V11H-0.5ZM-0.5 15V17H1.5V15H-0.5ZM-0.5 19V21H1.5V19H-0.5ZM-0.5 23V25H1.5V23H-0.5ZM-0.5 27V28H1.5V27H-0.5Z"
                            fill="#C1C7CD" mask="url(#path-1-inside-1_2101_84576)" />
                    </svg>

                </div>
                <form action="" method="GET" class="w-[calc(50%-10px)]">
                    <UiField class="w-full" :inp-attrs="{ placeholder: 'Поиск', class: 'bg-white' }">
                        <template #prepend>
                            <button type="submit">
                                <svg class="w-4 h-4" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </button>
                        </template>
                    </UiField>
                    <!-- <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" name="search" id="default-search"
                            class="block w-full ps-10 text-sm text-gray-900 border-b-2 border-gray-200 border-t-0 border-l-0 border-r-0 bg-white-50 py-4"
                            placeholder="Поиск">
                    </div> -->
                </form>
            </div>

            <div class="flex w-full mt-5">
                <ul class="hidden w-[100px] mr-3.5 lg:block">
                    <li
                        :class="{ 'pointer-events-none bg-my-gray': seriesActive === series }"
                        class="py-3 px-2 font-medium cursor-pointer border-b border-b-my-gray"
                        @click="setSeriesId(series)" v-for="series in equipment_series"
                    >
                        {{ series }}
                    </li>
                </ul>
                <div v-if="selectedCategory == 1 && seriesActive" class="w-full space-y-5 lg:w-[calc(100%-100px-14px)]">
                    <div v-if="props.equipment?.length">
                        <h3 class="font-bold text-gray1">Характеристики</h3>
                        <div class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1050px] text-xs">
                                <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[11.07%] py-2.5 px-2">Производитель</div>
                                    <div class="shrink-0 flex items-center justify-center w-[5.73%] py-2.5 px-2">Заход-<br>ность</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">Длина</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">Длина дс.</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">Статор <br>Ротор</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">Наработка</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">Наработка <br>дс.</div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.52%] py-2.5 px-2 text-center">Дата <br>изготовления</div>
                                    <div class="shrink-0 flex items-center justify-center w-[8.84%] py-2.5 px-2">Стоимость</div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.23%] py-2.5 px-2">Примечание</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-11.07%-5.73%-7.48%-7.48%-7.87%-7.87%-7.87%-9.52%-8.84%-9.23%-45px)] py-2.5 px-2">Состояние</div>
                                    <div class="shrink-0 flex items-center justify-center w-[45px] py-2.5 px-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z" fill="#21272A"/>
                                            <path d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z" fill="#21272A"/>
                                        </svg>
                                    </div>
                                </div>
                                <template v-for="item in equipment">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                        <a :href="item.hyperlink" class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M10.6666 13.3333L17.9999 6" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(11.07%-44px)] py-2.5 px-2">{{ item.manufactor ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[5.73%] py-2.5 px-2">{{ item.zahodnost ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">{{ item.length ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">{{ item.length_rezba ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">{{ item.stator_rotor ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">{{ item.operating ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">{{ item.narabotka_ds ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[9.52%] py-2.5 px-2">{{ item.manufactor_date ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[8.84%] py-2.5 px-2">{{ item.price ?? '-' }} ₽</div>
                                        <div class="shrink-0 flex items-center justify-center w-[9.23%] py-2.5 px-2">{{ item.notes ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-11.07%-5.73%-7.48%-7.48%-7.87%-7.87%-7.87%-9.52%-8.84%-9.23%-45px)] py-2.5 px-2">
                                            <span class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full bg-[#C9BFFF]"></span>
                                            {{ statuses[item.status] ?? '-' }}
                                        </div>
                                        <Link :href="'/directory/equipment/'+item.id" class="shrink-0 flex items-center justify-center w-[45px] py-2.5 px-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
                                            </svg>
                                        </Link>
                                    </div>
                                    <div class="flex">
                                        <div class="w-[11.07%] py-4 px-2.5">Комментарий:</div>
                                        <p class="py-4 px-2.5 border-l border-l-gray3">{{ item.commentary }}</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div v-if="repairs?.length">
                        <h3 class="font-bold text-gray1">История ремонта</h3>

                        <div class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1050px] text-xs">
                                <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[12.14%] py-2.5 px-2">Дата ремонта</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-12.14%-8.9%-50px)] py-2.5 px-2">Описание</div>
                                    <div class="shrink-0 flex items-center justify-center w-[8.9%] py-2.5 px-2">Расход</div>
                                    <div class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z" fill="#21272A"/>
                                            <path d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z" fill="#21272A"/>
                                        </svg>
                                    </div>
                                </div>
                                <template v-for="repair in repairs">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:last-child)]:border-r [&>*:not(:last-child)]:border-r-gray3 break-all">
                                        <a :href="repair.hyperlink" class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M10.6666 13.3333L17.9999 6" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(12.14%-44px)] py-2.5 px-2">{{ repair.repair_date ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-12.14%-8.9%-50px)] py-2.5 px-2">{{ repair.description ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[8.9%] py-2.5 px-2">{{ repair.expense ?? '-' }} ₽</div>
                                        <Link :href="'/directory/repairs/'+repair.id" class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
                                            </svg>
                                        </Link>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div v-if="tests?.length">
                        <h3 class="font-bold text-gray1">Стендовые испытания</h3>

                        <div class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1050px] text-xs">
                                <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[12.14%] py-2.5 px-2">Дата испытания</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-12.14%-8.9%-50px)] py-2.5 px-2">Примечание</div>
                                    <div class="shrink-0 flex items-center justify-center w-[8.9%] py-2.5 px-2">Расход</div>
                                    <div class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z" fill="#21272A"/>
                                            <path d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z" fill="#21272A"/>
                                        </svg>
                                    </div>
                                </div>
                                <template v-for="test in tests" :key="test.id">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:last-child)]:border-r [&>*:not(:last-child)]:border-r-gray3 break-all">
                                        <a :href="test.hyperlink" class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M10.6666 13.3333L17.9999 6" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(12.14%-44px)] py-2.5 px-2">{{ test.test_date ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-12.14%-8.9%-50px)] py-2.5 px-2">{{ test.description ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[8.9%] py-2.5 px-2">{{ test.expense ?? '-' }} ₽</div>
                                        <Link :href="'/directory/tests/'+test.id" class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
                                            </svg>
                                        </Link>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div v-if="history?.length">
                        <h3 class="font-bold text-gray1">История командировок</h3>

                        <div class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1050px] text-xs">
                                <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center w-[14.86%] py-2.5 px-2">Наименование</div>
                                    <div class="shrink-0 flex items-center w-[10.2%] py-2.5 px-2">Дата передачи</div>
                                    <div class="shrink-0 flex items-center justify-center w-[11.46%] py-2.5 px-2">Комплектация</div>
                                    <div class="shrink-0 flex items-center justify-center w-[10%] py-2.5 px-2">Наработка за <br>командировку</div>
                                    <div class="shrink-0 flex items-center justify-center w-[10%] py-2.5 px-2">Дата возврата</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-14.86%-10.2%-11.46%-10%-10%-10%-50px)] py-2.5 px-2">Причина вывоза</div>
                                    <div class="shrink-0 flex items-center justify-center w-[10%] py-2.5 px-2">Доход</div>
                                    <div class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z" fill="#21272A"/>
                                            <path d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z" fill="#21272A"/>
                                        </svg>
                                    </div>
                                </div>
                                <template v-for="history_item in history" :key="history_item.id">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                        <a :href="history_item.equipment.equipmentData.hyperlink" class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M10.6666 13.3333L17.9999 6" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        <div class="shrink-0 flex items-center w-[14.86%] py-2.5 px-2">{{ history_item.contragent.name ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[10.2%] py-2.5 px-2">{{ history_item.service.shipping_date ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[11.46%] py-2.5 px-2">
                                            {{ history_item.equipment.category ?? '-' }} -
                                            {{ history_item.equipment.size ?? '-' }} -
                                            {{ seriesActive ?? '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[10%] py-2.5 px-2">{{ history_item.service.operating ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[10%] py-2.5 px-2">{{ history_item.service.return_date ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-14.86%-10.2%-11.46%-10%-10%-10%-50px)] py-2.5 px-2">{{ history_item.contragent.reason ?? '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[10%] py-2.5 px-2">{{ history_item.service.income ?? '-' }}</div>
                                        <Link :href="'/directory/services/'+history_item.service.id" class="shrink-0 flex items-center justify-center w-[45px] py-2.5 px-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
                                            </svg>
                                        </Link>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="selectedCategory == 2" class="flex-col sm:pb-20 lg:pt-4 sm:overflow-x-auto flex-3 ">
                    <div class="flex flex-col overflow-x-auto">
                        <h3 class="text-side-gray-text font-bold">Характеристики</h3>
                        <div class="sm:overflow-x-auto">
                            <table class=" w-full mt-2 border-2 border-gray lg:table-fixed sm:table-auto ">
                                <thead class="bg-my-gray">
                                    <tr class="py-3">
                                        <th class="py-3 px-2 text-center sm:text-sm">Производитель</th>
                                        <th class="py-3 px-2 text-center sm:text-sm">Длина</th>
                                        <th class="py-3 px-2 text-center sm:text-sm">Наработка</th>
                                        <th class="py-3 px-2 text-center sm:text-sm">Дата изготовления</th>
                                        <th class="py-3 px-2 text-center sm:text-sm">Стоимость</th>
                                        <th class="py-3 px-2 text-center sm:text-sm">Примечание</th>
                                        <th class="py-3 px-2 text-center sm:text-sm">Состояние</th>
                                        <th class="flex justify-center items-center pt-2 pr-2"><svg width="30"
                                                height="30" viewBox="0 0 24 24" fill="none"
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
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-my-gray whitespace-nowrap" v-for="item in equipment">
                                        <td class="text-center border border-slate-200 p-2">
                                            <div class="flex justify-start ">
                                                <svg width="20" height="20" viewBox="0 0 14 14" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10.9999 7.66706V11.667C10.9999 12.0206 10.8594 12.3598 10.6094 12.6098C10.3594 12.8599 10.0202 13.0003 9.6666 13.0003H2.33332C1.9797 13.0003 1.64057 12.8599 1.39052 12.6098C1.14047 12.3598 1 12.0206 1 11.667V4.33375C1 3.98013 1.14047 3.641 1.39052 3.39095C1.64057 3.1409 1.9797 3.00043 2.33332 3.00043H6.33329"
                                                        stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M9 1H13V4.99997" stroke="#808192" stroke-width="1.2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M5.66663 8.33328L12.9999 1" stroke="#808192"
                                                        stroke-width="1.2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <div class="ml-4">
                                                    {{ item.manufactor }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center border border-slate-200 p-2">{{ item.length }}</td>
                                        <td class="text-center border border-slate-200 p-2">{{ item.operating }}
                                        </td>

                                        <td class="text-center border border-slate-200 p-2">{{ item.manufactor_date }}
                                        </td>
                                        <td class="text-center border border-slate-200 p-2">{{ item.price }}</td>
                                        <td class="text-center border border-slate-200 p-2 overflow-hidden text-ellipsis whitespace-nowrap">{{ item.notes }}</td>
                                        <td class="text-center border border-slate-200 p-2">
                                            <EquipStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />
                                        </td>
                                        <td
                                            class="text-center border flex items-center justify-between border-slate-200 p-2">
                                            <div class="mr-2"><svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                        fill="#21272A" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                        fill="#21272A" />
                                                </svg></div>
                                            <div class="ml-2">


                                                <Menu as="div" class="relative inline-block text-left">
                                                    <div class="sm:overflow-x-visible">
                                                        <MenuButton
                                                            class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                                                            <svg width="4" height="16" viewBox="0 0 4 16" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M3.5 2C3.5 2.82843 2.82843 3.5 2 3.5C1.17157 3.5 0.5 2.82843 0.5 2C0.5 1.17157 1.17157 0.5 2 0.5C2.82843 0.5 3.5 1.17157 3.5 2Z"
                                                                    fill="#687182" />
                                                                <path
                                                                    d="M3.5 8C3.5 8.82843 2.82843 9.5 2 9.5C1.17157 9.5 0.5 8.82843 0.5 8C0.5 7.17157 1.17157 6.5 2 6.5C2.82843 6.5 3.5 7.17157 3.5 8Z"
                                                                    fill="#687182" />
                                                                <path
                                                                    d="M3.5 14C3.5 14.8284 2.82843 15.5 2 15.5C1.17157 15.5 0.5 14.8284 0.5 14C0.5 13.1716 1.17157 12.5 2 12.5C2.82843 12.5 3.5 13.1716 3.5 14Z"
                                                                    fill="#687182" />
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
                                                                <Link :href="route('contragents.create')"
                                                                    class="flex items-center justify-around"
                                                                    :class="['block px-4 py-2 text-sm']">Создать
                                                                контрагента
                                                                <svg width="16" height="16" viewBox="0 0 16 16"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M8.74999 2.75004C8.74999 2.33582 8.4142 2.00004 7.99999 2.00004C7.58578 2.00004 7.24999 2.33582 7.24999 2.75004V7.25H2.75C2.33579 7.25 2 7.58579 2 8C2 8.41422 2.33579 8.75 2.75 8.75H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75H13.25C13.6642 8.75 14 8.41422 14 8C14 7.58579 13.6642 7.25 13.25 7.25H8.74999V2.75004Z"
                                                                        fill="#464F60" />
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
                                    <tr>
                                        <td colspan="2" class="p-4 border-r-2 bg-gray-100">
                                            Комментарий:
                                        </td>
                                        <td colspan="6" v-for="item in equipment" class="px-6 py-2 bg-gray-100">
                                            <p class="ml-5 whitespace-normal break-words">{{ item.commentary }}</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="lg:pt-4 sm:overflow-x-auto">
                            <h3 class="text-side-gray-text font-bold">История ремонта</h3>

                            <table class="table-auto w-full border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="border px-4 py-2 text-left text-gray-600">Дата ремонта</th>
                                        <th class="border px-4 py-2 text-left text-gray-600">Место проведения</th>
                                        <th class="border px-4 py-2 text-left text-gray-600">Описание</th>
                                        <th class="border px-4 py-2 text-left text-gray-600">Расход</th>
                                        <th class="border px-4 py-2 text-left text-gray-600"><svg width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="repair in repairs" :key="repair.id">
                                        <td class="border px-4 py-2">{{ repair.repair_date }}</td>
                                        <td class="border px-4 py-2">{{ getLocationName(repair.location_id) }}</td>
                                        <td class="border px-4 py-2">{{ repair.description }}</td>
                                        <td class="border px-4 py-2">{{ repair.expense }}</td>
                                        <td class="border px-4 py-2 flex justify-center items-center">
                                            <svg class="w-7 h-7 text-gray-500 " fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 12H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                            </svg>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="lg:pt-4 sm:overflow-x-auto">
                            <h3 class="text-side-gray-text font-bold"> Стендовые испытания</h3>

                            <table class="table-auto w-full border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="border px-4 py-2 text-left text-gray-600">Дата испытания</th>
                                        <th class="border px-4 py-2 text-left text-gray-600">Место проведения</th>
                                        <th class="border px-4 py-2 text-left text-gray-600">Описание</th>
                                        <th class="border px-4 py-2 text-left text-gray-600">Расход</th>
                                        <th class="border px-4 py-2 text-left text-gray-600 flex justify-center"><svg
                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
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
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="test in tests" :key="test.id">
                                        <td class="border px-4 py-2">{{ test.test_date }}</td>
                                        <td class="border px-4 py-2">{{ getLocationName(test.location_id) }}</td>
                                        <td class="border px-4 py-2">{{ test.description }}</td>
                                        <td class="border px-4 py-2">{{ test.expense }}</td>
                                        <td class="border px-4 py-2 flex justify-center text-center items-center">

                                            <svg class="w-7 h-7 text-gray-500 ml-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 12H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                            </svg>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
