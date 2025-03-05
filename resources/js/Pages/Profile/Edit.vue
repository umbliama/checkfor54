<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import axios from 'axios';
import UiNotification from '@/Components/Ui/UiNotification.vue';


const form = reactive({
    avatar: null
})

const handleAvatar = (event) => {
    const selectedFile = event.target.files[0]; 

    if (!selectedFile) return;

    let formData = new FormData();
    formData.append('avatar', selectedFile);

    router.post('/profileAvatar', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });
}
const updateAvatar = () => {
    const formData = new FormData();
    formData.append('avatar', form.avatar);

}

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
        <UiNotification type="success" :description="$page.props.flash.message" v-model="$page.props.flash.message" />
        <UiNotification type="error" :description="$page.props.flash.error" v-model="$page.props.flash.error" />

    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h3>Аватар</h3>
                    <input type="file" name="avatar" @change="handleAvatar">
                </div>
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status"
                        class="max-w-xl" />
                </div>
                
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
                
                
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <Link method="POST" :href="route('logout')" class="p-2 sm:p-4 bg-black text-white shadow sm:rounded-lg">Выйти из учётной записи</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
