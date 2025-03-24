<script setup>
import ContactItem from './ContactItem.vue';

defineProps({
    contacts: {
        type: Array,
        default: []
    },
    contactId: {
        type: Number
    }
});

defineEmits(['contact-selected']);
</script>

<template>
    <div class="bg-gray-500">
        <ContactItem :contactId="contactId" :contacts="contacts" @contact-selected="$emit('contact-selected', $event)" />
    </div>
</template>
