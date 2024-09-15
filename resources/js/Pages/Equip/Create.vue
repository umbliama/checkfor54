<script setup>
import { reactive, defineProps, useAttrs } from 'vue'
import { router } from '@inertiajs/vue3'

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
    <div class="flex h-screen">
        <div class="m-auto">
            <form class="w-full max-w-lg" @submit.prevent="submit">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-manufactor">
                            Производитель
                        </label>
                        <input name="manufactor" v-model="form.manufactor"
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="grid-manufactor" type="text">
                        <p class="text-red-500 text-xs italic">Обязательное поле</p>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-category">
                            Категория
                        </label>
                        <div class="relative">
                            <select name="category" v-model="form.category_id"
                                class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="grid-category">
                                <option v-for="category in equipment_categories" :key="category.id"
                                    :value="category.id"> {{ category.name }}</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-series">
                            Серия
                        </label>
                        <input v-model="form.series"
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="grid-series" type="text">
                        <p class="text-red-500 text-xs italic">Обязательное поле</p>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-category">
                            Дата производства
                        </label>
                        <input v-model="form.manufactor_date"
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="grid-category" type="text">
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-2">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-price">
                            Стоимость
                        </label>
                        <input v-model="form.price"
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="grid-price" type="text" placeholder="1200000">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-state">
                            Состояние
                        </label>
                        <div class="relative">
                            <select v-model="form.status"
                                class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="grid-state">
                                <option value="new">Новое</option>
                                <option value="good" >Хорошее</option>
                                <option value="satisfactory" >Удовлетворитено</option>
                                <option value="bad" >Плохое</option>
                                <option value="off" > Списан</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">

                            </div>
                        </div>
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-size">
                            Размерность
                        </label>
                        <div class="relative">
                            <select v-model="form.size_id"
                                class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="grid-size">
                                <option v-for="size in equipment_sizes" :key="size.id" :value="size.id">
                                    {{ size.name }}
                                </option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            </div>
                        </div>
                    </div>
                    <div class="w-full md:w-3/3 px-3 mt-2 mb-6 md:mb-0">
                        <div class="relative">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-size">
                            Примечание
                        </label>
                        <input v-model="form.notes"
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="grid-category" type="text">
                        </div>
                    </div>
                </div>
                <div class="flex text-center flex-wrap m-20">
                    <div class=" w-full md:w-3/3 px-3 mb-6 md:mb-0">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            type="submit">Создать</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</template>