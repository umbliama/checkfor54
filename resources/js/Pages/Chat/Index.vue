<script setup>
import { onMounted, ref } from 'vue';
import ContactList from './ContactList.vue';
import ContactChat from './ContactChat.vue';

const contacts = ref([]);
const chosenContact = ref(null);

const setChosenContact = (id) => {
    chosenContact.value = id;
};

onMounted(() => {
    axios.get('/chat/getContacts')
        .then(response => {
            contacts.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching contacts:', error);
        });
});
</script>

<template>
    <div class="flex">
        <ContactList :contactId="chosenContact" :contacts="contacts" @contact-selected="setChosenContact" />        
        <ContactChat v-if="chosenContact" :contactId="chosenContact" />
    </div>
</template>
