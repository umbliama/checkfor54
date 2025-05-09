<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const message = ref('')
const sending = ref(false) // Track sending state

const props = defineProps({
    recipient_id: {
        type: Number,
        required: true
    },
    onMessageSent: Function,
    contactType:{
        type:String
    }
})

const page = usePage()
const user_id = computed(() => page.props.user.id)

const sendMessage = async () => {
    if (!message.value.trim()) return; // Prevent empty messages

    sending.value = true;

    try {
        // Подготовка данных для отправки
        const payload = {
            user_id: user_id.value,
            recipient_id: props.recipient_id, // Всегда добавляем recipient_id
            message: message.value,
        };

        // Если тип контакта - группа, добавляем group_id
        if (props.contactType === 'group') {
            payload.group_id = props.recipient_id; // recipient_id здесь является group_id
        }

        await router.post('/chat/sendMessage', payload, {
            preserveScroll: true,
            onSuccess: () => {
                message.value = ''; // Очищаем поле ввода
                if (props.onMessageSent) {
                    props.onMessageSent(); // Вызываем коллбэк после успешной отправки
                }
            },
            onError: (errors) => {
                console.error("Error sending message:", errors);
            },
            onFinish: () => {
                sending.value = false; // Сбрасываем состояние отправки
            },
        });
    } catch (error) {
        console.error("Request failed:", error);
    }
};
</script>

<template>
    <div class="flex">
        <input @keyup.enter="sendMessage" v-model="message"
            class="block min-w-0 grow border-2 border-indigo-500 py-1.5 px-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm"
            type="text" :disabled="sending" placeholder="Введите сообщение...">
        <button class="p-4 bg-indigo-500 text-white hover:bg-indigo-600 transition disabled:opacity-50"
            @click="sendMessage" :disabled="sending">
            {{ sending ? 'Отправка...' : 'Отправить' }}
        </button>
    </div>
</template>
