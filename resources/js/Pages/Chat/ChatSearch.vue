<script setup>
import { ref, computed } from 'vue';

const searchQuery = ref('');
const emit = defineEmits(['update:search']);

const updateSearch = () => {
  emit('update:search', searchQuery.value);
};
</script>

<template>
  <div class="">
    <input
      type="text"
      v-model="searchQuery"
      @input="updateSearch"
      placeholder="Найти сотрудника или чат"
      class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300"
    />
  </div>
</template>