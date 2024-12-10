<script setup>
import { usePage } from '@inertiajs/inertia-vue3';
import { computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import store from '../../../store';



const props = defineProps({
    notifications: Array,
    user_id: Number,
    read_notifications: Array
})

const page = usePage()

const user = computed(() => page.props.auth.user)
const userId = computed(() => store.getters['getUserId']);



const unreadNotifications = computed(() => {
  return props.notifications.filter((notification) => {
    return!props.read_notifications.some((readNotification) => readNotification.notification_id === notification.id);
  });
})
const formatDate = (dateString) => {
    const date = new Date(dateString);
    const options = { year: "numeric", month: "long", day: "numeric" };
    const timeOptions = { hour: "2-digit", minute: "2-digit" };

    return `${date.toLocaleDateString("en-US", options)}, ${date.toLocaleTimeString(
        "en-US",
        timeOptions
    )}`;
}

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

</script>

<template>
    <AuthenticatedLayout>

        <div class="min-h-screen bg-gray-100 p-6">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow">
                <div class="border-b p-4">
                    <h1 class="text-xl font-bold">Уведомления</h1>
                </div>
                <div class="divide-y">

                        <div v-for="(notification, index) in unreadNotifications" :key="index"
                        class="p-4 hover:bg-gray-50 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ notification.user }}</p>
                            <p class="text-sm text-gray-600">{{ notification.type }}</p>
                        </div>
                        <p class="text-sm text-gray-500">{{ formatDate(notification.created_at) }}</p>
                        <button @click="readNotification(notification.id, user_id)">Прочитать</button>
                    </div>
                </div>
            </div>
            <button @click="readAllNotifications">Прочитать всё</button>
        </div>
    </AuthenticatedLayout>

</template>