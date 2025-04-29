    <script setup>
import { SwitchRoot, SwitchThumb } from "radix-vue";

const $props = defineProps({
    requiredSize: Boolean,

    selectedCategory: Number,
    selectedSize    : Number,

    categoriesCounts: Object,
    sizesCounts     : Object,

    categories: Array,
    sizes     : Array,

    showSizeSwitch: {
        type: Boolean,
        default: false
    }
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
                        <span v-if="$props.categoriesCounts[item.id] !== 0"
                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text"
                        >
                            {{ $props.categoriesCounts[item.id] || 0 }}
                        </span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="relative mt-4">
            <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
            <ul class="relative flex items-center w-full font-medium space-x-6 overflow-x-auto">
                <li v-if="$props.showSizeSwitch">
                    <SwitchRoot
                        id="airplane-mode"
                        v-model="switchState"
                        v-slot="{ checked }"
                    >
                        <div
                            :class="{
                                'bg-side-gray-text': checked,
                                'bg-side-gray-text/40': !checked,
                            }"
                            class="w-[30px] h-4 flex relative rounded-full"
                        >
                            <SwitchThumb
                                :class="{
                                    'translate-x-[calc(100%+1px)]': checked,
                                    'translate-x-px': !checked,
                                }"
                                class="w-3.5 h-3.5 my-auto bg-white rounded-full transition-transform will-change-transform"
                            />
                        </div>
                    </SwitchRoot>
                </li>

                <li
                    v-if="!$props.requiredSize"
                    :class="{ '!border-[#001D6C] text-[#001D6C]': !$props.selectedSize }"
                    class="flex items-center border-b-2 border-transparent py-3 cursor-pointer"
                    @click="$emit('sizeClick', 0)"
                >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_3234_25341)">
                            <path d="M3.5 5.5L5 7L7.5 4.5" stroke="#21272A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3.5 11.5L5 13L7.5 10.5" stroke="#21272A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3.5 17.5L5 19L7.5 16.5" stroke="#21272A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11 6H20" stroke="#21272A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11 12H20" stroke="#21272A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11 18H20" stroke="#21272A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_3234_25341">
                                <rect width="24" height="24" fill="white"/>
                            </clipPath>
                        </defs>
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
                        <span v-if="$props.sizesCounts[item.id] !== 0"
                            class="flex items-center h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text"
                        >
                            {{ $props.sizesCounts[item.id] || 0}}
                        </span>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>
