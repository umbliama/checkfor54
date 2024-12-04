<script setup>
import {
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectItemIndicator,
    SelectItemText,
    SelectPortal,
    SelectRoot,
    SelectTrigger,
    SelectValue,
    SelectViewport,
} from "radix-vue";
import { computed, onMounted } from "vue";

const value = defineModel();

const $props = defineProps({
    items: Array, // [ { title: 'Title', value: 'some_val' } ]
    placeholder: String
});

onMounted(() => {
    if (!value.value) value.value = { ...$props.items[0] };
})

</script>

<template>
    <SelectRoot v-model="value">
        <SelectTrigger
            class="inline-flex items-center justify-between min-w-44 py-3 px-4 border-b border-b-[#C1C7CD] bg-my-gray text-side-gray-text lg:hidden"
            aria-label="Customise options"
        >
            <SelectValue>
                {{ value?.title }}
            </SelectValue>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.071 13.314L17.021 8.364C17.1133 8.26849 17.2236 8.19231 17.3456 8.1399C17.4676 8.08749 17.5988 8.05991 17.7316 8.05875C17.8644 8.0576 17.9961 8.0829 18.119 8.13318C18.2419 8.18346 18.3535 8.25772 18.4474 8.35161C18.5413 8.4455 18.6156 8.55715 18.6658 8.68005C18.7161 8.80295 18.7414 8.93463 18.7403 9.06741C18.7391 9.20018 18.7115 9.3314 18.6591 9.45341C18.6067 9.57541 18.5305 9.68576 18.435 9.778L12.778 15.435C12.5905 15.6225 12.3362 15.7278 12.071 15.7278C11.8059 15.7278 11.5515 15.6225 11.364 15.435L5.70702 9.778C5.61151 9.68576 5.53533 9.57541 5.48292 9.45341C5.43051 9.3314 5.40292 9.20018 5.40177 9.06741C5.40062 8.93463 5.42592 8.80295 5.4762 8.68005C5.52648 8.55715 5.60073 8.4455 5.69463 8.35161C5.78852 8.25772 5.90017 8.18346 6.02307 8.13318C6.14596 8.0829 6.27764 8.0576 6.41042 8.05875C6.5432 8.05991 6.67442 8.08749 6.79643 8.1399C6.91843 8.19231 7.02877 8.26849 7.12102 8.364L12.071 13.314Z" fill="#697077"/>
            </svg>
        </SelectTrigger>

        <SelectPortal>
            <SelectContent
                :side-offset="5"
                position="popper"
                class="bg-white rounded z-[100] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
            >
                <SelectViewport class="p-[5px]">
                    <SelectGroup>
                        <SelectItem
                            v-for="(option, index) in $props.items"
                            :key="index"
                            :value="option"
                            :class="{ '!bg-[#464F60] text-white': option?.value === value?.value }"
                            class="text-[13px] leading-none text-grass11 rounded-[3px] flex items-center h-[25px] pr-[35px] pl-[25px] relative outline-none"
                        >
                            <SelectItemText >
                                {{ option.title }}
                            </SelectItemText>
                        </SelectItem>
                    </SelectGroup>
                </SelectViewport>
            </SelectContent>
        </SelectPortal>
    </SelectRoot>
</template>
