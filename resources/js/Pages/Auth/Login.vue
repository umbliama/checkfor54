<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import UiField from '@/Components/Ui/UiField.vue';
import UiCheckbox from '@/Components/Ui/UiCheckbox.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <GuestLayout>
        <Head title="Вход" />
        
        <div class="font-bold text-2xl text-center lg:text-4xl">Вход</div>
        <div class="mt-2 text-sm text-center lg:text-lg">Войдите, чтобы продолжить</div>
        
        <div v-if="status" class="mb-4 font-medium text-sm text-center text-green-600">{{ status }}</div>
        
        <form class="mt-12 space-y-4 text-sm" @submit.prevent="submit">
            <UiField
                v-model="form.email"
                :inp-attrs="{ required: true, autofocus: true, autocomplete: 'username', type: 'email' }"
                :error="form.errors.email"
                label="Адрес электронной почты"
            />
            <UiField
                v-model="form.password"
                :inp-attrs="{ required: true, autocomplete: 'current-password', type: 'password' }"
                :error="form.errors.password"
                hint="Длина пароля не менее 8 символов."
                label="Пароль"
            />

            <div class="flex items-center justify-between">
                <UiCheckbox v-model="form.remember" label="Запомнить меня" />

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-selected-blue"
                >
                    Забыли пароль?
                </Link>
            </div>

            <button
                :disabled="form.processing"
                class="flex items-center justify-center w-full h-12 font-medium text-base text-white bg-[#0F62FE] disabled:pointer-events-none disabled:opacity-60"
                type="submit"
            >Авторизоваться</button>
            
        </form>
        <Link
            :href="route('register')"
            class="block mt-6 pt-6 text-sm text-center border-t border-t-[#DDE1E6] disabled:pointer-events-none disabled:opacity-60"
        >Зарегистрироваться</Link>
    </GuestLayout>
</template>
