<template>
    <div class="flex items-center justify-evenly">
      <span class="relative flex h-2 w-2">

        <span
          class="relative inline-flex rounded-full h-2 w-2"
          :style="{ backgroundColor: dotColor }"
        ></span>
      </span>
      <p>{{ elipsedText }}</p>
    </div>
  </template>
  
  <script>
  import { computed, defineComponent, toRefs } from 'vue';
  
  export default defineComponent({
    name: 'EquipStatus',
    props: {
      status: {
        type: String,
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
          case 'new':
            return 'Новое';
          case 'good':
            return 'Хорошее';
          case 'satisfactory':
            return 'Удовлетворительно';
          case 'bad':
            return 'Плохое';
          case 'off':
            return 'Списано';
          default:
            return 'Неизвестный статус';
        }
      };
  
      // Determine the color for the dot based on status
      const dotColorBasedOnStatus = computed(() => {
        switch (status.value) {
          case 'new':
            return '#C9BFFF'; // Green for 'new'
          case 'good':
            return '#C9BFFF'; // Blue for 'good'
          case 'satisfactory':
            return '#C9BFFF'; // Yellow for 'satisfactory'
          case 'bad':
            return '#DA1E28'; // Red for 'bad'
          case 'off':
            return '#F3F1FE'; // Gray for 'off'
          default:
            return '#000000'; // Default color if status is unknown
        }
      });
  
      // Compute the translated text
      const elipsedText = computed(() => elipsisText(statusTranslate(status.value)));
  
      return {
        pingColor,
        dotColor: dotColorBasedOnStatus, // Use computed color for the dot
        elipsedText
      };
    },
  });
  </script>
  