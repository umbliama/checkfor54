<script setup>
import SideMenu from '@/Layouts/SideMenu.vue';
import { MenuItem, MenuItems, Menu, MenuButton } from '@headlessui/vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';


defineProps({
  columns: Array, 
  blocks: Array
})


const createColumn = () => {
  axios.post('const')
}


const createBlock = (columnId,type) => {
  router.post(`/constructor/column/${columnId}/block`,{type:`${type}`})
}
</script>

<template>
  <div class="flex">
    <!-- Sidebar placeholder -->
    <div class="w-64">
      <SideMenu></SideMenu>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-4">
      <!-- Top Navigation -->
      <nav class="bg-my-gray ">
        <div class="max-w-screen-xl px-4 py-3">
          <div class="p-6  items-center">
            <ul
              class="flex flex border-b-2 items-center flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
              <li>
                <Link :href="route('equip.report')" class="text-lg">Задачи</Link>
              </li>
              <li>
                <Link :href="route('equip.index')" class="text-lg">Рекламация</Link>
              </li>
              <li>
                <Link :href="route('equip.repair')" class="text-lg">История</Link>
              </li>
              <li>
                <Link :href="route('equip.tests')" class="text-lg text-side-gray-text">Admin</Link>
              </li>
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
                  leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95">
                  <MenuItems
                    class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <div class="py-1">
                      <MenuItem v-slot="{ active }">
                      <Link method="POST" @click="createColumn" :href="route('constructor.columnCreate')"
                        class="flex items-center justify-around" :class="['block px-4 py-2 text-sm']">Добавить столбец
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
            </ul>
          </div>
        </div>
      </nav>
      <!-- Cards Layout -->
      <div class="grid grid-cols-3 mt-3 gap-4">
        <div v-for="column in columns" class="bg-white border-8 border-my-gray-500  rounded p-4">
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
                leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95">
                <MenuItems
  class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
  <div class="py-1 px-2">
    <!-- Heading -->
    <h3 class="px-2 border-b-2  py-2 text-sm font-semibold text-gray-700">Добавить блок</h3>
    <!-- List of Links -->
    <ul class="px-2 py-1 space-y-1">
      <li>
        <Link method="POST" @click="createBlock(column.id,'customer')" :href="route('constructor.createBlock', column.id,'customer')">
          Заказчик
        </Link>
      </li>
      <li>
        <Link method="POST" @click="createBlock(column.id,'equipment')" :href="route('constructor.createBlock', column.id,'equipment')">
          Оборудование
        </Link>
      </li>
      <li>
        <Link method="POST" @click="createBlock(column.id,'commentary')" :href="route('constructor.createBlock', column.id,'commentary')">
          Комментарий
        </Link>
      </li>
      <li>
        <Link method="POST" @click="createBlock(column.id,'media')" :href="route('constructor.createBlock', column.id,'media')">
          Медиа
        </Link>
      </li>
      <li>
        <Link method="POST" @click="createBlock(column.id,'files')" :href="route('constructor.createBlock', column.id,'files')">
          Файлы
        </Link>
      </li>
    </ul>
    <div class="border-b-2 "></div>
    <ul class="px-2 py-1 space-y-1">
      <li>
        <Link method="POST" @click="createBlock(column.id)" :href="route('constructor.createBlock', column.id)">
          Добавить разрыв
        </Link>
      </li>
      <li>
        <Link method="POST" @click="createBlock(column.id)" :href="route('constructor.createBlock', column.id)">
          Удалить разрыв
        </Link>
      </li>
      <li>
        <Link method="POST" @click="createBlock(column.id)" :href="route('constructor.createBlock', column.id)">
          Удалить блок
        </Link>
      </li>
    </ul>
  </div>
</MenuItems>
              </transition>
            </Menu>
            </Link>


          </div>
          <div class="mt-4">
      <div v-for="block in column.blocks" :key="block.id" class="p-2 border rounded mb-2">
        <p><strong>Type:</strong> {{ block.type }}</p>
        <p><strong>Content:</strong> {{ block.content }}</p>
      </div>
    </div>

        </div>


      </div>
    </div>
  </div>
</template>