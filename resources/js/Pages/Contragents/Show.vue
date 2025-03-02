<script setup>
import { reactive, defineProps, useAttrs, ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import SideMenu from '@/Layouts/SideMenu.vue';
import { Switch } from '@headlessui/vue';
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
} from 'radix-vue'
import { computed } from 'vue';
import store from '../../../store';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UiUserAvatar from '@/Components/Ui/UiUserAvatar.vue';
import UiFieldSelect from '@/Components/Ui/UiFieldSelect.vue';
import UiNotification from '@/Components/Ui/UiNotification.vue';

const updateContrAgentID = (id) => {
    store.dispatch('contragent/updateCreatedContragentID', id)
}

const props = defineProps({
    contragent: Object,
    legalStatuses: Object
});

const mobile_nav_items = [
    { title: 'Просмотр', value: route('contragents.show', props.contragent.id), tab_name: null },
    { title: 'Профиль', value: route('contragents.edit', props.contragent.id), tab_name: 'profile' },
    { title: 'Банк', value: route('contragents.edit', props.contragent.id), tab_name: 'bank' },
    { title: 'Контакты', value: route('contragents.edit', props.contragent.id), tab_name: 'contacts' }
];

const getActiveTab = computed(() => store.getters["contragent/getActiveTab"]);

const contragent_navigation = ref({ ...mobile_nav_items[0] });

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
});

watch(contragent_navigation, new_val => {
    router.visit(new_val.value);
    if (new_val.tab_name) setTab(new_val.tab_name);
});

const navigateToRoute = (event) => {
    const selectedRoute = event.target.value;
    if (selectedRoute) router.visit(selectedRoute); // Inertia.js navigation
};

const translateCountry = (country) => {
    switch (country) {
        case "russia":
            return "Российская Федерация";
            break;
        default:
            break;
    }
};

const translateLegal = (legal) => {
    switch (legal) {
        case "russia":
            return "Российская Федерация";
            break;
        default:
            break;
    }
};

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
</script>

