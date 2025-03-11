<script setup>
import DashboardToolbar from '@/Components/Dashboard/DashboardToolbar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Chart } from 'chart.js/auto';
import { onMounted, ref, toRaw, watch } from 'vue';
import ProgressBar from 'progressbar.js'
import { duration } from 'moment/moment';
import UiUserAvatar from '@/Components/Ui/UiUserAvatar.vue';

const props = defineProps({
    contragents_count: Number,
    contragents_inactive: Number,
    recent_contragents_count: Number,
    recent_contragents_percentage: Number,
    contragents_with_active_services_count: Number,
    active_contragents_percentage: Number,
    equipment_count: Number,
    recent_equipment_count: Number,
    equipment_in_active_services_count: Number,
    equipment_count_active_sum_percent: Number,
    equipment_categories: Array,
    on_store: Number,
    unavailable: Number,
    categoryData: Number,
    contragentincome: Array,
    categoryDataIncome: Array,
    categoriesProgress: Array,
    categoryPercentages: Array,
})

const firstFour = ref(Object.values(props.categoryDataIncome).slice(0, 4));
const remaining = ref(Object.values(props.categoryDataIncome).slice(4));


const graphColors = ['bg-[#0F62FE]', 'bg-[#DAC41E]', 'bg-[#31C246]', 'bg-[#DA1E28]', 'bg-[#7300FF]', 'bg-[#041328]', 'bg-[#DDE1E6]'];
const chosenCategory = ref(1);
const percent = ref(0)
let semi_circle2 = null;
const setCategory = (id) => {
    chosenCategory.value = id;
    percent.value = props.categoryPercentages[chosenCategory.value - 1]?.percent || 0;
};
onMounted(() => {
    const maxProgress = 0.7;

    setCategory(1);
    const semi_circle = new ProgressBar.Circle('.progress-bar-bg', {
        color: '#DDE1E6',
        easing: 'easeInOut',
        trailColor: 'transparent',
        strokeWidth: 10,
        duration: 0,
        svgStyle: { strokeLinecap: 'round' }
    });

    semi_circle.path.style.strokeLinecap = 'round';
    semi_circle.animate(0.7);

    semi_circle2 = new ProgressBar.Circle('.progress-bar', {
        color: '#878D96',
        easing: 'easeInOut',
        trailColor: 'transparent',
        strokeWidth: 10,
        duration: 500,
        svgStyle: { strokeLinecap: 'round' }
    });

    semi_circle2.path.style.strokeLinecap = 'round';
    semi_circle2.animate(Math.min(percent.value / 100, maxProgress));


    watch(chosenCategory, (newValue) => {
        percent.value = props.categoryPercentages[newValue - 1]?.percent || 0;

        const maxProgress = 0.7;
        const progress = Math.min(percent.value / 100, maxProgress);

        semi_circle2.animate(progress);
    });

    new Chart(document.querySelector('#rent-graphic'), {
        type: 'polarArea',
        data: {
            datasets: [{
                label: 'My First Dataset',
                data: props.categoryData.map(category => category.total_service_count),
                backgroundColor: ['#0F62FE', '#DAC41E', '#31C246', '#7300FF', '#7300FF']
            }]
        },
        options: {
            scales: {
                r: {
                    grid: { color: '#C1C7CD' },
                    ticks: { display: false }
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
                    data: firstFour.value.map(category => category.percentage),
                    backgroundColor: [
                        '#0F62FE',
                        '#DAC41E',
                        '#31C246',
                        '#DA1E28',

                    ]
                },
                {
                    label: 'My Second Dataset',
                    data: remaining.value.map(category => category.percentage),
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
        <DashboardToolbar dashboard-page-type="analysis" />
        <div class="p-4 lg:p-6">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-4 xl:grid-cols-6 lg:gap-6">
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">Всего заказчиков</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{ contragents_count }}</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            <span class="block w-1.5 h-1.5 mr-1.5 rounded-full bg-danger"></span>
                            {{ contragents_inactive }}
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">Новых заказчиков</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{ recent_contragents_count }}</div>
                        <div
                            class="flex items-center h-6 px-3 rounded-full text-sm border border-gray1 bg-gray1 text-white">
                            +{{ recent_contragents_percentage }}%
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">Активных заказчиков
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{ contragents_with_active_services_count }}</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            {{ Number(active_contragents_percentage).toFixed(1) }}%
                        </div>
                    </div>
                </div>

                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">Всего оборудования
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{ equipment_count }}</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            <span class="block w-1.5 h-1.5 mr-1.5 rounded-full bg-[#0F62FE]"></span>
                            +{{ recent_equipment_count }}
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">В аренде</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{ equipment_in_active_services_count }}</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            {{ Number(equipment_count_active_sum_percent).toFixed(2) }}%
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">На складе</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{ on_store }}</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            <span class="block w-1.5 h-1.5 mr-1.5 rounded-full bg-[#DAC41E]"></span>
                            {{ unavailable }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 mt-6 xl:grid-cols-2 lg:gap-6">
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <ul class="flex space-x-5 text-sm text-nowrap overflow-x-auto">
                        <li v-for="category in equipment_categories" @click="setCategory(category.id)"
                            class="shrink-0 py-2 font-medium border-b-2 text-[#001D6C] cursor-pointer"
                            :class="{ 'border-b-[#001D6C]': chosenCategory === category.id }">
                            {{ category.name }}</li>
                    </ul>

                    <div class="flex itms-center space-x-4 mt-2.5">
                        <div class="flex items-center text-sm text-gray1">
                            <span class="block w-3 h-3 mr-1 rounded-full bg-gray1"></span>
                            В аренде
                        </div>
                        <div class="flex items-center text-sm text-gray1">
                            <span class="block w-3 h-3 mr-1 rounded-full bg-[#DDE1E6]"></span>
                            На складе
                        </div>
                    </div>

                    <div class="flex flex-col items-center mt-6 lg:flex-row lg:items-start">
                        <div class="relative w-[170px] lg:mr-6">
                            <div class="progress-bar-bg rotate-[-127deg] w-[170px] h-[170px]"></div>
                            <div class="progress-bar rotate-[-127deg] absolute left-0 top-0 w-[170px] h-[170px]"></div>
                            <span class="absolute left-1/2 -translate-x-1/2 bottom-0 font-bold text-3xl text-gray1">{{
                                categoryPercentages[chosenCategory - 1].percent }}%</span>
                        </div>
                        <div
                            class="w-full mt-10 pt-4 border-t border-t-gray1 text-gray1 lg:w-[calc(100%-170px-24px)] lg:mt-0 lg:pt-0 lg:border-t-0">
                            <ul class="grid grid-cols-2 gap-3 text-sm">
                                <li v-for="item in categoriesProgress[chosenCategory - 1]">{{ item.size }}<span
                                        class="font-medium"></span> {{ item.numberOfSizeOnRent }} из {{
                                            item.equipmentLeft }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="font-bold text-lg">Аренда</div>
                    <div class="flex flex-col items-center mt-6 lg:flex-row lg:items-start">
                        <div class="w-[220px] mr-0 lg:mr-6">
                            <canvas id="rent-graphic"></canvas>
                        </div>
                        <div
                            class="w-full mt-10 pt-4 border-t border-t-gray1 lg:w-[calc(100%-220px-24px)] lg:mt-0 lg:pt-0 lg:border-t-0">

                            <ul class="space-y-4 text-xs lg:text-sm">
                                <li v-for="(item, index) in categoryData" class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full"
                                        :class="'' + graphColors[index]"></span>
                                    <span class="block mr-auto">{{ item.name }}</span>
                                    <span class="text-gray1">{{ item.total_service_count }} из {{ item.total_equipment
                                        }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="font-bold text-lg">Рейтинг</div>

                    <ul class="mt-4 space-y-4 text-xs lg:text-sm">
                        <li v-for="item in contragentincome" class="flex items-center">
                            <UiUserAvatar class="shrink-0 mr-2" />
                            <span class="w-[calc(100%-24px-8px-64px-40px)] mr-auto">{{ item.contragentname }}</span>
                            <span class="sheink-0 block w-10 lg:w-16 ml-4 mr-4 text-center">{{ item.percent }}%</span>
                            <span class="sheink-0 block w-10 lg:w-16 text-right">₽{{ item.fullincome }}</span>
                        </li>
                    </ul>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="font-bold text-lg">Процент выручки</div>
                    <div class="flex flex-col items-center mt-6 lg:flex-row lg:items-start">
                        <div class="w-[220px] mr-0 lg:mr-6">
                            <canvas id="income-graphic"></canvas>
                        </div>
                        <div
                            class="w-full mt-10 pt-4 border-t border-t-gray1 text-gray1 lg:w-[calc(100%-220px-24px)] lg:mt-0 lg:pt-0 lg:border-t-0">
                            <ul class="space-y-4 text-sm">
                                <li v-for="(item, categoryName, index) in categoryDataIncome" class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full "
                                        :class="'' + graphColors[index % graphColors.length]"></span> 

                                    <span class="block mr-auto">{{ item.category }}</span>
                                    <span class="w-10 lg:w-14 mr-3 text-gray1">{{ item.percentage }}%</span>
                                    <span class="w-10 lg:w-14 text-gray1">₽{{ item.full_income.toFixed(1) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>