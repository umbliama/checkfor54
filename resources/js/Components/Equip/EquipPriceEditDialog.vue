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
import { router } from "@inertiajs/vue3";
import UiField from "@/Components/Ui/UiField.vue";
import UiFieldDate from "@/Components/Ui/UiFieldDate.vue";
import UiFieldSelect from "@/Components/Ui/UiFieldSelect.vue";


const model = defineModel();

const $props = defineProps({
    title      : String,
    contragents: Array,
    price      : Object,
});

const $emit = defineEmits(['edit']);

const store_date      = ref(null);
const contragent      = ref(null);
const notes           = ref(null);
const store_price     = ref(null);
const operation_price = ref(null);

const formatted_contragents = computed(() => {
    return $props.contragents.map(l=>({ title: l.name, value: l.id }));
});

const is_edited = computed(() => {
    const curr = $props.price;

    return curr.store_date != store_date?.value || curr.contragent_id != contragent?.value.value
            || curr.notes != notes?.value || curr.store_price != store_price?.value
            || curr.operation_price != operation_price?.value;
});

watch(model, new_val => {
    if (!new_val) return;



    store_date     .value = $props.price.store_date;
    contragent     .value = { title: $props.contragents.find(l=>l.id==$props.price.contragent_id).name, value: $props.price.contragent_id };
    notes          .value = $props.price.notes;
    store_price    .value = $props.price.store_price;
    operation_price.value = $props.price.operation_price;
});

async function submit() {
    router.put(`/equip/price/update/${$props.price.id}`, {
        store_date     : store_date     .value,
        contragent_id  : contragent     .value.value,
        notes          : notes          .value,
        store_price    : store_price    .value,
        operation_price: operation_price.value,
    });
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
                        Редактировать стоимость оборудования

                        <DialogClose class="shrink-0 ml-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16.95 8.464a1 1 0 0 0-1.414-1.414L12 10.586L8.464 7.05A1 1 0 1 0 7.05 8.464L10.586 12L7.05 15.536a1 1 0 1 0 1.414 1.414L12 13.414l3.536 3.536a1 1 0 1 0 1.414-1.414L13.414 12z"/></svg>
                        </DialogClose>
                    </DialogTitle>
                    <form class="mt-8 space-y-4" @submit="submit">
                        <UiFieldDate
                            v-model="store_date"
                            label="Дата"
                            :inp-attrs="{ placeholder: 'Укажите дату ремонта' }"
                        />
                        <UiFieldSelect v-model="contragent" :items="formatted_contragents" label="Наименование" />
                        <UiField v-model="notes" label="Описание" />
                        <UiField v-model="store_price" label="Цена хранения, ₽" />
                        <UiField v-model="operation_price" label="Цена за наработку, ₽" />

                        <button
                            :disabled="!is_edited"
                            type="submit"
                            class="w-full mt-4 bg-my-gray text-side-gray-text font-bold px-6 py-3 disabled:opacity-40"
                        >Сохранить</button>
                    </form>
                </DialogContent>
            </transition>
        </DialogPortal>
    </DialogRoot>
</template>
