<script setup>
import store from "../../../store/index.js";
import { Link, router, usePage } from '@inertiajs/vue3';
import {
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuPortal,
  DropdownMenuRoot,
  DropdownMenuTrigger,
} from 'radix-vue'
import { computed, ref } from "vue";
import UiFieldDate from "@/Components/Ui/UiFieldDate.vue";
</script>

<template>
    <div class="relative flex items-center flex-col lg:flex-row">
        <span class="absolute left-0 bottom-0 w-full h-[1px] bg-[#e5e7eb]"></span>

        <ul class="relative flex items-center w-full font-medium space-x-8 overflow-x-auto text-nowrap lg:overflow-x-visible">
            <li class="flex items-center border-b-2 border-transparent py-3 cursor-pointer !border-[#001D6C] text-[#001D6C]">
                Новая аренда:
            </li>
            <li>
                <DropdownMenuRoot>
                    <DropdownMenuTrigger
                        aria-label="Customise options"
                    >
                        <svg class="block" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5 13C5.26522 13 5.51957 12.8946 5.70711 12.7071C5.89464 12.5196 6 12.2652 6 12C6 11.7348 5.89464 11.4804 5.70711 11.2929C5.51957 11.1054 5.26522 11 5 11C4.73478 11 4.48043 11.1054 4.29289 11.2929C4.10536 11.4804 4 11.7348 4 12C4 12.2652 4.10536 12.5196 4.29289 12.7071C4.48043 12.8946 4.73478 13 5 13ZM5 15C4.20435 15 3.44129 14.6839 2.87868 14.1213C2.31607 13.5587 2 12.7956 2 12C2 11.2044 2.31607 10.4413 2.87868 9.87868C3.44129 9.31607 4.20435 9 5 9C5.79565 9 6.55871 9.31607 7.12132 9.87868C7.68393 10.4413 8 11.2044 8 12C8 12.7956 7.68393 13.5587 7.12132 14.1213C6.55871 14.6839 5.79565 15 5 15ZM19 15C18.2044 15 17.4413 14.6839 16.8787 14.1213C16.3161 13.5587 16 12.7956 16 12C16 11.2044 16.3161 10.4413 16.8787 9.87868C17.4413 9.31607 18.2044 9 19 9C19.7956 9 20.5587 9.31607 21.1213 9.87868C21.6839 10.4413 22 11.2044 22 12C22 12.7956 21.6839 13.5587 21.1213 14.1213C20.5587 14.6839 19.7956 15 19 15ZM19 13C19.2652 13 19.5196 12.8946 19.7071 12.7071C19.8946 12.5196 20 12.2652 20 12C20 11.7348 19.8946 11.4804 19.7071 11.2929C19.5196 11.1054 19.2652 11 19 11C18.7348 11 18.4804 11.1054 18.2929 11.2929C18.1054 11.4804 18 11.7348 18 12C18 12.2652 18.1054 12.5196 18.2929 12.7071C18.4804 12.8946 18.7348 13 19 13ZM12 15C11.2044 15 10.4413 14.6839 9.87868 14.1213C9.31607 13.5587 9 12.7956 9 12C9 11.2044 9.31607 10.4413 9.87868 9.87868C10.4413 9.31607 11.2044 9 12 9C12.7956 9 13.5587 9.31607 14.1213 9.87868C14.6839 10.4413 15 11.2044 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15ZM12 13C12.2652 13 12.5196 12.8946 12.7071 12.7071C12.8946 12.5196 13 12.2652 13 12C13 11.7348 12.8946 11.4804 12.7071 11.2929C12.5196 11.1054 12.2652 11 12 11C11.7348 11 11.4804 11.1054 11.2929 11.2929C11.1054 11.4804 11 11.7348 11 12C11 12.2652 11.1054 12.5196 11.2929 12.7071C11.4804 12.8946 11.7348 13 12 13Z"
                                fill="#697077" />
                        </svg>
                    </DropdownMenuTrigger>

                    <DropdownMenuPortal>
                        <transition name="fade">
                            <DropdownMenuContent
                                class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                :side-offset="5"
                                align="end"
                            >
                                <DropdownMenuItem>
                                    <Link
                                        :href="route('services.create')"
                                        class="flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all"
                                    >
                                        Открыть аренду
                                        <svg class="block ml-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.75 2.75003C8.75 2.33582 8.41421 2.00003 8 2.00003C7.58578 2.00003 7.25 2.33582 7.25 2.75003V7.25H2.75001C2.33579 7.25 2.00001 7.58578 2.00001 8C2.00001 8.41421 2.33579 8.75 2.75001 8.75H7.25L7.25001 13.25C7.25001 13.6642 7.58579 14 8.00001 14C8.41422 14 8.75001 13.6642 8.75001 13.25L8.75 8.75H13.25C13.6642 8.75 14 8.41421 14 8C14 7.58578 13.6642 7.25 13.25 7.25H8.75V2.75003Z" fill="#464F60"/>
                                        </svg>
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </transition>
                    </DropdownMenuPortal>
                </DropdownMenuRoot>
            </li>
        </ul>
    </div>
</template>
