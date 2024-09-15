<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Equipment from '@/Pages/Equip/Index.vue'
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

    

</script>

<template>

    <AuthenticatedLayout>
        <template #header>
            <nav class="bg-white-50 bg-my-gray">
                <div class="max-w-screen-xl flex items-center px-4 py-3 mx-auto">
                    <div class="flex items-center">
                        <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                            <li>
                                <a href="#" class="text-my-nav-text text-lg hover:underline" aria-current="page">В
                                    аренде</a>
                            </li>   
                            <li>
                                <a href="#" class="text-my-nav-text text-lg hover:underline">Свободно</a>
                            </li>
                            <li>
                                <a href="#" class="text-my-nav-text text-lg hover:underline">На сервисе</a>
                            </li>
                            <li>
                                <a href="#" class="text-my-nav-text text-lg hover:underline">Анализ</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="mt-5">
                <component :is="currentComponent" />
            </div>


        </template>
    </AuthenticatedLayout>
</template>
