<script setup>
import { Link } from '@inertiajs/vue3';



defineProps({
    contacts: {
        type: Array,
        default: []
    },
    contactId:{
        type:Number
    }
})
const emit = defineEmits(['contact-selected']);

</script>

<template>
    <div v-if="contacts.length">
        <ul>
            <li :class="{'bg-blue-500': contactId === contact.id}" class="p-4" v-for="contact in contacts" :key="contact.id">
                <button @click="emit('contact-selected', contact.id)" class="p-2 block w-full text-left hover:bg-gray-200">
                    {{ contact.name }}
                </button>
            </li>
        </ul>
    </div>
    <div v-else>
        No contacts found.
    </div>
</template>