<script setup>
import { reactive, defineProps, useAttrs, computed, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EquipNav from '@/Components/Equip/EquipNav.vue';
import FormSuccess from '@/Components/FormSuccess.vue';
import FormError from '@/Components/FormError.vue';
import store from '../../../store/index';
import { MenuButton, MenuItems, MenuItem, Menu } from '@headlessui/vue';
import EquipFilter from "@/Components/Equip/EquipFilter.vue";
import { EquipMenuItems } from "../../../constants/index.js";
import UiField from "@/Components/Ui/UiField.vue";
import UiFieldSelect from "@/Components/Ui/UiFieldSelect.vue";


const page = usePage()

const statuses = [
    { title: 'Новое', value: 'new' },
    { title: 'Хорошее', value: 'good' },
    { title: 'Удволетворительное', value: 'satisfactory' },
    { title: 'Плохое', value: 'bad' },
    { title: 'Списано', value: 'off' },
];

const locationId = computed(() => store.getters['equipment/getLocationActive']);
const locationModal = computed(() => store.getters['equipment/getLocationModal']);
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);


const success = computed(() => page.props.flash.success)
const errors = computed(() => page.props.errors)

const updateErrors = (errors) => {
    store.dispatch('equipment/updateErrors', errors)
}

const formLocation = reactive({
    'name': null,
})


const form = reactive({
    'manufactor': null,
    'status': null,
    'size_id': selectedSize,
    'category_id': selectedCategory,
    'series': null,
    'price': null,
    'manufactor_date': null,
    'notes': null,
    'location_id': null,
    'zahodnost': null,
    'stator_rotor': null,
    'narabotka_ds': null,
    'rezbi': null,
    'length_rezba': null,
    'diameter': null,
    'length': null,
    'operating': null,
    'commentary': null,
    'dlina_ds': null
})

const setCategoryId = (categoryId) => {
    if (selectedCategory.value) setSizeId(null);
    store.dispatch('equipment/updateCategory', categoryId);
    updateUrl();
}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    updateUrl();
}

const updateLocationModal = (value) => {
    store.dispatch('equipment/updateLocationModal', value)
}

const updateUrl = () => {
    const params = {};

    if (selectedCategory.value) params.category_id = selectedCategory.value;
    if (selectedSize.value) params.size_id = selectedSize.value;

    Object.keys(params).length && router.get(route('equip.create', params));
};


const props = defineProps({
    equipment_categories: Array,
    equipment_sizes: Array,
    equipment_location: Array,
    equipment_sizes_counts: Object,
    equipment_categories_counts: Object
})

function submitLocation() {
    router.post('/equip/location', {
        name: formLocation.location
    })
    store.dispatch('equipment/updateLocationModal', false)
}

function submit() {
    if (form.location_id) {
        updateErrors({ 1: 'Выберите склад' })
    }
    if (locationId !== 0 && selectedCategory && selectedSize) {
        router.post('/equip', {
            manufactor: form.manufactor,
            size_id: form.size_id,
            category_id: form.category_id,
            status: form.status.value,
            series: form.series,
            price: form.price,
            manufactor_date: form.manufactor_date,
            notes: form.notes,
            location_id: form.location_id,
            zahodnost: form.zahodnost,
            stator_rotor: form.stator_rotor,
            narabotka_ds: form.narabotka_ds,
            rezbi: form.rezbi,
            length_rezba: form.length_rezba,
            diameter: form.diameter,
            length: form.length,
            operating: form.operating,
            dlina_ds: form.dlina_ds,
            commentary: form.commentary
        },
            {
                onSuccess: () => console.log(page.props.flash.success),
            },
        )
    }
}

