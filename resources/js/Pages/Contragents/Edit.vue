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


watch(contragent_navigation, new_val => {
    if (new_val.value === 'show') {
        router.visit(route('contragents.show', props.contragent.id));
        return;
    }

    setTab(new_val.value);
});



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
                type="success" @close="handleClose" />
            <UiNotification v-model="$page.props.flash.error"  :description="$page.props.flash.error"
                type="error" @close="handleClose" />

            <ContragentsCreateOrEditForm :contragent="props.contragent" :countries="props.countries"
                :legal-statuses="props.legalStatuses" />
        </div>

    </AuthenticatedLayout>
</template>