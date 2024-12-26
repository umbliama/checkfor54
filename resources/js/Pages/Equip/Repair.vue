<script setup>
import EquipNav from '@/Components/Equip/EquipNav.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import store from '../../../store/index';
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { MenuItem, Menu, MenuItems, MenuButton } from '@headlessui/vue';
import EquipFilter from "@/Components/Equip/EquipFilter.vue";
import { EquipMenuItems } from "../../../constants/index.js";
import UiFieldSelect from "@/Components/Ui/UiFieldSelect.vue";
import UiField from "@/Components/Ui/UiField.vue";
import {
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuPortal,
  DropdownMenuRoot,
  DropdownMenuTrigger,
} from 'radix-vue';
import { PopoverAnchor, PopoverArrow, PopoverClose, PopoverContent, PopoverPortal, PopoverRoot, PopoverTrigger } from 'radix-vue'

const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const menuActive = computed(() => store.getters['equipment/getMenuActiveItem']);
const repairs = computed(() => store.getters['equipment/getEquipmentRepairs'])
const seriesActive = computed(() => store.getters['equipment/getSeriesActive'])

const localSeriesActive = ref({
    title: seriesActive,
    value: seriesActive,
});

const setCategoryId = (categoryId) => {
    if (selectedCategory.value) setSizeId(null);
    store.dispatch('equipment/updateCategory', categoryId);
    updateUrl();
}

const getLocationName = (id) => {
    const location = props.equipment_location.find(loc => loc.id === id);
    return location ? location.name : 'Location not found';

}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    setSeriesId(null);
    updateUrl();
}

/*const setSeriesId = (seriesId) => {
    store.dispatch('equipment/updateSeriesId', seriesId)
    if (selectedCategory.value && selectedSize.value && seriesActive.value) {
        console.log(selectedSize.value, seriesActive.value)
        updateUrl()
        updateRepairTable(selectedCategory.value, selectedSize.value, seriesActive.value);
    }
}*/

const setSeriesId = (seriesId) => {
    store.dispatch('equipment/updateSeriesId', seriesId)
    updateUrl();

    if (!selectedCategory.value) return;

    updateRepairTable(selectedCategory.value, selectedSize.value, seriesActive.value);
}

