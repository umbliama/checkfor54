<script setup>
import { computed, ref, useSlots } from "vue";

const $props = defineProps({
    modelValue: [String, Number],
    label     : String,
    error     : String,
    hint      : String,
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
    'py-2 min-h-10 text-base lg:min-h-12 lg:py-3': $props.size === 'default',
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
                :class="{ 'top-2 lg:top-3': $props.size === 'default', 'top-0.5': $props.size === 'sm' }" class="absolute left-2 inline-flex items-center justify-center w-6 h-6 mr-2">
                <slot name="prepend"></slot>
            </span>
            <component
                :is="tagName"
                v-model="model_value"
                :class="{ 'pl-10': !!$slots.prepend, ...inpSizeClasses }"
                class="w-full px-5 bg-inherit outline-0"
                type="text"
                v-bind="inpAttrs"
                @focusin="is_focused = true"
                @focusout="is_focused = false"
                @blur="$event => $emit('blur', $event.target.value)"
                @input="$event => $emit('update:modelValue', $event.target.value)"
            />
        </div>
        <div v-if="$props.hint" class="mt-1 text-xs text-gray1">{{ $props.hint }}</div>
        <div v-if="$props.error" class="mt-1 text-xs text-red-warning">{{ $props.error }}</div>
    </div>
</template>