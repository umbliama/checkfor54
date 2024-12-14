<script setup>
import { Link } from '@inertiajs/vue3';


const props = defineProps({
    employees: Array,
    notApproved:Array
})


</script>

<template>
    Пользователи ждущие одобрения
    <ul>
        <li v-for="employee in notApproved">{{ employee.name }} <Link method="POST" :href="route('admin.approve', employee.id)">Одобрить</Link></li> 
    </ul>
</template>