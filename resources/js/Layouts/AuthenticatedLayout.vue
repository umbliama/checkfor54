<script setup>
import { ref } from 'vue';
import SideMenu from '@/Layouts/SideMenu.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';


</script>

<template>
    <div class="flex">
        <div class="w-3/12">
        <SideMenu class="" />

        </div>
        <div class="w-4/5 min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">

           
            </nav>

            <header class="bg-white shadow" v-if="$slots.header">
                <div class="flex max-w-7xl mx-auto bg-my-gray py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
