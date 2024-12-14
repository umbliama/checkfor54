<script setup>
import { Link } from '@inertiajs/vue3';


</script>

<template>
    <div>
        Обратитесь к администратору для подтверждения вашей учетной записи.

        <Link :href="route('logout')" method="post" as="button"
            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Выйти</Link>
    </div>
</template>