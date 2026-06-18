<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import {
    Wallet,
    ArrowUpRight,
    ArrowDownLeft,
    ArrowLeftRight,
    PieChart,
    List,
    PiggyBank,
    TrendingUp,
    AlertTriangle,
    ArrowRight
} from 'lucide-vue-next';
import { computed } from 'vue';
import NavUser from '@/components/NavUser.vue';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps<{
    totalBalance: number;
    recentTransactions: Array<any>;
    monthStats: Array<{ name: string, total: number }>;
    overBudgets: Array<string>;
}>();

const totalSpent = computed(() => {
    if (!props.monthStats || props.monthStats.length === 0) return 0;

    return props.monthStats.reduce((acc, item) => {
        const val = Number(item.total);
        return acc + (isNaN(val) ? 0 : val);
    }, 0);
});

const chartData = computed(() => ({
    labels: props.monthStats.map(item => item.name),
    datasets: [{
        backgroundColor: [
            '#EE6D4A',
            '#B93FE3',
            '#2585EC',
            '#30B5E3',
            '#82CE25',
            '#EBC415',
            '#EA2D8C',
            '#6C52E6',
            '#00A3AC',
            '#EC8C11',
            '#22B057',
            '#74859A'
        ],
        data: props.monthStats.map(item => item.total),
        borderWidth: 0,
        cutout: '75%'
    }]
}));

const formatMoney = (amount: number) => {
    if (isNaN(amount)) return '0 ₽';
    return new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';
};
</script>

