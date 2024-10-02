<script setup>
import { reactive, defineProps, useAttrs } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import SideMenu from '@/Layouts/SideMenu.vue';
import { Switch } from '@headlessui/vue';
import { computed } from 'vue';
import store from '../../../store';

const updateContrAgentID = (id) => {
    store.dispatch('contragent/updateCreatedContragentID', id)
}

const props = defineProps({
    contragent: Object  
})
const form = reactive({
    agentTypeLegal: props.contragent.agentTypeLegal || null,
    country: props.contragent.country || null,
    name: props.contragent.name || null,
    fullname: props.contragent.fullname || null,
    inn: props.contragent.inn || null,
    kpp: props.contragent.kpp || null,
    ogrn: props.contragent.ogrn || null,
    reason: props.contragent.reason || null,
    notes: props.contragent.notes || null,
    commentary: props.contragent.commentary || null,
    group: props.contragent.group || null,
    bankname: props.contragent.bankname || null,
    bank_bik: props.contragent.bank_bik || null,
    bank_inn: props.contragent.bank_inn || null,
    bank_rs: props.contragent.bank_rs || null,
    bank_kpp: props.contragent.bank_kpp || null,
    bank_ca: props.contragent.bank_ca || null,
    bank_commnetary: props.contragent.bank_commnetary || null,
    supplier: !!props.contragent.supplier,
    customer: !!props.contragent.customer,
    address: props.contragent.address || null,
    site: props.contragent.site || null,
    phone: props.contragent.phone || null,
    email: props.contragent.email || null,
    contact_person: props.contragent.contact_person || null,
    contact_person_phone: props.contragent.contact_person_phone || null,
    contact_person_email: props.contragent.contact_person_email || null,
    contact_person_notes: props.contragent.contact_person_notes || null,
    contact_person_commentary: props.contragent.contact_person_commentary || null,
    status: !!props.contragent.status,
    avatar: props.contragent.avatar || null,})



function updateForm() {
    store.dispatch('contragent/updateFormData', {
        agentTypeLegal: form.agentTypeLegal,
        country: form.country,
        name: form.name,
        fullname: form.fullname,
        inn: form.inn,
        kpp: form.kpp,
        ogrn: form.ogrn,
        reason: form.reason,
        notes: form.notes,
        commentary: form.commentary,
        group: form.group,
        bankname: form.bankname,
        bank_bik: form.bank_bik,
        bank_inn: form.bank_inn,
        bank_rs: form.bank_rs,
        bank_kpp: form.bank_kpp,
        bank_ca: form.bank_ca,
        bank_commnetary: form.bank_commnetary,
        supplier: form.supplier,
        customer: form.customer,
        address: form.address,
        site: form.site,
        phone: form.phone,
        email: form.email,
        contact_person: form.contact_person,
        contact_person_phone: form.contact_person_phone,
        contact_person_email: form.contact_person_email,
        contact_person_notes: form.contact_person_notes,
        contact_person_commentary: form.contact_person_commentary,
        status: form.status,
        avatar: form.avatar,
    });
}


function submit() {
    updateForm();
    router.patch(`/contragents/update/${props.contragent.id}`, {
        agentTypeLegal: form.agentTypeLegal,
        country: form.country,
        name: form.name,
        fullname: form.fullname,
        inn: form.inn,
        kpp: form.kpp,
        ogrn: form.ogrn,
        reason: form.reason,
        notes: form.notes,
        commentary: form.commentary,
        group: form.group,
        bankname: form.bankname,
        bank_bik: form.bank_bik,
        bank_inn: form.bank_inn,
        bank_rs: form.bank_rs,
        bank_kpp: form.bank_kpp,
        bank_ca: form.bank_ca,
        bank_commnetary: form.bank_commnetary,
        supplier: form.supplier,
        customer: form.customer,
        address: form.address,
        site: form.site,
        phone: form.phone,
        email: form.email,
        contact_person: form.contact_person,
        contact_person_phone: form.contact_person_phone,
        contact_person_email: form.contact_person_email,
        contact_person_notes: form.contact_person_notes,
        contact_person_commentary: form.contact_person_commentary,
        status: form.status,
        avatar: form.avatar,
    })
}

const setTab = (tab) => {
    store.dispatch('contragent/updateActiveTab', tab);
}

const getActiveTab = computed(() => store.getters['contragent/getActiveTab']);
</script>

