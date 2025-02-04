<script setup>
import DashboardToolbar from '@/Components/Dashboard/DashboardToolbar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Chart } from 'chart.js/auto';
import { onMounted } from 'vue';
import ProgressBar from 'progressbar.js'
import { duration } from 'moment/moment';
import UiUserAvatar from '@/Components/Ui/UiUserAvatar.vue';

onMounted(() => {
    const semi_cricle = new ProgressBar.Circle('.progress-bar-bg', {
        color: '#DDE1E6',
        easing: 'easeInOut',
        trailColor: 'transparent',
        strokeWidth: 10,
        duration: 0,
        svgStyle: {
            strokeLinecap: 'round'
        }
    });

    semi_cricle.path.style.strokeLinecap = 'round';

    semi_cricle.animate(0.7);

    const semi_cricle2 = new ProgressBar.Circle('.progress-bar', {
        color: '#878D96',
        easing: 'easeInOut',
        trailColor: 'transparent',
        strokeWidth: 10,
        duration: 0,
        svgStyle: {
            strokeLinecap: 'round'
        }
    });

    semi_cricle2.path.style.strokeLinecap = 'round';

    semi_cricle2.animate(0.3);


    new Chart(document.querySelector('#rent-graphic'), {
        type: 'polarArea',
        data: {
            datasets: [{
                label          : 'My First Dataset',
                data           : [ 4.5, 5.5, 4.5, 2.5, 5.5 ],
                backgroundColor: [ '#31C246', '#DAC41E', '#DA1E28', '#7300FF', '#0F62FE' ]
            }]
        },
        options: {
            scales: {
                r: {
                    grid : { color: '#C1C7CD' },
                    ticks: { display: false   }
                }
            },
            plugins: {
                tooltip: { enabled: false },
                legend: { display: false }
            }
        }
    });


    new Chart(document.querySelector('#income-graphic'), {
        type: 'doughnut',
        data: {
            datasets: [
                {
                    label: 'My First Dataset',
                    data: [33, 17, 33, 17],
                    backgroundColor: [
                        '#0F62FE',
                        '#31C246',
                        '#DAC41E',
                        '#DA1E28',
                    ]
                },
                {
                    label: 'My Second Dataset',
                    data: [10, 9, 20],
                    backgroundColor: [
                        '#7300FF',
                        '#041328',
                        '#DDE1E6',
                    ]
                }
            ]
        },
        options: {
            plugins: {
                tooltip: { enabled: false },
            }
        }
    });
});
</script>

<template>
    <AuthenticatedLayout bg="gray">
        <DashboardToolbar dashboard-page-type="commercial" />

        <div class="p-4 lg:p-6">
            
        </div>
    </AuthenticatedLayout>
</template>