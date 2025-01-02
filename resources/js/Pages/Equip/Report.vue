<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { EquipMenuItems } from '../../../constants/index';
import { Link, router } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref, watch } from 'vue';


import store from '../../../store/index';
import EquipNav from '@/Components/Equip/EquipNav.vue';
import UiFieldSelect from '@/Components/Ui/UiFieldSelect.vue';
import UiField from '@/Components/Ui/UiField.vue';
import EquipFilter from "@/Components/Equip/EquipFilter.vue";
import UiHyperlink from "@/Components/Ui/UiHyperlink.vue";

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
    if (selectedCategory.value) setSizeId(null);
    store.dispatch('equipment/updateCategory', categoryId);
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


const form = reactive({
    hyperlink: ''
});

const statuses = {
    'new'         : 'Новое',
    'good'        : 'Хорошее',
    'satisfactory': 'Удовлетворительно',
    'bad'         : 'Плохое',
    'off'         : 'Списано',
    'unknown'     : 'Неизвестный статус'
};
const statuses_colors = {
    'new'         : 'bg-[#0F62FE]',
    'good'        : 'bg-[#31C246]',
    'satisfactory': 'bg-[#DAC41E]',
    'bad'         : 'bg-[#DA1E28]',
    'off'         : 'bg-[#000000]',
    'unknown'     : 'bg-[#C0C0C0]'
};

const localSeriesActive = ref({
    title: seriesActive,
    value: seriesActive,
});

function submitHyperlink(id, data) {
    router.post(`/equipment/${id}/hyperlink`, {
        'hyperlink': form.hyperlink
    });
    form.hyperlink = '';
}

watch(localSeriesActive, ({ value: seriesId }) => {
    store.dispatch('equipment/updateSeriesId', seriesId)
    updateUrl();
});

onMounted(() => {
    store.dispatch('fetchLocations')
    store.dispatch('equipment/updateEquipmentCategories', props.equipment_categories)
    store.dispatch('equipment/updateEquipmentCategoriesCounts', props.equipment_categories_counts)
    store.dispatch('equipment/updateEquipmentSizes', props.equipment_sizes)
    store.dispatch('equipment/updateEquipmentSizesCounts', props.equipment_sizes_counts)

    const url_params = new URLSearchParams(window.location.search);

    const fetch_by = {};

    if (url_params.get('size_id')) {
        fetch_by.size_id = +url_params.get('size_id');
        store.dispatch('equipment/updateSize', fetch_by.size_id);
    } else {
        store.dispatch('equipment/updateSize', null);
    }
    if (url_params.get('series'))  {
        fetch_by.series  = url_params.get('series');
        store.dispatch('equipment/updateSeriesId', fetch_by.series);
    } else {
        store.dispatch('equipment/updateSeriesId', null);
    }

    if (url_params.get('category_id')) {
        fetch_by.category_id = +url_params.get('category_id');
        store.dispatch('equipment/updateCategory', +url_params.get('category_id'));
    } else {
        fetch_by.category_id = 1;
        store.dispatch('equipment/updateCategory', 1);
    }

    updateReportTable (fetch_by.category_id, fetch_by?.size_id, fetch_by?.series);
    updateRepairTable (fetch_by.category_id, fetch_by?.size_id, fetch_by?.series);
    updateTestsTable  (fetch_by.category_id, fetch_by?.size_id, fetch_by?.series);
    updateHistoryTable(fetch_by.category_id, fetch_by?.size_id, fetch_by?.series);

    store.dispatch('equipment/updateMenuItem', EquipMenuItems.REPORT);
});

</script>

