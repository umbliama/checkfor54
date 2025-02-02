<script setup>
import DashboardToolbar from '@/Components/Dashboard/DashboardToolbar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Chart } from 'chart.js/auto';
import { onMounted } from 'vue';
import ProgressBar from 'progressbar.js'
import { duration } from 'moment/moment';

onMounted(() => {
    const semi_cricle = new ProgressBar.Circle('.progress-bar-bg', {
        color: '#DDE1E6',
        easing: 'easeInOut',
        trailColor: 'transparent',
        strokeWidth: 7,
        duration: 0,
        svgStyle: {
            strokeLinecap: 'round'
        }
    });

    semi_cricle.path.style.strokeLinecap = 'round';

    semi_cricle.animate(0.7)

    const semi_cricle2 = new ProgressBar.Circle('.progress-bar', {
        color: '#878D96',
        easing: 'easeInOut',
        trailColor: 'transparent',
        strokeWidth: 7,
        duration: 0,
        svgStyle: {
            strokeLinecap: 'round'
        }
    });

    semi_cricle2.path.style.strokeLinecap = 'round';

    semi_cricle2.animate(0.3)
});
</script>

<template>
    <AuthenticatedLayout>
        <DashboardToolbar dashboard-page-type="analysis" />

        <div class="relative w-[200px] h-[400px]">
            <div class="progress-bar-bg rotate-[-127deg] w-[200px] h-[200px]"></div>
            <div class="progress-bar rotate-[-127deg] absolute left-0 top-0 w-[200px] h-[200px]"></div>
        </div>
    </AuthenticatedLayout>
</template>