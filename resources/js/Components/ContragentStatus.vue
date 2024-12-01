<template>
    <div
        v-if="status == 0"
        class="inline-flex items-center max-w-[155px] py-[1px] px-2 rounded-full text-center text-xs bg-my-gray"
    >
      <span class="relative mr-2 flex h-2 w-2">
        <span
            class="relative inline-flex rounded-full h-2 w-2"
            :style="{ backgroundColor: dotColor }"
        ></span>
      </span>
        <p>{{ elipsedText }}</p>
    </div>
    <div v-else class="inline-flex items-center max-w-[100px] py-[1px] px-2 rounded-full text-center text-xs bg-my-gray">
        <p>{{ elipsedText }}</p>
    </div>
</template>

<script>
import { computed, defineComponent, toRefs } from 'vue';

export default defineComponent({
    name: 'EquipStatus',
    props: {
        status: {
            type: [String, Number],
            default: ''
        },
        pingColor: {
            type: String,
            default: '#38bdf8', // Default color for ping
        },
        dotColor: {
            type: String,
            default: '#0ea5e9', // Default color for dot
        },
    },
    setup(props) {
        const { pingColor, dotColor, status } = toRefs(props);

        const lengthToReplace = 12; // Define how many characters to replace from the end

        const elipsisText = (text) => {
            if (text.length > 7) {
                return text.slice(0, -lengthToReplace) + "...";
            }
            return text;
        };

        const statusTranslate = (status) => {
            switch (status) {
                case 0:
                    return 'Не активный';
                case 1:
                    return 'Активный';
                default:
                    return 'Неизвестный статус';
            }
        };

        // Determine the color for the dot based on status
        const dotColorBasedOnStatus = computed(() => {
            switch (status.value) {
                case 0:
                    return '#DA1E28'; // Green for 'new'
                case 1:
                    return ''; // Blue for 'good'
                default:
                    return '#000000'; // Default color if status is unknown
            }
        });

        // Compute the translated text
        const elipsedText = computed(() => statusTranslate(status.value));

        return {
            pingColor,
            dotColor: dotColorBasedOnStatus, // Use computed color for the dot
            elipsedText
        };
    },
});
</script>
