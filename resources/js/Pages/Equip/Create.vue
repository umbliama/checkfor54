<script setup>
import { reactive, defineProps, useAttrs, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EquipNav from '@/Components/Equip/EquipNav.vue';
import FormSuccess from '@/Components/FormSuccess.vue';
import FormError from '@/Components/FormError.vue';
import store from '../../../store/index';
import { MenuButton, MenuItems, MenuItem, Menu } from '@headlessui/vue';


const page = usePage()


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
    'status': null,
    'series': null,
    'price': null,
    'manufactor_date': null,
    'notes': null,
    'location_id': locationId,
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
    store.dispatch('equipment/updateCategory', categoryId)
    updateUrl();
}

const setSizeId = (sizeId) => {
    store.dispatch('equipment/updateSize', sizeId)
    updateUrl();
}

const setLocation = (location) => {
    store.dispatch('equipment/updateLocationActive', location)

}

const updateLocationModal = (value) => {
    store.dispatch('equipment/updateLocationModal', value)
}

const updateUrl = () => {
    if (selectedCategory.value && selectedSize.value) {
        router.get(route('equip.create', { category_id: selectedCategory.value, size_id: selectedSize.value }));
    }
};


const props = defineProps({
    equipment_categories: Array,
    equipment_sizes: Array,
    equipment_location: Array,
    equipment_sizes_counts: Array,
    equipment_categories_counts: Array
})

function submitLocation() {
    router.post('/equip/location', {
        name: formLocation.location
    })
    store.dispatch('equipment/updateLocationModal', false)
}

