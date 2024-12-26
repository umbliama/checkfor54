<script setup>
import { Link, router } from '@inertiajs/vue3';

const $props = defineProps({
    selectedCategory: Number,
    selectedSize    : Number,

    categoriesCounts: Object,
    sizesCounts     : Object,

    categories: Array,
    sizes     : Array
});

const $emit = defineEmits(['categoryClick', 'sizeClick']);

</script>

<template>
    <nav class="py-2.5 px-6 rounded-xl text-sm bg-my-gray">
        <div class="relative">
            <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
            <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                <li
                    :class="{ '!border-[#001D6C] text-[#001D6C]': $props.selectedCategory === item.id }"
                    class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                    @click="$emit('categoryClick', item.id)" v-for="item in $props.categories" :key="item.id"
                >
<!--                        :href="route('equip.report', { category_id: item.id })"-->
                    <div
                        class="flex items-center justify-between"
                    >
                        {{ item.name }}
                        <span
                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text"
                        >
                            {{ $props.categoriesCounts[item.id] }}
                        </span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="relative mt-4">
            <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
            <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                <li
                    :class="{ '!border-[#001D6C] text-[#001D6C]': !$props.selectedSize }"
                    class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                    @click="$emit('sizeClick', 0)"
                >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.314 12.071L11.364 7.121C11.1818 6.9324 11.0811 6.67979 11.0833 6.4176C11.0856 6.1554 11.1908 5.90459 11.3762 5.71918C11.5616 5.53377 11.8124 5.4286 12.0746 5.42632C12.3368 5.42405 12.5894 5.52484 12.778 5.707L18.435 11.364C18.6225 11.5515 18.7278 11.8058 18.7278 12.071C18.7278 12.3362 18.6225 12.5905 18.435 12.778L12.778 18.435C12.5894 18.6172 12.3368 18.718 12.0746 18.7157C11.8124 18.7134 11.5616 18.6082 11.3762 18.4228C11.1908 18.2374 11.0856 17.9866 11.0833 17.7244C11.0811 17.4622 11.1818 17.2096 11.364 17.021L16.314 12.071ZM10.314 12.071L5.364 7.121C5.26849 7.02875 5.19231 6.91841 5.1399 6.7964C5.08749 6.6744 5.05991 6.54318 5.05875 6.4104C5.0576 6.27762 5.0829 6.14594 5.13318 6.02304C5.18346 5.90015 5.25772 5.7885 5.35161 5.6946C5.4455 5.60071 5.55715 5.52646 5.68005 5.47618C5.80295 5.4259 5.93463 5.40059 6.06741 5.40175C6.20018 5.4029 6.3314 5.43049 6.45341 5.4829C6.57541 5.53531 6.68576 5.61149 6.778 5.707L12.435 11.364C12.6225 11.5515 12.7278 11.8058 12.7278 12.071C12.7278 12.3362 12.6225 12.5905 12.435 12.778L6.778 18.435C6.5894 18.6172 6.3368 18.718 6.0746 18.7157C5.81241 18.7134 5.56159 18.6082 5.37619 18.4228C5.19078 18.2374 5.08561 17.9866 5.08333 17.7244C5.08105 17.4622 5.18185 17.2096 5.364 17.021L10.314 12.071Z" fill="#21272A"/>
                    </svg>
                </li>
                <li
                    :class="{ '!border-[#001D6C] text-[#001D6C]': $props.selectedSize === item.id }"
                    class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                    @click="$emit('sizeClick', item.id)" v-for="item in $props.sizes" :key="item.id"
                >
<!--                     :href="route('equip.report', { size_id: item.id })"-->

                    <div class="flex items-center justify-between">
                        {{ item.name }}
                        <span
                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text"
                        >
                            {{ $props.sizesCounts[item.id] }}
                        </span>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>