<template>
    <Head title="Рабочий стол" />

    <div class="min-h-screen bg-[#FDFCFB] py-6 sm:py-8 px-4 sm:px-6 text-slate-900">
        <div class="max-w-7xl mx-auto space-y-6 sm:space-y-8">

            <div v-if="props.overBudgets && props.overBudgets.length > 0"
                class="bg-red-50/60 border border-red-100 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                        <AlertTriangle class="w-5 h-5 stroke-[2.5]" />
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">Внимание! Превышение лимитов</h4>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Вы вышли за рамки бюджета в категориях:
                            <span class="font-semibold text-red-600">{{ props.overBudgets.join(', ') }}</span>.
                        </p>
                    </div>
                </div>
                <Link href="/budgets"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-[#A66353] hover:text-[#8c5245] bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-2xs self-start sm:self-center">
                    <span>Управление лимитами</span>
                    <ArrowRight class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
                <div class="flex justify-between items-center gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800">Общий обзор</h1>
                        <p class="text-sm sm:text-base text-slate-500 italic mt-0.5 sm:mt-0">Аналитическая панель управления капиталом</p>
                    </div>
                </div>

                <div class="shrink-0">
                    <NavUser :user="$page.props.auth.user" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">

                <div class="lg:col-span-8 space-y-6 sm:space-y-8">

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                        <Link href="/transactions"
                            class="group p-4 bg-white border border-slate-100 rounded-2xl hover:border-[#A66353] hover:shadow-md transition-all">
                            <div
                                class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#A66353] group-hover:text-white transition-colors mb-3">
                                <List class="w-5 h-5" />
                            </div>
                            <span class="block font-bold text-sm">Операции</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">История</span>
                        </Link>

                        <Link href="/directories"
                            class="group p-4 bg-white border border-slate-100 rounded-2xl hover:border-[#A66353] hover:shadow-md transition-all">
                            <div
                                class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#A66353] group-hover:text-white transition-colors mb-3">
                                <Wallet class="w-5 h-5" />
                            </div>
                            <span class="block font-bold text-sm">Счета</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Управление</span>
                        </Link>

                        <Link href="/analytics"
                            class="group p-4 bg-white border border-slate-100 rounded-2xl hover:border-[#A66353] hover:shadow-md transition-all">
                            <div
                                class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#A66353] group-hover:text-white transition-colors mb-3">
                                <PieChart class="w-5 h-5" />
                            </div>
                            <span class="block font-bold text-sm">Аналитика</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Графики</span>
                        </Link>

                        <Link href="/budgets"
                            class="group p-4 bg-white border border-slate-100 rounded-2xl hover:border-[#A66353] hover:shadow-md transition-all">
                            <div
                                class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#A66353] group-hover:text-white transition-colors mb-3">
                                <PiggyBank class="w-5 h-5" />
                            </div>
                            <span class="block font-bold text-sm">Бюджеты</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Лимиты</span>
                        </Link>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-5 sm:p-8 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-linear-to-r from-white to-slate-50/50">
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-slate-800">Последняя активность</h3>
                            </div>
                            <div class="text-left sm:text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Доступно средств</p>
                                <p class="text-xl sm:text-2xl font-black text-[#A66353]">{{ formatMoney(totalBalance) }}</p>
                            </div>
                        </div>

                        <div class="divide-y divide-slate-50">
                            <div v-for="tx in recentTransactions" :key="tx.id"
                                class="p-4 sm:p-6 flex items-center justify-between gap-3 hover:bg-slate-50/50 transition">
                                <div class="flex items-center gap-3 sm:gap-5 min-w-0">
                                    <div :class="tx.destination_account_id ? 'bg-amber-50 text-amber-600' : (tx.category?.type === 'expense' ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500')"
                                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center shrink-0">
                                        <ArrowLeftRight v-if="tx.destination_account_id" class="w-5 sm:w-6 h-5 sm:h-6" />
                                        <ArrowDownLeft v-else-if="tx.category?.type === 'expense'" class="w-5 sm:w-6 h-5 sm:h-6" />
                                        <ArrowUpRight v-else class="w-5 sm:w-6 h-5 sm:h-6" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 text-sm sm:text-base truncate">
                                            {{ tx.destination_account_id ? 'Перевод между счетами' : (tx.description || tx.category?.name) }}
                                        </p>
                                        <div class="flex items-center gap-2 text-[11px] sm:text-xs text-slate-400 mt-0.5 flex-wrap">
                                            <template v-if="tx.destination_account_id">
                                                <span class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-600 font-medium text-ellipsis overflow-hidden">{{ tx.account?.name }}</span>
                                                <span>→</span>
                                                <span class="bg-amber-50 px-1.5 py-0.5 rounded text-amber-800 font-medium text-ellipsis overflow-hidden">{{ tx.destination_account?.name }}</span>
                                            </template>
                                            <template v-else>
                                                <span class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-600 font-medium text-ellipsis overflow-hidden">{{ tx.account?.name }}</span>
                                            </template>
                                            <span>•</span>
                                            <span>{{ tx.transaction_date }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-base sm:text-lg font-black"
                                        :class="tx.destination_account_id ? 'text-slate-600' : (tx.category?.type === 'expense' ? 'text-slate-800' : 'text-green-600')">
                                        {{ tx.destination_account_id ? '' : (tx.category?.type === 'expense' ? '-' : '+') }}{{ formatMoney(tx.amount) }}
                                    </p>
                                    <p class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 mt-0.5 truncate max-w-25 sm:max-w-none">
                                        {{ tx.destination_account_id ? 'Перевод' : (tx.category?.name || 'Без категории') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white p-5 sm:p-8 rounded-3xl border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 sm:mb-8">
                            <h3 class="font-bold text-slate-800 text-base sm:text-lg">Структура трат</h3>
                            <TrendingUp class="w-5 h-5 text-[#A66353]" />
                        </div>

                        <div class="relative h-48 sm:h-56 mb-6 sm:mb-8">
                            <Doughnut v-if="monthStats.length > 0" :data="chartData"
                                :options="{ maintainAspectRatio: false, plugins: { legend: { display: false } } }" />

                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest">Траты (мес.)</span>
                                <span class="text-lg sm:text-xl font-black text-[#A66353]">{{ formatMoney(totalSpent) }}</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div v-for="(item, idx) in monthStats.slice(0, 5)" :key="item.name"
                                class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-2 h-2 rounded-full shrink-0"
                                        :style="{ backgroundColor: chartData.datasets[0].backgroundColor[idx] }"></div>
                                    <span class="text-slate-600 truncate">{{ item.name }}</span>
                                </div>
                                <span class="font-bold text-slate-800 shrink-0">{{ formatMoney(item.total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Планирование -->
                    <div class="bg-[#A66353] p-6 sm:p-8 rounded-3xl text-white shadow-xl shadow-[#a663532c] relative overflow-hidden group">
                        <div class="relative z-10">
                            <h4 class="text-base sm:text-lg font-bold mb-2">Планирование</h4>
                            <p class="text-xs sm:text-sm text-[#F3D9D4] mb-6 leading-relaxed">
                                Установите лимиты на категории, чтобы система предупредила о перерасходе.
                            </p>
                            <Link href="/budgets"
                                class="inline-flex items-center gap-2 bg-white text-[#A66353] px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl font-bold text-xs sm:text-sm hover:bg-slate-50 transition-colors">
                                <span>Настроить лимиты</span>
                                <ArrowUpRight class="w-4 h-4" />
                            </Link>
                        </div>
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white opacity-5 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>