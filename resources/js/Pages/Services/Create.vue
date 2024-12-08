<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideMenu from '@/Layouts/SideMenu.vue';
import { MenuItem, MenuItems, Menu, MenuButton } from '@headlessui/vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive, watch } from 'vue';
import store from '../../../store/index';
import { computed, onMounted, ref } from 'vue';
import ServiceModal from '@/Components/ServiceModal.vue';

const props = defineProps({
  contragents:Array,
  equipmentFormatted: Array,
})


const subEquipment = computed(() => store.getters['services/getSubEquipment']);
const selectedEquipment = computed(() => store.getters['services/getSelectedEquipment']);
const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedSubEquipmentArray = computed(() => store.getters['services/getSubSelectedEquipmentObjects']);
const selectedEquipmentService = computed(() => store.getters['services/getSelectedEquipmentService']);
const subRowsCount = computed(() => store.getters['services/getsubRowsCount']);

const incSubRow = () => {
  store.dispatch('services/updateIncSubRowsCount');
  store.dispatch('services/updateSubSelectedEquipmentObjects', {
    subequipment_id: '',
    shipping_date: '',
    period_start_date: '',
    return_date: '',
    period_end_date: '',
    store: '',
    operating: false,
    return_reason: '',
    commentary: '',

  });
}

const updateByKey = (index, field, value) => {
  store.dispatch('services/updateSubSelectedEquipmentObjectsByKey', { index, field, value });

}

const showModal = (value) => {
  store.dispatch('services/updateModalShown', value)
}

watch(selectedEquipment, async (newValue, oldValue) => {
  if (newValue) {
    try {
      const response = await fetch(`/api/equipment/${newValue}`);
      const data = await response.json();
      store.dispatch('services/updateSelectedEquipmentService', data);
    } catch (error) {
      console.error(error);
    }
  }
});

watch(subEquipmentArray, async (newValue, oldValue) => {
  if (newValue.length) {
    const lastValue = newValue[newValue.length - 1];
    try {
      const response = await fetch(`/api/equipment/${lastValue}`);
      const data = await response.json();
      store.dispatch('services/updateSubEquipment', data);
    } catch (error) {
      console.error(error);
    }
  }
}, { deep: true });



const modalShown = computed(() => store.getters['services/getModalShown']);


const form = reactive({
  contragent_id: null,
  shipping_date: null,
  service_number: null,
  service_date: null,
  period_start_date: null,
  return_date: null,
  period_end_date: null,
  store: null,
  operating: null,
  return_reason: null,
  active: null,
  income: null,
  equipment_id: selectedEquipment,

})

function submit() {
  const cleanedSubEquipment = selectedSubEquipmentArray.value.filter(sub => {
    return sub.subequipment_id && sub.shipping_date && sub.period_start_date && sub.commentary;  
  });
  console.log(cleanedSubEquipment)
  router.post('/services', {
    equipment_id: form.equipment_id,
    contragent_id: form.contragent_id,
    shipping_date: form.shipping_date,
    service_number: form.service_number,
    service_date: form.service_date,
    period_start_date: form.period_start_date,
    return_date: form.return_date,
    period_end_date: form.period_end_date,
    store: form.store,
    operating: form.operating,
    return_reason: form.return_reason,
    active: form.active,
    income: form.income,
    subEquipment: JSON.parse(JSON.stringify(cleanedSubEquipment))
  })
}

</script>

