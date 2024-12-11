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
const addService = (service) => {
    store.dispatch('sale/updateExtraServices',service)

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
    store.dispatch('sale/updateExtraServicesModal')

});


</script>

<template>

    <div class=" max-w-full py-4 px-10">
        <div @click="showModal(false)" class="absolute t-0 l-0">X</div>

   

        <div class="w-full overflow-x-auto  md:overflow-x-auto sm:overflow-x-auto">
            <ul class="p-4">
                <li @click="addService({item:{value:key,item:item}})" v-for="(key,item) in extraServices">{{ key }}</li>
            </ul>
        </div>


    </div>
</template>