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
  contragents: Array,
  equipmentFormatted: Array,
  saleStatuses: Object,
  extraServices: Array
})


const subEquipment = computed(() => store.getters['services/getSubEquipment']);
const selectedEquipment = computed(() => store.getters['services/getSelectedEquipment']);
const subEquipmentArray = computed(() => store.getters['services/getSubEquipmentArray']);
const selectedSubEquipmentArray = computed(() => store.getters['services/getSubSelectedEquipmentObjects']);
const selectedEquipmentService = computed(() => store.getters['services/getSelectedEquipmentService']);
const subRowsCount = computed(() => store.getters['services/getsubRowsCount']);
const activeTab = computed(() => store.getters['sale/getActiveTab']);
const getExtraServices = computed(() => store.getters['sale/getExtraServices']);

const selectedServices = ref(new Array(subRowsCount.length).fill(null))

const addService = (service, subRowIndex) => {
  store.dispatch('sale/updateExtraServices', service,subRowIndex)
}

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

const updateActiveTab = (tab) => {
  store.dispatch('sale/updateActiveTab', tab)
}

const updateByKey = (index, field, value) => {
  store.dispatch('services/updateSubSelectedEquipmentObjectsByKey', { index, field, value });

}

const showModal = (value) => {
  store.dispatch('services/updateModalShown', value)
}
const form = reactive({
  contragent_id: null,
  shipping_date: null,
  sale_number: null,
  sale_date: null,
  commentary: null,
  status: null,
  price: null,
  equipment_id: selectedEquipment,
})


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

