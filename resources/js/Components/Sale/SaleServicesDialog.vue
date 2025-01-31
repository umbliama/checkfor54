<script setup>
import {Link, router} from '@inertiajs/vue3';
import store from '../../../store/index';
import {computed, onMounted, ref, watch} from 'vue';
import EquipStatus from '@/Components/EquipStatus.vue';


import {Menu, MenuItems, MenuItem, MenuButton} from '@headlessui/vue'
import {DialogClose, DialogContent, DialogPortal, DialogRoot, DialogTitle, DialogOverlay} from "radix-vue";


const selectCategory = (category) => {
    store.dispatch('equipment/updateCategory', category);
    fetchEquipmentSizesById(category)
    fetchEquipmentSizes()

};

const fetchEquipmentSizesById = (category) => {
    store.dispatch('services/fetchEquipmentSizesById', category)
}

const fetchEquipmentSizes = () => {
    store.dispatch('services/fetchEquipmentSizesCount')
}
const addService = (service) => {
    store.dispatch('sale/updateSelectedServices', service)

    showModal(false)
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
const extraServices = computed(() => store.getters['sale/getExtraServices']);


const toggleInputLocation = (value) => {
    store.dispatch('equipment/updateInputLocationShown', value);
};
const showModal = (value) => {
    store.dispatch('sale/showModal', value)
}


// Fetch initial data on component mount
onMounted(() => {
    store.dispatch('services/fetchEquipmentCategories')
    store.dispatch('services/fetchEquipmentCategoriesCount')
    store.dispatch('sale/updateExtraServices')


});


</script>

<template>

<!--    <div class=" max-w-full py-4 px-10">
        <div @click="showModal(false)" class="absolute t-0 l-0">X</div>

        <div class="w-full overflow-x-auto  md:overflow-x-auto sm:overflow-x-auto">
            <ul class="p-4">
                <li @click="addService({item:{value:key,item:item}})" v-for="(key,item) in extraServices">{{ key }}</li>
            </ul>
        </div>
    </div>-->

    <DialogRoot open>
        <DialogPortal>
            <transition name="fade">
                <DialogOverlay class="fixed left-0 top-0 w-full h-full bg-black bg-opacity-60 z-[60]"/>
            </transition>
            <transition name="fade">
                <DialogContent
                    class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-h-[85vh] w-[90vw] max-w-[300px] p-6 rounded bg-white overflow-y-auto z-[100]"
                >
                    <DialogTitle class="flex items-center justify-between font-semibold">
                        Выбрать услугу

                        <DialogClose class="shrink-0 ml-3" @click="showModal(false)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                      d="M16.95 8.464a1 1 0 0 0-1.414-1.414L12 10.586L8.464 7.05A1 1 0 1 0 7.05 8.464L10.586 12L7.05 15.536a1 1 0 1 0 1.414 1.414L12 13.414l3.536 3.536a1 1 0 1 0 1.414-1.414L13.414 12z"/>
                            </svg>
                        </DialogClose>
                    </DialogTitle>

                    <ul class="mt-5">
                        <li
                            v-for="(key,item) in extraServices"
                            class="shrink-0 flex items-center justify-between border-b-2 border-transparent py-1.5 cursor-pointer"
                            @click="addService({value:key,item:item})"
                        >
                            {{ key }}
                        </li>
                    </ul>
                </DialogContent>
            </transition>
        </DialogPortal>
    </DialogRoot>
</template>
