<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex flex-col justify-center items-center min-h-screen p-6 bg-bg1">
        <div
            class="w-full max-w-[680px] overflow-y-auto py-8 px-4 bg-white shadow-md overflow-hidden lg:p-20"
        >
            <slot />
        </div>
    </div>
</template>