<template>

    <div class="">

        <div class="flex bg-my-gray">
            <!-- Insert your sidebar component here -->

            <SideMenu class="bg-white w-1/4"></SideMenu>
            <div class="flex flex-col mt-5">
                <h2 class="font-semibold text-xl ml-4  mb-4">Добавить контрагента</h2>
                <div class="flex">


                    <div class="w-1/4 bg-gray-100 p-4">

                        <div class="bg-white ">
                            <ul class="space-y-2 p-2">
                                <li @click="setTab('profile')" :class="{ 'bg-my-gray': getActiveTab === 'profile' }"
                                    class="p-2">
                                    <Link :href="route('contragents.create')">Профиль</Link>
                                </li>
                                <li @click="setTab('bank')" :class="{ 'bg-my-gray': getActiveTab === 'bank' }"
                                    class="p-2 ">
                                    <Link>Банк</Link>
                                </li>
                                <li @click="setTab('contacts')" :class="{ 'bg-my-gray': getActiveTab === 'contacts' }"
                                    class="p-2 ">
                                    <Link>Контакты</Link>
                                </li>
                            </ul>
                        </div>

                        <div class="flex bg-white mt-10 px-3 py-2 flex-col">
                            <div class="mt-2 flex items-center justify-between">
                                <label class="block text-gray-700">Заказчик</label>
                                <input  v-model="form.customer" type="checkbox" class="rounded-sm mr-2 p-2">
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <label class="block text-gray-700">Поставщик</label>
                                <input :checked="form.supplier == 1" v-model="form.supplier" type="checkbox" class="rounded-sm mr-2 p-2">
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <label for="">Активный</label>
                                <Switch v-model="form.status" :class="form.status ? 'bg-blue-600' : 'bg-gray-200'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full">
                                    <span class="sr-only">Enable notifications</span>
                                    <span :class="form.status ? 'translate-x-6' : 'translate-x-1'"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition" />
                                </Switch>
                            </div>

                        </div>

                        <div class="mt-8">
                            <label class="block text-gray-700">Причина</label>
                            <textarea v-model="form.reason" class="w-full mt-2 p-2 border rounded" rows="3"
                                placeholder="Заключен договор поставки оборудования"></textarea>
                        </div>
                    </div>

                    <!-- Main Form -->
                    <div class="w-3/4 shadow-lg">
                        <!-- Logo upload section -->
                        <div class="flex flex-col">
                            <div class="flex items-start space-x-8 p-6 bg-white w-full">
                                <!-- Avatar and Upload/Delete Buttons -->
                                <div class="flex items-center space-x-6">
                                    <!-- Avatar -->
                                    <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
                                        <span class="text-gray-400 text-4xl">+</span>
                                    </div>

                                    <!-- Upload/Delete Buttons -->
                                    <div class="flex flex-col">
                                        <button class="bg-blue-500 text-white px-4 py-2 rounded-md">Загрузить</button>
                                        <button class="ml-2 text-red-600">Удалить</button>
                                    </div>
                                </div>

                                <!-- Divider -->
                                <div class="border-l border-gray-300 h-20"></div>

                                <!-- Requirements Text -->
                                <div class="text-lg text-gray-700">
                                    <p>Требования к изображению:</p>
                                    <ul class="list-disc ml-5">
                                        <li>Минимум 400 x 400 px</li>
                                        <li>Максимум 2 MB</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Bank information -->
                            <div v-if="getActiveTab == 'profile'" class="bg-white mt-3 p-3">
                                <h3 class="font-semibold text-lg mb-4">Информация о компании:</h3>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700">Организационно-правовая форма *</label>
                                        <select @blur="updateForm" v-model="form.agentTypeLegal" type="text"
                                            class="w-full mt-2 p-2 border rounded" placeholder="048073601">
                                            <option value="OOO">ООО</option>
                                            <option value="OAO">ОАО</option>
                                            <option value="ZAO">ЗАО</option>
                                            <option value="PAO">ПАО</option>
                                            <option value="individual">Физ.лицо</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">Страна регистрации *</label>
                                        <select @blur="updateForm" v-model="form.country" type="text"
                                            class="w-full mt-2 p-2 border rounded" placeholder="048073601">
                                            <option value="russia">Россия</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-gray-700">Полное наименование</label>
                                        <input  @blur="updateForm" v-model="form.fullname" type="text"
                                            class="w-full mt-2 p-2 border rounded"
                                            placeholder="Отделение Банка № 1234 ПАО Жулики">
                                    </div>

                                    <div>
                                        <label class="block text-gray-700">Наименование</label>
                                        <input @blur="updateForm" v-model="form.name" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">ИНН</label>
                                        <input @blur="updateForm" v-model="form.inn" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">КПП</label>
                                        <input @blur="updateForm" v-model="form.kpp" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">ОРГН</label>
                                        <input @blur="updateForm" v-model="form.ogrn" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">Примечание</label>
                                        <input @blur="updateForm" v-model="form.notes" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">Входит в группу</label>
                                        <input @blur="updateForm" v-model="form.group" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-gray-700">Комментарий</label>
                                        <textarea @blur="updateForm" v-model="form.commentary" class="w-full mt-2 p-2 border rounded"
                                            rows="3"
                                            placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div v-if="getActiveTab == 'bank'" class="bg-white mt-3 p-3">
                                <h3 class="font-semibold text-lg mb-4">Банковские реквизиты</h3>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-gray-700">Наименование банка*</label>
                                        <input @blur="updateForm" v-model="form.bankname" type="text"
                                            class="w-full mt-2 p-2 border rounded"
                                            placeholder="Отделение Банка № 1234 ПАО Жулики">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-gray-700">БИК</label>
                                        <input @blur="updateForm" v-model="form.bank_bik" type="text"
                                            class="w-full mt-2 p-2 border rounded"
                                            placeholder="Отделение Банка № 1234 ПАО Жулики">
                                    </div>

                                    <div>
                                        <label class="block text-gray-700">Корреспондентский счёт</label>
                                        <input @blur="updateForm" v-model="form.bank_ca" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">Расчётный счёт</label>
                                        <input @blur="updateForm" v-model="form.bank_rs" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">ИНН</label>
                                        <input @blur="updateForm" v-model="form.bank_inn" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">КПП</label>
                                        <input @blur="updateForm" v-model="form.bank_kpp" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>


                                    <div class="col-span-2">
                                        <label class="block text-gray-700">Комментарий</label>
                                        <textarea @blur="updateForm" v-model="form.bank_commnetary" class="w-full mt-2 p-2 border rounded"
                                            rows="3"
                                            placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div v-if="getActiveTab == 'contacts'" class="bg-white mt-3 p-3">
                                <h3 class="font-semibold text-lg mb-4">Контакты</h3>

                                <div class="grid grid-cols-3 gap-4">
                                    <!-- 1 колонка, растягивается на 3 колонки -->
                                    <div class="col-span-3">
                                        <label class="block text-gray-700">Адрес*</label>
                                        <input @blur="updateForm" v-model="form.address" type="text"
                                            class="w-full mt-2 p-2 border rounded"
                                            placeholder="Отделение Банка № 1234 ПАО Жулики">
                                    </div>

                                    <!-- 3 колонки -->
                                    <div>
                                        <label class="block text-gray-700">Сайт *</label>
                                        <select @blur="updateForm" v-model="form.site" class="w-full mt-2 p-2 border rounded">
                                            <option value="russia">Россия</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">Телефон компании</label>
                                        <input @blur="updateForm" v-model="form.phone" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="Телефон компании">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700">Эл.почта</label>
                                        <input @blur="updateForm" v-model="form.email" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="example@mail.com">
                                    </div>
                                </div>


                                <div class="grid grid-cols-4 gap-4">
                                    <!-- 2 колонки -->
                                    <div class="col-span-2">
                                        <label class="block text-gray-700">Контактное лицо</label>
                                        <input @blur="updateForm" v-model="form.contact_person" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-gray-700">Телефон</label>
                                        <input @blur="updateForm" v-model="form.contact_person_phone" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>

                                    <!-- 2 колонки -->
                                    <div class="col-span-2">
                                        <label class="block text-gray-700">Эл. почта</label>
                                        <input @blur="updateForm" v-model="form.contact_person_email" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="1234567890">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-gray-700">Примечание</label>
                                        <input @blur="updateForm" v-model="form.contact_person_notes" type="text" class="w-full mt-2 p-2 border rounded"
                                            placeholder="Примечание">
                                    </div>

                                    <!-- 2 колонки -->
                                    <div class="col-span-4">
                                        <label class="block text-gray-700">Комментарий</label>
                                        <textarea @blur="updateForm" v-model="form.contact_person_commentary" class="w-full mt-2 p-2 border rounded"
                                            rows="3" placeholder="Комментарий"></textarea>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!-- Save button -->
                        <div class="mt-6 flex justify-end">
                            <button @click="submit"
                                class="bg-blue-500 text-white px-6 py-2 rounded-md">Сохранить</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>



    </div>
</template> 