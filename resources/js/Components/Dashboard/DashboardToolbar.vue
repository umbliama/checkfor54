<script setup>
import { usePage } from '@inertiajs/inertia-vue3';
import UiFieldDate from '../Ui/UiFieldDate.vue';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const $props = defineProps({
    dashboardPageType: String,
    filter           : Boolean
});

const start_date = ref();
const end_date   = ref();

</script>

<template>
    <nav class="p-4 bg-my-gray">
        <div class="relative flex items-center flex-col lg:flex-row">
            <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
            
            <ul class="relative flex items-center w-full font-medium space-x-8 overflow-x-auto text-nowrap lg:overflow-x-visible">
                <li
                    :class="{ '!border-[#001D6C] text-[#001D6C]': $props.dashboardPageType === 'rent' }"
                    class="border-b-2 border-transparent cursor-pointer"
                >
                    <Link :href="route('rent')" class="flex items-center py-3">
                        В аренде
                    </Link>
                </li>
                <li
                    :class="{ '!border-[#001D6C] text-[#001D6C]': $props.dashboardPageType === 'free' }"
                    class="border-b-2 border-transparent cursor-pointer"
                >
                    <Link :href="route('free')" class="flex items-center py-3">
                        Свободно
                    </Link>
                </li>
                <li
                    :class="{ '!border-[#001D6C] text-[#001D6C]': $props.dashboardPageType === 'serviced' }"
                    class="border-b-2 border-transparent cursor-pointer"
                >
                    <Link :href="route('serviced')" class="flex items-center py-3">
                        На сервисе
                    </Link>
                </li>
                <li
                    :class="{ '!border-[#001D6C] text-[#001D6C]': $props.dashboardPageType === 'analysis' }"
                    class="border-b-2 border-transparent cursor-pointer"
                >
                    <Link :href="route('analysis')" class="flex items-center py-3">
                        Анализ
                    </Link>
                </li>
            </ul>

            <div v-if="$props.filter" class="hidden w-full mt-4 lg:w-auto lg:mt-0 lg:flex">
                <UiFieldDate v-model="start_date" :inp-attrs="{ placeholder: 'Начало', class: 'bg-white' }" class="w-[calc(100%/2-12px)] lg:w-[178px]" />
                <button
                    class="flex items-center justify-center w-6 h-12 bg-[#DDE1E6] text-gray1 lg:w-12"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.75 10.75h-3m12.5-2c0 3-2.798 5.5-6.25 5.5c-3.75 0-6.25-3.5-6.25-3.5v3.5m9.5-9h3m-12.5 2c0-3 2.798-5.5 6.25-5.5c3.75 0 6.25 3.5 6.25 3.5v-3.5"/></svg>
                </button>
                <UiFieldDate v-model="end_date" :inp-attrs="{ placeholder: 'Завершение', class: 'bg-white' }" class="w-[calc(100%/2-12px)] lg:w-[178px]" />
            </div>
        </div>
    </nav>
</template>