<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import SendForm from './SendForm.vue';


const props = defineProps({
    contactId: Number,
    contactType: {
        type: String
    }
});

const messages = ref([]);
const page = usePage();
const currentUserId = computed(() => page.props.auth.user.id);
const contactUser = ref(null)
const loading = ref(true);

const fetchMessages = () => {
    if (!props.contactId) return;
    loading.value = true;

    axios.get(`/chat/user/messages/${props.contactId}`)
        .then(response => {
            messages.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
        })
        .finally(() => {
            loading.value = false;

        })
};
const fetchMessagesGroup = () => {
    if (!props.contactId) return;
    loading.value = true;
    axios.get(`/chat/group/messages/${props.contactId}`)
        .then(response => {
            messages.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
        })
        .finally(() => {
            loading.value = false;

        })
};
window.Echo.private(`chat.${currentUserId.value}`)
    .listen("MessageSent", (event) => {
        console.log("New message for user:", props.contactId);
        console.log("Received message:", event.message);
        if (props.contactType === 'contact') {
            fetchMessages();
        } else {
            fetchMessagesGroup();
        }
    });

function formatDateToMDYHMS(dateString) {
    // Создаем объект Date из строки ISO 8601
    const date = new Date(dateString);

    // Извлекаем компоненты даты и времени
    const month = String(date.getMonth() + 1).padStart(2, '0'); 
    const day = String(date.getDate()).padStart(2, '0');        
    const year = date.getFullYear();                           
    const hours = String(date.getHours()).padStart(2, '0');     
    const minutes = String(date.getMinutes()).padStart(2, '0');

    // Формируем строку в формате MM/DD/YYYY:HH:mm
    return `${month}/${day}/${year} ${hours}:${minutes}`;
}
window.Echo.connector.pusher.connection.bind('error', function (err) {
    console.error("Echo Error:", err);
});

const listenForMessages = () => {

};

const getUserInfo = (id) => {
    axios.get(`/chat/getUserInfo`, {
        params: {
            id: id
        }
    }).then(response => {
        contactUser.value = response.data
    })
}

const getGroupInfo = (id) => {
    axios.get('/chat/getGroupInfo', {
        params: {
            id: id
        }
    }).then(response => {
        contactUser.value = response.data
    })
}

onMounted(() => {
    // Подключение к WebSocket для личных сообщений
    window.Echo.private(`chat.${currentUserId.value}`)
        .listen("MessageSent", (event) => {
            console.log("New message for user:", props.contactId);
            console.log("Received message:", event.message);
            if (props.contactType === 'contact') {
                console.log(props.contactType)
                fetchMessages();
            } else {
                fetchMessagesGroup();
            }
        });



        window.Echo.private(`chat.group.${props.contactId}`)
            .listen(".GroupMessageSent", (event) => {
                console.log("New group message:", event.message);
                fetchMessagesGroup();
            });

    // Обработка ошибок подключения
    window.Echo.connector.pusher.connection.bind('error', function (err) {
        console.error("Echo Error:", err);
    });

    // Загрузка сообщений при монтировании компонента
    if (props.contactType === 'contact') {
        fetchMessages();
    } else {
        fetchMessagesGroup();
    }

    getUserInfo(props.contactId)
});
// Watch for contactId changes and fetch messages
watch(() => props.contactId, fetchMessages, { immediate: true });
if (props.contactType === 'group') {
    watch(() => props.contactId, fetchMessagesGroup, { immediate: true });

}
watch(() => props.contactId, (newContactId) => {

    if (props.contactType === 'contact') {
        getUserInfo(newContactId);

    } else {
        getGroupInfo(newContactId)
    }
}, { immediate: true });

</script>

<template>
    <div class="relative flex-1 h-full p-10 space-y-6 flex flex-col">
        <h2 v-if="contactId">{{ contactUser.name }}</h2>
        <h2 v-else-if="contactId || loading">{{ 'Загрузка' }}</h2>
        <div v-else>No contact selected</div>
        <!-- Загрузочный индикатор -->
        <div v-if="loading" class="flex justify-center items-center h-full">
            <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500"></div>
        </div>

        <!-- Панель сообщений -->
        <div v-else class="flex-1 overflow-y-auto p-4 space-y-2">
            <div v-for="message in messages.messages" :key="message.id"
                :class="message.user.id === currentUserId ? 'flex justify-end' : 'flex justify-start'">
                <div :class="[
                    'max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow',
                    message.user.id === currentUserId ? 'bg-blue-500 text-white self-end' : 'bg-gray-200 text-gray-800 self-start'
                ]">
                    <p v-if="contactType === 'group'">{{ message.user.name }}</p>
                    <p class="text-sm">{{ message.message }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ formatDateToMDYHMS(message.created_at) }}</p>
                </div>
            </div>
        </div>

        <SendForm :contactType="contactType" :recipient_id="contactId"
            :onMessageSent="contactType === 'group' ? fetchMessagesGroup : fetchMessages" />
    </div>
</template>