<template>
    <AuthenticatedLayout bg="gray">
        <div class="p-4 lg:p-6">
            <div class="flex items-center justify-between pb-6">
                <h1 class="font-semibold text-2xl">Контрагент</h1>
                <UiFieldSelect v-model="contragent_navigation" :items="mobile_nav_items"
                    class="min-w-44 ml-3 lg:hidden" />
            </div>
            <div class="grow flex items-start flex-wrap lg:flex-nowrap">
                <div class="grow space-y-4">
                    <div class="flex items-start p-4 lg:items-center lg:p-6 content-block">
                        <UiUserAvatar :image="contragent.avatar" :size="{ pc: '96px', mob: '80px' }" />

                        <div
                            class="grow flex flex-col items-start justify-between ml-4 lg:flex-row lg:items-center lg:ml-6">
                            <Link class="px-5 py-2 font-medium tracking-wider border-black border-2"
                                :href="route('contragents.edit', contragent.id)">
                            Редактировать
                            </Link>

                            <ul class="mt-2 space-y-1 text-sm">
                                <li>Наименование: {{ contragent.name }}</li>
                                <li>ИНН: {{ contragent.inn }}</li>
                                <li>КПП: {{ contragent.kpp }}</li>
                                <li>ОГРН: {{ contragent.ogrn }}</li>
                            </ul>
                        </div>
                    </div>

                    <div v-if="contragent.customer == 1 || contragent.supplier == 1 || contragent.status == 1"
                        class="flex space-x-4 py-2 px-4 text-sm content-block">
                        <p v-if="contragent.customer == 1" class="font-robotoBold font-bold">
                            Заказчик
                        </p>
                        <p v-if="contragent.supplier == 1" class="font-robotoBold font-bold">
                            Поставщик
                        </p>
                        <p v-if="contragent.status == 1" class="font-robotoBold font-bold">
                            Активный
                        </p>
                    </div>

                    <div v-if="contragent.reason" class="py-2 px-4 text-sm content-block">
                        <span class="text-gray-400">Причина:</span>
                        {{ contragent.reason }}
                    </div>

                    <div class="p-3 text-sm content-block">
                        <h3 class="font-bold text-lg">Информация о компании:</h3>

                        <div class="mt-6 space-y-4">
                            <div>
                                <span class="text-gray-400">Страна регистрации:</span>
                                {{ contragent.formatted_country.title || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Организационно-правовая форма:</span>
                                {{ contragent.legal_statuses.title || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Полное наименование:</span>
                                {{ contragent.fullname || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400"> Примечание:</span>
                                {{ contragent.notes || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Входит в группу:</span>
                                {{ contragent.group || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Комментарий:</span>
                                {{ contragent.commentary || '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="p-3 text-sm content-block">
                        <h3 class="font-bold text-lg">Банковские реквизиты:</h3>

                        <div class="mt-6 space-y-4">
                            <div>
                                <span class="text-gray-400">Наименование банка:</span>
                                {{ contragent.bankname || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400"> БИК:</span>
                                {{ contragent.bank_bik || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Корреспондентский счёт:</span>
                                {{ contragent.bank_ca || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Расчётный счёт:</span>
                                {{ contragent.bank_rs || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">ИНН:</span>
                                {{ contragent.bank_inn || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">КПП:</span>
                                {{ contragent.bank_kpp || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Комментарий:</span>
                                {{ contragent.bank_commnetary || '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="p-3 text-sm content-block">
                        <h3 class="font-bold text-lg">
                            Контакты:
                        </h3>

                        <div class="mt-6 space-y-4">
                            <div>
                                <span class="text-gray-400">Адрес:</span>
                                {{ contragent.address || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Сайт:</span>
                                {{ contragent.site || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Телефон компании:</span>
                                {{ contragent.phone || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Эл. почта компании:</span>
                                {{ contragent.email || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Контактное лицо:</span>
                                {{ contragent.contact_person || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Телефон:</span>
                                {{ contragent.contact_person_phone || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Эл.почта:</span>
                                {{ contragent.contact_person_email || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Примечание:</span>
                                {{ contragent.contact_person_notes || '-' }}
                            </div>
                            <div>
                                <span class="text-gray-400">Комментарий:</span>
                                {{ contragent.contact_person_commentary || '-' }}
                            </div>

                        </div>
                    </div>

                    <UiNotification type="error" title="Title" description="Description" default-open />
                    <UiNotification type="success" title="Title" description="Description" default-open />
                </div>
                <div class="shrink-0 w-full mt-4 space-y-4 lg:w-60 lg:mt-0 lg:ml-6 lg:space-y-1">
                    <div class="py-4 px-2 content-block">
                        <div class="font-medium">Комм. предложения:</div>


                        <div class="mt-5 space-y-5">
                            <div>
                                <div class="font-medium">Исходящие:</div>
                                <ul class="mt-2 space-y-3.5 bg-my-gray">
                                    <li v-if="form.commercials_incoming !== null || contragent.documents.some(doc => doc.type === 'commercials_incoming')"
                                        v-for="file in (form.commercials_incoming || contragent.documents.filter(doc => doc.type === 'commercials_incoming'))"
                                        class="flex items-center">
                                        <svg class="shrink-0 block" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                fill="#697077" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                fill="#697077" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                fill="#697077" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                fill="#697077" />
                                        </svg>

                                        <span class="text-elipsis" v-if="form.commercials_incoming">{{ file.name
                                            }}</span>
                                        <span class="text-elipsis" v-else>{{ file.file_path.split('/').pop() }}</span>
                                        <Link method="DELETE"
                                            :href="route('contragents.deleteFile', { contragentId: contragent.id, fileId: file.id })"
                                            class="shrink-0" type="button">
                                        <button>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                    fill="#697077" />
                                            </svg>
                                        </button>
                                        </Link>
                                    </li>
                                </ul>
                                <div class="flex items-center justify-between mt-2">
                                    <ul class="flex -space-x-2">
                                        <li>
                                            <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                        </li>
                                        <li>
                                            <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                        </li>
                                    </ul>
                                    <DropdownMenuRoot>
                                        <DropdownMenuTrigger>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                    fill="#697077" />
                                            </svg>
                                        </DropdownMenuTrigger>

                                        <DropdownMenuPortal>
                                            <transition name="fade">
                                                <DropdownMenuContent
                                                    class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                    :side-offset="5" align="end">
                                                    <DropdownMenuItem>
                                                        <label
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                            Загрузить
                                                            <input type="file" multiple
                                                                @change="handleFileUpload($event, 'commercials_incoming')"
                                                                hidden>
                                                        </label>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
                                </div>
                            </div>
                            <div>
                                <div class="font-medium">Входящие:</div>
                                <ul class="mt-2 space-y-3.5 bg-my-gray">
                                    <li v-if="form.commercials_outcoming !== null || contragent.documents.some(doc => doc.type === 'commercials_outcoming')"
                                        v-for="file in (form.commercials_outcoming || contragent.documents.filter(doc => doc.type === 'commercials_outcoming'))"
                                        class="flex items-center">
                                        <svg class="shrink-0 block" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                fill="#697077" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                fill="#697077" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                fill="#697077" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                fill="#697077" />
                                        </svg>

                                        <span class="text-elipsis" v-if="form.commercials_outcoming">{{ file.name
                                            }}</span>
                                        <span class="text-elipsis" v-else>{{ file.file_path.split('/').pop() }}</span>
                                        <Link method="DELETE"
                                            :href="route('contragents.deleteFile', { contragentId: contragent.id, fileId: file.id })"
                                            class="shrink-0" type="button">
                                        <button>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                    fill="#697077" />
                                            </svg>
                                        </button>
                                        </Link>
                                    </li>
                                </ul>
                                <div class="flex items-center justify-between mt-2">
                                    <ul class="flex -space-x-2">
                                        <li>
                                            <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                        </li>
                                        <li>
                                            <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                        </li>
                                    </ul>
                                    <DropdownMenuRoot>
                                        <DropdownMenuTrigger>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                    fill="#697077" />
                                            </svg>
                                        </DropdownMenuTrigger>

                                        <DropdownMenuPortal>
                                            <transition name="fade">
                                                <DropdownMenuContent
                                                    class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                    :side-offset="5" align="end">
                                                    <DropdownMenuItem>
                                                        <label
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                            Загрузить
                                                            <input type="file" multiple
                                                                @change="handleFileUpload($event, 'commercials_outcoming')"
                                                                hidden>
                                                        </label>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
                                </div>
                            </div>
                            <div>
                                <div class="font-medium">Тендер:</div>
                                <ul class="mt-2 space-y-3.5 bg-my-gray">
                                    <li v-if="form.commercials_tender !== null || contragent.documents.some(doc => doc.type === 'commercials_tender')"
                                        v-for="file in (form.commercials_tender || contragent.documents.filter(doc => doc.type === 'commercials_tender'))"
                                        class="flex items-center">
                                        <svg class="shrink-0 block" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                fill="#697077" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                fill="#697077" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                fill="#697077" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                fill="#697077" />
                                        </svg>

                                        <span class="text-elipsis" v-if="form.commercials_tender">{{ file.name }}</span>
                                        <span class="text-elipsis" v-else>{{ file.file_path.split('/').pop() }}</span>
                                        <Link method="DELETE"
                                            :href="route('contragents.deleteFile', { contragentId: contragent.id, fileId: file.id })"
                                            class="shrink-0" type="button">
                                        <button>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                    fill="#697077" />
                                            </svg>
                                        </button>
                                        </Link>
                                    </li>
                                </ul>
                                <div class="flex items-center justify-between mt-2">
                                    <ul class="flex -space-x-2">
                                        <li>
                                            <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                        </li>
                                        <li>
                                            <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                        </li>
                                    </ul>
                                    <DropdownMenuRoot>
                                        <DropdownMenuTrigger>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                                    fill="#697077" />
                                            </svg>
                                        </DropdownMenuTrigger>

                                        <DropdownMenuPortal>
                                            <transition name="fade">
                                                <DropdownMenuContent
                                                    class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                    :side-offset="5" align="end">
                                                    <DropdownMenuItem>
                                                        <label
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                            Загрузить
                                                            <input type="file" multiple
                                                                @change="handleFileUpload($event, 'commercials_tender')"
                                                                hidden>
                                                        </label>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="py-4 px-2 content-block">
                        <div class="font-medium">Договоры:</div>
                        <ul v-if="contragent" class="mt-2 space-y-3.5 bg-my-gray">
                            <li v-if="form.contracts !== null || contragent.documents.some(doc => doc.type === 'contracts')"
                                v-for="file in (form.contracts || contragent.documents.filter(doc => doc.type === 'contracts'))"
                                :key="file" class="flex items-center">
                                <svg class="shrink-0 block" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                        fill="#697077" />
                                </svg>
                                <span class="text-elipsis" v-if="form.contracts">{{ file.name }}</span>
                                <span class="text-elipsis" v-else>{{ file.file_path.split('/').pop() }}</span>
                            </li>
                        </ul>
                        <div class="flex items-center justify-between mt-2">
                            <ul class="flex -space-x-2">
                                <li>
                                    <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                </li>
                                <li>
                                    <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                </li>
                            </ul>
                            <DropdownMenuRoot>
                                <DropdownMenuTrigger>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                            fill="#697077" />
                                    </svg>
                                </DropdownMenuTrigger>

                                <DropdownMenuPortal>
                                    <transition name="fade">
                                        <DropdownMenuContent
                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                            :side-offset="5" align="end">
                                            <DropdownMenuItem>

                                                <label
                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                    Загрузить
                                                    <input type="file" multiple
                                                        @change="handleFileUpload($event, 'contracts')" hidden>
                                                </label>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </transition>
                                </DropdownMenuPortal>
                            </DropdownMenuRoot>
                        </div>
                    </div>
                    <div class="py-4 px-2 content-block">
                        <div class="font-medium">Транспортные документы:</div>
                        <ul v-if="contragent" class="mt-2 space-y-3.5 bg-my-gray">
                            <li v-if="form.transport !== null || contragent.documents.some(doc => doc.type === 'transport')"
                                v-for="file in (form.transport || contragent.documents.filter(doc => doc.type === 'transport'))"
                                class="flex items-center">
                                <svg class="shrink-0 block" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                        fill="#697077" />
                                </svg>
                                <span v-if="form.transport">{{ file.name }}</span>
                                <span v-else>{{ file.file_path.split('/').pop() }}</span>
                            </li>
                        </ul>
                        <div class="flex items-center justify-between mt-2">
                            <ul class="flex -space-x-2">
                                <li>
                                    <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                </li>
                                <li>
                                    <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                </li>
                            </ul>
                            <DropdownMenuRoot>
                                <DropdownMenuTrigger>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                            fill="#697077" />
                                    </svg>
                                </DropdownMenuTrigger>

                                <DropdownMenuPortal>
                                    <transition name="fade">
                                        <DropdownMenuContent
                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                            :side-offset="5" align="end">
                                            <DropdownMenuItem>
                                                <label
                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                    Загрузить
                                                    <input type="file" multiple
                                                        @change="handleFileUpload($event, 'transport')" hidden>
                                                </label>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </transition>
                                </DropdownMenuPortal>
                            </DropdownMenuRoot>
                        </div>
                    </div>
                    <div class="py-4 px-2 content-block">
                        <div class="font-medium">Финансовая отчетность:</div>
                        <ul v-if="contragent" class="mt-2 space-y-3.5 bg-my-gray">
                            <li v-if="form.financial !== null || contragent.documents.some(doc => doc.type === 'financial')"
                                v-for="file in (form.financial || contragent.documents.filter(doc => doc.type === 'financial'))"
                                class="flex items-center">
                                <svg class="shrink-0 block" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                        fill="#697077" />
                                </svg>
                                <span v-if="form.financial">{{ file.name }}</span>
                                <span v-else>{{ file.file_path.split('/').pop() }}</span>
                            </li>
                        </ul>
                        <div class="flex items-center justify-between mt-2">
                            <ul class="flex -space-x-2">
                                <li>
                                    <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                </li>
                                <li>
                                    <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                </li>
                            </ul>
                            <DropdownMenuRoot>
                                <DropdownMenuTrigger>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                            fill="#697077" />
                                    </svg>
                                </DropdownMenuTrigger>

                                <DropdownMenuPortal>
                                    <transition name="fade">
                                        <DropdownMenuContent
                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                            :side-offset="5" align="end">
                                            <DropdownMenuItem>
                                                <label
                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                    Загрузить
                                                    <input type="file" multiple
                                                        @change="handleFileUpload($event, 'financial')" hidden>
                                                </label>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </transition>
                                </DropdownMenuPortal>
                            </DropdownMenuRoot>
                        </div>
                    </div>
                    <div class="py-4 px-2 content-block">
                        <div class="font-medium">Доп. документы:</div>
                        <ul v-if="contragent" class="mt-2 space-y-3.5 bg-my-gray">
                            <li v-if="form.adddocs !== null || contragent.documents.some(doc => doc.type === 'adddocs')"
                                v-for="file in (form.adddocs || contragent.documents.filter(doc => doc.type === 'adddocs'))"
                                class="flex items-center">
                                <svg class="shrink-0 block" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                        fill="#697077" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                        fill="#697077" />
                                </svg>
                                <span v-if="form.adddocs">{{ file.name }}</span>
                                <span v-else>{{ file.file_path.split('/').pop() }}</span>
                            </li>
                        </ul>
                        <div class="flex items-center justify-between mt-2">
                            <ul class="flex -space-x-2">
                                <li>
                                    <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                </li>
                                <li>
                                    <UiUserAvatar size="24px" class="border border-[#DDE1E6]" />
                                </li>
                            </ul>
                            <DropdownMenuRoot>
                                <DropdownMenuTrigger>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                            fill="#697077" />
                                    </svg>
                                </DropdownMenuTrigger>

                                <DropdownMenuPortal>
                                    <transition name="fade">
                                        <DropdownMenuContent
                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                            :side-offset="5" align="end">
                                            <DropdownMenuItem>
                                                <label
                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                    Загрузить
                                                    <input type="file" multiple
                                                        @change="handleFileUpload($event, 'adddocs')" hidden>
                                                </label>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </transition>
                                </DropdownMenuPortal>
                            </DropdownMenuRoot>
                        </div>

                        <div class="mt-6 flex justify-end lg:hidden">
                            <button
                                class="inline-flex items-center justify-center py-3 px-7 font-medium tracking-wider bg-my-gray text-side-gray-text"
                                @click="submit">
                                Сохранить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>