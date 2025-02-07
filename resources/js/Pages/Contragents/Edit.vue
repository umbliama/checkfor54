<script setup>
import { reactive, defineProps, useAttrs, ref, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import SideMenu from '@/Layouts/SideMenu.vue';
import { Switch } from '@headlessui/vue';
import { computed } from 'vue';
import store from '../../../store';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormSuccess from '@/Components/FormSuccess.vue';
import FormError from '@/Components/FormError.vue';
// import contragent from 'resources/store/modules/contragent';
import UiFieldSelect from '@/Components/Ui/UiFieldSelect.vue';
import ContragentsCreateOrEditForm from '@/Components/Contragents/ContragentsCreateOrEditForm.vue';
import UiNotification from '@/Components/Ui/UiNotification.vue';

const updateContrAgentID = (id) => {
    store.dispatch('contragent/updateCreatedContragentID', id)
}

const page = usePage()

const mobile_nav_items = [
    { title: 'Просмотр', value: 'show' },
    { title: 'Профиль', value: 'profile' },
    { title: 'Банк', value: 'bank' },
    { title: 'Контакты', value: 'contacts' }
];

const getActiveTab = computed(() => store.getters['contragent/getActiveTab']);

const contragent_navigation = ref(getActiveTab.value ? { ...mobile_nav_items.find(item => item.value === getActiveTab.value) } : { ...mobile_nav_items[0] });

const success = computed(() => page.props.flash.success)

const error = ref(null)


const props = defineProps({
    contragent: Object,
    countries: Object,
    legalStatuses: Object
});

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
    avatar: null
});

watch(contragent_navigation, new_val => {
    if (new_val.value === 'show') {
        router.visit(route('contragents.show', props.contragent.id));
        return;
    }

    setTab(new_val.value);
});


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
        }, {
        onSuccess: () => console.log(page.props.flash.success),
    })
}
function onFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.avatar = file;
        console.log(form)

    }
}
const setTab = (tab) => {
    store.dispatch('contragent/updateActiveTab', tab);
}
</script>


<template>
    <AuthenticatedLayout bg="gray">
        <div class="p-4 lg:p-6">
            <div class="flex items-center justify-between pb-6">
                <h2 class="font-semibold text-2xl">Редактировать контрагента</h2>
                <UiFieldSelect v-model="contragent_navigation" :items="mobile_nav_items"
                    class="min-w-44 ml-3 lg:hidden" />
            </div>
            <UiNotification v-model="$page.props.flash.message"  :description="$page.props.flash.message"
                type="message" @close="handleClose" />
            <UiNotification v-model="$page.props.flash.error"  :description="$page.props.flash.error"
                type="error" @close="handleClose" />

            <ContragentsCreateOrEditForm :contragent="props.contragent" :countries="props.countries"
                :legal-statuses="props.legalStatuses" />
        </div>

    </AuthenticatedLayout>
</template>