<script setup>
import { reactive, defineProps, useAttrs, onMounted, ref, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import SideMenu from '@/Layouts/SideMenu.vue';
import { Switch } from '@headlessui/vue';
import { computed } from 'vue';
import store from '../../../store';
import FormError from '@/Components/FormError.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UiCheckbox from '@/Components/Ui/UiCheckbox.vue';
import UiUserAvatar from '@/Components/Ui/UiUserAvatar.vue';
import UiField from '@/Components/Ui/UiField.vue';
import UiFieldSelect from '@/Components/Ui/UiFieldSelect.vue';
import ContragentsCreateOrEditForm from '@/Components/Contragents/ContragentsCreateOrEditForm.vue';
import UiNotification from '@/Components/Ui/UiNotification.vue';


const page = usePage()

const errors = computed(() => page.props.errors)
const success = computed(() => page.props.flash.success)


const updateContrAgentID = (id) => {
    store.dispatch('contragent/updateCreatedContragentID', id)
}

const mobile_nav_items = [
    { title: 'Профиль' , value: 'profile'  },
    { title: 'Банк'    , value: 'bank'     },
    { title: 'Контакты', value: 'contacts' }
];

const contragent_navigation = ref({ ...mobile_nav_items[0] });

const form = reactive({
    agentTypeLegal: null,
    country: null,
    name: null,
    fullname: null,
    inn: null,
    kpp: null,
    ogrn: null,
    reason: null,
    notes: null,
    commentary: null,
    group: null,
    bankname: null,
    bank_bik: null,
    bank_inn: null,
    bank_rs: null,
    bank_kpp: null,
    bank_ca: null,
    bank_commnetary: null,
    supplier: false,
    customer: false,
    address: null,
    site: null,
    phone: null,
    email: null,
    contact_person: null,
    contact_person_phone: null,
    contact_person_email: null,
    contact_person_notes: null,
    contact_person_commentary: null,
    status: null,
    avatar: null,
})

const props = defineProps({
    equipment_categories: Array,
    equipment_sizes: Array,
    countries: Object,
    legalStatuses: Object
});

watch(contragent_navigation, new_val => {
    setTab(new_val.value);
});

function updateForm() {
    store.dispatch('contragent/updateFormData', {
        agentTypeLegal: form.agentTypeLegal.value,
        country: form.country.value,
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

function onFileChange(event) {
    const file = event.target.files[0];
    console.log(file)
    if (file) {
        form.avatar = file;
    }
}

function onFileDelete(){
    if (form.avatar)
    form.avatar = null
}

function submit() {
    updateForm();
    router.post('/contragents', {
        agentTypeLegal: form.agentTypeLegal.value,
        country: form.country.value,
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
        status: form.status ?? 0,
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
                <h2 class="font-semibold text-2xl">Добавить контрагента</h2>
                <UiFieldSelect
                    v-model="contragent_navigation"
                    :items="mobile_nav_items"
                    class="min-w-44 ml-3 lg:hidden"
                />
            </div>
            <UiNotification v-model="$page.props.flash.message"  :description="$page.props.flash.message"
                type="message" @close="handleClose" />
            <UiNotification v-model="$page.props.flash.error"  :description="$page.props.flash.error"
                type="error" @close="handleClose" />
            <ContragentsCreateOrEditForm :countries="props.countries" :legal-statuses="props.legalStatuses" />
        
        </div>
    </AuthenticatedLayout>
</template>