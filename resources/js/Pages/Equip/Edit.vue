<script setup>
import { reactive, defineProps, useAttrs, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EquipNav from '@/Components/EquipNav.vue';
import FormSuccess from '@/Components/FormSuccess.vue';
import FormError from '@/Components/FormError.vue';


const page = usePage()



const success = computed(() => page.props.flash.success)
const errors = computed(() => page.props.errors)


const props = defineProps({
    equipment:Array,
    equipment_categories: Array,
    equipment_sizes: Array,
    equipment_location: Array
})

const form = reactive({
    'manufactor': null,
    'status': null,
    'size_id': null,
    'category_id': null,
    'status': null,
    'series': null,
    'price': null,
    'manufactor_date': null,
    'notes': null,
    'location_id': null,
    'zahodnost': null,
    'stator_rotor': null,
    'narabotka_ds': null,
    'rezbi': null,
    'length_rezba': null,
    'diameter': null,
    'length': null,
    'operating': null,
    'dlina_ds': null
})


function submit() {
    router.post('/equip', {
        manufactor: form.manufactor,
        status: form.status,
        size_id: form.size_id,
        category_id: form.category_id,
        status: form.status,
        series: form.series,
        price: form.price,
        manufactor_date: form.manufactor_date,
        notes: form.notes,
        location_id: form.location_id,
        zahodnost: form.zahodnost,
        stator_rotor: form.stator_rotor,
        narabotka_ds: form.narabotka_ds,
        rezbi: form.rezbi,
        length_rezba: form.length_rezba,
        diameter: form.diameter,
        length: form.length,
        operating: form.operating,
        dlina_ds: form.dlina_ds
    },
        {
            onSuccess: () => console.log(page.props.flash.success),
        })
}
</script>

<template>
    <AuthenticatedLayout>
        <EquipNav></EquipNav>
        <div class="flex h-screen">
            <FormSuccess class="bg-black-500" v-if="success" :message="success" />

            <FormError v-if="Object.keys(errors).length > 0" :message="Object.values(errors)" />

            <div class="flex flex-col p-8 w-full">
                <h3 class="font-bold text-lg">Добавить новое оборудование:</h3>

                <div class="grid grid-cols-3 gap-6 mb-6 mt-4">
                    <!-- First row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Категория</label>
                        <select required v-model="form.category_id" placeholder="Выберите категорию"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ВЗД">
                            <option v-for="item in equipment_categories" :value="item.id">{{ item.name }}</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Типоразмер</label>
                        <select required v-model="form.size_id"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="172">
                            <option v-for="item in equipment_sizes" :value="item.id">{{ item.name }}</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Выбрать склад</label>
                        <select required v-model="form.location_id"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 ">
                            <option v-for="item in equipment_location" :value="item.id">{{ item.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- First Divider -->
                <hr class="border-t-2 border-dotted border-gray-400 my-6 w-full">

                <!-- Second Divider -->
                <hr class="border-t-2 border-dotted border-gray-400 my-6 w-full">

                <div v-if="form.category_id == 1" class="grid grid-cols-3 gap-6 mb-6">
                    <!-- Second row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Производитель</label>
                        <input required v-model="form.manufactor" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Сокол">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Серия</label>
                        <input required v-model="form.series" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ДР - 3980">
                    </div>
                    
                    <div class="col-span-1">
                        <label class="block text-gray-700">Заходность</label>
                        <input required v-model="form.zahodnost" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ДР - 3980">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-700">Длина</label>
                        <input required v-model="form.length" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>



                    <div class="col-span-1">
                        <label class="block text-gray-700">Длина дс.</label>
                        <input required v-model="form.dlina_ds" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="2200">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Статор / Ротор</label>
                        <input required v-model="form.stator_rotor" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="181334001 / 1711025">
                    </div>

                    <!-- Fourth row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Наработка</label>
                        <input required v-model="form.operating" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="250">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Наработка дс.</label>
                        <input required v-model="form.narabotka_ds" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="250">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Дата изготовления</label>
                        <input required v-model="form.manufactor_date" type="date"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="15.03.2023">
                    </div>

                    <!-- Fifth row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Стоимость</label>
                        <input required v-model="form.price" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="1 280 000 Р">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Примечание</label>
                        <input required v-model="form.notes" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Гистранс">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Состояние</label>
                        <select required v-model="form.status" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Новое">
                            <option value="new">Новое</option>
                            <option value="good">Хорошее</option>
                            <option value="satisfactory">Удволетворительное</option>
                            <option value="bad">Плохое</option>
                            <option value="off">Списано</option>
                        </select>
                    </div>
                </div>
                <div v-if="form.category_id == 2" class="grid grid-cols-3 gap-6 mb-6">
                    <!-- Second row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Производитель</label>
                        <input required v-model="form.manufactor" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Сокол">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Серия</label>
                        <input required v-model="form.series" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ДР - 3980">
                    </div>


                    <!-- Third row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Длина</label>
                        <input required v-model="form.length" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>


                    <!-- Fourth row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Наработка</label>
                        <input required v-model="form.operating" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="250">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-700">Дата изготовления</label>
                        <input required v-model="form.manufactor_date" type="date"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="15.03.2023">
                    </div>

                    <!-- Fifth row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Стоимость</label>
                        <input required v-model="form.price" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="1 280 000 Р">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Примечание</label>
                        <input required v-model="form.notes" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Гистранс">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Состояние</label>
                        <select required v-model="form.status" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Новое">
                            <option value="new">Новое</option>
                            <option value="good">Хорошее</option>
                            <option value="satisfactory">Удволетворительное</option>
                            <option value="bad">Плохое</option>
                            <option value="off">Списано</option>
                        </select>
                    </div>
                </div>
                <div v-if="form.category_id == 3 || form.category_id == 4 || form.category_id == 5 || form.category_id == 7"
                    class="grid grid-cols-3 gap-6 mb-6">
                    <!-- Second row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Производитель</label>
                        <input required v-model="form.manufactor" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Сокол">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Серия</label>
                        <input required v-model="form.series" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="ДР - 3980">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-700">Диаметр</label>
                        <input required v-model="form.diameter" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="250">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-700">Длина</label>
                        <input required v-model="form.length" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Длина с резьбой</label>
                        <input required v-model="form.length_rezba" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Резьбы</label>
                        <input required v-model="form.rezbi" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-gray-700">Наработка</label>
                        <input required v-model="form.operating" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="4950">
                    </div>



                    <div class="col-span-1">
                        <label class="block text-gray-700">Дата изготовления</label>
                        <input required v-model="form.manufactor_date" type="date"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="15.03.2023">
                    </div>

                    <!-- Fifth row -->
                    <div class="col-span-1">
                        <label class="block text-gray-700">Стоимость</label>
                        <input required v-model="form.price" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="1 280 000 Р">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Примечание</label>
                        <input required v-model="form.notes" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Гистранс">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-gray-700">Состояние</label>
                        <select required v-model="form.status" type="text"
                            class="mt-1 block w-full border-b-2 border-gray-300 bg-my-gray border-r-0 border-t-0 border-l-0 px-3 "
                            value="Новое">
                            <option value="new">Новое</option>
                            <option value="good">Хорошее</option>
                            <option value="satisfactory">Удволетворительное</option>
                            <option value="bad">Плохое</option>
                            <option value="off">Списано</option>
                        </select>
                    </div>
                </div>

                <div class="col-span-3 flex justify-end">
                    <button @click="submit"
                        class="bg-my-gray text-side-gray-text font-bold px-6 py-3 ">Сохранить</button>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>