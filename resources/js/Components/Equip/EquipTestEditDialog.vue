<script setup>
import {
    DialogClose,
    DialogContent,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'radix-vue';
import { computed, nextTick, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import UiField from "@/Components/Ui/UiField.vue";
import UiFieldDate from "@/Components/Ui/UiFieldDate.vue";
import UiFieldSelect from "@/Components/Ui/UiFieldSelect.vue";


const model = defineModel();

const $props = defineProps({
    title    : String,
    locations: Array,
    test   : Object
});

const $emit = defineEmits(['edit']);

const date        = ref(null);
const location    = ref(null);
const description = ref(null);
const expense     = ref(null);

const formatted_locations = computed(() => {
    return $props.locations.map(l=>({ title: l.name, value: l.id }));
});

const is_edited = computed(() => {
    const curr = $props.test;

    return curr.test_date != date?.value || curr.location_id != location?.value.value
            || curr.description != description?.value || curr.expense != expense?.value;
});

watch(model, new_val => {
    if (!new_val) return;

    date       .value = $props.test.test_date;
    location   .value = { title: $props.locations.find(l=>l.id==$props.test.location_id).name, value: $props.test.location_id };
    description.value = $props.test.description;
    expense    .value = $props.test.expense;
});

async function submit() {
    /*await fetch(`/equip/tests/update/${$props.test.id}`, {
        method: 'put',
        body: JSON.stringify({
            test_date  : date       .value,
            location_id: location   .value.value,
            description: description.value,
            expense    : expense    .value,
        })
    });*/

    router.put(`/equip/tests/update/${$props.test.id}`, {
        test_date  : date       .value,
        location_id: location   .value.value,
        description: description.value,
        expense    : expense    .value,
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
                        Редактировать запись испытания

                        <DialogClose class="shrink-0 ml-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16.95 8.464a1 1 0 0 0-1.414-1.414L12 10.586L8.464 7.05A1 1 0 1 0 7.05 8.464L10.586 12L7.05 15.536a1 1 0 1 0 1.414 1.414L12 13.414l3.536 3.536a1 1 0 1 0 1.414-1.414L13.414 12z"/></svg>
                        </DialogClose>
                    </DialogTitle>
                    <form class="mt-8 space-y-4" @submit="submit">
                        <UiFieldDate
                            v-model="date"
                            label="Дата ремонта"
                            :inp-attrs="{ placeholder: 'Укажите дату ремонта' }"
                        />
                        <UiFieldSelect v-model="location" :items="formatted_locations" label="Место проведения" />
                        <UiField v-model="description" label="Описание" />
                        <UiField v-model="expense" label="Расход, ₽" />

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
