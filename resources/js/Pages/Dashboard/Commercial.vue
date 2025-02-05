<script setup>
import DashboardToolbar from '@/Components/Dashboard/DashboardToolbar.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UiUserAvatar from '@/Components/Ui/UiUserAvatar.vue';
import {
    DialogClose,
    DialogContent,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'radix-vue';
import {
    PopoverAnchor,
    PopoverArrow,
    PopoverClose,
    PopoverContent,
    PopoverPortal,
    PopoverRoot,
    PopoverTrigger
} from 'radix-vue'
import {
    AccordionContent,
    AccordionHeader,
    AccordionItem,
    AccordionRoot,
    AccordionTrigger
} from "radix-vue";
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
} from 'radix-vue';
import { ref } from 'vue';
import UiField from '@/Components/Ui/UiField.vue';
import UiSelect from '@/Components/Ui/UiSelect.vue';
import UiFieldSelect from '@/Components/Ui/UiFieldSelect.vue';
import { Link } from '@inertiajs/vue3';


const statuses = {
    'new': 'Новое',
    'good': 'Хорошее',
    'satisfactory': 'Удовлетворительно',
    'bad': 'Плохое',
    'off': 'Списано',
    'unknown': 'Неизвестный статус'
};
const statuses_colors = {
    'new': 'bg-[#0F62FE]',
    'good': 'bg-[#31C246]',
    'satisfactory': 'bg-[#DAC41E]',
    'bad': 'bg-[#DA1E28]',
    'off': 'bg-[#000000]',
    'unknown': 'bg-[#C0C0C0]'
};

const edit_dialog_state = ref(false);

const edit_status = ref(null);

function editRow() {
    edit_dialog_state.value = true;
}
</script>

