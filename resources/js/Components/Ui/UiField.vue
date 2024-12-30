<script setup>
import { computed, ref, useSlots } from "vue";

const $props = defineProps({
    modelValue: [String, Number, null],
    label     : String,
    textarea  : Boolean,
    disabled  : Boolean,
    size      : {
        type: String,
        default: 'default',
        validator(v) {
            return ['sm', 'default'].includes(v);
        }
    },
    inpAttrs  : Object,
});

const $emit = defineEmits([ 'blur', 'update:modelValue' ]);

const $slots = useSlots();

const is_focused = ref(false);

const stateClasses = computed(() => {
    return {
        'text-my-black border-selected-blue'                   : is_focused.value,
        'text-side-gray-text border-my-gray border-b-[#C1C7CD]': !is_focused.value,
    }
});

const inpSizeClasses = computed(() => ({
    'min-h-12 py-3 text-base': $props.size === 'default',
    'min-h-7 py-1.5 text-xs': $props.size === 'sm',
}));

const tagName = computed(() => $props.textarea ? 'textarea' : 'input');

</script>

<template>
    <div :class="[ $props.disabled ? 'opacity-60 pointer-events-none' : '' ]">
        <label v-if="$props.label" class="block mb-2"> {{ $props.label }} <span v-if="$props.inpAttrs?.required" class="text-red-warning">*</span></label>
        <div
            :class="[ stateClasses ]"
            class="relative inline-flex items-center w-full border bg-my-gray"
        >
            <span
                v-if="$slots.prepend"
                :class="{ 'top-3': $props.size === 'default', 'top-0.5': $props.size === 'sm' }" class="absolute left-2 inline-flex items-center justify-center w-6 h-6 mr-2">
                <slot name="prepend"></slot>
            </span>
            <component
                :is="tagName"
                :value="$props.modelValue"
                :class="{ 'pl-10': !!$slots.prepend, ...inpSizeClasses }"
                class="w-full px-5 bg-inherit outline-0"
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
