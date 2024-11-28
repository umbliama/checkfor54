<script setup>

import { computed } from "vue";

const $props = defineProps({
    variant: {
        type: String,
        default: 'filled',
        validator(v) {
            return ['filled', 'outlined', 'link'];
        }
    },
    href: String,
    badgeCond: Boolean,
    badge: [String, Number],
    icon: {
        type: Boolean,
        default: false
    }
});

const sizes = computed(() => {
    if ($props.icon) {
        return 'w-12 h-12';
    }
});

</script>

<template>
    <component
        :is="$props.href ? 'a' : 'button'"
        :href="$props.href"
        :class="[ sizes ]"
        class="relative inline-flex items-center justify-center"
    >
        <span
            v-if="$props.badge && $props.badgeCond"
            v-text="$props.badge"
            class="absolute top-2 left-1/2 flex justify-center items-center min-w-[18px] rounded-full text-white text-xs bg-danger"
        />

        <slot />
    </component>
</template>
