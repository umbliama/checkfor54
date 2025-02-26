<script setup>
import { EquipMenuItems } from '../../../constants/index.js';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth.user);
</script>

<template>
    <nav class="mt-4 mx-4 px-4 lg:m-0 lg:p-4 bg-my-gray">
        <div class="relative flex items-center flex-col lg:flex-row">
            <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
            <ul class="relative flex items-center w-full font-medium space-x-8 overflow-x-auto lg:overflow-x-visible">
                <li>
                    <Link
                        :href="route('sale.create')"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': page.url === '/sale/create' }"
                        class="flex items-center border-b-2 border-transparent py-3 text-base"
                    >Новая продажа</Link>
                </li>
                <li>
                    <Link
                        :href="route('sale.index')"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': page.url === '/sale' }"
                        class="flex items-center border-b-2 border-transparent py-3 text-base"
                    >История</Link>
                </li>
            </ul>
        </div>
    </nav>
</template>
