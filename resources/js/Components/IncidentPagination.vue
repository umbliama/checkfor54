<template>
    <div class="flex justify-center mt-6 px-4 bg-bg1">
      <!-- Previous Button -->
      <button
        type="button"
        class="flex items-center justify-center w-10 h-10"
        :disabled="currentPage === 1"
        @click="changePage(currentPage - 1)"
      >
        <svg width="25" height="24" viewBox="0 0 25 24" fill="none">
          <path
            d="M11.257 11.9999L16.207 16.9499C16.3025 17.0422 16.3787 17.1525 16.4311 17.2745C16.4835 17.3965 16.5111 17.5278 16.5122 17.6605C16.5134 17.7933 16.4881 17.925 16.4378 18.0479C16.3875 18.1708 16.3133 18.2824 16.2194 18.3763C16.1255 18.4702 16.0138 18.5445 15.8909 18.5948C15.768 18.645 15.6364 18.6703 15.5036 18.6692C15.3708 18.668 15.2396 18.6404 15.1176 18.588C14.9956 18.5356 14.8852 18.4594 14.793 18.3639L9.13599 12.7069C8.94852 12.5194 8.8432 12.2651 8.8432 11.9999C8.8432 11.7348 8.94852 11.4805 9.13599 11.2929L14.793 5.63594C14.9816 5.45378 15.2342 5.35298 15.4964 5.35526C15.7586 5.35754 16.0094 5.46271 16.1948 5.64812C16.3802 5.83353 16.4854 6.08434 16.4877 6.34654C16.4899 6.60873 16.3891 6.86133 16.207 7.04994L11.257 11.9999Z"
            fill="#697077"
          />
        </svg>
      </button>
  
      <!-- Pagination Numbers -->
      <button
        v-if="currentPage > 1"
        type="button"
        class="flex items-center justify-center w-10 h-10 cursor-pointer"
        @click="changePage(currentPage - 1)"
      >
        {{ currentPage - 1 }}
      </button>
  
      <button
        type="button"
        class="flex items-center justify-center w-10 h-10 bg-white border-b border-b-gray1"
      >
        {{ currentPage }}
      </button>
  
      <button
        v-if="currentPage < totalPages"
        type="button"
        class="flex items-center justify-center w-10 h-10 cursor-pointer"
        @click="changePage(currentPage + 1)"
      >
        {{ currentPage + 1 }}
      </button>
  
      <!-- Next Button -->
      <button
        type="button"
        class="flex items-center justify-center w-10 h-10"
        :disabled="currentPage === totalPages"
        @click="changePage(currentPage + 1)"
      >
        <svg width="25" height="24" viewBox="0 0 25 24" fill="none">
          <path
            d="M13.814 12.071L8.86399 7.12098C8.68184 6.93238 8.58104 6.67978 8.58332 6.41758C8.5856 6.15538 8.69077 5.90457 8.87618 5.71916C9.06158 5.53375 9.3124 5.42859 9.57459 5.42631C9.83679 5.42403 10.0894 5.52482 10.278 5.70698L15.935 11.364C16.1225 11.5515 16.2278 11.8058 16.2278 12.071C16.2278 12.3361 16.1225 12.5905 15.935 12.778L10.278 18.435C10.0894 18.6171 9.83679 18.7179 9.57459 18.7157C9.3124 18.7134 9.06158 18.6082 8.87618 18.4228C8.69077 18.2374 8.5856 17.9866 8.58332 17.7244C8.58104 17.4622 8.68184 17.2096 8.86399 17.021L13.814 12.071Z"
            fill="#697077"
          />
        </svg>
      </button>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from "vue";
  import { router } from '@inertiajs/vue3'
  
  const props = defineProps({
    totalPages: Number,
    modelValue: Number,
  });
  
  const emit = defineEmits(["update:modelValue"]);
  
  const currentPage = ref(props.modelValue);
  
  watch(
    () => props.modelValue,
    (newVal) => {
      currentPage.value = newVal;
    }
  );

  const changePage = (page) => {
    if (page < 1 || page > props.totalPages) return;

    const queryParams = new URLSearchParams(window.location.search);
    queryParams.set("page", page);

    const newUrl = `${window.location.pathname}?${queryParams.toString()}`;

    router.get(newUrl, {}, {
        preserveState: true,
        replace: true
    });
};

  </script>
  