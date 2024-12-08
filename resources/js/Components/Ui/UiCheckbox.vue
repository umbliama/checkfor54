<script setup>

const is_checked = defineModel();

const $props = defineProps({
    required: Boolean,
    size: {
        type: String,
        default: 'normal',
        validator(v) {
            return ['normal', 'lg'].includes(v);
        }
    }
})

</script>

<template>
    <label>
        <input v-model="is_checked" type="checkbox" hidden :required="$props.required">
        <span
            :class="{ 'w-4 h-4': $props.size === 'normal', 'w-6 h-6 border-2': $props.size === 'lg', 'bg-[#121619]': is_checked }"
            class="flex items-center justify-center border border-[#121619]"
        >
            <svg :class="{ 'w-2.5 h-2': $props.size === 'normal', 'w-4 h-3.5': $props.size === 'lg' }" class="block" width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.5858 5.06065L1.46448 2.93933L0.0502625 4.35354L3.5858 7.88908L9.94976 1.52511L8.53554 0.110901L3.5858 5.06065Z" fill="white"/>
            </svg>
        </span>
    </label>
</template>