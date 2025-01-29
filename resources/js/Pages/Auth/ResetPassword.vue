<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import UiField from '@/Components/Ui/UiField.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <form class="mt-12 space-y-4 text-sm" @submit.prevent="submit">
            <UiField
                v-model="form.email"
                :inp-attrs="{ required: true, autofocus: true, autocomplete: 'email', type: 'email' }"
                :error="form.errors.email"
                label="Адрес электронной почты"
            />
            <UiField
                v-model="form.password"
                :inp-attrs="{ required: true, autocomplete: 'new-password', type: 'password' }"
                :error="form.errors.password"
                label="Пароль"
            />
            <UiField
                v-model="form.password_confirmation"
                :inp-attrs="{ required: true, autocomplete: 'new-password', type: 'password' }"
                :error="form.errors.password_confirmation"
                label="Подтверждение пароля"
            />

            <button
                :disabled="form.processing"
                class="flex items-center justify-center w-full h-12 font-medium text-base text-white bg-[#0F62FE] disabled:pointer-events-none disabled:opacity-60"
                type="submit"
            >Сбросить пароль</button>
        </form>
    </GuestLayout>
</template>
