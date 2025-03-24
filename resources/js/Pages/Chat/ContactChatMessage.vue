<script setup>
import { computed, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import SendForm from './SendForm.vue';
import axios from 'axios';

const props = defineProps({
    contactId: Number
});

const messages = ref([]);
const page = usePage();
const currentUserId = computed(() => page.props.auth.user.id);

// ✅ Fetch messages function
const fetchMessages = () => {
    axios.get(`/chat/user/messages/${props.contactId}`)
        .then(response => {
            messages.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
        });
};

// ✅ Fetch messages when component is mounted
onMounted(() => {
    fetchMessages();
});
</script>

<template>
    <div class="p-4 space-y-2">
        <h2>Chat with {{ contactId }}</h2>
        <div v-for="message in messages" :key="message.id"
            :class="message.user.id === currentUserId ? 'flex justify-end' : 'flex justify-start'">
            
            <div :class="[
                'max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow',
                message.user.id === currentUserId ? 'bg-blue-500 text-white self-end' : 'bg-gray-200 text-gray-800 self-start'
            ]">
                <p class="text-sm">{{ message.message }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ message.created_at }}</p>
            </div>
        </div>

        <!-- Pass fetchMessages as a prop to SendForm -->
        <SendForm :contactId="contactId" @messageSent="fetchMessages" />
    </div>
</template>
