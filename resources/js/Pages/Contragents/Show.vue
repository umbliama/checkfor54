<script setup>
import { reactive, defineProps, useAttrs } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import SideMenu from '@/Layouts/SideMenu.vue';
import { Switch } from '@headlessui/vue';
import { computed } from 'vue';
import store from '../../../store';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const updateContrAgentID = (id) => {
    store.dispatch('contragent/updateCreatedContragentID', id)
}

const props = defineProps({
    contragent: Object
})

const translateCountry = (country) => {
    switch (country) {
        case 'russia':
            return 'Российская Федерация'
            break;

        default:
            break;
    }
}

const translateLegal = (legal) => {
    switch (legal) {
        case 'russia':
            return 'Российская Федерация'
            break;

        default:
            break;
    }
}


const navigateToRoute = (event) => {
    const selectedRoute = event.target.value;
    if (selectedRoute) {
        router.visit(selectedRoute); // Inertia.js navigation
    }
}


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
    <AuthenticatedLayout>

        <div class="">

            <div class="flex bg-my-gray">
                <!-- Insert your sidebar component here -->

                <div class="flex w-3/5 sm:w-full sm:px-4 lg:ml-6 flex-col mt-5">
                    <div class="flex sm:justify-between">
                        <h2 class="font-semibold text-xl ml-4  mb-4">Контрагент</h2>
                        <select class="border-r-0 border-l-0 border-t-0 bg-my-gray  text-side-gray-text lg:hidden md:hidden ml-4  mb-4 " @change="navigateToRoute($event)" name="" id="">
                            <option selected value="">Просмотр</option>
                            <option :value="route('contragents.edit', contragent.id)"> Редактировать</option>
                            <option :value="route('contragents.create')">Добавить</option>
                        </select>
                    </div>
                    <div class="flex">

                        <!-- Main Form -->
                        <div class="w-full ">
                            <!-- Logo upload section -->
                            <div class="flex flex-col sm:mx-auto sm:pb-20">
                                <div class="flex items-start justify-between space-x-8 p-6 bg-white w-full">
                                    <!-- Avatar and Upload/Delete Buttons -->
                                    <div class="flex items-center space-x-6">
                                        <!-- Avatar -->
                                        <img v-if="contragent.avatar" class="sm:max-w-100 sm:rounded-3xl"
                                            :src="contragent.avatar" alt="">
                                        <span v-else><svg width="96" height="96" viewBox="0 0 96 96" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect width="96" height="96" rx="48" fill="#F2F4F8" />
                                                <path
                                                    d="M44.534 48.07C44.6569 48.0175 44.7892 47.9903 44.9228 47.9898C45.0565 47.9893 45.1889 48.0156 45.3122 48.0672C45.4355 48.1188 45.5473 48.1945 45.6408 48.29C45.7344 48.3855 45.8078 48.4988 45.8568 48.6231C45.9058 48.7475 45.9294 48.8804 45.9262 49.014C45.9229 49.1477 45.8929 49.2793 45.8379 49.4011C45.7829 49.523 45.7041 49.6325 45.606 49.7234C45.508 49.8142 45.3927 49.8845 45.267 49.93C44.5985 50.1934 44.0248 50.6519 43.6205 51.2458C43.2162 51.8397 43 52.5415 43 53.26V55C43 55.2652 43.1054 55.5196 43.2929 55.7071C43.4804 55.8946 43.7348 56 44 56H52C52.2652 56 52.5196 55.8946 52.7071 55.7071C52.8946 55.5196 53 55.2652 53 55V53.353C53.0001 52.6115 52.7749 51.8874 52.3541 51.2768C51.9334 50.6662 51.337 50.1979 50.644 49.934C50.5175 49.8901 50.4012 49.8213 50.3018 49.7316C50.2024 49.6419 50.1221 49.5332 50.0655 49.4119C50.009 49.2905 49.9773 49.1591 49.9725 49.0253C49.9677 48.8916 49.9897 48.7582 50.0374 48.6331C50.0851 48.508 50.1574 48.3938 50.25 48.2972C50.3427 48.2006 50.4538 48.1235 50.5767 48.0706C50.6997 48.0177 50.832 47.9901 50.9659 47.9893C51.0998 47.9885 51.2324 48.0146 51.356 48.066C52.4276 48.4742 53.3499 49.1983 54.0006 50.1425C54.6514 51.0867 54.9999 52.2063 55 53.353V55C55 55.7956 54.6839 56.5587 54.1213 57.1213C53.5587 57.6839 52.7956 58 52 58H44C43.2044 58 42.4413 57.6839 41.8787 57.1213C41.3161 56.5587 41 55.7956 41 55V53.26C41.0001 52.1402 41.3373 51.0463 41.9676 50.1206C42.5978 49.195 43.4921 48.4805 44.534 48.07ZM48 38C49.0609 38 50.0783 38.4214 50.8284 39.1716C51.5786 39.9217 52 40.9391 52 42V44C52 45.0609 51.5786 46.0783 50.8284 46.8284C50.0783 47.5786 49.0609 48 48 48C46.9391 48 45.9217 47.5786 45.1716 46.8284C44.4214 46.0783 44 45.0609 44 44V42C44 40.9391 44.4214 39.9217 45.1716 39.1716C45.9217 38.4214 46.9391 38 48 38V38ZM48 40C47.4696 40 46.9609 40.2107 46.5858 40.5858C46.2107 40.9609 46 41.4696 46 42V44C46 44.5304 46.2107 45.0391 46.5858 45.4142C46.9609 45.7893 47.4696 46 48 46C48.5304 46 49.0391 45.7893 49.4142 45.4142C49.7893 45.0391 50 44.5304 50 44V42C50 41.4696 49.7893 40.9609 49.4142 40.5858C49.0391 40.2107 48.5304 40 48 40Z"
                                                    fill="#C1C7CD" />
                                            </svg>

                                        </span>

                                        <!-- Upload/Delete Buttons -->
                                        <div class="lg:flex md:flex sm:hidden flex flex-col">
                                            <Link class="border-black border-2 px-6 py-2"
                                                :href="route('contragents.edit', contragent.id)">Редактировать
                                            </Link>
                                        </div>
                                    </div>

                                    <div class="text-lg  text-gray-700">
                                        <div class="flex flex-col">
                                            <div class="flex lg:hidden md:hidden">
                                                <Link class="border-black border-2 px-5 py-2"
                                                    :href="route('contragents.edit', contragent.id)">Редактировать
                                                </Link>
                                            </div>
                                            <div class="flex">
                                                <p>Наименование: {{ contragent.name }}</p>
                                            </div>
                                            <div class="flex">
                                                <p>ИНН: {{ contragent.inn }}</p>
                                            </div>
                                            <div class="flex">
                                                <p>КПП: {{ contragent.kpp }}</p>
                                            </div>
                                            <div class="flex">
                                                <p>ОГРН: {{ contragent.ogrn }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white mt-5 py-2 px-4 flex">
                                    <p class="font-bold" v-if="contragent.customer == 1">Заказчик</p>
                                    <p class="font-bold" v-if="contragent.supplier == 1">Поставщик</p>
                                    <p class="font-bold" v-if="contragent.active == 1">Активный</p>
                                </div>

                                <div class="bg-white mt-5 py-2 px-4 flex">
                                    <p class=""><span class="text-gray-400">Причина:</span> {{ contragent.reason }}</p>
                                </div>

                                <!-- Bank information -->
                                <div class="bg-white mt-3 p-3">
                                    <h3 class="font-semibold text-lg mb-4">Информация о компании:</h3>

                                    <div class="flex flex-col gap-4">
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> Страна регистрации:</span> {{
                                                translateCountry(contragent.country) }}</p>

                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> Организационно-правовая
                                                    форма:</span>
                                                {{ contragent.agentTypeLegal }}</p>

                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> Полное наименование:</span>
                                                {{ contragent.fullname }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> Примечание:</span>
                                                {{ contragent.notes }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> Входит в группу:</span>
                                                {{ contragent.group }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Комментарий:</span>
                                                {{ contragent.commentary }}</p>
                                        </div>


                                    </div>
                                </div>
                                <div class="bg-white mt-3 p-3">
                                    <h3 class="font-semibold text-lg mb-4">Банковские реквизиты:</h3>

                                    <div class="flex flex-col gap-4">
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> Наименование банка:</span> {{
                                                contragent.bankname }}</p>

                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> БИК:</span>
                                                {{ contragent.bank_bik }}</p>

                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> Корреспондентский счёт:</span>
                                                {{ contragent.bank_ca }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> Расчётный счёт:</span>
                                                {{ contragent.bank_rs }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400"> ИНН:</span>
                                                {{ contragent.bank_inn }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">КПП:</span>
                                                {{ contragent.bank_kpp }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Комментарий:</span>
                                                {{ contragent.bank_commnetary }}</p>
                                        </div>


                                    </div>
                                </div>
                                <div class="bg-white mt-3 p-3">
                                    <h3 class="font-semibold text-lg mb-4">Контакты:</h3>

                                    <div class="flex flex-col gap-4">
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Адрес:</span> {{
                                                contragent.address }}</p>

                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Сайт:</span>
                                                {{ contragent.site }}</p>

                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Телефон компании:</span>
                                                {{ contragent.phone }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Эл. почта компании:</span>
                                                {{ contragent.email }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Контактное лицо:</span>
                                                {{ contragent.contact_person }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Телефон:</span>
                                                {{ contragent.contact_person_phone }}</p>
                                        </div>
                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Эл.почта:</span>
                                                {{ contragent.contact_person_email }}</p>
                                        </div>

                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Примечание:</span>
                                                {{ contragent.contact_person_notes }}</p>
                                        </div>

                                        <div class="flex">
                                            <p class=""><span class="text-gray-400">Комментарий:</span>
                                                {{ contragent.contact_person_commentary }}</p>
                                        </div>


                                    </div>
                                </div>

                            </div>
                            <!-- Save button -->
                        </div>

                    </div>
                </div>
            </div>



        </div>
    </AuthenticatedLayout>

</template>