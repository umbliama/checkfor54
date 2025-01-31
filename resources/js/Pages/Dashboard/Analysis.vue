<script setup>
import DashboardToolbar from '@/Components/Dashboard/DashboardToolbar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

<template>
    <AuthenticatedLayout>
        <DashboardToolbar dashboard-page-type="analysis" />

        
    </AuthenticatedLayout>
</template>