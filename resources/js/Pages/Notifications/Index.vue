<script setup>
import { usePage } from '@inertiajs/inertia-vue3';
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import store from '../../../store';

const props = defineProps({
    notifications: {
        type: Array,
        default: () => []
    },
    user_id: Number,
    read_notifications: {
        type: Array,
        default: () => []
    }
});

// 🔹 Создаем реактивные копии, чтобы Vue следил за изменениями
const unreadNotifications = ref([...props.notifications]);
const readNotifications = ref([...props.read_notifications]);
const activeTab = ref('unread');

const page = usePage();
const user = computed(() => page.props.auth.user);
const userId = computed(() => store.getters['getUserId']);

// 📌 Метод "Прочитать все"
function markAllAsRead() {
    axios.post('/notifications/read-all')
    .then(() => {
        // 🔹 Переносим все "непрочитанные" в "прочитанные"
        readNotifications.value = [...readNotifications.value, ...unreadNotifications.value];

        // 🔹 Очищаем список "непрочитанных"
        unreadNotifications.value = [];

        // 🔹 Если активная вкладка - "новые", переключаем на "прочитанные"
        if (activeTab.value === 'unread') {
            activeTab.value = 'read';
        }

        // 🔹 Обновляем глобальный счетчик
        localStorage.setItem('notifCount', 0);
        window.dispatchEvent(new Event('storage'));
    })
    .catch(error => {
        console.error("Ошибка при обновлении статуса уведомлений:", error);
    });
}

// 📌 Функция для форматирования даты
function formatDate(isoString) {
    const date = new Date(isoString);
    const day = String(date.getUTCDate()).padStart(2, '0');
    const month = String(date.getUTCMonth() + 1).padStart(2, '0');
    const year = date.getUTCFullYear();
    const hours = String(date.getUTCHours()).padStart(2, '0');
    const minutes = String(date.getUTCMinutes()).padStart(2, '0');

    return `${day}.${month}.${year} в ${hours}:${minutes}`;
}
</script>

<template>
    <AuthenticatedLayout bg="gray">
        <div class="py-6 px-5">
            <button @click="markAllAsRead">Прочитать все</button>

            <div class="relative flex items-center flex-col lg:flex-row">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>

                <ul class="relative flex items-center w-full font-medium space-x-8 overflow-x-auto text-nowrap lg:overflow-x-visible">
                    <li
                        @click="activeTab = 'unread'"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': activeTab === 'unread' }"
                        class="flex items-center py-3 border-b-2 border-transparent cursor-pointer"
                    >
                        Новые
                        <span class="flex items-center min-w-[18px] h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            {{ unreadNotifications.length }}
                        </span>
                    </li>
                    <li
                        @click="activeTab = 'read'"
                        :class="{ '!border-[#001D6C] text-[#001D6C]': activeTab === 'read' }"
                        class="flex items-center py-3 border-b-2 border-transparent cursor-pointer"
                    >
                        Просмотренные
                        <span class="flex items-center min-w-[18px] h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            {{ readNotifications.length }}
                        </span>
                    </li>
                </ul>
            </div>

            <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[1050px] text-xs">
                    <div class="flex font-bold border-b border-b-gray3">
                        <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2 bg-violet-full/10">
                            Дата
                        </div>
                        <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2">
                            Пользователь
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-120px-120px)] py-2.5 px-2">
                            Уведомление
                        </div>
                    </div>

                    <!-- Отображение уведомлений в зависимости от активного таба -->
                    <div v-if="activeTab === 'unread'">
                        <div v-if="unreadNotifications.length === 0" class="text-center py-4 text-gray-500">
                            Нет новых уведомлений
                        </div>
                        <div v-for="item in unreadNotifications" :key="item.id" class="flex border-b border-b-gray3 break-all">
                            <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2 bg-violet-full/10">
                                {{ formatDate(item.created_at) }}
                            </div>
                            <div v-if="item.creator.isAdmin === 1" class="shrink-0 flex items-center w-[120px] py-2.5 text-danger px-2">
                                {{ item.creator.name }}
                            </div>
                            <div v-else class="shrink-0 flex items-center w-[120px] py-2.5 px-2">
                                {{ item.creator.name }}
                            </div>
                            <div v-if="item.creator.isAdmin === 1" class="shrink-0 flex items-center w-[calc(100%-120px-120px)] text-danger py-2.5 px-2">
                                {{ item.type }}
                            </div>
                            <div v-else class="shrink-0 flex items-center w-[calc(100%-120px-120px)] py-2.5 px-2">
                                {{ item.type }}
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'read'">
                        <div v-if="readNotifications.length === 0" class="text-center py-4 text-gray-500">
                            Нет просмотренных уведомлений
                        </div>
                        <div v-for="item in readNotifications" :key="item.id" class="flex border-b border-b-gray3 break-all">
                            <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2 bg-violet-full/10">
                                {{ formatDate(item.created_at) }}
                            </div>
                            <div v-if="item.creator.isAdmin === 1" class="shrink-0 flex items-center w-[120px] py-2.5 text-danger px-2">
                                {{ item.creator.name }}
                            </div>
                            <div v-else class="shrink-0 flex items-center w-[120px] py-2.5 px-2">
                                {{ item.creator.name }}
                            </div>
                            <div v-if="item.creator.isAdmin === 1" class="shrink-0 flex items-center w-[calc(100%-120px-120px)] text-danger py-2.5 px-2">
                                {{ item.type }}
                            </div>
                            <div v-else class="shrink-0 flex items-center w-[calc(100%-120px-120px)] py-2.5 px-2">
                                {{ item.type }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
