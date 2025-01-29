<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import UiField from '@/Components/Ui/UiField.vue';

const form = useForm({
    name: '',
    lastname: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="font-bold text-2xl text-center lg:text-4xl">Зарегистрироваться</div>
        <div class="mt-2 text-sm text-center lg:text-lg">Введите достоверные данные</div>

        <form class="mt-12 space-y-4 text-sm" @submit.prevent="submit">
            <UiField
                v-model="form.name"
                :inp-attrs="{ required: true, autofocus: true, autocomplete: 'name' }"
                :error="form.errors.name"
                label="Имя"
            />
            <UiField
                v-model="form.lastname"
                :inp-attrs="{ required: true, autocomplete: 'lastname' }"
                :error="form.errors.lastname"
                label="Фамилия"
            />
            <UiField
                v-model="form.email"
                :inp-attrs="{ required: true, autocomplete: 'email', type: 'email' }"
                :error="form.errors.email"
                label="Email"
            />
            <UiField
                v-model="form.password"
                :inp-attrs="{ required: true, autocomplete: 'new-password', type: 'password' }"
                :error="form.errors.password"
                label="Пароль"
            />

            <div class="text-center">Нажимая «Продолжить», вы принимаете пользовательское соглашение и политику конфиденциальности</div>

            <button
                :disabled="form.processing"
                class="flex items-center justify-center w-full h-12 font-medium text-base text-white bg-[#0F62FE] disabled:pointer-events-none disabled:opacity-60"
                type="submit"
            >Продолжить</button>
        </form>
        <Link
            :href="route('login')"
            class="block mt-6 pt-6 text-sm text-center border-t border-t-[#DDE1E6] disabled:pointer-events-none disabled:opacity-60"
        >Уже есть аккаунт?</Link>
    </GuestLayout>
</template>
