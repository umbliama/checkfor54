<script setup>
import { reactive, defineProps, useAttrs } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import SideMenu from '@/Layouts/SideMenu.vue';
import { Switch } from '@headlessui/vue';
import { computed } from 'vue';
import store from '../../../store';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormSuccess from '@/Components/FormSuccess.vue';
import FormError from '@/Components/FormError.vue';

const updateContrAgentID = (id) => {
    store.dispatch('contragent/updateCreatedContragentID', id)
}

const page = usePage()


const success = computed(() => page.props.flash.success)


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
    avatar: props.contragent.avatar || null,
})



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
    router.patch(`/contragents/update/${props.contragent.id}`, 
    {
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
    },{
        onSuccess: () => console.log(page.props.flash.success),
    })
}

const setTab = (tab) => {
    store.dispatch('contragent/updateActiveTab', tab);
}

const getActiveTab = computed(() => store.getters['contragent/getActiveTab']);
</script>


<template>
    <AuthenticatedLayout>
        <div class="">

            <div class="flex sm:flex-col bg-my-gray">
                <!-- Insert your sidebar component here -->
                <div class="flex flex-col mt-5">
                    <div class="sm:flex sm:justify-between sm:p-4">
                        <h2 class="lg:block md:block sm:hidden font-semibold text-xl ml-4  mb-4">Добавить контрагента
                        </h2>
                        <h2 class="lg:hidden md:hidden sm:block font-semibold text-xl ml-4  mb-4">Настройки</h2>

                        <div class="lg:hidden md:hidden sm:flex">
                            <select
                                class=" border-b-2 border-r-0 border-l-0 border-t-0 focus:outline-none border-gray-200  bg-transparent">
                                <option @click="setTab('profile')"
                                    :class="{ 'bg-my-gray': getActiveTab === 'profile' }">Профиль</option>
                                <option @click="setTab('bank')" :class="{ 'bg-my-gray': getActiveTab === 'bank' }">Банк
                                </option>
                                <option @click="setTab('contacts')"
                                    :class="{ 'bg-my-gray': getActiveTab === 'contacts' }">Контакты </option>
                            </select>
                        </div>
                    </div>
                    <div class="flex sm:justify-center sm:w-full">

                        <div class="w-1/4 bg-gray-100 p-4 lg:block sm:hidden">

                            <div class="bg-white ">
                                <ul class="space-y-2 p-2">
                                    <li @click="setTab('profile')" :class="{ 'bg-my-gray': getActiveTab === 'profile' }"
                                        class="p-2">
                                        <Link>Профиль</Link>
                                    </li>
                                    <li @click="setTab('bank')" :class="{ 'bg-my-gray': getActiveTab === 'bank' }"
                                        class="p-2 ">
                                        <Link>Банк</Link>
                                    </li>
                                    <li @click="setTab('contacts')"
                                        :class="{ 'bg-my-gray': getActiveTab === 'contacts' }" class="p-2 ">
                                        <Link>Контакты</Link>
                                    </li>
                                </ul>
                            </div>

                            <div class="flex bg-white mt-10 px-3 py-2 flex-col">
                                <div class="mt-2 flex items-center justify-between">
                                    <label class="block text-gray-700">Заказчик</label>
                                    <input required v-model="form.customer" type="checkbox" class="rounded-sm mr-2 p-2">
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <label class="block text-gray-700">Поставщик</label>
                                    <input required v-model="form.supplier" type="checkbox" class="rounded-sm mr-2 p-2">
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <label for="">Активный</label>
                                    <Switch required v-model="form.status"
                                        :class="status ? 'bg-blue-600' : 'bg-gray-200'"
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
                        <div class="lg:w-3/4 sm:w-full sm:p-4 sm:pb-20 shadow-lg">
                            <!-- Logo upload section -->
                            <div class="flex flex-col">

                                <div class="flex flex-col bg-white w-full">
                                    <h3 class="text-xl font-bold p-4">Логотип</h3>

                                    <div class="flex items-start space-x-8 p-6 bg-white w-full">

                                        <!-- Avatar and Upload/Delete Buttons -->
                                        <div class="flex items-center space-x-6">
                                            <!-- Avatar -->
                                            <div class="">
                                                <svg width="96" height="96" viewBox="0 0 96 96" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="96" height="96" rx="48" fill="#F2F4F8" />
                                                    <path
                                                        d="M44.534 48.07C44.6569 48.0175 44.7892 47.9903 44.9228 47.9898C45.0565 47.9893 45.1889 48.0156 45.3122 48.0672C45.4355 48.1188 45.5473 48.1945 45.6408 48.29C45.7344 48.3855 45.8078 48.4988 45.8568 48.6231C45.9058 48.7475 45.9294 48.8804 45.9262 49.014C45.9229 49.1477 45.8929 49.2793 45.8379 49.4011C45.7829 49.523 45.7041 49.6325 45.606 49.7234C45.508 49.8142 45.3927 49.8845 45.267 49.93C44.5985 50.1934 44.0248 50.6519 43.6205 51.2458C43.2162 51.8397 43 52.5415 43 53.26V55C43 55.2652 43.1054 55.5196 43.2929 55.7071C43.4804 55.8946 43.7348 56 44 56H52C52.2652 56 52.5196 55.8946 52.7071 55.7071C52.8946 55.5196 53 55.2652 53 55V53.353C53.0001 52.6115 52.7749 51.8874 52.3541 51.2768C51.9334 50.6662 51.337 50.1979 50.644 49.934C50.5175 49.8901 50.4012 49.8213 50.3018 49.7316C50.2024 49.6419 50.1221 49.5332 50.0655 49.4119C50.009 49.2905 49.9773 49.1591 49.9725 49.0253C49.9677 48.8916 49.9897 48.7582 50.0374 48.6331C50.0851 48.508 50.1574 48.3938 50.25 48.2972C50.3427 48.2006 50.4538 48.1235 50.5767 48.0706C50.6997 48.0177 50.832 47.9901 50.9659 47.9893C51.0998 47.9885 51.2324 48.0146 51.356 48.066C52.4276 48.4742 53.3499 49.1983 54.0006 50.1425C54.6514 51.0867 54.9999 52.2063 55 53.353V55C55 55.7956 54.6839 56.5587 54.1213 57.1213C53.5587 57.6839 52.7956 58 52 58H44C43.2044 58 42.4413 57.6839 41.8787 57.1213C41.3161 56.5587 41 55.7956 41 55V53.26C41.0001 52.1402 41.3373 51.0463 41.9676 50.1206C42.5978 49.195 43.4921 48.4805 44.534 48.07ZM48 38C49.0609 38 50.0783 38.4214 50.8284 39.1716C51.5786 39.9217 52 40.9391 52 42V44C52 45.0609 51.5786 46.0783 50.8284 46.8284C50.0783 47.5786 49.0609 48 48 48C46.9391 48 45.9217 47.5786 45.1716 46.8284C44.4214 46.0783 44 45.0609 44 44V42C44 40.9391 44.4214 39.9217 45.1716 39.1716C45.9217 38.4214 46.9391 38 48 38V38ZM48 40C47.4696 40 46.9609 40.2107 46.5858 40.5858C46.2107 40.9609 46 41.4696 46 42V44C46 44.5304 46.2107 45.0391 46.5858 45.4142C46.9609 45.7893 47.4696 46 48 46C48.5304 46 49.0391 45.7893 49.4142 45.4142C49.7893 45.0391 50 44.5304 50 44V42C50 41.4696 49.7893 40.9609 49.4142 40.5858C49.0391 40.2107 48.5304 40 48 40Z"
                                                        fill="#C1C7CD" />
                                                </svg>

                                            </div>

                                            <!-- Upload/Delete Buttons -->
                                            <div class="flex flex-col">
                                                <label class="font-bold border-2 border-black px-8 py-3"
                                                    for="files">Загрузить</label>
                                                <input id="files" type="file" @change="onFileChange" accept="image/*"
                                                    class="lg:hidden md:hidden sm:hidden w-full mt-2 p-2 border rounded"
                                                    placeholder="Загрузить" />
                                                <button class="ml-2 text-black-600">Удалить</button>
                                            </div>
                                        </div>

                                        <!-- Divider -->
                                        <div class="lg:block md:block sm:hidden border-l border-gray-300 h-20"></div>

                                        <!-- Requirements Text -->
                                        <div class="lg:block md:block sm:hidden text-lg text-gray-700">
                                            <p>Требования к изображению:</p>
                                            <ul class="list-disc ml-5">
                                                <li>Минимум 400 x 400 px</li>
                                                <li>Максимум 2 MB</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank information -->
                                <div v-if="getActiveTab == 'profile'" class="bg-white mt-3 p-3">
                                    <h3 class="font-semibold text-lg mb-4">Информация о компании:</h3>

                                    <div class="grid grid-cols-2  gap-4">
                                        <div class="lg:col-span-1 sm:col-span-2">
                                            <label class="block text-gray-700">Организационно-правовая форма *</label>
                                            <select required @blur="updateForm" v-model="form.agentTypeLegal"
                                                type="text" class="w-full mt-2 p-2 border rounded"
                                                placeholder="048073601">
                                                <option value="OOO">ООО</option>
                                                <option value="OAO">ОАО</option>
                                                <option value="ZAO">ЗАО</option>
                                                <option value="PAO">ПАО</option>
                                                <option value="individual">Физ.лицо</option>
                                            </select>
                                            <!-- <p v-if="errors.agentTypeLegal" class="text-red-500 text-sm mt-1">{{ errors.agentTypeLegal }}</p> -->

                                        </div>
                                        <div class="lg:col-span-1 sm:col-span-2">
                                            <label class="block text-gray-700">Страна регистрации *</label>
                                            <select required @blur="updateForm" v-model="form.country" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="048073601">
                                                <option value="russia">Россия</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-gray-700">Полное наименование</label>
                                            <input required @blur="updateForm" v-model="form.fullname" type="text"
                                                class="w-full mt-2 p-2 border rounded"
                                                placeholder="Отделение Банка № 1234 ПАО Жулики">
                                        </div>

                                        <div class="lg:col-span-1 sm:col-span-2">
                                            <label class="block text-gray-700">Наименование</label>
                                            <input required @blur="updateForm" v-model="form.name" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="lg:col-span-1 sm:col-span-2">
                                            <label class="block text-gray-700">ИНН</label>
                                            <input required @blur="updateForm" v-model="form.inn" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="lg:col-span-1 sm:col-span-2">
                                            <label class="block text-gray-700">КПП</label>
                                            <input required @blur="updateForm" v-model="form.kpp" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="lg:col-span-1 sm:col-span-2">
                                            <label class="block text-gray-700">ОРГН</label>
                                            <input required @blur="updateForm" v-model="form.ogrn" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="lg:col-span-1 sm:col-span-2">
                                            <label class="block text-gray-700">Примечание</label>
                                            <input required @blur="updateForm" v-model="form.notes" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="lg:col-span-1 sm:col-span-2">
                                            <label class="block text-gray-700">Входит в группу</label>
                                            <input required @blur="updateForm" v-model="form.group" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>

                                        <div class="col-span-2">
                                            <label class="block text-gray-700">Комментарий</label>
                                            <textarea required @blur="updateForm" v-model="form.commentary"
                                                class="w-full mt-2 p-2 border rounded" rows="3"
                                                placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="getActiveTab == 'bank'" class="bg-white mt-3 p-3">
                                    <h3 class="font-semibold text-lg mb-4">Банковские реквизиты</h3>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="lg:col-span-2 sm:col-span-2">
                                            <label class="block text-gray-700">Наименование банка*</label>
                                            <input @blur="updateForm" v-model="form.bankname" type="text"
                                                class="w-full mt-2 p-2 border rounded"
                                                placeholder="Отделение Банка № 1234 ПАО Жулики">
                                        </div>
                                        <div class="col-span-2 sm:col-span-2">
                                            <label class="block text-gray-700">БИК</label>
                                            <input @blur="updateForm" v-model="form.bank_bik" type="text"
                                                class="w-full mt-2 p-2 border rounded"
                                                placeholder="Отделение Банка № 1234 ПАО Жулики">
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label class="block text-gray-700">Корреспондентский счёт</label>
                                            <input @blur="updateForm" v-model="form.bank_ca" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-gray-700">Расчётный счёт</label>
                                            <input @blur="updateForm" v-model="form.bank_rs" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-gray-700">ИНН</label>
                                            <input @blur="updateForm" v-model="form.bank_inn" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-gray-700">КПП</label>
                                            <input @blur="updateForm" v-model="form.bank_kpp" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>


                                        <div class="col-span-2 sm:col-span-2">
                                            <label class="block text-gray-700">Комментарий</label>
                                            <textarea @blur="updateForm" v-model="form.bank_commnetary"
                                                class="w-full mt-2 p-2 border rounded" rows="3"
                                                placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="getActiveTab == 'contacts'" class="bg-white mt-3 p-3">
                                    <h3 class="font-semibold text-lg mb-4">Контакты</h3>

                                    <div class="grid grid-cols-3 gap-4">
                                        <!-- 1 колонка, растягивается на 3 колонки -->
                                        <div class="col-span-3 sm:col-span-4">
                                            <label class="block text-gray-700">Адрес*</label>
                                            <input @blur="updateForm" v-model="form.address" type="text"
                                                class="w-full mt-2 p-2 border rounded"
                                                placeholder="Отделение Банка № 1234 ПАО Жулики">
                                        </div>

                                        <!-- 3 колонки -->
                                        <div class="sm:col-span-4">
                                            <label class="block text-gray-700">Сайт *</label>
                                            <input @blur="updateForm" v-model="form.site"
                                                class="w-full mt-2 p-2 border rounded" />
                                        </div>
                                        <div class="sm:col-span-4">
                                            <label class="block text-gray-700">Телефон компании</label>
                                            <input @blur="updateForm" v-model="form.phone" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="Телефон компании">
                                        </div>
                                        <div class="sm:col-span-4">
                                            <label class="block text-gray-700">Эл.почта компании</label>
                                            <input @blur="updateForm" v-model="form.email" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="example@mail.com">
                                        </div>
                                    </div>


                                    <div class="grid grid-cols-4 gap-4">
                                        <!-- 2 колонки -->
                                        <div class="sm:col-span-4 col-span-2">
                                            <label class="block text-gray-700">Контактное лицо</label>
                                            <input @blur="updateForm" v-model="form.contact_person" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="sm:col-span-4 col-span-2">
                                            <label class="block text-gray-700">Телефон</label>
                                            <input @blur="updateForm" v-model="form.contact_person_phone" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>

                                        <!-- 2 колонки -->
                                        <div class="col-span-2 sm:col-span-4">
                                            <label class="block text-gray-700">Эл.почта</label>
                                            <input @blur="updateForm" v-model="form.contact_person_email" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="1234567890">
                                        </div>
                                        <div class="col-span-2 sm:col-span-4">
                                            <label class="block text-gray-700">Примечание</label>
                                            <input @blur="updateForm" v-model="form.contact_person_notes" type="text"
                                                class="w-full mt-2 p-2 border rounded" placeholder="Примечание">
                                        </div>

                                        <!-- 2 колонки -->
                                        <div class="col-span-4">
                                            <label class="block text-gray-700">Комментарий</label>
                                            <textarea @blur="updateForm" v-model="form.contact_person_commentary"
                                                class="w-full mt-2 p-2 border rounded" rows="3"
                                                placeholder="Комментарий"></textarea>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="flex bg-white mt-10 px-3 py-2 lg:hidden md:hidden flex-col">
                                <div class="mt-2 flex items-center justify-between">
                                    <label class="block text-gray-700">Заказчик</label>
                                    <input required v-model="form.customer" type="checkbox" class="rounded-sm mr-2 p-2">
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <label class="block text-gray-700">Поставщик</label>
                                    <input required v-model="form.supplier" type="checkbox" class="rounded-sm mr-2 p-2">
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <label for="">Активный</label>
                                    <Switch required v-model="form.status"
                                        :class="status ? 'bg-blue-600' : 'bg-gray-200'"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full">
                                        <span class="sr-only">Enable notifications</span>
                                        <span :class="form.status ? 'translate-x-6' : 'translate-x-1'"
                                            class="inline-block h-4 w-4 transform rounded-full bg-white transition" />
                                    </Switch>
                                </div>

                            </div>

                            <div class="mt-8 lg:hidden md:hidden">
                                <label class="block text-gray-700">Причина</label>
                                <textarea v-model="form.reason" class="w-full mt-2 p-2 border rounded" rows="3"
                                    placeholder="Заключен договор поставки оборудования"></textarea>
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

            <FormSuccess class="bg-black-500" v-if="success" :message="success"/>

        </div>
    </AuthenticatedLayout>
</template>