<script setup>
import { computed, ref, useSlots } from "vue";

const $props = defineProps({
    modelValue: [String, Number, null],
    label     : String,
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
        'text-my-black border-selected-blue'                   : $props.modelValue || is_focused.value,
        'text-side-gray-text border-my-gray border-b-[#C1C7CD]': !$props.modelValue && !is_focused.value,
    }
});

const inpSizeClasses = computed(() => ({
    'min-h-12 text-base': $props.size === 'default',
    'min-h-7 text-xs': $props.size === 'sm',
}));

</script>

<template>
    <div :class="[ $props.disabled ? 'opacity-60 pointer-events-none' : '' ]">
        <label v-if="$props.label" class="block mb-2"> {{ $props.label }} <span v-if="$props.inpAttrs?.required" class="text-red-warning">*</span></label>
        <label
            :class="[ stateClasses ]"
            class="relative inline-flex items-center w-full border bg-my-gray"
        >
            <span v-if="$slots.prepend" :class="{ 'top-3': $props.size === 'default', 'top-0.5': $props.size === 'sm' }" class="absolute left-2 inline-flex items-center justify-center w-6 h-6 mr-2">
                <slot name="prepend"></slot>
            </span>
            <input
                :value="$props.modelValue"
                type="date"
                onclick="this.showPicker()"
                class="absolute bottom-0 opacity-0 pointer-events-none"
                @focusin="is_focused = true"
                @focusout="is_focused = false"
                @blur="$event=>$emit('blur', $event)"
                @input="$emit('update:modelValue', $event.target.value)"
            />
            <div
                v-bind="inpAttrs"
                :class="{ 'pl-10': !!$slots.prepend, ...inpSizeClasses }"
                class="flex items-center justify-between w-full px-5 outline-0"
            >
                {{ $props.modelValue || $props?.inpAttrs?.placeholder }}

                <svg class="ml-3" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 10V8C20 7.73478 19.8946 7.48043 19.7071 7.29289C19.5196 7.10536 19.2652 7 19 7H18V8C18 8.26522 17.8946 8.51957 17.7071 8.70711C17.5196 8.89464 17.2652 9 17 9C16.7348 9 16.4804 8.89464 16.2929 8.70711C16.1054 8.51957 16 8.26522 16 8V7H8V8C8 8.26522 7.89464 8.51957 7.70711 8.70711C7.51957 8.89464 7.26522 9 7 9C6.73478 9 6.48043 8.89464 6.29289 8.70711C6.10536 8.51957 6 8.26522 6 8V7H5C4.73478 7 4.48043 7.10536 4.29289 7.29289C4.10536 7.48043 4 7.73478 4 8V10H20ZM20 12H4V18C4 18.2652 4.10536 18.5196 4.29289 18.7071C4.48043 18.8946 4.73478 19 5 19H19C19.2652 19 19.5196 18.8946 19.7071 18.7071C19.8946 18.5196 20 18.2652 20 18V12ZM18 5H19C19.7956 5 20.5587 5.31607 21.1213 5.87868C21.6839 6.44129 22 7.20435 22 8V18C22 18.7956 21.6839 19.5587 21.1213 20.1213C20.5587 20.6839 19.7956 21 19 21H5C4.20435 21 3.44129 20.6839 2.87868 20.1213C2.31607 19.5587 2 18.7956 2 18V8C2 7.20435 2.31607 6.44129 2.87868 5.87868C3.44129 5.31607 4.20435 5 5 5H6V4C6 3.73478 6.10536 3.48043 6.29289 3.29289C6.48043 3.10536 6.73478 3 7 3C7.26522 3 7.51957 3.10536 7.70711 3.29289C7.89464 3.48043 8 3.73478 8 4V5H16V4C16 3.73478 16.1054 3.48043 16.2929 3.29289C16.4804 3.10536 16.7348 3 17 3C17.2652 3 17.5196 3.10536 17.7071 3.29289C17.8946 3.48043 18 3.73478 18 4V5Z" fill="#697077"/>
                </svg>
            </div>
        </label>
    </div>
</template>

<style scoped>

input::-webkit-inner-spin-button,
input::-webkit-calendar-picker-indicator {
    display: none;
    -webkit-appearance: none;
}

input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20 10V8C20 7.73478 19.8946 7.48043 19.7071 7.29289C19.5196 7.10536 19.2652 7 19 7H18V8C18 8.26522 17.8946 8.51957 17.7071 8.70711C17.5196 8.89464 17.2652 9 17 9C16.7348 9 16.4804 8.89464 16.2929 8.70711C16.1054 8.51957 16 8.26522 16 8V7H8V8C8 8.26522 7.89464 8.51957 7.70711 8.70711C7.51957 8.89464 7.26522 9 7 9C6.73478 9 6.48043 8.89464 6.29289 8.70711C6.10536 8.51957 6 8.26522 6 8V7H5C4.73478 7 4.48043 7.10536 4.29289 7.29289C4.10536 7.48043 4 7.73478 4 8V10H20ZM20 12H4V18C4 18.2652 4.10536 18.5196 4.29289 18.7071C4.48043 18.8946 4.73478 19 5 19H19C19.2652 19 19.5196 18.8946 19.7071 18.7071C19.8946 18.5196 20 18.2652 20 18V12ZM18 5H19C19.7956 5 20.5587 5.31607 21.1213 5.87868C21.6839 6.44129 22 7.20435 22 8V18C22 18.7956 21.6839 19.5587 21.1213 20.1213C20.5587 20.6839 19.7956 21 19 21H5C4.20435 21 3.44129 20.6839 2.87868 20.1213C2.31607 19.5587 2 18.7956 2 18V8C2 7.20435 2.31607 6.44129 2.87868 5.87868C3.44129 5.31607 4.20435 5 5 5H6V4C6 3.73478 6.10536 3.48043 6.29289 3.29289C6.48043 3.10536 6.73478 3 7 3C7.26522 3 7.51957 3.10536 7.70711 3.29289C7.89464 3.48043 8 3.73478 8 4V5H16V4C16 3.73478 16.1054 3.48043 16.2929 3.29289C16.4804 3.10536 16.7348 3 17 3C17.2652 3 17.5196 3.10536 17.7071 3.29289C17.8946 3.48043 18 3.73478 18 4V5Z' fill='%23697077'/%3E%3C/svg%3E%0A");
    background-position: calc(100% - 16px) center;
    background-repeat: no-repeat;
}

</style>
