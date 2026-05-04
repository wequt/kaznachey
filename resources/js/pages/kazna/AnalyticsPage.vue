<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Doughnut } from 'vue-chartjs';
import { 
    Chart as ChartJS, 
    ArcElement, 
    Tooltip, 
    Legend, 
    ChartData, 
    ChartOptions 
} from 'chart.js';

// Регистрация компонентов Chart.js
ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps<{
    expensesStats: Array<{ name: string, total: number }>;
    incomeStats: Array<{ name: string, total: number }>;
    totals: { income: number, expense: number, profit: number };
    filters: { date_from: string, date_to: string };
}>();

// Состояние фильтров
const selectedType = ref('expense'); 
const dateFrom = ref(props.filters.date_from);
const dateTo = ref(props.filters.date_to);

// ИСПРАВЛЕНО: используем .value вместо .ref (как было на image_39e084.png)
const currentStats = computed(() => 
    selectedType.value === 'expense' ? props.expensesStats : props.incomeStats
);

// Применение фильтра дат через Inertia router
const applyFilters = () => {
    router.get('/analytics', { 
        date_from: dateFrom.value, 
        date_to: dateTo.value 
    }, { preserveState: true });
};

// Данные для диаграммы
const chartData = computed<ChartData<'doughnut'>>(() => ({
    labels: currentStats.value.map(item => item.name),
    datasets: [{
        backgroundColor: ['#A66353', '#C28476', '#8A4B3D', '#D9AFA6', '#703A2F'],
        data: currentStats.value.map(item => item.total),
        borderWidth: 2,
        borderColor: '#ffffff'
    }]
}));

const chartOptions: ChartOptions<'doughnut'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' }
    }
};

const formatMoney = (amount: number) => {
    return new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';
};
</script>

<template>
    <Head title="Аналитика" />

    <div class="min-h-screen bg-gray-50 py-12 px-4">
        <div class="max-w-5xl mx-auto">
            <h2 class="font-semibold text-2xl text-gray-800 mb-8">Аналитика финансов</h2>

            <!-- Верхняя панель фильтров -->
            <div class="bg-white p-6 rounded-xl shadow-sm mb-8 flex flex-wrap items-end gap-4 border border-gray-100">
                <div class="flex-1 min-w-50">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Период</label>
                    <div class="flex gap-2">
                        <input type="date" v-model="dateFrom" @change="applyFilters" 
                               class="w-full rounded-lg border-gray-300 focus:ring-[#A66353] focus:border-[#A66353]" />
                        <input type="date" v-model="dateTo" @change="applyFilters" 
                               class="w-full rounded-lg border-gray-300 focus:ring-[#A66353] focus:border-[#A66353]" />
                    </div>
                </div>
                <div class="min-w-37.5">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Показать</label>
                    <select v-model="selectedType" 
                            class="w-full rounded-lg border-gray-300 focus:ring-[#A66353] focus:border-[#A66353]">
                        <option value="expense">Расходы</option>
                        <option value="income">Доходы</option>
                    </select>
                </div>
            </div>

            <!-- Карточки итогов за период -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-green-500">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Доходы за период</p>
                    <p class="text-2xl font-black text-gray-800">{{ formatMoney(totals.income) }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-red-500">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Расходы за период</p>
                    <p class="text-2xl font-black text-gray-800">{{ formatMoney(totals.expense) }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-[#A66353]">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Чистый результат</p>
                    <p class="text-2xl font-black text-gray-800">{{ formatMoney(totals.profit) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Визуализация -->
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-medium mb-8" style="color: #A66353;">
                        {{ selectedType === 'expense' ? 'Структура расходов' : 'Структура доходов' }}
                    </h3>
                    <div v-if="currentStats.length > 0" class="h-64">
                        <Doughnut :data="chartData" :options="chartOptions" />
                    </div>
                    <div v-else class="h-64 flex items-center justify-center text-gray-400">
                        Нет данных за выбранный период
                    </div>
                </div>

                <!-- Список категорий -->
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-medium mb-6" style="color: #A66353;">Детализация</h3>
                    <div class="space-y-5">
                        <div v-for="item in currentStats" :key="item.name">
                            <div class="flex justify-between mb-1 text-sm">
                                <span class="text-gray-600">{{ item.name }}</span>
                                <span class="font-bold text-gray-800">{{ formatMoney(item.total) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500" 
                                     style="background-color: #A66353;"
                                     :style="{ width: (item.total / (selectedType === 'expense' ? totals.expense : totals.income) * 100) + '%' }">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>