onMounted(() => {
    store.dispatch('fetchLocations')
    store.dispatch('equipment/updateEquipmentCategories', props.equipment_categories)
    store.dispatch('equipment/updateEquipmentSizesCounts', props.equipment_sizes_counts)
    store.dispatch('equipment/updateEquipmentCategoriesCounts', props.equipment_categories_counts)
    store.dispatch('equipment/updateEquipmentSizes', props.equipment_sizes)

    const url_params = new URLSearchParams(window.location.search);

    if (url_params.get('category_id')) {
        if (+url_params.get('category_id') !== selectedCategory.value) setCategoryId(+url_params.get('category_id'));
    } else setCategoryId(null);

    if (url_params.get('size_id')) {
        if (+url_params.get('size_id') !== selectedSize.value) setSizeId(+url_params.get('size_id'));
    }

    store.dispatch('equipment/updateMenuItem', EquipMenuItems.EQUIPMENT);
});
</script>

<template>
    <AuthenticatedLayout>
        <EquipNav></EquipNav>

        <div class="py-9 px-6">
            <h3 class="mb-6 font-bold text-lg">Добавить новое оборудование:</h3>

            <EquipFilter :selected-category="selectedCategory" :selected-size="selectedSize"
                :categories-counts="equipment_categories_counts" :sizes-counts="equipment_sizes_counts"
                :categories="equipment_categories" :sizes="equipment_sizes"
                @category-click="cat_id => setCategoryId(cat_id)" @size-click="size_id => setSizeId(size_id)" />

            <!--            <div class="max-w-screen-xl py-2 px-4 ">
                            <div class=" flex items-center">
                                <ul
                                    class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                                    <li class="text-my-nav-text text-md w-full"
                                        :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                                        @click="setCategoryId(item.id)" v-for="item in equipment_categories" :key="item.id">
                                        <Link class="flex items-center justify-around"
                                            :href="route('equip.create', { category_id: item.id })">
                                        {{ item.name }}
                                        <span class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                            {{ equipment_categories_counts[item.id] }}
                                        </span>
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-6 flex items-center sm:overf low-x-auto">
                                <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                                    <li class="text-my-nav-text text-lg w-full"
                                        :class="{ 'border-b-2 border-blue-600': selectedSize === item.id }"
                                        @click="setSizeId(item.id)" v-for="item in equipment_sizes" :key="item.id">
                                        <Link class="flex items-center justify-around"
                                            :href="route('equip.create', { size_id: item.id })">
                                        {{ item.name }}
                                        <span class="ml-1 rounded-full text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                            {{ equipment_sizes_counts[item.id] }}
                                        </span>
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>-->

            <div class="relative mt-5 text-sm">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                <ul
                    class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto lg:overflow-x-visible">
                    <li v-for="location in store.getters.cities" :key="location.id"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': form.location_id === location.id }"
                        class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-3 cursor-pointer"
                        @click="form.location_id = location.id">
                        {{ location.name }}
                    </li>
                </ul>
            </div>

            <!--            <div class="sm:hidden lg:flex md:flex border-b-2  items-center mt-5 pl-5 space-x-6">

                <div v-for="location in equipment_location" @click="setLocation(location.id)"
                     :class="{ 'border-b-2 border-blue-500': locationId === location.id }" class="text-gray-800">{{
                        location.name
                    }}
                </div>
                <Menu as="div" class="relative inline-block text-left">
                    <div class="sm:overflow-x-visible">
                        <MenuButton
                            class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                            <svg width="4" height="16" viewBox="0 0 4 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M3.5 2C3.5 2.82843 2.82843 3.5 2 3.5C1.17157 3.5 0.5 2.82843 0.5 2C0.5 1.17157 1.17157 0.5 2 0.5C2.82843 0.5 3.5 1.17157 3.5 2Z"
                                    fill="#687182"/>
                                <path
                                    d="M3.5 8C3.5 8.82843 2.82843 9.5 2 9.5C1.17157 9.5 0.5 8.82843 0.5 8C0.5 7.17157 1.17157 6.5 2 6.5C2.82843 6.5 3.5 7.17157 3.5 8Z"
                                    fill="#687182"/>
                                <path
                                    d="M3.5 14C3.5 14.8284 2.82843 15.5 2 15.5C1.17157 15.5 0.5 14.8284 0.5 14C0.5 13.1716 1.17157 12.5 2 12.5C2.82843 12.5 3.5 13.1716 3.5 14Z"
                                    fill="#687182"/>
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
                                    <button @click="updateLocationModal(true)" class="flex items-center justify-around"
                                            :class="['block px-4 py-2 text-sm']">Создать локацию
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M8.74999 2.75004C8.74999 2.33582 8.4142 2.00004 7.99999 2.00004C7.58578 2.00004 7.24999 2.33582 7.24999 2.75004V7.25H2.75C2.33579 7.25 2 7.58579 2 8C2 8.41422 2.33579 8.75 2.75 8.75H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75H13.25C13.6642 8.75 14 8.41422 14 8C14 7.58579 13.6642 7.25 13.25 7.25H8.74999V2.75004Z"
                                                fill="#464F60"/>
                                        </svg>


                                    </button>
                                </MenuItem>

                            </div>
                        </MenuItems>
                    </transition>
                </Menu>

                <div v-if="locationModal" class="absolute bg-white border-2 p-5 gap-5 grid grid-cols-1">
                    <h3 class="text-lg text-center">Создать локацию</h3>
                    <input type="text" v-model="formLocation.location"/>
                    <button @click="submitLocation">Создать</button>
                </div>

            </div>-->

            <div v-if="selectedCategory && selectedSize" class="mt-6">
                <div :class="selectedCategory === 2 ? 'lg:grid-cols-5' : 'lg:grid-cols-6'"
                    class="grid grid-cols-1 gap-4 mb-6">
                    <UiField v-model="form.manufactor" label="Производитель" :inp-attrs="{ required: true }" />
                    <UiField v-model="form.series" label="Серия" :inp-attrs="{ required: true }" />

                    <template v-if="selectedCategory == 1">
                        <UiField v-model="form.zahodnost" label="Заходность" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.length" label="Длина" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.dlina_ds" label="Длина дс." :inp-attrs="{ required: true }" />
                        <UiField v-model="form.stator_rotor" label="Статор / Ротор" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.operating" label="Наработка" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.narabotka_ds" label="Наработка дс." :inp-attrs="{ required: true }" />
                    </template>
                    <template v-else-if="selectedCategory == 2">
                        <UiField v-model="form.zahodnost" label="Заходность" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.length" label="Длина" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.operating" label="Наработка" :inp-attrs="{ required: true }" />
                    </template>
                    <template v-else-if="selectedCategory">
                        <UiField v-model="form.diameter" label="Диаметр" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.length" label="Длина" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.length_rezba" label="Длина с резьбой" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.rezbi" label="Резьбы" :inp-attrs="{ required: true }" />
                        <UiField v-model="form.operating" label="Наработка" :inp-attrs="{ required: true }" />
                    </template>

                    <UiField v-model="form.manufactor_date" label="Дата изготовления"
                        :inp-attrs="{ type: 'date', required: true }" />
                    <UiField v-model="form.price" label="Стоимость" :inp-attrs="{ required: true }" />
                    <UiField v-model="form.notes" label="Примечание" :inp-attrs="{ required: true }" />
                    <UiFieldSelect v-model="form.status" :items="statuses" label="Состояние" />
                </div>

                <UiField v-model="form.commentary" label="Комментарий" textarea />

                <div class="flex justify-end mt-4">
                    <button @click="submit" class="bg-my-gray text-side-gray-text font-bold px-6 py-3 ">Сохранить
                    </button>

                </div>
            </div>
        </div>

        <FormSuccess class="bg-black-500" v-if="success" :message="success" />
        <FormError v-if="Object.keys(errors).length > 0" :message="Object.values(errors)" />
    </AuthenticatedLayout>
</template>