const updateRepairTable = (selectedCategory, selectedSize, seriesActive) => {
    store.dispatch('equipment/updateEquipmentRepair', {
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

    Object.keys(params).length && router.get(route('equip.repair', params));
};

/*const updateUrl = () => {
    if (selectedCategory.value && selectedSize.value) {
        router.get(route('equip.repair', { category_id: selectedCategory.value, size_id: selectedSize.value, series: seriesActive.value }));
    }
};*/

const props = defineProps({
    equipmentSeries: Array,
    equipment_location: Array,
    equipment_repairs: Object,
    equipment_sizes: Array,
    equipment_categories_counts: Object,
    equipment_sizes_counts: Object,
    equipment_categories: Array
})

const get_equipmentSeries = computed(() => {
    return props.equipmentSeries.map(s => ({ title: s, value: s }));
});

const form = reactive({
    'repair_date': null,
    'location_id': null,
    'expense': null,
    'description': null,
    'category_id': null,
    'size_id': null,
    'series': null
})

const hyperlink_form = reactive({
    'hyperlink': null
})

watch(localSeriesActive, ({ value: seriesId }) => {
    store.dispatch('equipment/updateSeriesId', seriesId);
    updateUrl();
    if (!selectedCategory.value) return;

    updateRepairTable (selectedCategory.value, selectedSize.value, seriesActive.value);
});

function submit() {
    router.post('/equip/repair', {
        repair_date: form.repair_date,
        location_id: form.location_id,
        expense: form.expense,
        description: form.description,
        category_id: selectedCategory.value,
        size_id: selectedSize.value,
        series: seriesActive.value
    })
    updateRepairTable(selectedCategory.value, selectedSize.value, seriesActive.value);
}

function submitHyperlink(id, data) {
    router.post(`/equipment/${id}/hyperlink`, {
        'hyperlink': data
    });
}

onMounted(() => {
    store.dispatch('fetchLocations')
    store.dispatch('equipment/updateEquipmentCategories', props.equipment_categories)
    store.dispatch('equipment/updateEquipmentCategoriesCounts', props.equipment_categories_counts)
    store.dispatch('equipment/updateEquipmentSizes', props.equipment_sizes)
    store.dispatch('equipment/updateEquipmentSizesCounts', props.equipment_sizes_counts)

    const url_params = new URLSearchParams(window.location.search);

    if (url_params.get('category_id')) {
        if (+url_params.get('category_id') !== selectedCategory.value) setCategoryId(+url_params.get('category_id'));
    } else setCategoryId(null);

    if (url_params.get('size_id')) {
        if (+url_params.get('size_id') !== selectedSize.value) setSizeId(+url_params.get('size_id'));
    }


    if (url_params.get('size_id')     && url_params.get('category_id') && !url_params.get('series')) setSeriesId(props.equipmentSeries[0]);
    if (url_params.get('series')      && url_params.get('series')       !== seriesActive.value)      setSeriesId(url_params.get('series'));

    store.dispatch('equipment/updateMenuItem', EquipMenuItems.REPAIR);
});
</script>

<template>
    <AuthenticatedLayout>
        <EquipNav></EquipNav>

        <div class="p-5">
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
                    :items="get_equipmentSeries"
                    :trigger-attrs="{ class: 'bg-white' }"
                    :disabled="!!!seriesActive"
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

            <div v-if="selectedCategory" class="flex mt-5">
                <ul class="hidden w-[100px] mr-3.5 lg:block">
                    <li :class="{ 'pointer-events-none bg-my-gray': seriesActive === series }"
                        class="py-3 px-2 font-medium cursor-pointer border-b border-b-my-gray"
                        @click="setSeriesId(series)" v-for="series in equipmentSeries">{{ series }}</li>
                </ul>
                <div v-if="seriesActive" class="w-full space-y-5 lg:w-[calc(100%-100px-14px)]">
                    <div class="font-bold text-gray1">Добавить новую запись ремонта</div>
                    <div class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                        <div class="min-w-[1050px] text-xs">
                            <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                <div class="shrink-0 flex items-center justify-center w-[12.14%] py-2.5 px-2">Дата ремонта</div>
                                <div class="shrink-0 flex items-center w-[8.94%] py-2.5 px-2">Место проведения</div>
                                <div class="shrink-0 flex items-center w-[calc(100%-12.14%-8.94%-8.94%-50px)] py-2.5 px-2">Описание</div>
                                <div class="shrink-0 flex items-center justify-center w-[8.94%] py-2.5 px-2">Расход</div>
                                <div class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z" fill="#21272A"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z" fill="#21272A"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z" fill="#21272A"/>
                                        <path d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z" fill="#21272A"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex border-b border-b-gray3 [&>*:not(:last-child)]:border-r [&>*:not(:last-child)]:border-r-gray3 break-all">
                                <div class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10.6666 13.3333L17.9999 6" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="shrink-0 flex items-center justify-center w-[calc(12.14%-44px)]">
                                    <input
                                        v-model="form.repair_date"
                                        type="date"
                                        class="block w-full h-full px-2 bg-transparent"
                                        onclick="this.showPicker()"
                                    />
                                </div>
                                <div class="shrink-0 flex items-center w-[8.94%]">
                                    <select v-model="form.location_id"
                                        class="w-full h-full border-none rounded px-2 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option v-for="location in equipment_location" :value="location.id">
                                            {{ location.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="shrink-0 flex items-center w-[calc(100%-12.14%-8.94%-8.94%-50px)]">
                                    <input v-model="form.description" type="text" class="block w-full h-full px-2 bg-transparent">
                                </div>
                                <div class="shrink-0 flex items-center justify-center w-[8.94%]">
                                    <input v-model="form.expense" type="number" class="block w-full h-full px-2 bg-transparent">
                                </div>
                                <div class="shrink-0 flex items-center justify-center w-[50px] py-2.5 px-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button
                            class="inline-flex items-center justify-center py-3 px-7 font-medium tracking-wider bg-my-gray text-side-gray-text"
                            @click="submit"
                        >
                            Сохранить
                        </button>
                    </div>

                    <template v-if="repairs">
                        <div class="mt-20 font-bold text-gray1">История ремонта</div>
                        <div class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                            <div class="min-w-[1050px] text-xs">
                                <div class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                                    <div class="shrink-0 flex items-center justify-center w-[12.14%] py-2.5 px-2">Дата ремонта</div>
                                    <div class="shrink-0 flex items-center w-[8.94%] py-2.5 px-2">Место проведения</div>
                                    <div class="shrink-0 flex items-center w-[calc(100%-12.14%-8.94%-8.94%-50px)] py-2.5 px-2">Описание</div>
                                    <div class="shrink-0 flex items-center justify-center w-[8.94%] py-2.5 px-2">Расход</div>
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
                                        <PopoverRoot>
                                            <PopoverTrigger class="shrink-0 flex items-center justify-center w-[44px] py-2.5 px-2">
                                                <svg v-if="repair.hyperlink" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14 6H18V9.99997" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M10.6667 13.3333L18 6" stroke="#644DED" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </PopoverTrigger>
                                            <PopoverPortal>
                                                <PopoverContent
                                                    align="start"
                                                    class="min-w-[137px] py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                >
                                                    <template v-if="repair.hyperlink">
                                                        <ul>
                                                            <li>
                                                                <Link
                                                                    :href="repair.hyperlink"
                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                                >
                                                                    Открыть
                                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z" fill="#464F60"/>
                                                                    </svg>
                                                                </Link>
                                                            </li>
                                                            <li>
                                                                <PopoverRoot>
                                                                    <PopoverTrigger class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                        Редактировать
                                                                        <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                                            <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                                        </svg>
                                                                    </PopoverTrigger>
                                                                    <PopoverPortal>
                                                                        <PopoverContent
                                                                            align="start"
                                                                            class="min-w-[137px] py-4 px-3 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                                        >
                                                                            <UiField v-model="hyperlink_form.hyperlink" :inp-attrs="{ placeholder: 'www.site.ru' }" size="sm" />
                                                                            <PopoverClose>
                                                                                <button
                                                                                    class="inline-block mt-2 font-bold text-xs text-violet-full"
                                                                                    @click="submitHyperlink(repair.id, hyperlink_form.hyperlink)"
                                                                                >Сохранить</button>
                                                                            </PopoverClose>
                                                                        </PopoverContent>
                                                                    </PopoverPortal>
                                                                </PopoverRoot>
                                                            </li>
                                                            <li>
                                                                <Link
                                                                    :href="repair.hyperlink"
                                                                    class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all"
                                                                >
                                                                    Удалить
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z" fill="#DC4067"/>
                                                                    </svg>
                                                                </Link>
                                                            </li>
                                                        </ul>
                                                    </template>
                                                    <template v-else>
                                                        <UiField v-model="hyperlink_form.hyperlink" :inp-attrs="{ placeholder: 'www.site.ru' }" size="sm" />
                                                        <PopoverClose>
                                                            <button
                                                                class="inline-block mt-2 font-bold text-xs text-violet-full"
                                                                @click="submitHyperlink(repair.id, hyperlink_form.hyperlink)"
                                                            >Сохранить</button>
                                                        </PopoverClose>
                                                    </template>
                                                </PopoverContent>
                                            </PopoverPortal>
                                        </PopoverRoot>
                                        <div class="shrink-0 flex items-center justify-center w-[calc(12.14%-44px)] py-2.5 px-2">{{ repair.repair_date || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[8.94%] py-2.5 px-2">{{ getLocationName(repair.location_id) || '-' }}</div>
                                        <div class="shrink-0 flex items-center w-[calc(100%-12.14%-8.94%-8.94%-50px)] py-2.5 px-2">{{ repair.description || '-' }}</div>
                                        <div class="shrink-0 flex items-center justify-center w-[8.94%] py-2.5 px-2">{{ repair.expense || '-' }} ₽</div>
                                        <div class="shrink-0 flex items-center w-[100px] py-2.5 px-2">
                                            <Link :href="'/directory/repairs/'+repair.id" class="mr-3.5">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z" fill="#21272A"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65799 7.03955C4.91229 6.71259 5.3835 6.65369 5.71046 6.90799L12 11.7999L18.2895 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5963 7.36651 19.5374 7.83772 19.2105 8.09202L12.4605 13.342C12.1896 13.5527 11.8104 13.5527 11.5395 13.342L4.78955 8.09202C4.46259 7.83772 4.40369 7.36651 4.65799 7.03955Z" fill="#21272A"/>
                                                </svg>
                                            </Link>
                                            <DropdownMenuRoot>
                                                <DropdownMenuTrigger aria-label="Customise options">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z" fill="#687182"/>
                                                        <path d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z" fill="#687182"/>
                                                        <path d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z" fill="#687182"/>
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
                                                                    :href="route('equip.edit', repair.id)"
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
                                                                    :href="route('equip.destroy', repair.id)"
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
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

<!--        <div class="container md:mx-auto sm:mx-auto">
            <nav class="bg-my-gray m-5 rounded-xl">
                <div class="max-w-screen-xl py-2 px-4 ">
                    <div class="mt-6 flex items-center">
                        <ul
                            class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-md w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                                @click="setCategoryId(item.id)" v-for="item in equipment_categories" :key="item.id">
                                <Link class="flex items-center justify-around"
                                    :href="route('equip.repair', { category_id: item.id })">
                                {{ equipment_categories[item.id - 1].name }}
                                <span
                                    class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                    {{ equipment_categories_counts[item.id] }}
                                </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 sm:overflow-x-auto flex items-center">
                        <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li class="text-my-nav-text text-lg w-full"
                                :class="{ 'border-b-2 border-blue-600': selectedSize === item.id }"
                                @click="setSizeId(item.id)" v-for="item in equipment_sizes" :key="item.id">
                                <Link class="flex items-center justify-around"
                                    :href="route('equip.repair', { size_id: item.id })">
                                {{ item.name }}
                                <span
                                    class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                    {{ equipment_sizes_counts[item.id] }}
                                </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="lg:hidden md:hidden flex bg-my-gray  p-4">

                <select v-model="seriesActive" class="text-gray-500 border-gray-200" name="" id="">
                    <option value="">Номер</option>
                    <option v-for="series in equipmentSeries" @click="setSeriesId(series)" :value="series">{{ series }}
                    </option>
                </select>
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
                <form action="" method="GET" class="max-w-md">
                    <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Search</label>
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
                    </div>
                </form>
            </div>

            <div class="flex m-5">
                <ul class="hidden w-[100px] mr-3.5 lg:block">
                    <li :class="{ 'pointer-events-none bg-my-gray': seriesActive === series }"
                        class="py-3 px-2 font-medium cursor-pointer border-b border-b-my-gray"
                        @click="setSeriesId(series)" v-for="series in equipmentSeries">{{ series }}</li>
                </ul>
                <div class="flex w-full flex-col py-1 px-4">
                    <h3 class="font-roboto text-side-gray-text  font-semibold text-lg">Добавить новую запись ремонта
                    </h3>
                    <div class="sm:overflow-x-auto mt-2">
                        &lt;!&ndash; Header Row &ndash;&gt;
                        <div class="grid grid-cols-5 bg-gray-50 border-b border-gray-200">
                            <div class="px-4 py-2 text-left text-gray-600 border-r">Дата ремонта</div>
                            <div class="px-4 py-2 text-left text-gray-600 border-r">Место проведения</div>
                            <div class="px-4 py-2 text-left text-gray-600 border-r">Описание</div>
                            <div class="px-4 py-2 text-left text-gray-600 border-r">Расход</div>
                            <div class="px-4 py-2 text-left text-gray-600"><svg width="24" height="24"
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
                            </div>
                        </div>

                        &lt;!&ndash; Data Row &ndash;&gt;
                        <div class="grid grid-cols-5 border-b border-gray-200">
                            &lt;!&ndash; Date Input with Icon &ndash;&gt;
                            <div class="flex items-center border-r">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                    <path
                                        d="M15.9999 12.6671V16.667C15.9999 17.0207 15.8594 17.3598 15.6094 17.6098C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6098C6.14047 17.3598 6 17.0207 6 16.667V9.33377C6 8.98015 6.14047 8.64101 6.39052 8.39096C6.64057 8.14092 6.9797 8.00044 7.33332 8.00044H11.3333"
                                        stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <input type="date" v-model="form.repair_date"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            &lt;!&ndash; Location Select &ndash;&gt;
                            <div class="border-r  flex items-center">
                                <select v-model="form.location_id"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option v-for="location in equipment_location" :value="location.id">{{ location.name
                                        }}</option>
                                </select>
                            </div>

                            &lt;!&ndash; Description Textarea &ndash;&gt;
                            <div class="border-r ">
                                <textarea v-model="form.description"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    rows="2">Lorem ipsum dolor sit amet...</textarea>
                            </div>

                            &lt;!&ndash; Expense Input &ndash;&gt;
                            <div class="border-r flex items-center">
                                <input v-model="form.expense"
                                    class="w-full border-none rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="280000">
                            </div>

                            &lt;!&ndash; Action Icon &ndash;&gt;
                            <div class="flex px-4 w-1/6 items-center ">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                        fill="#21272A" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.65802 7.03955C4.91232 6.71259 5.38353 6.65369 5.71049 6.90799L12 11.7999L18.2896 6.90799C18.6165 6.65369 19.0877 6.71259 19.342 7.03955C19.5964 7.36651 19.5375 7.83772 19.2105 8.09202L12.4605 13.342C12.1897 13.5527 11.8104 13.5527 11.5396 13.342L4.78958 8.09202C4.46262 7.83772 4.40372 7.36651 4.65802 7.03955Z"
                                        fill="#21272A" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex justify-end">

                            <button @click="submit"
                                class="text-center bg-my-gray    justify-end mt-10 py-3 px-10 bg-gray-300 p">Сохранить</button>

                        </div>
                        <div class="sm:overflow-x-auto mt-4 sm:pb-32">
                            <h3 class="font-roboto text-side-gray-text  font-semibold text-lg">История ремонта
                    </h3>

                            &lt;!&ndash; Header row &ndash;&gt;
                            <div
                                class="grid grid-cols-5 bg-gray-50 border-b border-gray-200 text-left text-gray-600 px-4 py-2 font-semibold">
                                <div>Дата ремонта</div>
                                <div>Место проведения</div>
                                <div>Описание</div>
                                <div>Расход</div>
                                <div><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
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
                                    </svg></div>
                            </div>

                            &lt;!&ndash; Data rows &ndash;&gt;
                            <div v-for="repair in repairs" :key="repair.id"
                                class="grid grid-cols-5 border-b border-gray-200 px-4 py-2">
                                <div class="flex py-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15.9999 12.6671V16.6671C15.9999 17.0207 15.8594 17.3598 15.6094 17.6099C15.3594 17.8599 15.0202 18.0004 14.6666 18.0004H7.33332C6.9797 18.0004 6.64057 17.8599 6.39052 17.6099C6.14047 17.3598 6 17.0207 6 16.6671V9.33378C6 8.98016 6.14047 8.64103 6.39052 8.39098C6.64057 8.14093 6.9797 8.00046 7.33332 8.00046H11.3333"
                                            stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ repair.repair_date }}</div>
                                <div class="py-2">{{ getLocationName(repair.location_id) }}</div>
                                <div class="py-2 overflow-hidden text-ellipsis whitespace-nowrap">{{ repair.description
                                    }}</div>
                                <div class="py-2">{{ repair.expense }}</div>
                                <div class="py-2 space-x-2 flex items-center">
                                    <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 12H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                    </svg>



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
                                                class="absolute top-6 z-200 right-2 justify-between w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                <div class="py-1">
                                                    <MenuItem>
                                                    <Link method="delete" :href="route('equip.destroyRepair', repair.id)"
                                                        class="flex items-center justify-around"
                                                        :class="['block px-4 py-2 text-sm']">Открыть
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z"
                                                            fill="#464F60" />
                                                    </svg>

                                                </Link>

                                                    </MenuItem>


                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>





                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>-->
    </AuthenticatedLayout>

</template>
