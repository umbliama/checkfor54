<script setup>
import { usePage } from '@inertiajs/inertia-vue3';
import { computed, onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import store from '../../../store';
import UiFieldDate from '@/Components/Ui/UiFieldDate.vue';



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


const page = usePage()

const user = computed(() => page.props.auth.user)
const userId = computed(() => store.getters['getUserId']);

const start_date = ref();
const end_date   = ref();



const unreadNotifications = computed(() => {
    if (Array.isArray(props.notifications)) {
        return props.notifications.filter((notification) => {
            return !props.read_notifications.some(
                (readNotification) => readNotification.notification_id === notification.id
            );
        });
    } else {
        console.error("notifications is not an array:", props.notifications);
        return [];
    }
});




const readAllNotifications = () => {
    fetch(`/api/notifications/read-all/${props.user_id}`, {
        method: 'POST',

    })
        .then(response => response.json())
        .then(data => window.location.reload());
}

const readNotification = (id,userId) => {
    fetch(`/api/notifications/read/${id}/${userId}`, {
        method: 'POST',

    })
        .then(response => response.json())
        .then(data => window.location.reload());
}
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
            <div class="relative flex items-center flex-col lg:flex-row">
                <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>
                
                <ul class="relative flex items-center w-full font-medium space-x-8 overflow-x-auto text-nowrap lg:overflow-x-visible">
                    <li
                        :class="{ '!border-[#001D6C] text-[#001D6C]': true }"
                        class="flex items-center py-3 border-b-2 border-transparent cursor-pointer"
                    >
                        Новые
                        <span class="flex items-center min-w-[18px] h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            23
                        </span>
                    </li>
                    <li
                        :class="{ '!border-[#001D6C] text-[#001D6C]': false }"
                        class="flex items-center py-3 border-b-2 border-transparent cursor-pointer"
                    >
                        Просмотренные
                        <span class="flex items-center min-w-[18px] h-[18px] ml-1 px-1.5 rounded-full font-roboto text-xs text-white bg-side-gray-text">
                            371
                        </span>
                    </li>
                </ul>

                <div class="flex w-full mt-4 lg:w-auto lg:mt-0">
                    <UiFieldDate v-model="start_date" :inp-attrs="{ placeholder: 'Начало', class: 'bg-white' }" class="w-[calc(100%/2-12px)] lg:w-[178px]" />
                    <button
                        class="flex items-center justify-center w-6 h-12 bg-[#DDE1E6] text-gray1 lg:w-12"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.75 6.81984L17.1802 5.25L12 10.4302L6.81984 5.25L5.25 6.81984L10.4302 12L5.25 17.1802L6.81984 18.75L12 13.5698L17.1802 18.75L18.75 17.1802L13.5698 12L18.75 6.81984Z" fill="#697077"/>
                        </svg>
                    </button>
                    <UiFieldDate v-model="end_date" :inp-attrs="{ placeholder: 'Завершение', class: 'bg-white' }" class="w-[calc(100%/2-12px)] lg:w-[178px]" />
                </div>
            </div>

            <div class="w-full max-w-full mt-6 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[1050px] text-xs">
                    <div
                        class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2 bg-violet-full/10">
                            Дата
                            <button type="button" class="shrink-0 ml-auto rounded-full bg-violet-full/10">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.26795 12.1904L10.6251 9.83325L12.9822 12.1904" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.625 9.8333L10.625 18.0832" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M19.5654 16.5594L17.2083 18.9165L14.8512 16.5594" stroke="#644DED"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.2083 10.6667L17.2083 18.9166" stroke="#644DED" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2">
                            Пользователь
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-120px-120px)] py-2.5 px-2">Уведомление</div>
                    </div>


                    <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                        <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2 bg-violet-full/10">
                            23.01.2025 в 12:47
                        </div>
                        <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2 text-danger">
                            Администратор:
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-120px-120px)] py-2.5 px-2 text-danger">
                            Добавлен новый пользователь admin@admin.ru
                        </div>
                    </div>
                    <div v-for="item in notifications" class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all">
                        <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2 bg-violet-full/10">
                            {{ formatDate(item.created_at) }}
                        </div>
                        <div class="shrink-0 flex items-center w-[120px] py-2.5 px-2">
                            {{ item.user.name }}:
                        </div>
                        <div class="shrink-0 flex items-center w-[calc(100%-120px-120px)] py-2.5 px-2">
                            {{ item.type }}
                        </div>
                    </div>
            
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>
