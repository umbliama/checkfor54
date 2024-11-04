<script setup>


import store from "../..//store";
import { computed, toRaw } from "vue";
import { InertiaLink } from '@inertiajs/inertia-vue3'
import { router } from '@inertiajs/vue3'

const currentPage = computed(() => store.getters['pagination/currentPage']);


const perPageValue = computed({
  get: () => store.getters['pagination/perPage'],
  set: (value) => {
    store.dispatch('pagination/updatePerPage', Number(value));
    changePage('current');
  }
});

const props = defineProps({
  links: {
    type: Array,
    default: () => []

  },
  totalPages: Number,
  nextPageUrl: String,
  prevPageUrl: String,
  currentPage: Number
})

const changePage = (direction) => {
  let targetUrl = null;

  const queryParams = {
    perPage: perPageValue.value,
    page: props.currentPage
  };

  if (direction === 'next' && props.nextPageUrl) {
    targetUrl = props.nextPageUrl;
    queryParams.page = props.currentPage + 1;
  } else if (direction === 'prev' && props.prevPageUrl) {
    targetUrl = props.prevPageUrl;
    queryParams.page = props.currentPage - 1;
  } else if (direction === 'current') {
    targetUrl = window.location.href;
  }

  if (targetUrl) {
    router.get(targetUrl, queryParams, {
      preserveState: true,
      replace: true
    });
  }
};

</script>

<template>
  <div v-if="links.length > 2"
    class="bg-my-gray py-4 pagination-container px-2 flex items-center justify-between bg-my-gray">
    <!-- Show item range and total items -->
    <div class="flex items-center">
      <span>{{ currentPage }} - {{ currentPage }} из {{ totalPages }}</span>
    </div>

    <!-- Dropdown to select items per page -->
    <div class="flex">
      <div class=" flex items-center">
        <label for="itemsPerPage">Отобразить</label>
        <select v-model="perPageValue" id="itemsPerPage" @change="changeItemsPerPage"
          class="border-none bg-transparent focus:none">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
        </select>
      </div>

      <!-- Pagination controls -->
      <div class="flex items-center">
        <!-- Previous button -->
        <button class="mr-2     text-sm leading-4 text-gray-400 border rounded"
          :disabled="!currentPage || currentPage <= 1" @click="changePage('prev')">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M10.686 11.929L15.636 16.879C15.8182 17.0676 15.919 17.3202 15.9167 17.5824C15.9144 17.8446 15.8093 18.0954 15.6239 18.2808C15.4384 18.4662 15.1876 18.5714 14.9254 18.5737C14.6632 18.576 14.4106 18.4752 14.222 18.293L8.56504 12.636C8.37757 12.4485 8.27225 12.1942 8.27225 11.929C8.27225 11.6639 8.37757 11.4095 8.56504 11.222L14.222 5.56502C14.4106 5.38286 14.6632 5.28207 14.9254 5.28434C15.1876 5.28662 15.4384 5.39179 15.6239 5.5772C15.8093 5.76261 15.9144 6.01342 15.9167 6.27562C15.919 6.53781 15.8182 6.79042 15.636 6.97902L10.686 11.929Z"
              fill="#697077" />
          </svg>


        </button>

        <!-- Current page and total pages -->
        <span class="mr-2">
          {{ currentPage }}  <span class="text-gray-inactive">/ {{ totalPages }}</span>
        </span>

        <!-- Next button -->
        <button class=" text-sm leading-4 text-gray-400 border rounded"
          :disabled="!currentPage || currentPage >= totalPages" @click="changePage('next')">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M13.314 12.071L8.36396 7.12098C8.18181 6.93238 8.08101 6.67978 8.08329 6.41758C8.08557 6.15538 8.19074 5.90457 8.37615 5.71916C8.56155 5.53375 8.81237 5.42859 9.07456 5.42631C9.33676 5.42403 9.58936 5.52482 9.77796 5.70698L15.435 11.364C15.6224 11.5515 15.7278 11.8058 15.7278 12.071C15.7278 12.3361 15.6224 12.5905 15.435 12.778L9.77796 18.435C9.58936 18.6171 9.33676 18.7179 9.07456 18.7157C8.81237 18.7134 8.56155 18.6082 8.37615 18.4228C8.19074 18.2374 8.08557 17.9866 8.08329 17.7244C8.08101 17.4622 8.18181 17.2096 8.36396 17.021L13.314 12.071Z"
              fill="#697077" />
          </svg>

        </button>
      </div>
    </div>
  </div>

</template>
