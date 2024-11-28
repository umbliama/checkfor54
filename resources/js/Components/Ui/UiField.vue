<script setup>
import { computed, ref, useSlots } from "vue";

const value = defineModel();

const $props = defineProps({
    label: String,
    inpAttrs: Object
});

const $slots = useSlots();

const is_focused = ref(false);

const stateClasses = computed(() => {
    return {
        'text-my-black border-selected-blue'                   : value.value || is_focused.value,
        'text-side-gray-text border-my-gray border-b-[#C1C7CD]': !value.value && !is_focused.value,
    }
});

</script>

<template>
    <div
        :class="[ stateClasses ]"
        class="relative inline-flex items-center px-4 border bg-my-gray"
    >
        <span v-if="$slots.prepend" class="shrink-0 inline-flex items-center justify-center w-6 h-6 mr-2">
            <slot name="prepend"></slot>
        </span>
        <input
            v-model="value"
            class="
                w-full h-12 bg-inherit
            "
            type="text"
            v-bind="inpAttrs"
            @focusin="is_focused = true"
            @focusout="is_focused = false"
        >
    </div>
</template>
