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


const getActiveTab = computed(() => store.getters['contragent/getActiveTab']);
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
            
            <!-- <div class="flex sm:justify-center sm:w-full">
                <FormError v-if="Object.keys(errors).length > 0" :message="Object.values(errors)" />
                
                <div class="hidden w-1/4 lg:block">

                    <div class="content-block">
                        <ul class="p-2">
                            <li @click="setTab('profile')" :class="{ 'bg-my-gray': getActiveTab === 'profile' }"
                                class="py-3 px-2 font-medium cursor-pointer">
                                Профиль
                            </li>
                            <li @click="setTab('bank')" :class="{ 'bg-my-gray': getActiveTab === 'bank' }"
                                class="py-3 px-2 font-medium cursor-pointer ">
                                Банк
                            </li>
                            <li  @click="setTab('contacts')"
                                :class="{ 'bg-my-gray': getActiveTab === 'contacts' }" class="py-3 px-2 font-medium cursor-pointer">
                                Контакты
                            </li>
                        </ul>
                    </div>

                    <div class="mt-12 p-2 space-y-2 content-block">
                        <label class="p-2 flex items-center justify-between cursor-pointer">
                            <span class="font-medium text-my-nav-text">Заказчик</span>
                            <UiCheckbox v-mode="form.customer" size="lg" required />
                        </label>
                        <label class="p-2 flex items-center justify-between cursor-pointer">
                            <span class="font-medium text-my-nav-text">Поставщик</span>
                            <UiCheckbox v-mode="form.supplier" size="lg" required />
                        </label>
                        <label class="p-2 flex items-center justify-between cursor-pointer">
                            <span class="font-medium text-my-nav-text">Активный</span>
                            <Switch required v-model="form.status"
                                :class="form.status ? 'bg-[#697077]' : 'bg-gray-200'"
                                class="relative inline-flex h-4 w-[30px] items-center rounded-full">
                                <span class="sr-only">Enable notifications</span>
                                <span :class="form.status ? 'translate-x-[15px]' : 'translate-x-0.5'"
                                    class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition" />
                            </Switch>
                        </label>
                    </div>

                    <div class="mt-8 p-4 content-block">
                        <UiField v-model="form.reason" :inpAttrs="{ placeholder: 'Заключен договор поставки оборудования', rows: '3' }" label="Причина" textarea />
                    </div>
                </div>

                <div class="w-full lg:px-6 lg:w-3/4">
                    <div class="space-y-4">
                        <div class="flex flex-col p-4 content-block">
                            <h3 class="font-bold text-lg lg:text-xl">Логотип</h3>

                            <div class="flex items-start mt-6">

                                <div class="flex items-center w-full lg:w-1/2">
                                    <UiUserAvatar size="96px" />

                                    <div class="flex flex-col pl-0 mx-auto lg:mx-0 lg:pl-6">
                                        <label class="py-2 px-5 font-bold text-sm border-2 border-black lg:px-8 lg:py-3 lg:text-base"
                                            for="files">Загрузить </label>
                                        <input id="files" type="file" @change="onFileChange" accept="image/* "
                                            class="hidden w-full mt-2 p-2 border rounded"
                                            placeholder="Загрузить" />
                                        <button @click="onFileDelete" class="mt-2 text-sm text-black-600 lg:text-base">Удалить</button>
                                    </div>
                                </div>

                                <div class="self-stretch hidden border-l border-gray-300 lg:block" />

                                <div class="hidden w-1/2 lg:block">
                                    <div class="w-4/5 ml-auto">
                                        <p class="font-roboto" >Требования к изображению:</p>
                                        <ul class="list-disc ml-5">
                                            <li class="font-roboto" >Минимум 400 x 400 px</li>
                                            <li class="font-roboto" >Максимум 2 MB</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 content-block">
                            <template v-if="getActiveTab == 'profile'">
                                <h3 class="font-bold text-lg mb-4">Информация о компании:</h3>
    
                                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                    <UiFieldSelect
                                        v-model="form.country"
                                        :items="[ { title: 'Российская Федерация', value: 'russia' } ]"
                                        label="Страна регистрации"
                                        @blur="updateForm"
                                        required
                                    />
    
                                    <UiFieldSelect
                                        v-model="form.agentTypeLegal"
                                        :items="
                                            [
                                                { title: 'ООО',      value: 'OAO'        },
                                                { title: 'ОАО',      value: 'OOO'        },
                                                { title: 'ЗАО',      value: 'ZAO'        },
                                                { title: 'ПАО',      value: 'PAO'        },
                                                { title: 'Физ.лицо', value: 'individual' },
                                            ]
                                        "
                                        label="Организационно-правовая форма"
                                        @blur="updateForm"
                                        required
                                    />
    
                                    <UiField
                                        v-model="form.fullname"
                                        :inpAttrs="{ placeholder: 'Общество с ограниченной ответственностью Азалмеган' }"
                                        class="lg:col-span-2"
                                        label="Полное наименование"
                                        @blur="updateForm"
                                    />
    
                                    <UiField
                                        v-model="form.name"
                                        :inpAttrs="{ placeholder: 'ООО Азалмеган', required: true }"    
                                        label="Наименование"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.inn"
                                        :inpAttrs="{ placeholder: '56842365', required: true }"         
                                        label="ИНН"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.kpp"
                                        :inpAttrs="{ placeholder: '446842365' }"        
                                        label="КПП"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.ogrn"
                                        :inpAttrs="{ placeholder: 'ОГРН' }"             
                                        label="ОГРН"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.notes"
                                        :inpAttrs="{ placeholder: 'Lorem ipsum dolor' }"
                                        label="Примечание"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.group"
                                        :inpAttrs="{ placeholder: 'Ахъмалаша' }"        
                                        label="Входит в группу"
                                        @blur="updateForm"
                                    />
    
                                    <UiField
                                        v-model="form.commentary"
                                        :inpAttrs="{ placeholder: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod' }"
                                        label="Комментарий"
                                        class="lg:col-span-2"
                                        @blur="updateForm"
                                        textarea
                                    />
                                </div>
                            </template>
                            <template v-if="getActiveTab == 'bank'">
                                <h3 class="font-bold text-lg mb-4">Банковские реквизиты</h3>
    
                                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                    <UiField
                                        v-model="form.bankname"
                                        :inpAttrs="{ placeholder: 'Отделение Банка № 1234 ПАО ЖУЛИКИ' }"
                                        label="Наименование банка"
                                        class="col-span-1 lg:col-span-2"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.bank_bik"
                                        :inpAttrs="{ placeholder: '048073601' }"
                                        label="БИК"
                                        class="col-span-1 lg:col-span-2"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.bank_ca"
                                        :inpAttrs="{ placeholder: '1234567890' }"
                                        label="Корреспондентский счёт"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.bank_rs"
                                        :inpAttrs="{ placeholder: '1234567890' }"
                                        label="Расчётный счёт"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.bank_inn"
                                        :inpAttrs="{ placeholder: '1234567890' }"
                                        label="ИНН"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.bank_kpp"
                                        :inpAttrs="{ placeholder: '1234567890' }"
                                        label="ИНН"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.bank_commnetary"
                                        :inpAttrs="{ placeholder: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor' }"
                                        class="col-span-1 lg:col-span-2"
                                        label="Комментарий"
                                        @blur="updateForm"
                                        textarea
                                    />
                                </div>
                            </template>
                            <template v-if="getActiveTab == 'contacts'">
                                <h3 class="font-bold text-lg mb-4">Контакты</h3>
    
                                <div class="grid grid-cols-1 gap-4 lg:grid-cols-6">
                                    <UiField
                                        v-model="form.address"
                                        :inpAttrs="{ placeholder: 'Спрингфилд, проспект Мира 123 стр. 1' }"
                                        label="Адрес"
                                        class="col-span-1 lg:col-span-6"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.site"
                                        :inpAttrs="{ placeholder: 'www.site.com' }"
                                        label="Сайт"
                                        class="col-span-1 lg:col-span-2"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.phone"
                                        :inpAttrs="{ placeholder: '+1 (234) 567 89-99', required: true }"
                                        label="Телефон компании"
                                        class="col-span-1 lg:col-span-2"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.email"
                                        :inpAttrs="{ placeholder: 'info@mail.com', required: true }"
                                        label="Эл. почта компании"
                                        class="col-span-1 lg:col-span-2"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.contact_person"
                                        :inpAttrs="{ placeholder: 'Алекс Коржов' }"
                                        label="Контактное лицо"
                                        class="col-span-1 lg:col-span-3"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.contact_person_phone"
                                        :inpAttrs="{ placeholder: '+1 (234) 567 89-99' }"
                                        label="Телефон"
                                        class="col-span-1 lg:col-span-3"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.contact_person_email"
                                        :inpAttrs="{ placeholder: 'info@mail.com' }"
                                        label="Эл.почта"
                                        class="col-span-1 lg:col-span-3"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.contact_person_notes"
                                        :inpAttrs="{ placeholder: 'Lorem ipsum dolor' }"
                                        label="Примечание"
                                        class="col-span-1 lg:col-span-3"
                                        @blur="updateForm"
                                    />
                                    <UiField
                                        v-model="form.contact_person_commentary"
                                        :inpAttrs="{ placeholder: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor' }"
                                        label="Комментарий"
                                        class="col-span-1 lg:col-span-6"
                                        @blur="updateForm"
                                        textarea
                                    />
                                </div>
                            </template>

                            <div class="mt-6 hidden justify-end lg:flex">
                                <button
                                    class="inline-flex items-center justify-center py-3 px-7 font-medium tracking-wider bg-my-gray text-side-gray-text"
                                    @click="submit"
                                >
                                    Сохранить
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="block mt-4 p-2 space-y-2 content-block lg:hidden">
                        <label class="p-2 flex items-center justify-between cursor-pointer">
                            <span class="font-medium text-my-nav-text">Заказчик</span>
                            <UiCheckbox v-mode="form.customer" size="lg" required />
                        </label>
                        <label class="p-2 flex items-center justify-between cursor-pointer">
                            <span class="font-medium text-my-nav-text">Поставщик</span>
                            <UiCheckbox v-mode="form.supplier" size="lg" required />
                        </label>
                        <label class="p-2 flex items-center justify-between cursor-pointer">
                            <span class="font-medium text-my-nav-text">Активный</span>
                            <Switch required v-model="form.status"
                                :class="form.status ? 'bg-[#697077]' : 'bg-gray-200'"
                                class="relative inline-flex h-4 w-[30px] items-center rounded-full">
                                <span class="sr-only">Enable notifications</span>
                                <span :class="form.status ? 'translate-x-[15px]' : 'translate-x-0.5'"
                                    class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition" />
                            </Switch>
                        </label>
                    </div>

                    <div class="block mt-1 p-4 content-block lg:hidden">
                        <UiField v-model="form.reason" :inpAttrs="{ placeholder: 'Заключен договор поставки оборудования', rows: '3' }" label="Причина" textarea />

                        <div class="mt-6 flex justify-end lg:hidden">
                            <button
                                class="inline-flex items-center justify-center py-3 px-7 font-medium tracking-wider bg-my-gray text-side-gray-text"
                                @click="submit"
                            >
                                Сохранить
                            </button>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- <FormSuccess class="bg-black-500" v-if="success" :message="success" /> -->
        </div>
    </AuthenticatedLayout>
</template>