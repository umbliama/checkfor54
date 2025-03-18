<script setup>

import {router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const message = ref('')


const page = usePage()

const user_id = computed(() => page.props.user.id)

const sendMessage = () => {
    router.post('/chat/sendMessage', {
        'user_id': user_id,
        'recipient_id' :2,
        'message': message.value
    })
}

</script>

<template>
    <input v-model="message" type="text">
    <button @click="sendMessage">send</button>
</template>