<template>
    <AuthenticatedLayout bg="white">
        <EquipNav />

        <div class="p-4 lg:p-5">
            <EquipFilter
                :selected-category="selectedCategory"
                :selected-size="selectedSize"
                :categories-counts="equipment_categories_counts"
                :sizes-counts="equipment_sizes_counts"
                :categories="equipment_categories"
                :sizes="equipment_sizes"
                @category-click="cat_id => setCategoryId(cat_id)"
                @size-click="size_id => setSizeId(size_id)"
            />

            <div class="flex justify-between bg-my-gray mt-4 p-1 lg:hidden">
                <UiFieldSelect
                    v-model="localSeriesActive"
                    :items="get_equipment_series"
                    :trigger-attrs="{ class: 'bg-white' }"
                    :disabled="!get_equipment_series.length"
                    placeholder="Номер"
                    class="w-[calc(50%-10px)]"
                />

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
                    <UiField
                        :inp-attrs="{ placeholder: 'Поиск', class: 'bg-white' }"
                        :disabled="!!!seriesActive"
                        class="w-full"
                    >
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
                </form>
            </div>

            <div v-if="selectedCategory" class="flex w-full mt-5">
                <ul class="hidden w-[100px] mr-3.5 lg:block">
                    <li
                        v-for="series in equipment_series"
                        :class="{ 'pointer-events-none bg-my-gray': seriesActive === series }"
                        class="py-3 px-2 font-medium cursor-pointer border-b border-b-my-gray"
                        @click="setSeriesId(series)"
                    >
                        {{ series }}
                    </li>
                </ul>

                <div v-if="seriesActive" class="w-full space-y-5 lg:w-[calc(100%-100px-14px)]">
                    <div v-if="props.equipment?.length">
                        <h3 class="font-bold text-gray1">Характеристики</h3>

                        <div v-if="selectedCategory === 2" class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1050px] text-xs">
                                <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[11.07%] py-2.5 px-2">Производитель</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">Длина</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">Наработка</div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.53%] py-2.5 px-2 text-center">Дата <br>изготовления</div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.23%] py-2.5 px-2">Стоимость</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-11.07%-7.48%-7.87%-9.53%-9.23%-11.66%-45px)] py-2.5 px-2">Примечание</div>
                                    <div class="shrink-0 flex items-center w-[11.66%] py-2.5 px-2">Состояние</div>
                                    <div class="shrink-0 flex items-center justify-center w-[45px] py-2.5 px-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z" fill="#21272A"/>
                                            <path d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z" fill="#21272A"/>
                                        </svg>
                                    </div>
                                </div>
                                <template v-for="item in props.equipment">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink" endpoint="/equipment" />
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(11.07%-44px)] py-2.5 px-2">{{ item.manufactor || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">{{ item.length || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">{{ item.operating || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[9.53%] py-2.5 px-2">{{ item.manufactor_date || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[9.23%] py-2.5 px-2">{{ item.price || '-' }} ₽</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-11.07%-7.48%-7.87%-9.53%-9.23%-11.66%-45px)] py-2.5 px-2">{{ item.notes || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[11.66%] py-2.5 px-2">
                                            <span :class="statuses_colors[item.status]" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                            <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses[item.status] || '-' }}</span>
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
                        <div
                            v-else-if="[3,4,5].includes(selectedCategory)"
                            class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3"
                        >
                            <div class="min-w-[1050px] text-xs">
                                <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[14.86%] py-2.5 px-2">Производитель</div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.89%] py-2.5 px-2">Диаметр</div>
                                    <div class="shrink-0 flex items-center justify-center w-[6.89%] py-2.5 px-2">Длина</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">Длина с <br>резьбой</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">Резьбы</div>
                                    <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">Наработка</div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.52%] py-2.5 px-2 text-center">Дата <br>изготовления</div>
                                    <div class="shrink-0 flex items-center justify-center w-[9.23%] py-2.5 px-2">Стоимость</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-14.86%-6.89%-6.89%-7.48%-7.87%-7.87%-9.52%-9.23%-11.66%-50px)] py-2.5 px-2">Примечание</div>
                                    <div class="shrink-0 flex items-center w-[11.66%] py-2.5 px-2">Состояние</div>
                                    <div class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z" fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z" fill="#21272A"/>
                                            <path d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z" fill="#21272A"/>
                                        </svg>
                                    </div>
                                </div>
                                <template v-for="item in props.equipment">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink" endpoint="/equipment" />
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(14.86%-44px)] py-2.5 px-2">{{ item.manufactor || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[6.89%] py-2.5 px-2">{{ item.diameter || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[6.89%] py-2.5 px-2">{{ item.length || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">{{ item.length_rezba || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">{{ item.rezbi || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">{{ item.operating || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[9.52%] py-2.5 px-2">{{ item.manufactor_date || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[9.23%] py-2.5 px-2">{{ item.price || '-' }} ₽</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-14.86%-6.89%-6.89%-7.48%-7.87%-7.87%-9.52%-9.23%-11.66%-50px)] py-2.5 px-2">{{ item.notes || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[11.66%] py-2.5 px-2">
                                            <span :class="statuses_colors[item.status]" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                            <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses[item.status] || '-' }}</span>
                                        </div>
                                        <Link :href="'/directory/equipment/'+item.id" class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
                                            </svg>
                                        </Link>
                                    </div>
                                    <div class="flex">
                                        <div class="w-[14.86%] py-4 px-2.5">Комментарий:</div>
                                        <p class="py-4 px-2.5 border-l border-l-gray3">{{ item.commentary }}</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div v-else class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
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
                                <template v-for="item in props.equipment">
                                    <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <UiHyperlink :item-id="item.id" :hyperlink="item.hyperlink" endpoint="/equipment" />
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(11.07%-44px)] py-2.5 px-2">{{ item.manufactor || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[5.73%] py-2.5 px-2">{{ item.zahodnost || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">{{ item.length || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">{{ item.length_rezba || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">{{ item.stator_rotor || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">{{ item.operating || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.87%] py-2.5 px-2">{{ item.narabotka_ds || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[9.52%] py-2.5 px-2">{{ item.manufactor_date || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[8.84%] py-2.5 px-2">{{ item.price || '-' }} ₽</div>
                                        <div class="shrink-0 flex items-center justify-center w-[9.23%] py-2.5 px-2">{{ item.notes || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-11.07%-5.73%-7.48%-7.48%-7.87%-7.87%-7.87%-9.52%-8.84%-9.23%-45px)] py-2.5 px-2">
                                            <span :class="statuses_colors[item.status]" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                            <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses[item.status] || '-' }}</span>
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
                                    <div class="shrink-0 flex items-center justify-center w-[14.86%] py-2.5 px-2">Дата ремонта</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-14.86%-8.9%-50px)] py-2.5 px-2">Описание</div>
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
                                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <UiHyperlink :item-id="repair.id" :hyperlink="repair.hyperlink" endpoint="/equipment/repair" />
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(14.86%-44px)] py-2.5 px-2">{{ repair.repair_date || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-14.86%-8.9%-50px)] py-2.5 px-2">{{ repair.description || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[8.9%] py-2.5 px-2">{{ repair.expense || '-' }} ₽</div>
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

                        <div v-if="selectedCategory === 2" class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1050px] text-xs">
                                <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[14.86%] py-2.5 px-2">Дата испытания</div>
                                    <div class="shrink-0 flex items-center w-[8.16%] py-2.5 px-2">Мех. Вверх</div>
                                    <div class="shrink-0 flex items-center w-[7.48%] py-2.5 px-2">Мех. Вниз</div>
                                    <div class="shrink-0 flex items-center w-[5.92%] py-2.5 px-2">Усилие</div>
                                    <div class="shrink-0 flex items-center w-[7.48%] py-2.5 px-2 text-center">Время задержки</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-14.86%-8.16%-7.48%-5.92%-7.48%-8.9%-50px)] py-2.5 px-2">Примечание</div>
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
                                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <UiHyperlink :item-id="test.id" :hyperlink="test.hyperlink" endpoint="/equipment/test" />
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(14.86%-44px)] py-2.5 px-2">{{ test.test_date || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[8.16%] py-2.5 px-2">{{ test.mex_vverx || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">{{ test.mex_vniz || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[5.92%] py-2.5 px-2">{{ test.usilie || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[7.48%] py-2.5 px-2">{{ test.delay || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-14.86%-8.16%-7.48%-5.92%-7.48%-8.9%-50px)] py-2.5 px-2">{{ test.description || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[8.9%] py-2.5 px-2">{{ test.expense || '-' }} ₽</div>
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
                        <div v-else class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1050px] text-xs">
                                <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[14.86%] py-2.5 px-2">Дата испытания</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-14.86%-8.9%-50px)] py-2.5 px-2">Примечание</div>
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
                                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <UiHyperlink :item-id="test.id" :hyperlink="test.hyperlink" endpoint="/equipment/test" />
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(14.86%-44px)] py-2.5 px-2">{{ test.test_date || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-14.86%-8.9%-50px)] py-2.5 px-2">{{ test.description || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[8.9%] py-2.5 px-2">{{ test.expense || '-' }} ₽</div>
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
                                        <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                            <UiHyperlink :item-id="history_item.id" :hyperlink="history_item.hyperlink" endpoint="/equipment/history" />
                                        </div>
                                        <div class="shrink-0 flex items-center w-[calc(14.86%-44px)] py-2.5 px-2">{{ history_item.contragent.name || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[10.2%] py-2.5 px-2">{{ history_item.service.shipping_date || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[11.46%] py-2.5 px-2">
                                            {{ history_item.equipment.category || '-' }} -
                                            {{ history_item.equipment.size || '-' }} -
                                            {{ seriesActive || '-' }}
                                        </div>
                                        <div class="shrink-0 flex items-center justify-center w-[10%] py-2.5 px-2">{{ history_item.service.operating || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[10%] py-2.5 px-2">{{ history_item.service.return_date || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-14.86%-10.2%-11.46%-10%-10%-10%-50px)] py-2.5 px-2">{{ history_item.contragent.reason || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[10%] py-2.5 px-2">{{ history_item.service.income || '-' }}</div>
                                        <Link :href="'/directory/services/'+history_item.service.id" class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
