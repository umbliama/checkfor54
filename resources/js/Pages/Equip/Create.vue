<script setup>
import { reactive, defineProps, useAttrs } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EquipNav from '@/Components/EquipNav.vue';

const form = reactive({
    'manufactor': null,
    'status': null,
    'size_id': null,
    'category_id': null,
    'state': null,
    'series': null,
    'price': null,
    'notes': null,
    'manufactor_date': null
})

const props = defineProps({
    equipment_categories: Array,
    equipment_sizes: Array
})

function submit() {
    router.post('/equip', {
        manufactor: form.manufactor,
        status: form.status,
        size_id: form.size_id,
        category_id: form.category_id,
        state: form.state,
        series: form.series,
        price: form.price,
        manufactor_date: form.manufactor_date,
        notes: form.notes
    })
}
</script>

<template>
    <AuthenticatedLayout>
        <EquipNav></EquipNav>
        <div class="flex h-screen">


            <div class="flex flex-col p-8 w-full">
                <h3 class="font-bold text-lg">Добавить новое оборудование:</h3>

                <div class="grid grid-cols-3 gap-6 mb-6 mt-4">
                    <!-- First row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Категория</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 " value="ВЗД">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Типоразмер</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 " value="172">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Выбрать склад</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="г. Пермь">
                    </div>
                </div>

                <!-- First Divider -->
                <hr class="border-t-2 border-dotted border-gray-400 my-6 w-full">

                <!-- Second Divider -->
                <hr class="border-t-2 border-dotted border-gray-400 my-6 w-full">

                <div class="grid grid-cols-3 gap-6 mb-6">
                    <!-- Second row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Производитель</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 " value="Сокол">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Серия</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ДР - 3980">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Заходность</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 " value="7/8">
                    </div>

                    <!-- Third row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Длина</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 " value="4950">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Длина дс.</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 " value="2200">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Статор / Ротор</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="181334001 / 1711025">
                    </div>

                    <!-- Fourth row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Наработка</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 " value="250">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Наработка дс.</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 " value="250">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Дата изготовления</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="15.03.2023">
                    </div>

                    <!-- Fifth row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Стоимость</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="1 280 000 Р">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Примечание</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Гистранс">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Состояние</label>
                        <input type="text" class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 " value="Новое">
                    </div>
                </div>

                <div class="col-span-3 flex justify-end">
                    <button class="bg-my-gray text-side-gray-text font-bold px-6 py-3 ">Сохранить</button>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>