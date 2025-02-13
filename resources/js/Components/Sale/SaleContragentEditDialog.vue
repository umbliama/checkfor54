<script setup>
import {
    DialogClose,
    DialogContent,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'radix-vue';
import { computed, ref, watch } from "vue";
import UiField from "@/Components/Ui/UiField.vue";
import UiFieldDate from '../Ui/UiFieldDate.vue';
import {router} from '@inertiajs/vue3'
const model = defineModel();

const $props = defineProps({
    contragent: Object
});

const $emit = defineEmits(['edit']);

const shipping_date = ref(null);
const commentary    = ref(null);

watch(model, new_val => {
    if (!new_val) return;

    shipping_date.value = $props.contragent.shipping_date;
    commentary   .value = $props.contragent.commentary;
});

async function submit() {
    router.post('/sale/setContragentServiceData', {
        'contragent_id': Number($props.contragent.contragent_id),
        'commentary': commentary.value,
        'shipping_date': shipping_date.value,
    })
    model.value = false;
}

</script>

<template>
    <DialogRoot v-model:open="model">
        <DialogPortal>
            <transition name="fade">
                <DialogOverlay class="fixed left-0 top-0 w-full h-full bg-black bg-opacity-60 z-30"/>
            </transition>
            <transition name="fade">
                <DialogContent
                    class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-h-[85vh] w-[90vw] max-w-[450px] p-6 rounded bg-white z-[100]"
                >
                    <DialogTitle class="flex items-center justify-between font-semibold">
                        Редактировать контрагента

                        <DialogClose class="shrink-0 ml-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16.95 8.464a1 1 0 0 0-1.414-1.414L12 10.586L8.464 7.05A1 1 0 1 0 7.05 8.464L10.586 12L7.05 15.536a1 1 0 1 0 1.414 1.414L12 13.414l3.536 3.536a1 1 0 1 0 1.414-1.414L13.414 12z"/></svg>
                        </DialogClose>
                    </DialogTitle>
                    <form class="mt-8 space-y-4" @submit.prevent="submit">
                        {{ $props.sale }}
                        
                        <UiFieldDate
                            v-model="shipping_date"
                            label="Дата отгрузки"
                            :inp-attrs="{ placeholder: 'Укажите дату отгрузки' }"
                        />
                        <UiField v-model="commentary" label="Комментарий" />

                        <button
                            :disabled="false"
                            type="submit"
                            class="w-full mt-4 bg-my-gray text-side-gray-text font-bold px-6 py-3 disabled:opacity-40"
                        >Сохранить</button>
                    </form>
                </DialogContent>
            </transition>
        </DialogPortal>
    </DialogRoot>
</template>