<template>
  <AuthenticatedLayout>
    <div class="mt-5 md:mx-auto sm:mx-auto">

      <div class="flex">
        <ServiceModal style="z-index: 1;" class="mt-14 absolute  bg-my-gray " v-if="modalShown"></ServiceModal>
        <div class="flex-1 sm:w-full">
          <nav class="">
            <div class=" sm:w-full px-4">
              <div
                class=" border-b-2 w-full flex items-center lg:overflow-x-visible md:overflow-x-visible sm:overflow-y-hidden sm:whitespace-nowrap sm:overflow-x-auto ">
                <ul class="flex items-center flex-row font-medium mt-0 space-x-8  rtl:space-x-reverse text-sm">
                  <li class="flex  border-b-2  border-selected-blue pb-4 " @click="updateMenuLink('all')">
                    <Link class="text-lg sm:text-md  text-selected-blue">Новая аренда</Link>

                  </li>
                  <li class="flex pb-4">
                    <Menu as="div" class="relative inline-block text-left">
                      <div class="sm:overflow-x-visible">
                        <MenuButton
                          class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                          <svg width="14" height="4" viewBox="0 0 14 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M2.33337 4C1.80294 4 1.29423 3.78929 0.91916 3.41421C0.544088 3.03914 0.333374 2.53043 0.333374 2C0.333374 1.46957 0.544088 0.960859 0.91916 0.585786C1.29423 0.210714 1.80294 0 2.33337 0C2.86381 0 3.37251 0.210714 3.74759 0.585786C4.12266 0.960859 4.33337 1.46957 4.33337 2C4.33337 2.53043 4.12266 3.03914 3.74759 3.41421C3.37251 3.78929 2.86381 4 2.33337 4ZM11.6667 4C11.1363 4 10.6276 3.78929 10.2525 3.41421C9.87742 3.03914 9.66671 2.53043 9.66671 2C9.66671 1.46957 9.87742 0.960859 10.2525 0.585786C10.6276 0.210714 11.1363 0 11.6667 0C12.1971 0 12.7058 0.210714 13.0809 0.585786C13.456 0.960859 13.6667 1.46957 13.6667 2C13.6667 2.53043 13.456 3.03914 13.0809 3.41421C12.7058 3.78929 12.1971 4 11.6667 4ZM7.00004 4C6.46961 4 5.9609 3.78929 5.58583 3.41421C5.21075 3.03914 5.00004 2.53043 5.00004 2C5.00004 1.46957 5.21075 0.960859 5.58583 0.585786C5.9609 0.210714 6.46961 0 7.00004 0C7.53047 0 8.03918 0.210714 8.41426 0.585786C8.78933 0.960859 9.00004 1.46957 9.00004 2C9.00004 2.53043 8.78933 3.03914 8.41426 3.41421C8.03918 3.78929 7.53047 4 7.00004 4Z"
                              fill="#697077" />
                          </svg>

                        </MenuButton>
                      </div>

                      <transition enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95">
                        <MenuItems
                          class="absolute top-6 z-200 right-2  w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                          <div class="py-1">
                            <MenuItem>
                            <Link :href="route('services.create')" class="flex items-center justify-around"
                              :class="['block px-4 py-2 text-sm']">Создать аренду
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path
                                d="M8.74999 2.75004C8.74999 2.33582 8.4142 2.00004 7.99999 2.00004C7.58578 2.00004 7.24999 2.33582 7.24999 2.75004V7.25H2.75C2.33579 7.25 2 7.58579 2 8C2 8.41422 2.33579 8.75 2.75 8.75H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75H13.25C13.6642 8.75 14 8.41422 14 8C14 7.58579 13.6642 7.25 13.25 7.25H8.74999V2.75004Z"
                                fill="#464F60" />
                            </svg>


                            </Link>
                            </MenuItem>

                          </div>
                        </MenuItems>
                      </transition>
                    </Menu>

                  </li>

                </ul>

              </div>
            </div>

          </nav>


          <div class="lg:block md:hidden sm:hidden grid grid-cols-2 gap-6 flex mt-4 px-4">
            <div class="flex whitespace-nowrap  justify-between items-center overflow-x-auto ">
              <div class="flex items-center flex-2 ">
                <label class="items-center mr-2" for="rent_num">Номер аренды:</label>

                <!-- SVG Icon -->
                <input v-model="form.service_number" id="rent_num"
                  class="w-full bg-input-gray rounded-lg border-none border-gray-200" placeholder="№" type="text">


                <div class="flex ml-2 items-center">
                  <label class="" for="rent_date">от</label>
                  <div class="flex">
                    <!-- SVG Icon -->
                  </div>
                  <input v-model="form.service_date" id="rent_date" class="w-full  bg-input-gray border-none rounded-lg"
                    type="date" placeholder="Дата">
                </div>
              </div>


              <div class="flex items-center flex-4">
                <label class="" for="rent_status">Статус</label>
                <div class="flex mx-3">
                  <!-- SVG Icon -->
                </div>
                <select v-model="form.active" id="rent_status" class="w-full bg-input-gray  rounded-lg border-none">
                  <option value="">Выберите</option>
                  <option value="0">Не активна</option>
                  <option value="1">Активна</option>
                </select>
              </div>
            </div>

            <div class="flex justify-between">
              <div
                class="flex flex-1 max-w-[600px] mt-2 flex-grow whitespace-nowrap items-center overflow-x-auto   py-2">
                <label class="" for="rent_customer">Заказчик</label>

                <select v-model="form.contragent_id"  id="rent_customer" class="border-none bg-input-gray  rounded-lg ml-12 w-full">
                  <option value="">Выберите</option>
                  <option v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
                </select>
              </div>
              <div class="flex items-center flex-4">
                <label class="" for="rent_status">Договор</label>
                <div class="flex mx-3">
                  <!-- SVG Icon -->
                </div>
                <select v-model="form.active" id="rent_status" class="w-full bg-input-gray  rounded-lg border-none">
                  <option value="">Выберите</option>
                  <option value="0">Не активна</option>
                  <option value="1">Активна</option>
                </select>
              </div>
            </div>
          </div>


          <div class="lg:hidden md:flex sm:flex flex flex-col mt-4">
            <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
              <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_num">Номер аренды</label>
              <div class="flex mx-3">
                <!-- SVG Icon -->
              </div>
              <input v-model="form.service_number" id="rent_num" class="w-full border-r-0 border-t-0 border-l-0 border-b-2 border-gray-200"
                placeholder="№" type="text">
            </div>

            <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
              <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_date">От</label>
              <div class="flex mx-3">
                <!-- SVG Icon -->
              </div>
              <input v-model="form.service_date" id="rent_date" class="w-full border-r-0 border-t-0 border-l-0 border-b-2 border-gray-200"
                type="date" placeholder="Дата">
            </div>

            <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
              <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_customer">Заказчик</label>
              <div class="flex">
                <!-- SVG Icon -->
              </div>
              <select v-model="form.contragent_id" id="rent_customer" class="w-full">
                <option value="">Выберите</option>
                <option v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
              </select>
            </div>
            <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
              <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_status">Статус</label>
              <div class="flex mx-3">
                <!-- SVG Icon -->
              </div>
              <select v-model="form.active" id="rent_status" class="w-full">
                <option value="">Выберите</option>
                <option value="0">Не активна</option>
                <option value="1">Активна</option>
              </select>
            </div>
          </div>




          <div class="p-4 overflow-x-auto whitespace-nowrap">
            <table class="table-auto bg-table-gray w-full ">
              <!-- Table Head -->
              <thead>
                <tr class="bg-table-gray">
                  <th class="bg-violet p-4">
                    <a @click.prevent="incSubRow" class="flex justify-between" href="">

                      <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08" />
                        <path
                          d="M15 6.5C15 5.94772 14.5523 5.5 14 5.5V5.5C13.4477 5.5 13 5.94772 13 6.5V21.5C13 22.0523 13.4477 22.5 14 22.5V22.5C14.5523 22.5 15 22.0523 15 21.5V6.5Z"
                          fill="#4A496C" />
                        <rect width="2" height="17" rx="1" transform="matrix(0 -1 -1 0 22 15)" fill="#4A496C" />
                      </svg>
                    </a>
                  </th>
                  <th class="p-4">Оборудование</th>
                  <th class="p-4">Дата отгрузки</th>
                  <th class="p-4">Начало периода</th>
                  <th class="p-4">Комментарий</th>
                  <th class="p-4">
                    <div><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                      </svg></div>

                  </th>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td class="flex justify-center items-center">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M15.9999 12.6671V16.667C15.9999 17.0206 15.8594 17.3598 15.6094 17.6098C15.3594 17.8599 15.0202 18.0003 14.6666 18.0003H7.33332C6.9797 18.0003 6.64057 17.8599 6.39052 17.6098C6.14047 17.3598 6 17.0206 6 16.667V9.33375C6 8.98013 6.14047 8.641 6.39052 8.39095C6.64057 8.1409 6.9797 8.00043 7.33332 8.00043H11.3333"
                        stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                      <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>



                  </td>
                  <td>
                    <!-- Show a button or link to trigger the modal to select equipment -->
                    <button v-if="!selectedEquipmentService" @click="showModal(true)"
                      class=" text-side-gray-text px-4 py-2 rounded">
                      Нажмите чтобы выбрать оборудование
                    </button>

                    <!-- When equipment is selected, display the details and pass the id to v-model -->
                    <div v-else class="p-4 bg-my-gray border whitespace-nowrap flex">
                      <p> {{ selectedEquipmentService.category.name }} {{ selectedEquipmentService.size.name }} {{
                        selectedEquipmentService.series }}</p>
                      <!-- Hidden input to hold the selected equipment id for the form -->
                      <input type="hidden" v-model="form.equipment_id">
                    </div>
                  </td>

                  <td><input v-model="form.shipping_date" type="date"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td><input v-model="form.period_start_date"type="date"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td><input type="text"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td>
                    <div class="flex items-center justify-around">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                          fill="#21272A" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                          fill="#21272A" />
                      </svg>
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
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

                    </div>
                  </td>
                </tr>
                <tr class="border-l-2 border-violet-full" v-for="(item, index) in subEquipment " :key="index"
                  v-if="subEquipment.length > 0">

                  <td class="flex justify-center items-center"> <svg width="40" height="40" viewBox="0 0 24 24"
                      fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M15.9999 12.6671V16.667C15.9999 17.0206 15.8594 17.3598 15.6094 17.6098C15.3594 17.8599 15.0202 18.0003 14.6666 18.0003H7.33332C6.9797 18.0003 6.64057 17.8599 6.39052 17.6098C6.14047 17.3598 6 17.0206 6 16.667V9.33375C6 8.98013 6.14047 8.641 6.39052 8.39095C6.64057 8.1409 6.9797 8.00043 7.33332 8.00043H11.3333"
                        stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                      <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </td>
                  <td colspan="1">
                    <button v-if="!subEquipment" @click="showModal(true)"
                      class=" text-side-gray-text px-4 py-2 rounded">
                      Нажмите чтобы выбрать оборудование
                    </button>

                    <div class="p-4 bg-my-gray border whitespace-nowrap flex" v-else>
                      <p> {{ item.category.name }} {{ item.size.name }} {{
                        item.series }}</p>
                    </div>

                    <input type="text" v-model="form.subEquipment" placeholder="Sub-equipment name"
                      class="input hidden border-r-0 border-t-0 border-b-0 border-l-2 border-violet-full bg-input-gray" />
                  </td>
                  <td><input @change="updateByKey(index,'subequipment_id',item.id)" @input="updateByKey(index, 'shipping_date', $event.target.value)" type="date"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td><input @input="updateByKey(index, 'period_start_date', $event.target.value)"
                      v-model="item.period_start_date" type="date"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td><input @input="updateByKey(index, 'commentary', $event.target.value)" v-model="item.commentary"
                      type="text"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>

                  <td>
                    <div class="flex items-center justify-around">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                          fill="#21272A" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                          fill="#21272A" />
                      </svg>
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
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

                    </div>
                  </td>

                </tr>
                <tr v-for="item in subRowsCount" v-if="subRowsCount > 0">

                  <td class="flex justify-center items-center"> <svg width="40" height="40" viewBox="0 0 24 24"
                      fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M15.9999 12.6671V16.667C15.9999 17.0206 15.8594 17.3598 15.6094 17.6098C15.3594 17.8599 15.0202 18.0003 14.6666 18.0003H7.33332C6.9797 18.0003 6.64057 17.8599 6.39052 17.6098C6.14047 17.3598 6 17.0206 6 16.667V9.33375C6 8.98013 6.14047 8.641 6.39052 8.39095C6.64057 8.1409 6.9797 8.00043 7.33332 8.00043H11.3333"
                        stroke="#808192" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M14 6H18V9.99997" stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                      <path d="M10.6667 13.3333L18 6" stroke="#808192" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </td>
                  <td colspan="5">
                    <input type="text" placeholder="Sub-equipment name"
                      class="input hidden border-r-0 border-t-0 border-b-0 border-l-2 border-violet-full bg-input-gray" />
                    <button @click="showModal(true)" class=" text-side-gray-text px-4 py-2 rounded">
                      Нажмите чтобы выбрать оборудование
                    </button>
                  </td>

                </tr>
              </tbody>
            </table>

            <!-- Add New Row Button -->

          </div>

          <!-- Cards Layout -->
          <div class="grid grid-cols-3 mt-3 gap-4">
            <div v-for="column in columns" class="bg-white border-2 border-my-gray-500 shadow rounded p-4">
              <div class="flex items-center justify-between">
                <h3>Хронология</h3>
                <Link @click="toggleDropdown">

                <Menu as="div" class="relative inline-block text-left">
                  <div>
                    <MenuButton
                      class="inline-flex w-full justify-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ">
                      <svg width="20" height="6" viewBox="0 0 20 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M3 4C3.26522 4 3.51957 3.89464 3.70711 3.70711C3.89464 3.51957 4 3.26522 4 3C4 2.73478 3.89464 2.48043 3.70711 2.29289C3.51957 2.10536 3.26522 2 3 2C2.73478 2 2.48043 2.10536 2.29289 2.29289C2.10536 2.48043 2 2.73478 2 3C2 3.26522 2.10536 3.51957 2.29289 3.70711C2.48043 3.89464 2.73478 4 3 4ZM3 6C2.20435 6 1.44129 5.68393 0.87868 5.12132C0.31607 4.55871 0 3.79565 0 3C0 2.20435 0.31607 1.44129 0.87868 0.87868C1.44129 0.31607 2.20435 0 3 0C3.79565 0 4.55871 0.31607 5.12132 0.87868C5.68393 1.44129 6 2.20435 6 3C6 3.79565 5.68393 4.55871 5.12132 5.12132C4.55871 5.68393 3.79565 6 3 6ZM17 6C16.2044 6 15.4413 5.68393 14.8787 5.12132C14.3161 4.55871 14 3.79565 14 3C14 2.20435 14.3161 1.44129 14.8787 0.87868C15.4413 0.31607 16.2044 0 17 0C17.7956 0 18.5587 0.31607 19.1213 0.87868C19.6839 1.44129 20 2.20435 20 3C20 3.79565 19.6839 4.55871 19.1213 5.12132C18.5587 5.68393 17.7956 6 17 6ZM17 4C17.2652 4 17.5196 3.89464 17.7071 3.70711C17.8946 3.51957 18 3.26522 18 3C18 2.73478 17.8946 2.48043 17.7071 2.29289C17.5196 2.10536 17.2652 2 17 2C16.7348 2 16.4804 2.10536 16.2929 2.29289C16.1054 2.48043 16 2.73478 16 3C16 3.26522 16.1054 3.51957 16.2929 3.70711C16.4804 3.89464 16.7348 4 17 4ZM10 6C9.20435 6 8.44129 5.68393 7.87868 5.12132C7.31607 4.55871 7 3.79565 7 3C7 2.20435 7.31607 1.44129 7.87868 0.87868C8.44129 0.31607 9.20435 0 10 0C10.7956 0 11.5587 0.31607 12.1213 0.87868C12.6839 1.44129 13 2.20435 13 3C13 3.79565 12.6839 4.55871 12.1213 5.12132C11.5587 5.68393 10.7956 6 10 6ZM10 4C10.2652 4 10.5196 3.89464 10.7071 3.70711C10.8946 3.51957 11 3.26522 11 3C11 2.73478 10.8946 2.48043 10.7071 2.29289C10.5196 2.10536 10.2652 2 10 2C9.73478 2 9.48043 2.10536 9.29289 2.29289C9.10536 2.48043 9 2.73478 9 3C9 3.26522 9.10536 3.51957 9.29289 3.70711C9.48043 3.89464 9.73478 4 10 4Z"
                          fill="#697077" />
                      </svg>

                    </MenuButton>
                  </div>

                  <transition enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <MenuItems
                      class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                      <div class="py-1">
                        <MenuItem v-slot="{ active }">
                        <Link class="flex items-center justify-around">Добавить блок
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M8.74999 2.75003C8.74999 2.33582 8.4142 2.00003 7.99999 2.00003C7.58578 2.00003 7.24999 2.33582 7.24999 2.75003V7.25H2.75C2.33579 7.25 2 7.58578 2 8C2 8.41421 2.33579 8.75 2.75 8.75H7.24999L7.25 13.25C7.25 13.6642 7.58579 14 8 14C8.41421 14 8.75 13.6642 8.75 13.25L8.74999 8.75H13.25C13.6642 8.75 14 8.41421 14 8C14 7.58578 13.6642 7.25 13.25 7.25H8.74999V2.75003Z"
                            fill="#464F60" />
                        </svg>
                        </Link>
                        </MenuItem>
                      </div>
                    </MenuItems>
                  </transition>
                </Menu>
                </Link>


              </div>
            </div>


          </div>

          <div class="flex justify-end p-4 ">
          <button class="flex text-center bg-my-gray    justify-end  py-3 px-10 bg-gray-300" @click="submit">Сохранить</button>

          </div>
        </div>

      </div>
    </div>

  </AuthenticatedLayout>

</template>