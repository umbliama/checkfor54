<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SideMenu from '@/Layouts/SideMenu.vue';
import { MenuItem, MenuItems, Menu, MenuButton } from '@headlessui/vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';



</script>

<template>
  <AuthenticatedLayout>
    <div class="container mt-5 md:mx-auto sm:mx-auto">

      <div class="flex">
        <div class="flex-1 p-4 sm:w-full">
          <nav class="bg-my-gray ">
            <div class="sm:w-full lg:max-w-screen-xl px-4 py-3 mx-auto">
              <div
                class="py-12 flex items-center lg:overflow-x-visible md:overflow-x-visible sm:overflow-y-hidden sm:whitespace-nowrap sm:overflow-x-auto ">
                <ul
                  class="flex items-center flex-row font-medium mt-0 space-x-8 border-b-2  rtl:space-x-reverse text-sm">
                  <li :class="{ 'border-b-2  border-blue-600': getActiveMenuLink == 'all' }" class="flex  pb-4 "
                    @click="updateMenuLink('all')">
                    <Link class="text-lg sm:text-md text-black">Открытые аренды</Link>

                  </li>
                  <li :class="{ 'border-b-2  border-blue-600': getActiveMenuLink == 'customers' }" class="flex pb-4"
                    @click="updateMenuLink('customers')">
                    <Link class="text-lg sm:text-md text-black">Закрытые аренды</Link>

                  </li>
                  <li class="flex pb-4">
                    <Link class="text-lg text-black">Admin</Link>
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
                            <Link :href="route('contragents.create')" class="flex items-center justify-around"
                              :class="['block px-4 py-2 text-sm']">Создать контрагента
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
          <nav class="bg-white">
            <div class="max-w-screen-xl py-2 px-4 ">
              <div class="mt-6 flex items-center">
                <ul
                  class="flex overflow-x-auto  flex-row pb-3 border-b-2 border-gray-200 font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                  <li class="text-my-nav-text text-md w-full">
                    Все
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Январь
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Февраль
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Март
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Апрель
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Май
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Июнь
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Июль
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Август
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Сентябрь
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Октябрь
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Ноябрь
                  </li>
                  <li class="text-my-nav-text text-md w-full">
                    Декабрь
                  </li>
                </ul>
              </div>
            </div>
          </nav>
          <div class="flex mt-2 justify-between">
            <h3>Список открытых аренд:</h3>
            <div class="flex ">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_2051_7507)">
                  <rect x="24" width="24" height="24" rx="5" transform="rotate(90 24 0)" fill="#F7F9FC" />
                  <path
                    d="M10.686 11.929L15.636 16.879C15.8182 17.0676 15.919 17.3202 15.9167 17.5824C15.9144 17.8446 15.8092 18.0954 15.6238 18.2808C15.4384 18.4662 15.1876 18.5714 14.9254 18.5737C14.6632 18.576 14.4106 18.4752 14.222 18.293L8.565 12.636C8.37753 12.4485 8.27221 12.1942 8.27221 11.929C8.27221 11.6639 8.37753 11.4095 8.565 11.222L14.222 5.56502C14.4106 5.38286 14.6632 5.28207 14.9254 5.28434C15.1876 5.28662 15.4384 5.39179 15.6238 5.5772C15.8092 5.76261 15.9144 6.01342 15.9167 6.27562C15.919 6.53781 15.8182 6.79042 15.636 6.97902L10.686 11.929Z"
                    fill="#697077" />
                </g>
                <rect x="23.5" y="0.5" width="23" height="23" rx="4.5" transform="rotate(90 23.5 0.5)"
                  stroke="#697077" />
                <defs>
                  <clipPath id="clip0_2051_7507">
                    <rect x="24" width="24" height="24" rx="5" transform="rotate(90 24 0)" fill="white" />
                  </clipPath>
                </defs>
              </svg>


              <p>2024</p>


              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_2051_7512)">
                  <rect y="24" width="24" height="24" rx="5" transform="rotate(-90 0 24)" fill="#F7F9FC" />
                  <path
                    d="M13.314 12.071L8.36399 7.12098C8.18184 6.93238 8.08104 6.67978 8.08332 6.41758C8.0856 6.15538 8.19077 5.90457 8.37618 5.71916C8.56158 5.53375 8.8124 5.42859 9.07459 5.42631C9.33679 5.42403 9.58939 5.52482 9.77799 5.70698L15.435 11.364C15.6225 11.5515 15.7278 11.8058 15.7278 12.071C15.7278 12.3361 15.6225 12.5905 15.435 12.778L9.77799 18.435C9.58939 18.6171 9.33679 18.7179 9.07459 18.7157C8.8124 18.7134 8.56158 18.6082 8.37618 18.4228C8.19077 18.2374 8.0856 17.9866 8.08332 17.7244C8.08104 17.4622 8.18184 17.2096 8.36399 17.021L13.314 12.071Z"
                    fill="#697077" />
                </g>
                <rect x="0.5" y="23.5" width="23" height="23" rx="4.5" transform="rotate(-90 0.5 23.5)"
                  stroke="#697077" />
                <defs>
                  <clipPath id="clip0_2051_7512">
                    <rect y="24" width="24" height="24" rx="5" transform="rotate(-90 0 24)" fill="white" />
                  </clipPath>
                </defs>
              </svg>

            </div>
          </div>

          <div class="p-4 overflow-x-auto whitespace-nowrap">
            <table class="table-auto w-full ">
              <!-- Table Head -->
              <thead>
                <tr class="bg-gray-200">
                  <th class="p-4">
                    <a href="" @click.prevent="showForm = true">

                      <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="28" height="28" rx="14" fill="#644DED" fill-opacity="0.08" />
                        <path
                          d="M15 6.5C15 5.94772 14.5523 5.5 14 5.5V5.5C13.4477 5.5 13 5.94772 13 6.5V21.5C13 22.0523 13.4477 22.5 14 22.5V22.5C14.5523 22.5 15 22.0523 15 21.5V6.5Z"
                          fill="#4A496C" />
                        <rect width="2" height="17" rx="1" transform="matrix(0 -1 -1 0 22 15)" fill="#4A496C" />
                      </svg>
                    </a>


                  </th>
                  <th class="p-4">Заказчик</th>
                  <th class="p-4">Дата отгрузки</th>
                  <th class="p-4">Комментарий</th>
                  <th class="p-4">Доход</th>
                  <th class="p-4">Действия</th>
                </tr>
              </thead>

              <!-- Form Row (first row) -->
              <tbody>
                <tr>
                  <td><input  type="text" class="input" /></td>
                  <td><input  type="date" class="input" /></td>
                  <td><input  type="text" class="input" /></td>
                  <td><input  type="number" class="input" /></td>
                  <td>
                    <!-- <button @click="saveOrder" class="bg-blue-500 text-white px-2 py-1 rounded">Save</button> -->
                  </td>
                </tr>

                <!-- Existing Orders (data rows) -->
                <!-- <tr v-for="order in orders" :key="order.id" class="border-b">
                  <td>{{ order.customer }}</td>
                  <td>{{ order.customer }}</td>
                  <td>{{ order.date }}</td>
                  <td>{{ order.comment }}</td>
                  <td>{{ order.revenue }} ₽</td>
                  <td>
                    <button class="bg-gray-200 px-2 py-1 rounded">Edit</button>
                  </td>
                </tr> -->
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
        </div>
      </div>
    </div>

  </AuthenticatedLayout>

</template> 