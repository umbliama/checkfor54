<script setup>
import DashboardToolbar from '@/Components/Dashboard/DashboardToolbar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Chart } from 'chart.js/auto';
import { onMounted } from 'vue';
import ProgressBar from 'progressbar.js'
import { duration } from 'moment/moment';
import UiUserAvatar from '@/Components/Ui/UiUserAvatar.vue';

const props = defineProps({
    contragents_count:Number,
    contragents_inactive:Number,
    recent_contragents_count:Number,
    recent_contragents_percentage: Number,
    contragents_with_active_services_count: Number,
    active_contragents_percentage: Number,
    equipment_count:Number,
    recent_equipment_count:Number,
    equipment_in_active_services_count:Number,
    equipment_count_active_sum_percent:Number,
    equipment_categories:Array
})

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
        <DashboardToolbar dashboard-page-type="analysis" />

        <div class="p-4 lg:p-6">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-4 xl:grid-cols-6 lg:gap-6">
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">Всего заказчиков</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{contragents_count}}</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            <span class="block w-1.5 h-1.5 mr-1.5 rounded-full bg-danger"></span>
                            {{ contragents_inactive }}
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">Новых заказчиков</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{recent_contragents_count}}</div>
                        <div class="flex items-center h-6 px-3 rounded-full text-sm border border-gray1 bg-gray1 text-white">
                            +{{recent_contragents_percentage}}%
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">Активных заказчиков</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{ contragents_with_active_services_count }}</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            {{Number(active_contragents_percentage).toFixed(1)}}%
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">Всего оборудования</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{equipment_count}}</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            <span class="block w-1.5 h-1.5 mr-1.5 rounded-full bg-[#0F62FE]"></span>
                            +{{ recent_equipment_count }}
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">В аренде</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">{{equipment_in_active_services_count}}</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            {{equipment_count_active_sum_percent}}%
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="text-nowrap text-[15px] text-ellipsis overflow-hidden text-gray1">На складе</div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xl lg:text-2xl">184</div>
                        <div class="flex items-center h-6 px-2 rounded-full text-sm border border-gray1 bg-bg1">
                            <span class="block w-1.5 h-1.5 mr-1.5 rounded-full bg-[#DAC41E]"></span>
                            -12
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 mt-6 xl:grid-cols-2 lg:gap-6">
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <ul class="flex space-x-5 text-sm text-nowrap overflow-x-auto">
                        <li v-for="category in equipment_categories" class="shrink-0 py-2 font-medium border-b-2 border-b-[#001D6C] text-[#001D6C] cursor-pointer">{{category.name}}</li>
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
                            <span class="absolute left-1/2 -translate-x-1/2 bottom-0 font-bold text-3xl text-gray1">67%</span>
                        </div>
                        <div class="w-full mt-10 pt-4 border-t border-t-gray1 text-gray1 lg:w-[calc(100%-170px-24px)] lg:mt-0 lg:pt-0 lg:border-t-0">
                            <ul class="grid grid-cols-2 gap-3 text-sm">
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
                                <li><span class="font-medium">ДР 43:</span> 10 из 43</li>
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
                        <div class="w-full mt-10 pt-4 border-t border-t-gray1 lg:w-[calc(100%-220px-24px)] lg:mt-0 lg:pt-0 lg:border-t-0">
                            <ul class="space-y-4 text-xs lg:text-sm">
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#0F62FE]"></span>
                                    <span class="block mr-auto">ВЗД</span>
                                    <span class="text-gray1">104 из 303</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#DAC41E]"></span>
                                    <span class="block mr-auto">ЯСС</span>
                                    <span class="text-gray1">32 из 48</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#31C246]"></span>
                                    <span class="block mr-auto">ФД</span>
                                    <span class="text-gray1">24 из 52</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#DA1E28]"></span>
                                    <span class="block mr-auto">КО</span>
                                    <span class="text-gray1">104 из 303</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#DA1E28]"></span>
                                    <span class="block mr-auto">ПК</span>
                                    <span class="text-gray1">9 из 9</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#DDE1E6]"></span>
                                    <span class="block mr-auto">Хомут</span>
                                    <span class="text-gray1">9 из 9</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#DDE1E6]"></span>
                                    <span class="block mr-auto">Переводник</span>
                                    <span class="text-gray1">9 из 9</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="font-bold text-lg">Рейтинг</div>

                    <ul class="mt-4 space-y-4 text-xs lg:text-sm">
                        <li class="flex items-center">
                            <UiUserAvatar class="shrink-0 mr-2" />
                            <span class="w-[calc(100%-24px-8px-64px-40px)] mr-auto">ООО Азалмеган</span>
                            <span class="sheink-0 block w-10 lg:w-16 ml-4 mr-4 text-center">27.5%</span>
                            <span class="sheink-0 block w-10 lg:w-16 text-right">₽4.5M</span>
                        </li>
                        <li class="flex items-center">
                            <UiUserAvatar class="shrink-0 mr-2" />
                            <span class="w-[calc(100%-24px-8px-64px-40px)] mr-auto">ООО Азалмеган</span>
                            <span class="sheink-0 block w-10 lg:w-16 ml-4 mr-4 text-center">27.5%</span>
                            <span class="sheink-0 block w-10 lg:w-16 text-right">₽4.5M</span>
                        </li>
                        <li class="flex items-center">
                            <UiUserAvatar class="shrink-0 mr-2" />
                            <span class="w-[calc(100%-24px-8px-64px-40px)] mr-auto">ООО Азалмеган</span>
                            <span class="sheink-0 block w-10 lg:w-16 ml-4 mr-4 text-center">27.5%</span>
                            <span class="sheink-0 block w-10 lg:w-16 text-right">₽4.5M</span>
                        </li>
                        <li class="flex items-center">
                            <UiUserAvatar class="shrink-0 mr-2" />
                            <span class="w-[calc(100%-24px-8px-64px-40px)] mr-auto">ООО Азалмеган</span>
                            <span class="sheink-0 block w-10 lg:w-16 ml-4 mr-4 text-center">27.5%</span>
                            <span class="sheink-0 block w-10 lg:w-16 text-right">₽4.5M</span>
                        </li>
                        <li class="flex items-center">
                            <UiUserAvatar class="shrink-0 mr-2" />
                            <span class="w-[calc(100%-24px-8px-64px-40px)] mr-auto">ООО Азалмеган</span>
                            <span class="sheink-0 block w-10 lg:w-16 ml-4 mr-4 text-center">27.5%</span>
                            <span class="sheink-0 block w-10 lg:w-16 text-right">₽4.5M</span>
                        </li>
                        <li class="flex items-center">
                            <UiUserAvatar class="shrink-0 mr-2" />
                            <span class="w-[calc(100%-24px-8px-64px-40px)] mr-auto">ООО Азалмеган</span>
                            <span class="sheink-0 block w-10 lg:w-16 ml-4 mr-4 text-center">27.5%</span>
                            <span class="sheink-0 block w-10 lg:w-16 text-right">₽4.5M</span>
                        </li>
                    </ul>
                </div>
                <div class="p-4 border border-[#DDE1E6] bg-white">
                    <div class="font-bold text-lg">Процент выручки</div>

                    <div class="flex flex-col items-center mt-6 lg:flex-row lg:items-start">
                        <div class="w-[220px] mr-0 lg:mr-6">
                            <canvas id="income-graphic"></canvas>
                        </div>
                        <div class="w-full mt-10 pt-4 border-t border-t-gray1 text-gray1 lg:w-[calc(100%-220px-24px)] lg:mt-0 lg:pt-0 lg:border-t-0">
                            <ul class="space-y-4 text-sm">
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#0F62FE]"></span>
                                    <span class="block mr-auto">ВЗД</span>
                                    <span class="w-10 lg:w-14 mr-3 text-gray1">34%</span>
                                    <span class="w-10 lg:w-14 text-gray1">₽1.2M</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#DAC41E]"></span>
                                    <span class="block mr-auto">ЯСС</span>
                                    <span class="w-10 lg:w-14 mr-3 text-gray1">34%</span>
                                    <span class="w-10 lg:w-14 text-gray1">₽800K</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#31C246]"></span>
                                    <span class="block mr-auto">ФД</span>
                                    <span class="w-10 lg:w-14 mr-3 text-gray1">34%</span>
                                    <span class="w-10 lg:w-14 text-gray1">₽800K</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#DA1E28]"></span>
                                    <span class="block mr-auto">КО</span>
                                    <span class="w-10 lg:w-14 mr-3 text-gray1">34%</span>
                                    <span class="w-10 lg:w-14 text-gray1">₽1.2M</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#7300FF]"></span>
                                    <span class="block mr-auto">ПК</span>
                                    <span class="w-10 lg:w-14 mr-3 text-gray1">34%</span>
                                    <span class="w-10 lg:w-14 text-gray1">₽800K</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#041328]"></span>
                                    <span class="block mr-auto">Хомут</span>
                                    <span class="w-10 lg:w-14 mr-3 text-gray1">34%</span>
                                    <span class="w-10 lg:w-14 text-gray1">₽800K</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="block w-3 h-3 mr-1 rounded-full bg-[#DDE1E6]"></span>
                                    <span class="block mr-auto">Переводник</span>
                                    <span class="w-10 lg:w-14 mr-3 text-gray1">34%</span>
                                    <span class="w-10 lg:w-14 text-gray1">₽800K</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>