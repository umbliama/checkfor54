<script setup>
import { Link, router } from '@inertiajs/vue3';
import store from '../../store/index';
import { computed, onMounted, ref, watch } from 'vue';
import EquipStatus from '@/Components/EquipStatus.vue';


import { Menu, MenuItems, MenuItem, MenuButton } from '@headlessui/vue'


const selectCategory = (category) => {
    store.dispatch('equipment/updateCategory', category);
    fetchEquipmentSizesById(category)
    fetchEquipmentSizes()

};

const fetchEquipmentSizesById = (category) => {
    store.dispatch('services/fetchEquipmentSizesById',category)
}

const fetchEquipmentSizes = () => {
    store.dispatch('services/fetchEquipmentSizesCount')
}
 
const updateSelectedEquipment = (value) => {
    if (!selectedEquipment.value) {
        // Dispatch action to update selected equipment
        store.dispatch('services/updateSelectedEquipment', value);
    } else {
        // Dispatch action to update sub-equipment array


        store.dispatch('services/updateSubEquipmentArray', value);
        store.dispatch('services/updateSubSelectedEquipmentObjects', {
            subequipment_id: '',
            shipping_date: '',
            period_start_date: '',
            return_date: '',
            period_end_date: '',
            store: '',
            operating: false,
            return_reason: '',
            commentary: '',

        });
    }

    // Close the modal
    showModal(false);
};
const selectSize = (size) => {
    store.dispatch('equipment/updateSize', size);
    if (selectedCategory.value && selectedSize.value) {
        store.dispatch('services/fetchEquipment', {
            category_id:
                selectedCategory.value, size_id: selectedSize.value
        })
    }
};

const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedEquipment = computed(() => store.getters['services/getSelectedEquipment']);
const selectedCategory = computed(() => store.getters['equipment/getCategoryActive']);
const selectedSize = computed(() => store.getters['equipment/getSizeActive']);
const equipment_categories = computed(() => store.getters['services/getEquipmentCategories']);
const equipment_categories_counts = computed(() => store.getters['services/getEquipmentCategoriesCounts']);
const equipment_sizes = computed(() => store.getters['services/getEquipmentSizes']);
const equipment_sizes_counts = computed(() => store.getters['services/getEquipmentSizesCounts']);
const equipment = computed(() => store.getters['services/getEquipment']);


const toggleInputLocation = (value) => {
    store.dispatch('equipment/updateInputLocationShown', value);
};
const showModal = (value) => {
    store.dispatch('services/updateModalShown', value)
}



// Fetch initial data on component mount
onMounted(() => {


    store.dispatch('services/fetchEquipmentCategories')
    store.dispatch('services/fetchEquipmentCategoriesCount')
    // store.dispatch('fetchLocations')
    // store.dispatch('equipment/updateEquipmentCategories', props.equipment_categories)
    // store.dispatch('equipment/updateEquipmentSizesCounts', props.equipment_sizes_counts)
    // store.dispatch('equipment/updateEquipmentCategoriesCounts', props.equipment_categories_counts)
    // store.dispatch('equipment/updateEquipmentSizes', props.equipment_sizes)

});


</script>

