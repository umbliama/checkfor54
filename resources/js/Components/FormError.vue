<template>
  <div v-if="visible"
    class="fixed top-0 left-0 w-full bg-red-500 text-white p-4 shadow-lg transform transition-transform translate-y-0 z-20"
    role="alert">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <span class="font-bold">Ошибка:</span>
      <ul class="ml-2">
        <li v-for="(msg, index) in message" :key="index">{{ msg }}</li>
      </ul>
      <button @click="closeAlert" class="ml-auto bg-transparent text-white font-bold px-2 focus:outline-none">
        ✕
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';

export default {
  props: {
    message: {
      type: String,
      required: true,
    },
    autoClose: {
      type: Boolean,
      default: true,
    },
    closeAfter: {
      type: Number,
      default: 5000, // default is 5 seconds
    },
  },
  setup(props) {
    const visible = ref(true);

    const closeAlert = () => {
      visible.value = false;
    };

    onMounted(() => {
      if (props.autoClose) {
        setTimeout(() => {
          closeAlert();
        }, props.closeAfter);
      }
    });

    return {
      visible,
      closeAlert,
    };
  },
};
</script>

<style scoped>
/* Add any additional styles if needed */
</style>