function submit() {
    if (locationId === 0) {
        updateErrors({1:'Выберите склад'})
    }
    if (locationId !== 0 && selectedCategory && selectedSize) {
        router.post('/equip', {
            manufactor: form.manufactor,
            status: form.status,
            size_id: form.size_id,
            category_id: form.category_id,
            status: form.status,
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
</script>

<template>
    <AuthenticatedLayout>
        <EquipNav></EquipNav>
        <nav class="lg:p-5  bg-my-gray m-5 rounded-xl ">
            <div class="max-w-screen-xl py-2 px-4 ">
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
            </div>
        </nav>

        <div class="sm:hidden lg:flex md:flex border-b-2  items-center mt-5 pl-5 space-x-6">
            <div v-for="location in equipment_location" @click="setLocation(location.id)"
                :class="{ 'border-b-2 border-blue-500': locationId === location.id }" class="text-gray-800">{{
                location.name }} </div>
            <Menu as="div" class="relative inline-block text-left">
                <div class="sm:overflow-x-visible">
                    <MenuButton
                        class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                        <svg width="4" height="16" viewBox="0 0 4 16" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
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
                                        fill="#464F60" />
                                </svg>


                            </button>
                            </MenuItem>

                        </div>
                    </MenuItems>
                </transition>
            </Menu>

            <div v-if="locationModal" class="absolute bg-white border-2 p-5 gap-5 grid grid-cols-1">
                <h3 class="text-lg text-center">Создать локацию</h3>
                <input type="text" v-model="formLocation.location" />
                <button @click="submitLocation">Создать</button>
            </div>

        </div>

        <div class="flex h-screen">
            <FormSuccess class="bg-black-500" v-if="success" :message="success" />

            <FormError v-if="Object.keys(errors).length > 0" :message="Object.values(errors)" />


            <div class="flex flex-col p-6 sm:mt-5 w-full sm:pb-25 pb-32">
                <h3 class="font-bold text-lg">Добавить новое оборудование:</h3>


                <div v-if="selectedCategory == 1" class="grid grid-cols-3 gap-6 mb-6">
                    <!-- Second row -->
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Производитель</label>
                        <input required v-model="form.manufactor" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Сокол">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Серия</label>
                        <input required v-model="form.series" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ДР - 3980">
                    </div>

                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Заходность</label>
                        <input required v-model="form.zahodnost" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ДР - 3980">
                    </div>

                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Длина</label>
                        <input required v-model="form.length" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>



                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Длина дс.</label>
                        <input required v-model="form.dlina_ds" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="2200">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Статор / Ротор</label>
                        <input required v-model="form.stator_rotor" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="181334001 / 1711025">
                    </div>

                    <!-- Fourth row -->
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Наработка</label>
                        <input required v-model="form.operating" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="250">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Наработка дс.</label>
                        <input required v-model="form.narabotka_ds" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="250">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Дата изготовления</label>
                        <input required v-model="form.manufactor_date" type="date"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="15.03.2023">
                    </div>

                    <!-- Fifth row -->
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Стоимость</label>
                        <input required v-model="form.price" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="1 280 000 Р">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Примечание</label>
                        <input required v-model="form.notes" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Гистранс">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Состояние</label>
                        <select required v-model="form.status" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Новое">
                            <option value="new">Новое</option>
                            <option value="good">Хорошее</option>
                            <option value="satisfactory">Удволетворительное</option>
                            <option value="bad">Плохое</option>
                            <option value="off">Списано</option>
                        </select>
                    </div>
                </div>
                <div v-if="selectedCategory == 2" class="grid grid-cols-3 gap-6 mb-6">
                    <!-- Second row -->
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Производитель</label>
                        <input required v-model="form.manufactor" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Сокол">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Серия</label>
                        <input required v-model="form.series" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ДР - 3980">
                    </div>


                    <!-- Third row -->
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Длина</label>
                        <input required v-model="form.length" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>


                    <!-- Fourth row -->
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Наработка</label>
                        <input required v-model="form.operating" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="250">
                    </div>

                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Дата изготовления</label>
                        <input required v-model="form.manufactor_date" type="date"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="15.03.2023">
                    </div>

                    <!-- Fifth row -->
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Стоимость</label>
                        <input required v-model="form.price" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="1 280 000 Р">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Примечание</label>
                        <input required v-model="form.notes" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Гистранс">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Состояние</label>
                        <select required v-model="form.status" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Новое">
                            <option value="new">Новое</option>
                            <option value="good">Хорошее</option>
                            <option value="satisfactory">Удволетворительное</option>
                            <option value="bad">Плохое</option>
                            <option value="off">Списано</option>
                        </select>
                    </div>
                </div>
                <div v-if="selectedCategory == 3 || selectedCategory == 4 || selectedCategory == 5 || selectedCategory == 7"
                    class="grid grid-cols-3 gap-6 mb-6">
                    <!-- Second row -->
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Производитель</label>
                        <input required v-model="form.manufactor" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Сокол">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Серия</label>
                        <input required v-model="form.series" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ДР - 3980">
                    </div>

                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Диаметр</label>
                        <input required v-model="form.diameter" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="250">
                    </div>

                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Длина</label>
                        <input required v-model="form.length" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Длина с резьбой</label>
                        <input required v-model="form.length_rezba" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Резьбы</label>
                        <input required v-model="form.rezbi" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>

                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Наработка</label>
                        <input required v-model="form.operating" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>



                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Дата изготовления</label>
                        <input required v-model="form.manufactor_date" type="date"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="15.03.2023">
                    </div>

                    <!-- Fifth row -->
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Стоимость</label>
                        <input required v-model="form.price" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="1 280 000 Р">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Примечание</label>
                        <input required v-model="form.notes" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Гистранс">
                    </div>
                    <div class="col-span-1 sm:col-span-3">
                        <label class="block text-gray-700">Состояние</label>
                        <select required v-model="form.status" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Новое">
                            <option value="new">Новое</option>
                            <option value="good">Хорошее</option>
                            <option value="satisfactory">Удволетворительное</option>
                            <option value="bad">Плохое</option>
                            <option value="off">Списано</option>
                        </select>
                    </div>
                </div>

                <div class="col-span-3 sm:col-span-3">
                    <label class="block text-gray-700">Комментарий</label>
                    <textarea required v-model="form.commentary" type="text"
                        class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 ">

                        </textarea>
                </div>

                <div class="col-span-3 flex sm:mt-2 sm:pb-20 justify-end">
                    <button @click="submit"
                        class="bg-my-gray text-side-gray-text font-bold px-6 py-3 ">Сохранить</button>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
