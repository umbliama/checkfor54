<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';



const props = defineProps({
    equipment: Array,
    equipment_categories: Array,
    equipment_sizes: Array

})

const isDropdownOpen = ref(false);

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const closeDropdown = () => {
    isDropdownOpen.value = false;
};

const selectedCategory = ref(new URLSearchParams(window.location.search).get('category') || '');


function filterByCategory() {
  // Update URL query parameters
  const query = new URLSearchParams(window.location.search);
  if (selectedCategory.value) {
    query.set('category', selectedCategory.value);
  } else {
    query.delete('category');
  }
  const newUrl = `${window.location.pathname}?${query.toString()}`;
  window.history.replaceState(null, '', newUrl);

  // Fetch data from the server
  get(newUrl);
}


// Fetch initial data on component mount
onMounted(() => {
  if (selectedCategory.value) {
    filterByCategory();
  }
});


</script>

<template>

    <AuthenticatedLayout>
        <nav class="bg-white-50 dark:my-gray">
            <div class="max-w-screen-xl px-4 py-3 mx-auto">
                <div class="mt-6 flex items-center">
                    <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                        <li class="text-my-nav-text text-lg" :class="{ 'bg-gray-200 text-blue-600': selectedCategory === item.category_id }"  v-for="item in equipment_categories" :key="item.id">
                            {{ item.name }}
                        </li>
                    </ul>
                </div>
                <div class="mt-6 flex items-center">
                    <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                        <li class="text-my-nav-text text-lg" v-for="item in equipment_sizes" :key="item.id">
                            {{ item.name }}
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <table class="w-full table-auto">
            <thead>
                <tr>
                    <th></th>
                    <th>Производитель</th>
                    <th>Cерия</th>
                    <th>Дата изготовления</th>
                    <th>Состояние</th>
                    <th>Стоимость</th>
                    <th>Примечание</th>
                    <th class="flex justify-center" ><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M2.25 11.625C2.25 6.44782 6.44782 2.25 11.625 2.25C16.8022 2.25 21 6.44782 21 11.625C21 16.8022 16.8022 21 11.625 21C6.44782 21 2.25 16.8022 2.25 11.625ZM11.625 3.75C7.27624 3.75 3.75 7.27624 3.75 11.625C3.75 15.9738 7.27624 19.5 11.625 19.5C15.9738 19.5 19.5 15.9738 19.5 11.625C19.5 7.27624 15.9738 3.75 11.625 3.75Z"
                                fill="#21272A" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M9.5625 10.3125C9.5625 9.89829 9.89829 9.5625 10.3125 9.5625H11.8125C12.2267 9.5625 12.5625 9.89829 12.5625 10.3125V15.75C12.5625 16.1642 12.2267 16.5 11.8125 16.5C11.3983 16.5 11.0625 16.1642 11.0625 15.75V11.0625H10.3125C9.89829 11.0625 9.5625 10.7267 9.5625 10.3125Z"
                                fill="#21272A" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M9 15.9375C9 15.5233 9.33579 15.1875 9.75 15.1875H13.875C14.2892 15.1875 14.625 15.5233 14.625 15.9375C14.625 16.3517 14.2892 16.6875 13.875 16.6875H9.75C9.33579 16.6875 9 16.3517 9 15.9375Z"
                                fill="#21272A" />
                            <path
                                d="M11.625 6.09375C11.384 6.09375 11.1483 6.16523 10.9479 6.29915C10.7475 6.43306 10.5913 6.62341 10.499 6.8461C10.4068 7.0688 10.3826 7.31385 10.4297 7.55027C10.4767 7.78668 10.5928 8.00384 10.7632 8.17429C10.9337 8.34473 11.1508 8.46081 11.3872 8.50783C11.6236 8.55486 11.8687 8.53072 12.0914 8.43848C12.3141 8.34623 12.5044 8.19002 12.6384 7.9896C12.7723 7.78918 12.8438 7.55355 12.8438 7.3125C12.8438 6.98927 12.7153 6.67927 12.4868 6.45071C12.2582 6.22215 11.9482 6.09375 11.625 6.09375Z"
                                fill="#21272A" />
                        </svg>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in equipment">
                    <td class="flex  justify-center text-center border border-slate-200 p-2"><svg width="20" height="20"
                            viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.9999 7.66706V11.667C10.9999 12.0206 10.8594 12.3598 10.6094 12.6098C10.3594 12.8599 10.0202 13.0003 9.6666 13.0003H2.33332C1.9797 13.0003 1.64057 12.8599 1.39052 12.6098C1.14047 12.3598 1 12.0206 1 11.667V4.33375C1 3.98013 1.14047 3.641 1.39052 3.39095C1.64057 3.1409 1.9797 3.00043 2.33332 3.00043H6.33329"
                                stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 1H13V4.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M5.66663 8.33328L12.9999 1" stroke="#808192" stroke-width="1.2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </td>
                    <td class="text-center border border-slate-200 p-2">{{ item.manufactor }}</td>
                    <td class="text-center border border-slate-200 p-2">{{ item.series }}</td>
                    <td class="text-center border border-slate-200 p-2">{{ item.manufactor_date }}</td>
                    <td class="text-center border border-slate-200 p-2">{{ item.status }}</td>
                    <td class="text-center border border-slate-200 p-2">{{ item.price }}</td>
                    <td class="text-center border border-slate-200 p-2">{{ item.notes }}</td>
                    <td class="text-center border flex items-center justify-between border-slate-200 p-2"><svg width="24" height="24" viewBox="0 0 24 24"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                fill="#21272A" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                fill="#21272A" />
                        </svg>
                        <svg width="16" height="16" viewBox="0 0 16 16"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.5 2C9.5 2.82843 8.82843 3.5 8 3.5C7.17157 3.5 6.5 2.82843 6.5 2C6.5 1.17157 7.17157 0.5 8 0.5C8.82843 0.5 9.5 1.17157 9.5 2Z"
                                fill="#687182" />
                            <path
                                d="M9.5 8C9.5 8.82843 8.82843 9.5 8 9.5C7.17157 9.5 6.5 8.82843 6.5 8C6.5 7.17157 7.17157 6.5 8 6.5C8.82843 6.5 9.5 7.17157 9.5 8Z"
                                fill="#687182" />
                            <path
                                d="M9.5 14C9.5 14.8284 8.82843 15.5 8 15.5C7.17157 15.5 6.5 14.8284 6.5 14C6.5 13.1716 7.17157 12.5 8 12.5C8.82843 12.5 9.5 13.1716 9.5 14Z"
                                fill="#687182" />
                        </svg>

                    </td>
                </tr>
            </tbody>
        </table>
    </AuthenticatedLayout>
</template>