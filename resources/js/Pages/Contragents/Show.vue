<script setup>
import { reactive, defineProps, ref, watch } from "vue";
import { Link, router } from "@inertiajs/vue3";
import { computed } from "vue";
import store from "../../../store";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UiUserAvatar from "@/Components/Ui/UiUserAvatar.vue";
import UiFieldSelect from "@/Components/Ui/UiFieldSelect.vue";

const updateContrAgentID = (id) => {
    store.dispatch("contragent/updateCreatedContragentID", id);
};

const props = defineProps({
    contragent: Object,
});

const mobile_nav_items = [
    { title: 'Просмотр', value: route('contragents.show', props.contragent.id), tab_name: null       },
    { title: 'Профиль' , value: route('contragents.edit', props.contragent.id), tab_name: 'profile'  },
    { title: 'Банк'    , value: route('contragents.edit', props.contragent.id), tab_name: 'bank'     },
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
    contact_person_commentary:
        props.contragent.contact_person_commentary || null,
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
    store.dispatch("contragent/updateFormData", {
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
    });
}

const setTab = (tab) => { store.dispatch("contragent/updateActiveTab", tab); };
</script>

<template>
    <AuthenticatedLayout bg="gray">
        <div class="p-4 lg:p-6">
            <div class="flex items-center justify-between pb-6">
                <h1 class="font-semibold text-2xl">Контрагент</h1>
                <UiFieldSelect
                    v-model="contragent_navigation"
                    :items="mobile_nav_items"
                    class="min-w-44 ml-3 lg:hidden"
                />
            </div>
            <div class="space-y-4">
                <div class="flex items-start p-4 lg:items-center lg:p-6 content-block">
                    <UiUserAvatar :image="contragent.avatar" :size="{ pc: '96px', mob: '80px' }" />

                    <div class="grow flex flex-col items-start justify-between ml-4 lg:flex-row lg:items-center lg:ml-6">
                        <Link
                            class="px-5 py-2 font-medium tracking-wider border-black border-2"
                            :href="route('contragents.edit', contragent.id)"
                        >
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

                <div
                    v-if="contragent.customer == 1 || contragent.supplier == 1 || contragent.status == 1"
                    class="flex space-x-4 py-2 px-4 text-sm content-block"
                >
                    <p
                        v-if="contragent.customer == 1"
                        class="font-robotoBold font-bold"
                    >
                        Заказчик
                    </p>
                    <p
                        v-if="contragent.supplier == 1"
                        class="font-robotoBold font-bold"
                    >
                        Поставщик
                    </p>
                    <p
                        v-if="contragent.status == 1"
                        class="font-robotoBold font-bold"
                    >
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
                            {{ translateCountry(contragent.country) }}
                        </div>
                        <div>
                            <span class="text-gray-400">Организационно-правовая форма:</span>
                            {{ contragent.agentTypeLegal || '-' }}
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
