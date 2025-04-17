<script setup>
import { Link } from '@inertiajs/vue3';



defineProps({
    contacts: {
        type: Array,
        default: []
    },
    contactId: {
        type: Number
    },
    contactType:{
        type:String
    }
})
const emit = defineEmits(['contact-selected', 'group-selected']);
const handleClick = (contact) => {
    console.log(contact)
    emit('contact-selected', contact.id);
    emit('group-selected', contact.type);
};
</script>

<template>
    <div v-if="contacts.length">
        <ul>
            <li :class="{ 'bg-blue-500': contactId === contact.id && contactType === contact.type }" class="p-4" v-for="contact in contacts"
                :key="contact.id">
                <button @click="handleClick(contact)" class="p-2 block w-full text-left">
                    {{ contact.name }} 
                </button>
            </li>
        </ul>
    </div>
    <div v-else>
        Контакты не найдены
    </div>
</template>