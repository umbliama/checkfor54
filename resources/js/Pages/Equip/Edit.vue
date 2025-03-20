<script setup>
import { reactive, defineProps, useAttrs, computed, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EquipNav from '@/Components/Equip/EquipNav.vue';
import FormSuccess from '@/Components/FormSuccess.vue';
import FormError from '@/Components/FormError.vue';
import store from '../../../store/index';
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
    { title: 'Продан', value: 'sold' },
    { title: 'Списано', value: 'off' },
    { title: 'Неизвестно', value: 'unknown' },
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

const props = defineProps({
    equipment: Object,
    equipment_categories: Array,
    equipment_sizes: Array,
    equipment_location: Array,
    equipment_sizes_counts: Object,
    equipment_categories_counts: Object,
    ownershipArray: Array

});

const formLocation = reactive({
    'name': null,
});


const form = reactive({
    'manufactor': props.equipment.manufactor,
    'status': props.equipment.status,
    'size_id': selectedSize,
    'category_id': selectedCategory,
    'series': props.equipment.series,
    'price': props.equipment.price,
    'manufactor_date': props.equipment.manufactor_date,
    'notes': props.equipment.notes,
    'location_id': props.equipment.location_id,
    'zahodnost': props.equipment.zahodnost,
    'stator_rotor': props.equipment.stator_rotor,
    'narabotka_ds': props.equipment.narabotka_ds,
    'rezbi': props.equipment.rezbi,
    'length_rezba': props.equipment.length_rezba,
    'diameter': props.equipment.diameter,
    'length': props.equipment.length,
    'operating': props.equipment.operating,
    'commentary': props.equipment.commentary,
    'dlina_ds': props.equipment.dlina_ds,
    'ownership': props.equipment.ownership
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
    const params = {
        id: props.equipment.id,
    };

    if ( props.equipment.category_id || selectedCategory.value ) {
        params.category_id = selectedCategory.value ?? props.equipment.category_id;
    }

    if ( props.equipment.size_id || selectedSize.value ) {
        params.size_id = selectedSize.value ?? props.equipment.size_id;
    }

    Object.keys(params).length && router.replace(route('equip.edit', params));
};

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
        router.put(`/equip/update/${props.equipment.id}`, {
            manufactor: form.manufactor,
            size_id: form.size_id,
            category_id: form.category_id,
            status: form.status.value,
            series: form.series,
            price: +form.price,
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
            commentary: form.commentary,
            ownership: form.ownership.value
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

    store.dispatch('equipment/updateCategory', form.category_id ?? props.equipment.category_id);
    store.dispatch('equipment/updateSize', form.size_id ?? props.equipment.size_id);
    updateUrl()
    store.dispatch('equipment/updateMenuItem', EquipMenuItems.EQUIPMENT)
});
</script>

<template>
    <AuthenticatedLayout>
        <EquipNav />

        <div class="py-9 px-6">
            <h3 class="mb-6 font-bold text-lg">Редактировать оборудование:</h3>

            <EquipFilter :selected-category="selectedCategory" :selected-size="selectedSize"
                :categories-counts="equipment_categories_counts" :sizes-counts="equipment_sizes_counts"
                :categories="equipment_categories" :sizes="equipment_sizes" required-size
                @category-click="cat_id => setCategoryId(cat_id)" @size-click="size_id => setSizeId(size_id)" />


            <div v-if="$page.props.user.isAdmin" class="relative mt-5 text-sm">
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

            <div class="mt-6">
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
                    <UiFieldSelect v-model="form.ownership" :items="ownershipArray" label="Право собственности" />

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
