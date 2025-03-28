<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import ContactList from './ContactList.vue';
import ChatSearch from './ChatSearch.vue';
import ContactChat from './ContactChat.vue';
import { usePage } from '@inertiajs/vue3';
import ChatGroupControls from './ChatGroupControls.vue';


const page = usePage();

const contacts = ref([]);
const chosenContact = ref(null);
const currentUserId = computed(() => page.props.auth.user.id);
const searchText = ref('');
const loading = ref(false); // Состояние загрузки
const chosenType = ref(null)

const handleSearch = (query) => {
    searchText.value = query;
    axios.get('/chat/searchContactByName', {
        params: { name: searchText.value } // Correct way to pass query parameters
    }).then(response => {
        contacts.value = response.data
    }).catch(error => {
        console.error('Error fetching contacts:', error);
    });
};

const setChosenContact = (id) => {
    console.log(id)
    chosenContact.value = id;
};

const setChosenType = (id) => {
    console.log(id)
    chosenType.value = id;
};


onMounted(() => {
    loading.value = true; // Начало загрузки

    // Выполняем оба запроса параллельно
    Promise.all([
        axios.get('/chat/getContacts'),
        axios.get('/chat/getGroups'),
    ])
        .then(([contactsResponse, groupsResponse]) => {
            // Объединяем данные из обоих запросов
            const contactsData = contactsResponse.data || [];
            const groupsData = groupsResponse.data || [];

            // Добавляем тип для различия между контактами и группами
            const formattedContacts = contactsData.map(contact => ({
                type: 'contact',
                id: contact.id,
                name: contact.name
            }));

            const formattedGroups = groupsData.map(group => ({
                type: 'group',
                id: group.id,
                name: group.name,
                members: group.members // Дополнительные данные о группе
            }));

            console.log(formattedContacts, formattedGroups)

            // Объединяем контакты и группы в один массив
            contacts.value = [...formattedContacts, ...formattedGroups];
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        })
        .finally(() => {
            loading.value = false; // Конец загрузки
        });
});



</script>

<template>
    <div class="flex w-screen h-screen ">
        <div class="w-1/4 bg-gray-100 p-4 flex flex-col">
            <ChatGroupControls />
            <ChatSearch @update:search="handleSearch" />
            <div v-if="loading" class="flex justify-center items-center h-full">
                <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            </div>

            <!-- Отображение списка контактов -->
            <div v-else>
                <ContactList :type="chosenType" :contactId="chosenContact" :contacts="contacts"
                    @group-selected="setChosenType" @contact-selected="setChosenContact" />
            </div>
        </div>
        <ContactChat v-if="chosenContact || chosenType" :contactType="chosenType" :contactId="chosenContact" :groupId="chosenType" />
    </div>
</template>
