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
    badge: [String, Number],
    badgeCond: Boolean,
    badgeColor: {
        type: String,
        default: 'bg-danger',
    },
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
            :class="[$props.badgeColor]"
            class="absolute top-2 left-1/2 flex justify-center items-center min-w-[18px] rounded-full text-white text-xs"
        />

        <slot />
    </component>
</template>
