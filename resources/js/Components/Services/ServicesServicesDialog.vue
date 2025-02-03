<script setup>
import store from '../../../store/index';
import {computed, onMounted, ref, watch} from 'vue';


import {DialogClose, DialogContent, DialogPortal, DialogRoot, DialogTitle, DialogOverlay} from "radix-vue";

const addService = (service) => {
    store.dispatch('services/updateSelectedServices', service)

    showModal(false)
}


const extraServices = computed(() => store.getters['sale/getExtraServices']);


const showModal = (value) => {
    store.dispatch('sale/showModal', value)
}


onMounted(() => {
    store.dispatch('services/fetchEquipmentCategories')
    store.dispatch('services/fetchEquipmentCategoriesCount')
    store.dispatch('sale/updateExtraServices')


});


</script>

<template>


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