<template>
    <AuthenticatedLayout bg="gray">
        <DashboardToolbar dashboard-page-type="commercial" filter />

        <div class="p-4 lg:p-6">
            <div class="w-full max-w-full mt-2.5 bg-bg2 overflow-x-auto border border-gray3">
                <div class="min-w-[1144px] text-xs">
                    <div
                        class="flex font-bold border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3">
                        <div class="shrink-0 flex items-center justify-center w-[40px] py-2.5 px-2"></div>
                        <div class="shrink-0 flex items-center justify-center w-[32px] py-2.5 px-2"></div>
                        <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">
                            Наименование

                            <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.16675 12L12.1667 7.99996M8.16675 3.66663V12V3.66663ZM8.16675 12L4.16675 7.99996L8.16675 12Z" stroke="#21272A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="relative shrink-0 flex items-center justify-between w-[14.08%] py-2.5 px-2 cursor-pointer">Автор</div>
                        <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Тип</div>
                        <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Дата</div>
                        <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">Примечание
                        </div>
                        <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">
                            Статус

                            <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.16675 12L12.1667 7.99996M8.16675 3.66663V12V3.66663ZM8.16675 12L4.16675 7.99996L8.16675 12Z" stroke="#21272A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="shrink-0 flex items-center w-[80px] py-2.5 px-2">
                            
                        </div>
                    </div>
                    <AccordionRoot type="multiple" :collapsible="true">
                        <AccordionItem value="item-1">
                            <AccordionHeader class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 bg-white">
                                <div class="shrink-0 flex items-center justify-center w-[40px]">
                                    <AccordionTrigger class="shrink-0 group">
                                        <svg class="group-data-[state=open]:hidden" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                fill="#242533" />
                                        </svg>
                                        <svg class="group-data-[state=closed]:hidden" width="20" height="20"
                                            viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                fill="#242533" />
                                        </svg>
                                    </AccordionTrigger>
                                </div>
                                <div class="shrink-0 flex items-center justify-center w-[32px]">
                                    <span class="flex items-center justify-center w-[18px] h-[18px] rounded-full text-xs bg-gray1 text-white">8</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">
                                    <UiUserAvatar size="40px" class="shrink-0 mr-2" />
                                    <div>
                                        <span class="flex justify-start items-start font-medium">
                                            ООО Азалмеган
                                        </span>
                                        <span class="flex text-gray-500 items-start text-xs">
                                            ИНН 56842365
                                        </span>
                                    </div>
                                </div>
                                <div class="relative shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Шакиров Руслан</div>
                                <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Исходящий</div>
                                <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">27.02.2024</div>
                                <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">ВЗД+ЯСС / Аренда
                                </div>
                                <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">
                                    <span :class="statuses_colors['good']" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                    <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses['good'] }}</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[80px] py-2.5 px-2">
                                    <Link
                                        v-if="true"
                                        :href="'/'"
                                        class="mr-3.5"
                                    >
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                    fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                    fill="#21272A"/>
                                        </svg>
                                    </Link>
                                    <PopoverRoot v-else>
                                        <PopoverTrigger class="mr-3.5">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                                <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                                <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                            </svg>
                                        </PopoverTrigger>
                                        <PopoverPortal>
                                            <PopoverContent side="bottom" align="end" class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                <div>Комментарий:</div>
                                                <p class="mt-2.5 text-xs">Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Страна если бросил, он всемогущая запятых грамматики себя ipsum точках, несколько меня строчка маленькая страну предупреждал которой раз проектах. Ему выйти составитель дал то ...</p>
                                                <div class="mt-3 p-4 bg-bg1 text-xs">
                                                    <div class="flex items-center max-w-full">
                                                        <span class="grow block mr-auto text-ellipsis overflow-hidden">Some file name</span>
                                                        <svg class="shrink-0 block ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                fill="#697077"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                fill="#697077"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                fill="#697077"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                fill="#697077"/>
                                                        </svg>
                                                        <DropdownMenuRoot>
                                                            <DropdownMenuTrigger aria-label="Customise options">
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                                        fill="#687182"/>
                                                                    <path
                                                                        d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                                        fill="#687182"/>
                                                                    <path
                                                                        d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                                        fill="#687182"/>
                                                                </svg>
                                                            </DropdownMenuTrigger>

                                                            <DropdownMenuPortal>
                                                                <transition name="fade">
                                                                    <DropdownMenuContent
                                                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                                        :side-offset="5" align="end">
                                                                        <DropdownMenuItem>
                                                                            <Link :href="'/'"
                                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                Скачать
                                                                                <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                    <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                </svg>
                                                                            </Link>
                                                                        </DropdownMenuItem>
                                                                    </DropdownMenuContent>
                                                                </transition>
                                                            </DropdownMenuPortal>
                                                        </DropdownMenuRoot>
                                                    </div>
                                                </div>
                                                <Link
                                                    class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                >
                                                    Редактировать

                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                        <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                    </svg>
                                                </Link>
                                            </PopoverContent>
                                        </PopoverPortal>
                                    </PopoverRoot>

                                    <DropdownMenuRoot>
                                        <DropdownMenuTrigger aria-label="Customise options">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                    fill="#687182"/>
                                                <path
                                                    d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                    fill="#687182"/>
                                                <path
                                                    d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                    fill="#687182"/>
                                            </svg>
                                        </DropdownMenuTrigger>

                                        <DropdownMenuPortal>
                                            <transition name="fade">
                                                <DropdownMenuContent
                                                    class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                    :side-offset="5" align="end">
                                                    <DropdownMenuItem>
                                                        <Link class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                            Открыть
                                                            <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z" fill="#464F60"/>
                                                            </svg>

                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <button
                                                            type="button"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                            @click="editRow"
                                                        >
                                                            Редактировать
                                                            <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                                <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                            </svg>
                                                        </button>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
                                </div>
                            </AccordionHeader>
                            <AccordionContent class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden">
                                <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all bg-[#F3F1FE]">
                                    <div class="shrink-0 flex items-center justify-center w-[40px]"></div>
                                    <div class="shrink-0 flex items-center justify-center w-[32px]"></div>
                                    <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">
                                        <UiUserAvatar size="40px" class="shrink-0 mr-2" />
                                        <div>
                                            <span class="flex justify-start items-start font-medium">
                                                ООО Азалмеган
                                            </span>
                                            <span class="flex text-gray-500 items-start text-xs">
                                                ИНН 56842365
                                            </span>
                                        </div>
                                    </div>
                                    <div class="relative shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Шакиров Руслан</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Исходящий</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">27.02.2024</div>
                                    <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">ВЗД+ЯСС / Аренда
                                    </div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">
                                        <span :class="statuses_colors['good']" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                        <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses['good'] }}</span>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[80px] py-2.5 px-2">
                                        <Link
                                            v-if="true"
                                            class="mr-3.5"
                                        >
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                        fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                        fill="#21272A"/>
                                            </svg>
                                        </Link>
                                        <PopoverRoot v-else>
                                            <PopoverTrigger class="mr-3.5">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                                    <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                                    <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                                </svg>
                                            </PopoverTrigger>
                                            <PopoverPortal>
                                                <PopoverContent side="bottom" align="end" class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                    <div>Комментарий:</div>
                                                    <p class="mt-2.5 text-xs">Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Страна если бросил, он всемогущая запятых грамматики себя ipsum точках, несколько меня строчка маленькая страну предупреждал которой раз проектах. Ему выйти составитель дал то ...</p>
                                                    <div class="mt-3 p-4 bg-bg1 text-xs">
                                                        <div class="flex items-center max-w-full">
                                                            <span class="grow block mr-auto text-ellipsis overflow-hidden">Some file name</span>
                                                            <svg class="shrink-0 block ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                    fill="#697077"/>
                                                            </svg>
                                                            <DropdownMenuRoot>
                                                                <DropdownMenuTrigger aria-label="Customise options">
                                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                                            fill="#687182"/>
                                                                        <path
                                                                            d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                                            fill="#687182"/>
                                                                        <path
                                                                            d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                                            fill="#687182"/>
                                                                    </svg>
                                                                </DropdownMenuTrigger>

                                                                <DropdownMenuPortal>
                                                                    <transition name="fade">
                                                                        <DropdownMenuContent
                                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                                            :side-offset="5" align="end">
                                                                            <DropdownMenuItem>
                                                                                <Link :href="'/'"
                                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                    Скачать
                                                                                    <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                        <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                    </svg>
                                                                                </Link>
                                                                            </DropdownMenuItem>
                                                                        </DropdownMenuContent>
                                                                    </transition>
                                                                </DropdownMenuPortal>
                                                            </DropdownMenuRoot>
                                                        </div>
                                                    </div>
                                                    <Link
                                                        class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Редактировать

                                                        <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                            <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                        </svg>
                                                    </Link>
                                                </PopoverContent>
                                            </PopoverPortal>
                                        </PopoverRoot>

                                        <DropdownMenuRoot>
                                            <DropdownMenuTrigger aria-label="Customise options">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                        fill="#687182"/>
                                                    <path
                                                        d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                        fill="#687182"/>
                                                    <path
                                                        d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                        fill="#687182"/>
                                                </svg>
                                            </DropdownMenuTrigger>

                                            <DropdownMenuPortal>
                                                <transition name="fade">
                                                    <DropdownMenuContent
                                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                        :side-offset="5" align="end">
                                                        <DropdownMenuItem>
                                                            <Link class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                Открыть
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z" fill="#464F60"/>
                                                                </svg>

                                                            </Link>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <button
                                                                type="button"
                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                                @click="editRow"
                                                            >
                                                                Редактировать
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                                    <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                                </svg>
                                                            </button>
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </transition>
                                            </DropdownMenuPortal>
                                        </DropdownMenuRoot>
                                    </div>
                                </div>
                                <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all bg-[#F3F1FE]">
                                    <div class="shrink-0 flex items-center justify-center w-[40px]"></div>
                                    <div class="shrink-0 flex items-center justify-center w-[32px]"></div>
                                    <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">
                                        <UiUserAvatar size="40px" class="shrink-0 mr-2" />
                                        <div>
                                            <span class="flex justify-start items-start font-medium">
                                                ООО Азалмеган
                                            </span>
                                            <span class="flex text-gray-500 items-start text-xs">
                                                ИНН 56842365
                                            </span>
                                        </div>
                                    </div>
                                    <div class="relative shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Шакиров Руслан</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Исходящий</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">27.02.2024</div>
                                    <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">ВЗД+ЯСС / Аренда
                                    </div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">
                                        <span :class="statuses_colors['good']" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                        <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses['good'] }}</span>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[80px] py-2.5 px-2">
                                        <Link
                                            v-if="true"
                                            :href="'/'"
                                            class="mr-3.5"
                                        >
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                        fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                        fill="#21272A"/>
                                            </svg>
                                        </Link>
                                        <PopoverRoot v-else>
                                            <PopoverTrigger>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                                    <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                                    <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                                </svg>
                                            </PopoverTrigger>
                                            <PopoverPortal>
                                                <PopoverContent side="bottom" align="end" class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                    <div>Комментарий:</div>
                                                    <p class="mt-2.5 text-xs">Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Страна если бросил, он всемогущая запятых грамматики себя ipsum точках, несколько меня строчка маленькая страну предупреждал которой раз проектах. Ему выйти составитель дал то ...</p>
                                                    <div class="mt-3 p-4 bg-bg1 text-xs">
                                                        <div class="flex items-center max-w-full">
                                                            <span class="grow block mr-auto text-ellipsis overflow-hidden">Some file name</span>
                                                            <svg class="shrink-0 block ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                    fill="#697077"/>
                                                            </svg>
                                                            <DropdownMenuRoot>
                                                                <DropdownMenuTrigger aria-label="Customise options">
                                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                                            fill="#687182"/>
                                                                        <path
                                                                            d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                                            fill="#687182"/>
                                                                        <path
                                                                            d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                                            fill="#687182"/>
                                                                    </svg>
                                                                </DropdownMenuTrigger>

                                                                <DropdownMenuPortal>
                                                                    <transition name="fade">
                                                                        <DropdownMenuContent
                                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                                            :side-offset="5" align="end">
                                                                            <DropdownMenuItem>
                                                                                <Link :href="'/'"
                                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                    Скачать
                                                                                    <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                        <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                    </svg>
                                                                                </Link>
                                                                            </DropdownMenuItem>
                                                                        </DropdownMenuContent>
                                                                    </transition>
                                                                </DropdownMenuPortal>
                                                            </DropdownMenuRoot>
                                                        </div>
                                                    </div>
                                                    <Link
                                                        class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Редактировать

                                                        <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                            <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                        </svg>
                                                    </Link>
                                                </PopoverContent>
                                            </PopoverPortal>
                                        </PopoverRoot>

                                        <DropdownMenuRoot>
                                            <DropdownMenuTrigger aria-label="Customise options">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                        fill="#687182"/>
                                                    <path
                                                        d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                        fill="#687182"/>
                                                    <path
                                                        d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                        fill="#687182"/>
                                                </svg>
                                            </DropdownMenuTrigger>

                                            <DropdownMenuPortal>
                                                <transition name="fade">
                                                    <DropdownMenuContent
                                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                        :side-offset="5" align="end">
                                                        <DropdownMenuItem>
                                                            <Link class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                Открыть
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z" fill="#464F60"/>
                                                                </svg>

                                                            </Link>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <button
                                                                type="button"
                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                                @click="editRow"
                                                            >
                                                                Редактировать
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                                    <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                                </svg>
                                                            </button>
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </transition>
                                            </DropdownMenuPortal>
                                        </DropdownMenuRoot>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                        <AccordionItem value="item-2">
                            <AccordionHeader class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 bg-white">
                                <div class="shrink-0 flex items-center justify-center w-[40px]">
                                    <AccordionTrigger class="shrink-0 group">
                                        <svg class="group-data-[state=open]:hidden" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12.9444 6.69444C12.9444 6.31091 12.6335 6 12.25 6C11.8665 6 11.5556 6.31091 11.5556 6.69444V11.5556H6.69444C6.31091 11.5556 6 11.8665 6 12.25C6 12.6335 6.31091 12.9444 6.69444 12.9444H11.5556V17.8056C11.5556 18.1891 11.8665 18.5 12.25 18.5C12.6335 18.5 12.9444 18.1891 12.9444 17.8056V12.9444H17.8056C18.1891 12.9444 18.5 12.6335 18.5 12.25C18.5 11.8665 18.1891 11.5556 17.8056 11.5556H12.9444V6.69444Z"
                                                fill="#242533" />
                                        </svg>
                                        <svg class="group-data-[state=closed]:hidden" width="20" height="20"
                                            viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M2.27198 9.29127C2.27198 8.9599 2.54061 8.69127 2.87198 8.69127H14.6285C14.9599 8.69127 15.2285 8.9599 15.2285 9.29127C15.2285 9.62265 14.9599 9.89127 14.6285 9.89127H2.87198C2.54061 9.89127 2.27198 9.62265 2.27198 9.29127Z"
                                                fill="#242533" />
                                        </svg>
                                    </AccordionTrigger>
                                </div>
                                <div class="shrink-0 flex items-center justify-center w-[32px]">
                                    <span class="flex items-center justify-center w-[18px] h-[18px] rounded-full text-xs bg-gray1 text-white">8</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">
                                    <UiUserAvatar size="40px" class="shrink-0 mr-2" />
                                    <div>
                                        <span class="flex justify-start items-start font-medium">
                                            ООО Азалмеган
                                        </span>
                                        <span class="flex text-gray-500 items-start text-xs">
                                            ИНН 56842365
                                        </span>
                                    </div>
                                </div>
                                <div class="relative shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Шакиров Руслан</div>
                                <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Исходящий</div>
                                <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">27.02.2024</div>
                                <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">ВЗД+ЯСС / Аренда
                                </div>
                                <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">
                                    <span :class="statuses_colors['good']" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                    <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses['good'] }}</span>
                                </div>
                                <div class="shrink-0 flex items-center w-[80px] py-2.5 px-2">
                                    <Link
                                        v-if="true"
                                        :href="'/'"
                                        class="mr-3.5"
                                    >
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                    fill="#21272A"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                    fill="#21272A"/>
                                        </svg>
                                    </Link>
                                    <PopoverRoot v-else>
                                        <PopoverTrigger class="mr-3.5">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                                <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                                <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                            </svg>
                                        </PopoverTrigger>
                                        <PopoverPortal>
                                            <PopoverContent side="bottom" align="end" class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                <div>Комментарий:</div>
                                                <p class="mt-2.5 text-xs">Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Страна если бросил, он всемогущая запятых грамматики себя ipsum точках, несколько меня строчка маленькая страну предупреждал которой раз проектах. Ему выйти составитель дал то ...</p>
                                                <div class="mt-3 p-4 bg-bg1 text-xs">
                                                    <div class="flex items-center max-w-full">
                                                        <span class="grow block mr-auto text-ellipsis overflow-hidden">Some file name</span>
                                                        <svg class="shrink-0 block ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                fill="#697077"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                fill="#697077"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                fill="#697077"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                fill="#697077"/>
                                                        </svg>
                                                        <DropdownMenuRoot>
                                                            <DropdownMenuTrigger aria-label="Customise options">
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                                        fill="#687182"/>
                                                                    <path
                                                                        d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                                        fill="#687182"/>
                                                                    <path
                                                                        d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                                        fill="#687182"/>
                                                                </svg>
                                                            </DropdownMenuTrigger>

                                                            <DropdownMenuPortal>
                                                                <transition name="fade">
                                                                    <DropdownMenuContent
                                                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                                        :side-offset="5" align="end">
                                                                        <DropdownMenuItem>
                                                                            <Link :href="'/'"
                                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                Скачать
                                                                                <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                    <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                </svg>
                                                                            </Link>
                                                                        </DropdownMenuItem>
                                                                    </DropdownMenuContent>
                                                                </transition>
                                                            </DropdownMenuPortal>
                                                        </DropdownMenuRoot>
                                                    </div>
                                                </div>
                                                <Link
                                                    class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                >
                                                    Редактировать

                                                    <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                        <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                    </svg>
                                                </Link>
                                            </PopoverContent>
                                        </PopoverPortal>
                                    </PopoverRoot>

                                    <DropdownMenuRoot>
                                        <DropdownMenuTrigger aria-label="Customise options">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                    fill="#687182"/>
                                                <path
                                                    d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                    fill="#687182"/>
                                                <path
                                                    d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                    fill="#687182"/>
                                            </svg>
                                        </DropdownMenuTrigger>

                                        <DropdownMenuPortal>
                                            <transition name="fade">
                                                <DropdownMenuContent
                                                    class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                    :side-offset="5" align="end">
                                                    <DropdownMenuItem>
                                                        <Link class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                            Открыть
                                                            <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z" fill="#464F60"/>
                                                            </svg>

                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem>
                                                        <button
                                                            type="button"
                                                            class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                            @click="editRow"
                                                        >
                                                            Редактировать
                                                            <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                                <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                            </svg>
                                                        </button>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </transition>
                                        </DropdownMenuPortal>
                                    </DropdownMenuRoot>
                                </div>
                            </AccordionHeader>
                            <AccordionContent class="data-[state=open]:animate-[accordionSlideDown_300ms_ease-in] data-[state=closed]:animate-[accordionSlideUp_300ms_ease-in] overflow-hidden">
                                <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all bg-[#F3F1FE]">
                                    <div class="shrink-0 flex items-center justify-center w-[40px]"></div>
                                    <div class="shrink-0 flex items-center justify-center w-[32px]"></div>
                                    <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">
                                        <UiUserAvatar size="40px" class="shrink-0 mr-2" />
                                        <div>
                                            <span class="flex justify-start items-start font-medium">
                                                ООО Азалмеган
                                            </span>
                                            <span class="flex text-gray-500 items-start text-xs">
                                                ИНН 56842365
                                            </span>
                                        </div>
                                    </div>
                                    <div class="relative shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Шакиров Руслан</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Исходящий</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">27.02.2024</div>
                                    <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">ВЗД+ЯСС / Аренда
                                    </div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">
                                        <span :class="statuses_colors['good']" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                        <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses['good'] }}</span>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[80px] py-2.5 px-2">
                                        <Link
                                            v-if="true"
                                            class="mr-3.5"
                                        >
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                        fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                        fill="#21272A"/>
                                            </svg>
                                        </Link>
                                        <PopoverRoot v-else>
                                            <PopoverTrigger class="mr-3.5">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                                    <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                                    <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                                </svg>
                                            </PopoverTrigger>
                                            <PopoverPortal>
                                                <PopoverContent side="bottom" align="end" class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                    <div>Комментарий:</div>
                                                    <p class="mt-2.5 text-xs">Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Страна если бросил, он всемогущая запятых грамматики себя ipsum точках, несколько меня строчка маленькая страну предупреждал которой раз проектах. Ему выйти составитель дал то ...</p>
                                                    <div class="mt-3 p-4 bg-bg1 text-xs">
                                                        <div class="flex items-center max-w-full">
                                                            <span class="grow block mr-auto text-ellipsis overflow-hidden">Some file name</span>
                                                            <svg class="shrink-0 block ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                    fill="#697077"/>
                                                            </svg>
                                                            <DropdownMenuRoot>
                                                                <DropdownMenuTrigger aria-label="Customise options">
                                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                                            fill="#687182"/>
                                                                        <path
                                                                            d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                                            fill="#687182"/>
                                                                        <path
                                                                            d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                                            fill="#687182"/>
                                                                    </svg>
                                                                </DropdownMenuTrigger>

                                                                <DropdownMenuPortal>
                                                                    <transition name="fade">
                                                                        <DropdownMenuContent
                                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                                            :side-offset="5" align="end">
                                                                            <DropdownMenuItem>
                                                                                <Link :href="'/'"
                                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                    Скачать
                                                                                    <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                        <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                    </svg>
                                                                                </Link>
                                                                            </DropdownMenuItem>
                                                                        </DropdownMenuContent>
                                                                    </transition>
                                                                </DropdownMenuPortal>
                                                            </DropdownMenuRoot>
                                                        </div>
                                                    </div>
                                                    <Link
                                                        class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Редактировать

                                                        <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                            <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                        </svg>
                                                    </Link>
                                                </PopoverContent>
                                            </PopoverPortal>
                                        </PopoverRoot>

                                        <DropdownMenuRoot>
                                            <DropdownMenuTrigger aria-label="Customise options">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                        fill="#687182"/>
                                                    <path
                                                        d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                        fill="#687182"/>
                                                    <path
                                                        d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                        fill="#687182"/>
                                                </svg>
                                            </DropdownMenuTrigger>

                                            <DropdownMenuPortal>
                                                <transition name="fade">
                                                    <DropdownMenuContent
                                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                        :side-offset="5" align="end">
                                                        <DropdownMenuItem>
                                                            <Link class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                Открыть
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z" fill="#464F60"/>
                                                                </svg>

                                                            </Link>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <button
                                                                type="button"
                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                                @click="editRow"
                                                            >
                                                                Редактировать
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                                    <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                                </svg>
                                                            </button>
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </transition>
                                            </DropdownMenuPortal>
                                        </DropdownMenuRoot>
                                    </div>
                                </div>
                                <div class="flex border-b border-b-gray3 [&>*:not(:first-child)]:border-l [&>*:not(:first-child)]:border-l-gray3 break-all bg-[#F3F1FE]">
                                    <div class="shrink-0 flex items-center justify-center w-[40px]"></div>
                                    <div class="shrink-0 flex items-center justify-center w-[32px]"></div>
                                    <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">
                                        <UiUserAvatar size="40px" class="shrink-0 mr-2" />
                                        <div>
                                            <span class="flex justify-start items-start font-medium">
                                                ООО Азалмеган
                                            </span>
                                            <span class="flex text-gray-500 items-start text-xs">
                                                ИНН 56842365
                                            </span>
                                        </div>
                                    </div>
                                    <div class="relative shrink-0 flex items-center w-[14.08%] py-2.5 px-2 cursor-pointer">Шакиров Руслан</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">Исходящий</div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">27.02.2024</div>
                                    <div class="shrink-0 flex items-center w-[15.14%] py-2.5 px-2">ВЗД+ЯСС / Аренда
                                    </div>
                                    <div class="shrink-0 flex items-center w-[14.08%] py-2.5 px-2">
                                        <span :class="statuses_colors['good']" class="shrink-0 block w-1.5 h-1.5 mr-2 rounded-full"></span>
                                        <span class="text-nowrap text-ellipsis overflow-hidden">{{ statuses['good'] }}</span>
                                    </div>
                                    <div class="shrink-0 flex items-center w-[80px] py-2.5 px-2">
                                        <Link
                                            v-if="true"
                                            :href="'/'"
                                            class="mr-3.5"
                                        >
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.125 5.25C3.50368 5.25 3 5.75368 3 6.375V17.625C3 18.2463 3.50368 18.75 4.125 18.75H19.875C20.4963 18.75 21 18.2463 21 17.625V6.375C21 5.75368 20.4963 5.25 19.875 5.25H4.125ZM1.5 6.375C1.5 4.92525 2.67525 3.75 4.125 3.75H19.875C21.3247 3.75 22.5 4.92525 22.5 6.375V17.625C22.5 19.0747 21.3247 20.25 19.875 20.25H4.125C2.67525 20.25 1.5 19.0747 1.5 17.625V6.375Z"
                                                        fill="#21272A"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.65802 7.03952C4.91232 6.71256 5.38353 6.65366 5.71049 6.90796L12 11.7998L18.2896 6.90796C18.6165 6.65366 19.0877 6.71256 19.342 7.03952C19.5964 7.36648 19.5375 7.83769 19.2105 8.09199L12.4605 13.342C12.1897 13.5526 11.8104 13.5526 11.5396 13.342L4.78958 8.09199C4.46262 7.83769 4.40372 7.36648 4.65802 7.03952Z"
                                                        fill="#21272A"/>
                                            </svg>
                                        </Link>
                                        <PopoverRoot v-else>
                                            <PopoverTrigger>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.26884 4.51884C2.76113 4.02656 3.42881 3.75 4.125 3.75H15C15.4142 3.75 15.75 4.08579 15.75 4.5C15.75 4.91421 15.4142 5.25 15 5.25H4.125C3.82663 5.25 3.54048 5.36853 3.3295 5.5795C3.11853 5.79048 3 6.07663 3 6.375V17.625C3 17.9234 3.11853 18.2095 3.3295 18.4205C3.54048 18.6315 3.82663 18.75 4.125 18.75H19.8155C20.1138 18.75 20.4 18.6315 20.611 18.4205C20.8219 18.2095 20.9405 17.9234 20.9405 17.625V11.2031C20.9405 10.7889 21.2763 10.4531 21.6905 10.4531C22.1047 10.4531 22.4405 10.7889 22.4405 11.2031V17.625C22.4405 18.3212 22.1639 18.9889 21.6716 19.4812C21.1793 19.9734 20.5117 20.25 19.8155 20.25H4.125C3.42881 20.25 2.76113 19.9734 2.26884 19.4812C1.77656 18.9889 1.5 18.3212 1.5 17.625V6.375C1.5 5.67881 1.77656 5.01113 2.26884 4.51884Z" fill="#21272A"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.65802 7.03958C4.91232 6.71262 5.38353 6.65372 5.71049 6.90802L12.0069 11.8052L15.6263 9.07314C15.9569 8.8236 16.4272 8.8893 16.6768 9.21991C16.9263 9.55051 16.8606 10.0208 16.53 10.2704L12.4519 13.3486C12.1813 13.5529 11.8072 13.5502 11.5396 13.342L4.78958 8.09205C4.46262 7.83775 4.40372 7.36654 4.65802 7.03958Z" fill="#21272A"/>
                                                    <path d="M20.2477 8.24995C21.489 8.24995 22.4953 7.24364 22.4953 6.0023C22.4953 4.76095 21.489 3.75464 20.2477 3.75464C19.0063 3.75464 18 4.76095 18 6.0023C18 7.24364 19.0063 8.24995 20.2477 8.24995Z" fill="#21272A"/>
                                                    <path d="M20.25 8.99995C19.6571 8.99995 19.0775 8.82414 18.5846 8.49476C18.0916 8.16537 17.7074 7.6972 17.4805 7.14945C17.2536 6.6017 17.1943 5.99897 17.3099 5.41748C17.4256 4.83599 17.7111 4.30186 18.1303 3.88263C18.5495 3.4634 19.0837 3.1779 19.6652 3.06224C20.2467 2.94657 20.8494 3.00594 21.3971 3.23282C21.9449 3.45971 22.4131 3.84393 22.7424 4.33689C23.0718 4.82985 23.2476 5.40942 23.2476 6.0023C23.247 6.79713 22.931 7.55924 22.369 8.12127C21.8069 8.68331 21.0448 8.99933 20.25 8.99995ZM20.25 4.50464C19.9532 4.50418 19.663 4.59176 19.416 4.7563C19.169 4.92084 18.9764 5.15494 18.8625 5.42899C18.7486 5.70304 18.7186 6.00471 18.7762 6.29584C18.8338 6.58696 18.9765 6.85446 19.1861 7.06447C19.3958 7.27448 19.6631 7.41758 19.9541 7.47564C20.2452 7.53371 20.5469 7.50414 20.8211 7.39068C21.0953 7.27722 21.3297 7.08496 21.4947 6.83824C21.6596 6.59151 21.7476 6.30141 21.7476 6.00464C21.7476 5.60722 21.5899 5.22604 21.3091 4.94481C21.0283 4.66357 20.6474 4.50526 20.25 4.50464Z" fill="#21272A"/>
                                                </svg>
                                            </PopoverTrigger>
                                            <PopoverPortal>
                                                <PopoverContent side="bottom" align="end" class="w-[300px] p-4 rounded-lg text-sm bg-white shadow-lg">
                                                    <div>Комментарий:</div>
                                                    <p class="mt-2.5 text-xs">Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Страна если бросил, он всемогущая запятых грамматики себя ipsum точках, несколько меня строчка маленькая страну предупреждал которой раз проектах. Ему выйти составитель дал то ...</p>
                                                    <div class="mt-3 p-4 bg-bg1 text-xs">
                                                        <div class="flex items-center max-w-full">
                                                            <span class="grow block mr-auto text-ellipsis overflow-hidden">Some file name</span>
                                                            <svg class="shrink-0 block ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                                                    fill="#697077"/>
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                                                    fill="#697077"/>
                                                            </svg>
                                                            <DropdownMenuRoot>
                                                                <DropdownMenuTrigger aria-label="Customise options">
                                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                                            fill="#687182"/>
                                                                        <path
                                                                            d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                                            fill="#687182"/>
                                                                        <path
                                                                            d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                                            fill="#687182"/>
                                                                    </svg>
                                                                </DropdownMenuTrigger>

                                                                <DropdownMenuPortal>
                                                                    <transition name="fade">
                                                                        <DropdownMenuContent
                                                                            class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                                            :side-offset="5" align="end">
                                                                            <DropdownMenuItem>
                                                                                <Link :href="'/'"
                                                                                    class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                                    Скачать
                                                                                    <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                                                        <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                                                    </svg>
                                                                                </Link>
                                                                            </DropdownMenuItem>
                                                                        </DropdownMenuContent>
                                                                    </transition>
                                                                </DropdownMenuPortal>
                                                            </DropdownMenuRoot>
                                                        </div>
                                                    </div>
                                                    <Link
                                                        class="inline-flex items-center mt-2 py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                    >
                                                        Редактировать

                                                        <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                            <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                        </svg>
                                                    </Link>
                                                </PopoverContent>
                                            </PopoverPortal>
                                        </PopoverRoot>

                                        <DropdownMenuRoot>
                                            <DropdownMenuTrigger aria-label="Customise options">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                                        fill="#687182"/>
                                                    <path
                                                        d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                                        fill="#687182"/>
                                                    <path
                                                        d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                                        fill="#687182"/>
                                                </svg>
                                            </DropdownMenuTrigger>

                                            <DropdownMenuPortal>
                                                <transition name="fade">
                                                    <DropdownMenuContent
                                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                                        :side-offset="5" align="end">
                                                        <DropdownMenuItem>
                                                            <Link class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                                Открыть
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14 8C14 11.3138 11.3138 14 8 14C4.68616 14 2 11.3138 2 8C2 4.68625 4.68616 2 8 2C11.3138 2 14 4.68625 14 8ZM6.70239 6.65747H8.82398V11.1769C8.82398 11.6144 8.46933 11.969 8.03186 11.969C7.59439 11.969 7.23975 11.6144 7.23975 11.1769V8.06226H6.70239C6.31447 8.06226 6 7.74778 6 7.35986C6 6.97194 6.31447 6.65747 6.70239 6.65747ZM7.00977 4.93247C7.00977 4.80778 7.03192 4.69149 7.07587 4.58359C7.12345 4.47236 7.18773 4.37629 7.26909 4.29539C7.35044 4.21449 7.44524 4.15048 7.55383 4.10326C7.60323 4.0824 7.65408 4.06615 7.70674 4.0545C7.77357 4.03987 7.84294 4.03247 7.91485 4.03247C8.03688 4.03247 8.15202 4.05604 8.26061 4.10326C8.29003 4.11599 8.31836 4.12998 8.3456 4.14515C8.41933 4.18623 8.4858 4.23634 8.54536 4.29539C8.62671 4.37629 8.691 4.47236 8.73858 4.58359C8.76328 4.63984 8.7818 4.69835 8.79342 4.75912C8.80432 4.815 8.80977 4.87279 8.80977 4.93247C8.80977 4.99965 8.80287 5.06492 8.78906 5.12822C8.77744 5.18239 8.76037 5.23512 8.73858 5.2864C8.691 5.3943 8.62671 5.48865 8.54536 5.56955C8.50359 5.61099 8.45856 5.64801 8.40989 5.6806C8.3634 5.71157 8.31364 5.73866 8.26061 5.76168C8.15202 5.80891 8.03688 5.83247 7.91485 5.83247C7.85129 5.83247 7.78991 5.82678 7.73071 5.81532C7.66933 5.80358 7.61049 5.7857 7.55383 5.76168C7.44524 5.71446 7.35044 5.65045 7.26909 5.56955C7.18773 5.48865 7.12345 5.3943 7.07587 5.2864C7.03192 5.17517 7.00977 5.05716 7.00977 4.93247Z" fill="#464F60"/>
                                                                </svg>

                                                            </Link>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem>
                                                            <button
                                                                type="button"
                                                                class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                                                @click="editRow"
                                                            >
                                                                Редактировать
                                                                <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M13.0981 5.10827C12.6795 5.52693 12.4702 5.73626 12.2506 5.77677C12.0309 5.81728 11.8868 5.67315 11.5986 5.3849L10.6151 4.40144C10.3269 4.11319 10.1827 3.96906 10.2232 3.74946C10.2638 3.52985 10.4731 3.32052 10.8917 2.90186L11.1184 2.67522C11.537 2.25656 11.7464 2.04723 11.966 2.00672C12.1856 1.96621 12.3297 2.11034 12.618 2.39859L13.6014 3.38204C13.8897 3.6703 14.0338 3.81442 13.9933 4.03403C13.9528 4.25364 13.7434 4.46297 13.3248 4.88162L13.0981 5.10827Z" fill="#464F60"/>
                                                                    <path d="M2.95406 13.9107C2.4542 14.0029 2.20427 14.049 2.07763 13.9224C1.95099 13.7957 1.99709 13.5458 2.0893 13.0459L2.31175 11.84C2.35173 11.6233 2.37172 11.515 2.43005 11.4101C2.48838 11.3052 2.57913 11.2145 2.76064 11.033L8.31438 5.47921C8.73303 5.06056 8.94236 4.85123 9.16197 4.81072C9.38158 4.77021 9.5257 4.91433 9.81396 5.20259L10.7974 6.18604C11.0857 6.4743 11.2298 6.61842 11.1893 6.83803C11.1488 7.05764 10.9394 7.26697 10.5208 7.68562L4.96705 13.2394C4.78554 13.4209 4.69479 13.5116 4.58991 13.5699C4.48503 13.6283 4.37668 13.6483 4.15997 13.6882L2.95406 13.9107Z" fill="#464F60"/>
                                                                </svg>
                                                            </button>
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </transition>
                                            </DropdownMenuPortal>
                                        </DropdownMenuRoot>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    </AccordionRoot>
                </div>
            </div>
        </div>

        <DialogRoot v-model:open="edit_dialog_state">
            <DialogPortal>
                <transition name="fade">
                    <DialogOverlay class="fixed left-0 top-0 w-full h-full bg-black bg-opacity-60 z-[60]" />
                </transition>
                <transition name="fade">
                    <DialogContent
                        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-h-[85vh] w-[90vw] max-w-[400px] p-6 rounded bg-white overflow-y-auto z-[100]">
                        <DialogTitle class="flex items-center justify-between font-semibold">
                            Редактирование

                            <DialogClose class="shrink-0 ml-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M16.95 8.464a1 1 0 0 0-1.414-1.414L12 10.586L8.464 7.05A1 1 0 1 0 7.05 8.464L10.586 12L7.05 15.536a1 1 0 1 0 1.414 1.414L12 13.414l3.536 3.536a1 1 0 1 0 1.414-1.414L13.414 12z" />
                                </svg>
                            </DialogClose>
                        </DialogTitle>

                        <form class="mt-8 space-y-6">
                            <UiField label="Примечание" textarea />
                            <UiFieldSelect
                                v-model="edit_status"
                                :items="Object.entries(statuses).map(s=>({ title: s[1], value: s[0] }))"
                                label="Статус"
                                only-value
                            />
                            <div class="flex justify-end">
                                <button class="inline-flex items-center justify-center py-3 px-7 font-medium tracking-wider bg-my-gray text-side-gray-text">
                                    Сохранить
                                </button>
                            </div>
                        </form>
                    </DialogContent>
                </transition>
            </DialogPortal>
        </DialogRoot>
    </AuthenticatedLayout>
</template>