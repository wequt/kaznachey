<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    stats: {
        total_balance: number;
        monthly_income: number;
        monthly_expense: number;
    }
}>();

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(amount);
};
</script>

<template>
    <Head title="Обзор" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Финансовый обзор</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6 border-l-4 border-[#A66353]">
                    <div class="text-sm font-medium text-gray-500 uppercase">Общий баланс</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">
                        {{ formatCurrency(props.stats.total_balance) }}
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Доходы (май)</div>
                    <div class="mt-2 text-3xl font-bold text-green-600">
                        + {{ formatCurrency(props.stats.monthly_income) }}
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Расходы (май)</div>
                    <div class="mt-2 text-3xl font-bold text-red-600">
                        - {{ formatCurrency(props.stats.monthly_expense) }}
                    </div>
                </div>

            </div>

            <div class="mt-8 p-12 bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg text-center text-gray-400">
                Здесь будет график аналитики (интерактивная диаграмма)
            </div>
        </div>
    </div>
</template>