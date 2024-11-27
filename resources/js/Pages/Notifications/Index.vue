<script setup>

const props = defineProps({
    notifications: Array
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



</script>

<template>
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
    </div>

</template>