<script setup>
import { computed, ref, useSlots } from "vue";

const $props = defineProps({
    modelValue: [String, Number, null],
    label     : String,
    inpAttrs  : Object,
    textarea  : Boolean,
});

const $emit = defineEmits([ 'blur', 'update:modelValue' ]);

const $slots = useSlots();

const is_focused = ref(false);

const stateClasses = computed(() => {
    return {
        'text-my-black border-selected-blue'                   : $props.modelValue || is_focused.value,
        'text-side-gray-text border-my-gray border-b-[#C1C7CD]': !$props.modelValue && !is_focused.value,
    }
});

const tagName = computed(() => $props.textarea ? 'textarea' : 'input');

</script>

<template>
    <div>
        <label v-if="$props.label" class="block"> {{ $props.label }} <span v-if="$props.inpAttrs.required" class="text-red-warning">*</span></label>
        <div
            :class="[ stateClasses ]"
            class="relative inline-flex items-center w-full mt-2 border bg-my-gray"
        >
            <span v-if="$slots.prepend" class="absolute left-2 top-3 inline-flex items-center justify-center w-6 h-6 mr-2">
                <slot name="prepend"></slot>
            </span>
            <component
                :is="tagName"
                :value="$props.modelValue"
                :class="{ 'pl-10': !!$slots.prepend }"
                class="
                    w-full min-h-12 py-3 px-4 bg-inherit outline-0
                "
                type="text"
                v-bind="inpAttrs"
                @focusin="is_focused = true"
                @focusout="is_focused = false"
                @blur="$event=>$emit('blur', $event)"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </div>
    </div>
</template>
