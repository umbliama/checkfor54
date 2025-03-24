<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import SendForm from './SendForm.vue';


const props = defineProps({
    contactId: Number
});

const messages = ref([]);
const page = usePage();
const currentUserId = computed(() => page.props.auth.user.id);

const fetchMessages = () => {
    if (!props.contactId) return;

    axios.get(`/chat/user/messages/${props.contactId}`)
        .then(response => {
            messages.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
        });
};
window.Echo.private(`chat.${currentUserId.value}`)
    .listen("MessageSent", (event) => {
        console.log("New message for user:", props.contactId);
        console.log("Received message:", event.message);
        fetchMessages();
    });



window.Echo.connector.pusher.connection.bind('error', function (err) {
    console.error("Echo Error:", err);
});

const listenForMessages = () => {

};

onMounted(() => {
    console.log(props.contactId)
    listenForMessages();
});
// Watch for contactId changes and fetch messages
watch(() => props.contactId, fetchMessages, { immediate: true });

</script>

<template>
    <div class="p-10 space-y-6">
        <h2 v-if="contactId">Chat with {{ contactId }}</h2>
        <div v-else>No contact selected</div>
        <div class="h-96 overflow-y-auto p-4 space-y-2">
            <div v-for="message in messages.messages" :key="message.id"
                :class="message.user.id === currentUserId ? 'flex justify-end' : 'flex justify-start'">

                <div :class="[
                    'max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow',
                    message.user.id === currentUserId ? 'bg-blue-500 text-white self-end' : 'bg-gray-200 text-gray-800 self-start'
                ]">
                    <p class="text-sm">{{ message.message }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ message.created_at }}</p>
                </div>
            </div>
        </div>

        <SendForm :recipient_id="contactId" />
    </div>
</template>