<template>

    <div class="max-w-full py-4 px-10">
        <nav class="bg-my-gray">
            <div class=" sm:rounded-2xl py-2 px-4 ">
                <div class="mt-6 flex items-center">
                    <ul
                        class="flex overflow-x-auto flex-row border-b-2 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                        <li class="text-my-nav-text text-md w-full sm:py-2"
                            :class="{ 'border-b-2 border-blue-600': selectedCategory === item.id }"
                            @click="selectCategory(item.id)" v-for="item in equipment_categories" :key="item.id">
                            <button class="flex justify-around">
                            {{ item.name }}
                            <span v-if="equipment_categories_counts"
                                class="ml-1 rounded-2xl text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                {{ equipment_categories_counts[item.id - 1].count }}
                            </span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="mt-6 flex items-center">
                    <ul class="flex flex-row sm:overflow-x-auto font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                        <li class="text-my-nav-text text-lg sm:py-2 w-full"
                            :class="{ 'border-b-2 border-blue-600': selectedSize === item.id }"
                            @click="selectSize(item.id)" v-for="item in equipment_sizes" :key="item.id">
                            <button class="flex justify-around">
                            {{ item.name }}
                            <span v-if="equipment_sizes_counts"
                                class="ml-1 rounded-2xl text-sm flex items-center px-3 py-1 text-white bg-gray-500 ">
                                {{ equipment_sizes_counts[item.id] }}
                            </span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>


        <!-- <div class="sm:hidden lg:flex md:flex border-b-2  items-center mt-5 space-x-6">
                <div class="border-b-2 border-blue-900 flex items-center space-x-2">
                    <span class="text-blue-900 font-bold">Все</span>
                </div>

                <div v-for="location in store.getters.cities" class="text-gray-800">{{ location.name }}</div>
            </div> -->

        <div
            class="lg:hidden md:hidden mt-5  py-2 px-1  sm:bg-gray-200 flex lg:justify-start sm:justify-center items-center">
            <form action="" method="GET" class="max-w-md">
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Поиск</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" name="search" id="default-search"
                        class="block w-full py-4 ps-10 text-sm text-gray-900 border-b-2 border-gray-200  border-t-0 border-l-0 border-r-0 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Поиск">
                </div>
            </form>
            <div class="ml-4 mr-4">
                <svg width="3" height="28" viewBox="0 0 3 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="path-1-inside-1_2101_83802" fill="white">
                        <path d="M0.5 0H2.5V28H0.5V0Z" />
                    </mask>
                    <path d="M0.5 0H2.5V28H0.5V0Z" fill="white" />
                    <path
                        d="M0 0V1H1V0H0ZM0 3V5H1V3H0ZM0 7V9H1V7H0ZM0 11V13H1V11H0ZM0 15V17H1V15H0ZM0 19V21H1V19H0ZM0 23V25H1V23H0ZM0 27V28H1V27H0ZM-0.5 0V1H1.5V0H-0.5ZM-0.5 3V5H1.5V3H-0.5ZM-0.5 7V9H1.5V7H-0.5ZM-0.5 11V13H1.5V11H-0.5ZM-0.5 15V17H1.5V15H-0.5ZM-0.5 19V21H1.5V19H-0.5ZM-0.5 23V25H1.5V23H-0.5ZM-0.5 27V28H1.5V27H-0.5Z"
                        fill="#C1C7CD" mask="url(#path-1-inside-1_2101_83802)" />
                </svg>

            </div>
            <button class="flex  border-2 bg-white py-4 items-center  px-10">
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2.58007 2L9.06207 10.101C9.34581 10.4558 9.50029 10.8967 9.50007 11.351V18L11.5001 16.5V11.35C11.5001 10.896 11.6545 10.4556 11.9381 10.101L18.4201 2H2.58107H2.58007ZM2.58007 0H18.4201C18.7969 4.70007e-05 19.166 0.106524 19.4849 0.307166C19.8038 0.507808 20.0595 0.794454 20.2227 1.13409C20.3858 1.47372 20.4497 1.85253 20.4069 2.22687C20.3642 2.60122 20.2166 2.95588 19.9811 3.25L13.5001 11.35V16.5C13.5001 16.8105 13.4278 17.1167 13.2889 17.3944C13.1501 17.6721 12.9485 17.9137 12.7001 18.1L10.7001 19.6C10.4029 19.8229 10.0496 19.9586 9.67968 19.9919C9.30976 20.0253 8.93786 19.955 8.60565 19.7889C8.27343 19.6227 7.99404 19.3674 7.79877 19.0515C7.6035 18.7355 7.50007 18.3714 7.50007 18V11.35L1.01907 3.25C0.783588 2.95588 0.635973 2.60122 0.593233 2.22687C0.550493 1.85253 0.614365 1.47372 0.777494 1.13409C0.940622 0.794454 1.19637 0.507808 1.51528 0.307166C1.83419 0.106524 2.2033 4.70007e-05 2.58007 0V0Z"
                        fill="#697077" />
                </svg>

                Фильтры</button>
        </div>

        <div class="flex lg:flex md:flex sm:hidden items-baseline">
            <form action="" method="GET" class="max-w-md mt-4">
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">поис</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" name="search" id="default-search"
                        class="block w-full p-1 py-2 ps-10 text-sm text-gray-900 border-b-2 border-gray-200 rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Поиск">
                </div>
            </form> <button class="flex ml-6 border-2 items-center rounded-lg px-6"><svg width="13" height="9"
                    viewBox="0 0 13 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="2.60474" cy="6.50366" r="2" fill="white" stroke="#415476" />
                    <line x1="4.10474" y1="6.50366" x2="12.1047" y2="6.50366" stroke="#415476" />
                    <circle cx="9.89476" cy="2.8417" r="2" transform="rotate(-177.996 9.89476 2.8417)" fill="white"
                        stroke="#415476" />
                    <line x1="8.10474" y1="2.50366" x2="0.104735" y2="2.50366" stroke="#415476" />
                </svg>
                Фильтры</button>
        </div>

        <div v-if="equipment" class="w-full overflow-x-auto  md:overflow-x-auto sm:overflow-x-auto">
            <table class="w-full mt-5 table-auto">
                <thead class="overflow-x-auto ">
                    <tr class="whitespace-nowrap">
                        <th class="sm:text-sm">Производитель</th>
                        <th class="sm:text-sm">Cерия</th>
                        <th class="sm:text-sm">Дата изготовления</th>
                        <th class="sm:text-sm">Состояние</th>
                        <th class="sm:text-sm">Стоимость</th>
                        <th class="sm:text-sm">Примечание</th>
                        <th class="flex justify-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
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
                <tbody class="overflow-x-auto ">
                    <tr class="whitespace-nowrap" v-for="item in equipment.data">
                        <td class="text-center border border-slate-200 p-2">
                            <div class="flex justify-start ">
                                <svg width="20" height="20" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.9999 7.66706V11.667C10.9999 12.0206 10.8594 12.3598 10.6094 12.6098C10.3594 12.8599 10.0202 13.0003 9.6666 13.0003H2.33332C1.9797 13.0003 1.64057 12.8599 1.39052 12.6098C1.14047 12.3598 1 12.0206 1 11.667V4.33375C1 3.98013 1.14047 3.641 1.39052 3.39095C1.64057 3.1409 1.9797 3.00043 2.33332 3.00043H6.33329"
                                        stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M9 1H13V4.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M5.66663 8.33328L12.9999 1" stroke="#808192" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="ml-4">
                                    {{ item.manufactor }}
                                </div>
                            </div>
                        </td>
                        <td @click="updateSelectedEquipment(item.id)" class="text-center border border-slate-200 p-2">{{
                            item.series }}</td>
                        <td class="text-center border border-slate-200 p-2">{{ item.manufactor_date }}</td>
                        <td class="text-center border border-slate-200 p-2">
                            <EquipStatus pingColor="#ff0000" dotColor="#00ff00" :status="item.status" />

                        </td>
                        <td class="text-center border border-slate-200 p-2">{{ item.price }}</td>
                        <td class="text-center border border-slate-200 p-2">{{ item.notes }}</td>
                        <td class="text-center border flex items-center justify-between border-slate-200 p-2">
                            <div class="mr-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
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
                                                    :class="['block px-4 py-2 text-sm']">Создать контрагента
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
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
                </tbody>
            </table>
        </div>
        <!-- <pagination class="mt-5" :links="equipment.links" /> -->


    </div>
</template>