function submit() {
  const cleanedSubEquipment = selectedSubEquipmentArray.value.filter(sub => {
    return sub.subequipment_id && sub.shipping_date && sub.price && sub.commentary
  });
  console.log(cleanedSubEquipment)
  router.post('/sales', {
    equipment_id: form.equipment_id,
    contragent_id: form.contragent_id,
    shipping_date: form.shipping_date,
    sale_number: form.sale_number,
    sale_date: form.sale_date,
    commentary: form.commentary,
    status: form.status,
    price: form.price,
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
            <div class=" sm:w-full lg:max-w-screen-xl px-4  mx-auto">
              <div
                class=" border-b-2 flex items-center lg:overflow-x-visible md:overflow-x-visible sm:overflow-y-hidden sm:whitespace-nowrap sm:overflow-x-auto ">
                <ul class="flex items-center flex-row font-medium mt-0 space-x-8  rtl:space-x-reverse text-sm">
                  <li class="flex  border-b-2  border-selected-blue pb-4 ">
                    <Link class="text-lg sm:text-md  text-selected-blue">Новая продажа</Link>

                  </li>
                  <li class="flex  border-b-2  border-selected-blue pb-4 ">
                    <Link :href="route('sale.index')" class="text-lg sm:text-md  text-selected-blue">История</Link>

                  </li>


                </ul>

              </div>
            </div>

          </nav>


          <div class="lg:block md:hidden sm:hidden grid grid-cols-2 gap-6 flex mt-4">
            <div class="flex whitespace-nowrap  justify-between items-center overflow-x-auto ">
              <div class="flex items-center flex-2 mx-3">
                <label class="items-center mr-2" for="rent_num">Номер продажи:</label>

                <!-- SVG Icon -->
                <input v-model="form.sale_number" id="rent_num"
                  class="w-full bg-input-gray rounded-lg border-none border-gray-200" placeholder="№" type="text">


                <div class="flex ml-2 items-center">
                  <label class="" for="rent_date">от</label>
                  <div class="flex mx-3">
                    <!-- SVG Icon -->
                  </div>
                  <input v-model="form.sale_date" id="rent_date" class="w-full  bg-input-gray border-none rounded-lg"
                    type="date" placeholder="Дата">
                </div>
              </div>


              <div class="flex items-center flex-4">
                <label class="" for="status">Статус</label>
                <div class="flex mx-3">
                  <!-- SVG Icon -->
                </div>
                <select v-model="form.status" id="status" class="w-full bg-input-gray  rounded-lg border-none">
                  <option value="">Выберите</option>
                  <option v-for="(key, value) in saleStatuses" :value="value">{{ key }}</option>
                </select>
              </div>
            </div>

            <div class="flex justify-between">
              <div
                class="flex flex-1 max-w-[600px] mt-2 flex-grow whitespace-nowrap items-center overflow-x-auto  px-4 py-2">
                <label class="" for="rent_customer">Заказчик</label>

                <select v-model="form.contragent_id" id="rent_customer"
                  class="border-none bg-input-gray  rounded-lg ml-12 w-full">
                  <option value="">Выберите</option>
                  <option v-for="agent in contragents" :value="agent.id">{{ agent.name }}</option>
                </select>
              </div>
              <div class="flex items-center flex-4">
                <label class="" for="rent_status">Договор</label>
                <div class="flex mx-3">
                  <!-- SVG Icon -->
                </div>
                <select v-model="form.status" id="status" class="w-full bg-input-gray  rounded-lg border-none">
                  <option value="">Выберите</option>
                  <option value="credit">Кредит</option>
                  <option value="full">Полная</option>
                  <option value="pred">Предоплата</option>



                </select>
              </div>
            </div>
          </div>


          <div class="lg:hidden md:flex sm:flex flex flex-col mt-4">
            <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
              <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_num">Номер продажи</label>
              <div class="flex mx-3">
                <!-- SVG Icon -->
              </div>
              <input v-model="form.sale_number" id="rent_num"
                class="w-full border-r-0 border-t-0 border-l-0 border-b-2 border-gray-200" placeholder="№" type="text">
            </div>

            <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
              <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_date">От</label>
              <div class="flex mx-3">
                <!-- SVG Icon -->
              </div>
              <input v-model="form.sale_date" id="rent_date"
                class="w-full border-r-0 border-t-0 border-l-0 border-b-2 border-gray-200" type="date"
                placeholder="Дата">
            </div>

            <div class="flex whitespace-nowrap items-center overflow-x-auto bg-my-gray px-4 py-2">
              <label class="w-56 border-b-2 border-gray-200 pb-3" for="rent_customer">Заказчик</label>
              <div class="flex mx-3">
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
              <select v-model="form.status" id="status" class="w-full bg-input-gray  rounded-lg border-none">
                <option value="">Выберите</option>
                <option value="credit">Кредит</option>
                <option value="full">Полная</option>
                <option value="pred">Предоплата</option>



              </select>
            </div>
          </div>

          <div class="p-4">
            <ul class="flex gap-4">
              <li @click="updateActiveTab('sale')">Продажа</li>
              <li @click="updateActiveTab('extra')">Доп.услуги</li>
            </ul>
          </div>


          <div v-if="activeTab == 'sale'" class="p-4 overflow-x-auto whitespace-nowrap">
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
                  <th class="p-4">Комментарий</th>
                  <th class="p-4">Цена</th>
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

                  <td><input type="text" v-model="form.commentary"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td>
                    <input
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"
                      type="text" v-model="form.price">
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
                  <td><input @change="updateByKey(index, 'subequipment_id', item.id)"
                      @input="updateByKey(index, 'shipping_date', $event.target.value)" type="date"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td><input @input="updateByKey(index, 'commentary', $event.target.value)" v-model="item.commentary"
                      type="text"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td><input @input="updateByKey(index, 'price', $event.target.value)" v-model="item.price" type="text"
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
          <div v-if="activeTab == 'extra'" class="p-4 overflow-x-auto whitespace-nowrap">
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
                  <th class="p-4">Услуги</th>
                  <th class="p-4">Дата перевозки</th>
                  <th class="p-4">Комментарий</th>
                  <th class="p-4">Цена без НДС</th>
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
                    <select @change="addService($event.target.value, subRowIndex)" v-model="selectedServices[subRowIndex]">
      <option v-for="(value, key) in extraServices" :value="key" :key="key">{{ value }}</option>
    </select>

                  </td>

                  <td><input v-model="form.shipping_date" type="date"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>

                  <td><input type="text" v-model="form.commentary"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td>
                    <input
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none"
                      type="text" v-model="form.price">
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
                    <select name="" id="">
                      <option v-for="(key, value) in extraServices" :value="key">{{ value }}</option>
                    </select>

                    <input type="text" v-model="form.subEquipment" placeholder="Sub-equipment name"
                      class="input hidden border-r-0 border-t-0 border-b-0 border-l-2 border-violet-full bg-input-gray" />
                  </td>
                  <td><input @change="updateByKey(index, 'subequipment_id', item.id)"
                      @input="updateByKey(index, 'shipping_date', $event.target.value)" type="date"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td><input @input="updateByKey(index, 'commentary', $event.target.value)" v-model="item.commentary"
                      type="text"
                      class="border-transparent focus:border-transparent focus:ring-0 input bg-input-gray  border-none" />
                  </td>
                  <td><input @input="updateByKey(index, 'price', $event.target.value)" v-model="item.price" type="text"
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
                    <select @change="addService($event.target.value, subRowIndex)" v-model="selectedServices[subRowIndex]">
      <option v-for="(value, key) in extraServices" :value="key" :key="key">{{ value }}</option>
    </select>
                  </td>

                </tr>
              </tbody>
            </table>

            <!-- Add New Row Button -->

          </div>

          <p>Итого </p>

          <button @click="submit">Сохранить</button>
        </div>

      </div>
    </div>

  </AuthenticatedLayout>

</template>