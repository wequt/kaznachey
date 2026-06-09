<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Doughnut } from 'vue-chartjs';
import { 
    TrendingUp, 
    Calendar, 
    ArrowUpRight, 
    ArrowDownLeft, 
    BarChart3 
} from 'lucide-vue-next';
import { 
    Chart as ChartJS, 
    ArcElement, 
    Tooltip, 
    Legend, 
    ChartData, 
    ChartOptions 
} from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps<{
    expensesStats: Array<{ name: string, total: number }>;
    incomeStats: Array<{ name: string, total: number }>;
    totals: { income: number, expense: number, profit: number };
    filters: { date_from: string, date_to: string };
}>();

const selectedType = ref('expense'); 
const dateFrom = ref(props.filters.date_from);
const dateTo = ref(props.filters.date_to);

const currentStats = computed(() => 
    selectedType.value === 'expense' ? props.expensesStats : props.incomeStats
);

const applyFilters = () => {
    router.get('/analytics', { 
        date_from: dateFrom.value, 
        date_to: dateTo.value 
    }, { preserveState: true });
};

const chartColors = ['#A66353', '#C28476', '#8A4B3D', '#D9AFA6', '#703A2F'];

const chartData = computed<ChartData<'doughnut'>>(() => ({
    labels: currentStats.value.map(item => item.name),
    datasets: [{
        backgroundColor: chartColors,
        data: currentStats.value.map(item => item.total),
        borderWidth: 0,
        cutout: '75%'
    }]
}));

const chartOptions: ChartOptions<'doughnut'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    }
};

const formatMoney = (amount: number) => {
    return new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';
};
</script>

<template>
    <Head title="Аналитика" />

    <div class="min-h-screen bg-[#FDFCFB] py-8 px-6 text-slate-900">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-800">Аналитика финансов</h1>
                    <p class="text-slate-500 italic">Подробный разбор ваших доходов и расходов</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center gap-4 bg-slate-50/30">
                
                <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full">
                    
                    <div class="flex bg-slate-100 p-1 rounded-xl flex-1 h-10.5">
                        <button 
                            @click="selectedType = 'income'"
                            type="button"
                            :class="selectedType === 'income' ? 'bg-green-600 text-white shadow-xs' : 'text-slate-600 hover:bg-white/60'"
                            class="flex-1 text-xs font-bold rounded-lg transition-all cursor-pointer"
                        >
                            Доходы
                        </button>
                        <button 
                            @click="selectedType = 'expense'"
                            type="button"
                            :class="selectedType === 'expense' ? 'bg-red-500 text-white shadow-xs' : 'text-slate-600 hover:bg-white/60'"
                            class="flex-1 text-xs font-bold rounded-lg transition-all cursor-pointer"
                        >
                            Расходы
                        </button>
                    </div>

                    <div class="flex-2 flex items-center gap-2 h-10.5">
                        <div class="relative flex-1 h-full">
                            <input 
                                type="date" 
                                v-model="dateFrom" 
                                @change="applyFilters" 
                                class="w-full text-sm rounded-xl border border-slate-200 bg-white p-2 h-full font-medium text-slate-700 focus:border-[#A66353] focus:ring-[#A66353] outline-hidden" 
                            />
                        </div>
                        <span class="text-slate-400 text-sm select-none">—</span>
                        <div class="relative flex-1 h-full">
                            <input 
                                type="date" 
                                v-model="dateTo" 
                                @change="applyFilters" 
                                class="w-full text-sm rounded-xl border border-slate-200 bg-white p-2 h-full font-medium text-slate-700 focus:border-[#A66353] focus:ring-[#A66353] outline-hidden" 
                            />
                        </div>
                    </div>

                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-widest mb-1">Доходы за период</p>
                        <p class="text-2xl font-black text-green-600">{{ formatMoney(totals.income) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-500">
                        <ArrowUpRight class="w-6 h-6" />
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-widest mb-1">Расходы за период</p>
                        <p class="text-2xl font-black text-slate-800">{{ formatMoney(totals.expense) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-500">
                        <ArrowDownLeft class="w-6 h-6" />
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-widest mb-1">Чистый результат</p>
                        <p class="text-2xl font-black text-[#A66353]">{{ formatMoney(totals.profit) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-[#fdf5f3] rounded-2xl flex items-center justify-center text-[#A66353]">
                        <TrendingUp class="w-6 h-6" />
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-5 bg-white p-8 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-slate-800">
                            {{ selectedType === 'expense' ? 'Структура расходов' : 'Структура доходов' }}
                        </h3>
                        <BarChart3 class="w-5 h-5 text-[#A66353]" />
                    </div>
                    
                    <div class="relative h-64 my-auto">
                        <template v-if="currentStats.length > 0">
                            <Doughnut :data="chartData" :options="chartOptions" />
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Итого</span>
                                <span class="text-xl font-black text-[#A66353]">
                                    {{ selectedType === 'expense' ? formatMoney(totals.expense) : formatMoney(totals.income) }}
                                </span>
                            </div>
                        </template>
                        <div v-else class="h-full flex flex-col items-center justify-center text-slate-400 gap-2">
                            <p class="font-medium text-base">Нет данных за выбранный период</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-6">Детализация по категориям</h3>
                    
                    <div v-if="currentStats.length > 0" class="space-y-5">
                        <div v-for="(item, idx) in currentStats" :key="item.name" class="group">
                            <div class="flex justify-between mb-1.5 text-sm items-center">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: chartColors[idx % chartColors.length] }"></div>
                                    <span class="font-medium text-slate-600 group-hover:text-slate-900 transition-colors">{{ item.name }}</span>
                                </div>
                                <span class="font-bold text-slate-800">{{ formatMoney(Number(item.total)) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500 shadow-xs" 
                                     :style="{ 
                                         backgroundColor: chartColors[idx % chartColors.length],
                                         width: (item.total / (selectedType === 'expense' ? (totals.expense || 1) : (totals.income || 1)) * 100) + '%' 
                                     }">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-20 text-slate-400">
                        <p class="font-medium">Список пуст</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>