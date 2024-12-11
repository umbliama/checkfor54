<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const page = usePage()


const props = defineProps({
    directory: Array
})

const handleFileUpload = (event) => {
  form.files = Array.from(event.target.files);
};


const form = reactive({
    commentary:null,
    service_id:null,
    equipment_id:null,
    sale_id:null,
    files:null
})

const submit = () => {
    const path = window.location.pathname;
    const parts = path.split('/');
    let type = parts[2];
    let id = parts[3];

    const formData = new FormData();
    formData.append('commentary', form.commentary);

    if (form.files) {
        form.files.forEach((file) => {
            formData.append('files[]', file);
        });
    }

    console.log(formData)
    if (type === 'equip') {
        formData.append('equipment_id', id);
    } else if (type === 'service') {
        formData.append('service_id', id);
    } else if (type === 'sale') {
        formData.append('sale_id', id);
    }

    router.post(`/directory/${type}/${id}`, formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });
};
</script>

<template>

    <AuthenticatedLayout>

        <div class="flex h-screen bg-gray-100">
            <!-- Sidebar -->
            <div class="w-1/4 bg-white p-4 shadow-md">
                <h2 class="text-lg font-bold mb-4">Справочник</h2>
                <div class="mb-4">
                    <h3 class="text-sm font-semibold mb-2">Файлы:</h3>
                    <ul>
                        <li class="flex items-center gap-2 mb-2">
                            <span class="inline-block w-6 h-6 bg-gray-300 flex justify-center items-center rounded-md">
                                📄
                            </span>
                            <span>Паспорт двигателя</span>
                            <a v-for="file in directory.files" href="">{{file}}</a>
                        </li>
                    </ul>
                </div>
                <button class="flex items-center gap-2 text-blue-500">
                    <span class="inline-block w-6 h-6 bg-gray-300 flex justify-center items-center rounded-md">
                        ➕
                    </span>
                    <input @change="handleFileUpload"  type="file"  name="files[]" multiple >
                </button>
            </div>

            <!-- Main Content -->
            <div class="flex-1 p-6">
                <h2 class="text-lg font-bold mb-4">Общая информация:</h2>
                <div>
                    <label for="commentary" class="block mb-2 text-sm font-semibold">Комментарий</label>
                    <textarea v-model="form.commentary" id="commentary" rows="10"
                        class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите текст...">
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        </textarea>
                </div>
                <button @click="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Сохранить
                </button>
                <p class="mt-4 text-sm text-green-500 flex items-center gap-2">
                    ✅ Успешно сохранено. Справочник сохранен.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>