<script setup>
import { usePage } from '@inertiajs/inertia-vue3';
import { computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import store from '../../../store';



const props = defineProps({
    notifications: Array
})

const page = usePage()

const user = computed(() => page.props.auth.user)
const userId = computed(() => store.getters['getUserId']);

onMounted(() => {
    console.log(userId)
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
    fetch(`/api/notifications/read-all/${userId}`, {
        method: 'POST',

    })
        .then(response => response.json())
        .then(data => console.log(data));
}



</script>

<template>
    <AuthenticatedLayout>

        <div class="min-h-screen bg-gray-100 p-6">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow">
                <div class="border-b p-4">
                    <h1 class="text-xl font-bold">Notifications</h1>
                </div>
                <div class="divide-y">
                    <div v-for="(notification, index) in notifications" :key="index"
                        class="p-4 hover:bg-gray-50 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ notification.user }}</p>
                            <p class="text-sm text-gray-600">{{ notification.type }}</p>
                        </div>
                        <p class="text-sm text-gray-500">{{ formatDate(notification.created_at) }}</p>
                    </div>
                </div>
            </div>
            <button @click="readAllNotifications">read all</button>
        </div>
    </AuthenticatedLayout>

</template>