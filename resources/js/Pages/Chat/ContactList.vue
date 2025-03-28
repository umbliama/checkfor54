<script setup>
import ContactItem from './ContactItem.vue';

defineProps({
    contacts: {
        type: Array,
        default: []
    },
    contactId: {
        type: Number
    },
    contactType: {
        type: String
    }
});

defineEmits(['contact-selected','group-selected']);
</script>

<template>
    <div class="bg-white-500">
        <ContactItem :contactId="contactId" :contacts="contacts" @group-selected="$emit('group-selected', $event)" @contact-selected="$emit('contact-selected', $event)" />
    </div>
</template>
