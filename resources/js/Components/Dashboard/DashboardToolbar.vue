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
                <li
                    :class="{ '!border-[#001D6C] text-[#001D6C]': $props.dashboardPageType === 'commercial' }"
                    class="border-b-2 border-transparent cursor-pointer"
                >
                    <Link :href="route('commercial')" class="flex items-center py-3">
                        КП
                    </Link>
                </li>
            </ul>

            <div v-if="$props.filter" class="flex w-full mt-4 lg:w-auto lg:mt-0">
                <UiFieldDate v-model="start_date" :inp-attrs="{ placeholder: 'Начало', class: 'bg-white' }" class="w-[calc(100%/2-12px)] lg:w-[178px]" />
                <button
                    class="flex items-center justify-center w-6 h-12 bg-[#DDE1E6] text-gray1 lg:w-12"
                >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.75 6.81984L17.1802 5.25L12 10.4302L6.81984 5.25L5.25 6.81984L10.4302 12L5.25 17.1802L6.81984 18.75L12 13.5698L17.1802 18.75L18.75 17.1802L13.5698 12L18.75 6.81984Z" fill="#697077"/>
                    </svg>
                </button>
                <UiFieldDate v-model="end_date" :inp-attrs="{ placeholder: 'Завершение', class: 'bg-white' }" class="w-[calc(100%/2-12px)] lg:w-[178px]" />
            </div>
        </div>
    </nav>
</template>