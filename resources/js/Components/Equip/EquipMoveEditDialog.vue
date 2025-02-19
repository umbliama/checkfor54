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
    title    : String,
    locations: Array,
    move     : Object
});

const $emit = defineEmits(['edit']);

const send_date      = ref(null);
const departure_date = ref(null);
const from           = ref(null);
const to             = ref(null);
const reason         = ref(null);
const expense        = ref(null);

const formatted_locations = computed(() => {
    return $props.locations.map(l=>({ title: l.name, value: l.id }));
});

watch(model, new_val => {
    if (!new_val) return;

    send_date     .value = $props.move.send_date;
    departure_date.value = $props.move.departure_date;
    from          .value = { title: $props.locations.find(l=>l.id==$props.move.from)?.name || $props.locations[0].id, value: $props.move.from };
    to            .value = { title: $props.locations.find(l=>l.id==$props.move.to)  ?.name || $props.locations[0].id, value: $props.move.to   };
    reason        .value = $props.move.reason;
    expense       .value = $props.move.expense;
});

async function submit() {
    router.put(`/equip/move/update/${$props.move.id}`, {
        send_date     : send_date     .value,
        departure_date: departure_date.value,
        from          : from          .value.value,
        to            : to            .value.value,
        reason        : reason        .value,
        expense       : expense       .value,
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
                        Редактировать перемещние

                        <DialogClose class="shrink-0 ml-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16.95 8.464a1 1 0 0 0-1.414-1.414L12 10.586L8.464 7.05A1 1 0 1 0 7.05 8.464L10.586 12L7.05 15.536a1 1 0 1 0 1.414 1.414L12 13.414l3.536 3.536a1 1 0 1 0 1.414-1.414L13.414 12z"/></svg>
                        </DialogClose>
                    </DialogTitle>
                    <div class="mt-8 space-y-4" >
                        <UiFieldDate
                            v-model="send_date"
                            label="Дата отправки"
                            :inp-attrs="{ placeholder: 'Укажите дату отправки' }"
                        />
                        <UiFieldDate
                            v-model="departure_date"
                            label="Дата прибытия"
                            :inp-attrs="{ placeholder: 'Укажите дату прибытия' }"
                        />
                        <UiFieldSelect v-model="from" :items="formatted_locations" label="Откуда" />
                        <UiFieldSelect v-model="to" :items="formatted_locations" label="Куда" />
                        <UiField v-model="reason" label="Описание" />
                        <UiField v-model="expense" :inp-attrs="{ type: 'number' }" label="Расход, ₽" />

                        <button @click="submit"
                            :disabled="false"
                            type="submit"
                            class="w-full mt-4 bg-my-gray text-side-gray-text font-bold px-6 py-3 disabled:opacity-40"
                        >Сохранить</button>
                    </div>
                </DialogContent>
            </transition>
        </DialogPortal>
    </DialogRoot>
</template>
