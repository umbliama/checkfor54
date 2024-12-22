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
