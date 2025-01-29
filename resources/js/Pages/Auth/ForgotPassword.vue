<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import UiField from '@/Components/Ui/UiField.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="font-bold text-2xl text-center lg:text-4xl">Забыли пароль?</div>
        <div class="mt-2 text-sm text-center lg:text-lg">Мы отправим вам сообщение, которое поможет вам сбросить пароль.</div>

        <div v-if="status" class="mb-4 font-medium text-sm text-center text-green-600">{{ status }}</div>

        <form class="mt-12 space-y-4 text-sm" @submit.prevent="submit">
            <UiField
                v-model="form.email"
                :inp-attrs="{ required: true, autofocus: true, placeholder: 'Введите адрес электронной почты указанный при регистрации', autocomplete: 'email', type: 'email' }"
                :error="form.errors.email"
                label="Адрес электронной почты"
            />

            <button
                :disabled="form.processing"
                class="flex items-center justify-center w-full h-12 font-medium text-base text-white bg-[#0F62FE] disabled:pointer-events-none disabled:opacity-60"
                type="submit"
            >Отправить ссылку для сброса</button>
        </form>
    </GuestLayout>
</template>
