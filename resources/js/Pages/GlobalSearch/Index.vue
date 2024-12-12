<template>
    <AuthenticatedLayout>
        <div class="container">
            <h1>Результаты поиска</h1>

            <div v-if="search_results.length > 0" class="results flex flex-col">
                <p>Контрагенты</p>
                <li v-for="result in contragents" :key="result.id">
                    {{ result.name }} ({{ result.description }})
                </li>
                <p>Оборудование</p>
                <li v-for="result in equipment" :key="result.id">
                    {{ result.name }} ({{ result.description }})
                </li>
                <p>Продажи</p>
                <li v-for="result in sales" :key="result.id">
                    {{ result.name }} ({{ result.description }})
                </li>
                <p>Услуги</p>
                <li v-for="result in services" :key="result.id">
                    {{ result.name }} ({{ result.description }})
                </li>

            </div>
            <div v-else>
                Результатов нет
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()
const search_results = computed(() => page.props.flash.search_results)

const types = ['contragent', 'equipment', 'ale', 'ervice']
const selectedType = ref('contragent')

const contragents = computed(() => search_results.value.filter(result => result.type === 'contragent'))
const equipment = computed(() => search_results.value.filter(result => result.type === 'equipment'))
const sales = computed(() => search_results.value.filter(result => result.type === 'sale'))
const services = computed(() => search_results.value.filter(result => result.type === 'service'))
</script>