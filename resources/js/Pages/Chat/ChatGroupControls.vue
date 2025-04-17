<script setup>
import { ref, computed, onMounted } from 'vue';

// Состояния
const isModalOpen = ref(false); // Открыто ли модальное окно
const groupName = ref(''); // Название группы
const selectedUsers = ref([]); // Выбранные пользователи
const searchQuery = ref(''); 

const users = ref([]);

const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value; 
    return users.value.filter(user =>
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const getAllUsers = () => {

    axios.get('/chat/getAllUsers').then( response => users.value = response.data)
}


onMounted(() => {
        getAllUsers();
})

// Функции
const openModal = () => {
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    groupName.value = ''; // Очистить название группы
    selectedUsers.value = []; // Очистить выбранных пользователей
    searchQuery.value = ''; // Очистить поисковый запрос
};

const emit = defineEmits(['group-created']);

const createGroup = () => {
    if (!groupName.value.trim()) {
        alert('Введите название группы');
        return;
    }

    if (selectedUsers.value.length === 0) {
        alert('Выберите хотя бы одного пользователя');
        return;
    }

    // Формируем данные для отправки на сервер
    const groupData = {
        name: groupName.value,
        members: selectedUsers.value.map(user => user.id), // Отправляем только ID пользователей
    };

    // Отправляем запрос на сервер
    axios.post('/chat/createGroup', groupData)
        .then(response => {
            console.log('Группа успешно создана:', response.data);
            alert('Группа успешно создана!');
            emit('group-created');
            closeModal(); // Закрыть модальное окно
        })
        .catch(error => {
            console.error('Ошибка при создании группы:', error);
            alert('Произошла ошибка при создании группы.');
        });
};
</script>

<template>
    <div>
        <!-- Кнопка "Создать группу" -->
        <button
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
            @click="openModal"
        >
            Создать группу
        </button>

        <!-- Модальное окно -->
        <div v-if="isModalOpen" class="fixed z-50 inset-0 bg-black bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-xl font-bold mb-4">Создание группы</h2>

                <!-- Поле для названия группы -->
                <div class="mb-4">
                    <label for="group-name" class="block text-sm font-medium text-gray-700">Название группы</label>
                    <input
                        id="group-name"
                        v-model="groupName"
                        type="text"
                        placeholder="Введите название"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <!-- Поле поиска -->
                <div class="mb-4">
                    <label for="search-users" class="block text-sm font-medium text-gray-700">Поиск пользователей</label>
                    <input
                        id="search-users"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Поиск..."
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <!-- Список пользователей -->
                <div class="max-h-48 overflow-y-auto">
                    <div v-for="user in filteredUsers" :key="user.id" class="flex items-center mb-2">
                        <input
                            :id="'user-' + user.id"
                            v-model="selectedUsers"
                            type="checkbox"
                            :value="user"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                        />
                        <label :for="'user-' + user.id" class="ml-2 text-sm text-gray-700">{{ user.name }}</label>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex justify-end space-x-2 mt-4">
                    <button
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                        @click="closeModal"
                    >
                        Отмена
                    </button>
                    <button
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        @click="createGroup"
                    >
                        Создать
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>