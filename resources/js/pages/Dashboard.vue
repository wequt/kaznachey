<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { 
    Wallet, 
    ArrowUpRight, 
    ArrowDownLeft, 
    PieChart, 
    List,
    PiggyBank,
    TrendingUp
} from 'lucide-vue-next'; 
import { computed } from 'vue';
import NavUser from '@/components/NavUser.vue';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps<{
    totalBalance: number;
    recentTransactions: Array<any>;
    monthStats: Array<{ name: string, total: number }>;
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
        backgroundColor: ['#A66353', '#C28476', '#8A4B3D', '#D9AFA6', '#703A2F'],
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

    <div class="min-h-screen bg-[#FDFCFB] py-8 px-6 text-slate-900">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-10 flex justify-between items-start">
    <div>
        <h1 class="text-3xl font-black tracking-tight text-slate-800">Общий обзор</h1>
        <p class="text-slate-500 italic">Аналитическая панель управления капиталом</p>
    </div>

    <div class="flex items-center">
        <NavUser :user="$page.props.auth.user" />
    </div>
</div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-8 space-y-8">
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <Link href="/transactions" class="group p-4 bg-white border border-slate-100 rounded-2xl hover:border-[#A66353] hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#A66353] group-hover:text-white transition-colors mb-3">
                                <List class="w-5 h-5" />
                            </div>
                            <span class="block font-bold text-sm">Операции</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">История</span>
                        </Link>
                        
                        <Link href="/directories" class="group p-4 bg-white border border-slate-100 rounded-2xl hover:border-[#A66353] hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#A66353] group-hover:text-white transition-colors mb-3">
                                <Wallet class="w-5 h-5" />
                            </div>
                            <span class="block font-bold text-sm">Счета</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Управление</span>
                        </Link>

                        <Link href="/analytics" class="group p-4 bg-white border border-slate-100 rounded-2xl hover:border-[#A66353] hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#A66353] group-hover:text-white transition-colors mb-3">
                                <PieChart class="w-5 h-5" />
                            </div>
                            <span class="block font-bold text-sm">Аналитика</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Графики</span>
                        </Link>

                        <Link href="/budgets" class="group p-4 bg-white border border-slate-100 rounded-2xl hover:border-[#A66353] hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#A66353] group-hover:text-white transition-colors mb-3">
                                <PiggyBank class="w-5 h-5" />
                            </div>
                            <span class="block font-bold text-sm">Бюджеты</span>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Лимиты</span>
                        </Link>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-linear-to-r from-white to-slate-50">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Последняя активность</h3>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Доступно средств</p>
                                <p class="text-2xl font-black text-[#A66353]">{{ formatMoney(totalBalance) }}</p>
                            </div>
                        </div>

                        <div class="divide-y divide-slate-50">
                            <div v-for="tx in recentTransactions" :key="tx.id" class="p-6 flex items-center justify-between hover:bg-slate-50/50 transition">
                                <div class="flex items-center gap-5">
                                    <div :class="tx.category.type === 'expense' ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500'" 
                                         class="w-12 h-12 rounded-2xl flex items-center justify-center">
                                        <ArrowDownLeft v-if="tx.category.type === 'expense'" class="w-6 h-6" />
                                        <ArrowUpRight v-else class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ tx.description || tx.category.name }}</p>
                                        <div class="flex items-center gap-2 text-xs text-slate-400">
                                            <span class="bg-slate-100 px-2 py-0.5 rounded text-slate-600">{{ tx.account.name }}</span>
                                            <span>•</span>
                                            <span>{{ tx.transaction_date }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-black" :class="tx.category.type === 'expense' ? 'text-slate-800' : 'text-green-600'">
                                        {{ tx.category.type === 'expense' ? '-' : '+' }}{{ formatMoney(tx.amount) }}
                                    </p>
                                    <p class="text-[10px] uppercase font-bold text-slate-300">{{ tx.category.name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="font-bold text-slate-800">Структура трат</h3>
                            <TrendingUp class="w-5 h-5 text-[#A66353]" />
                        </div>
                        
                        <div class="relative h-56 mb-8">
                            <Doughnut 
                                v-if="monthStats.length > 0" 
                                :data="chartData" 
                                :options="{ maintainAspectRatio: false, plugins: { legend: { display: false } } }" 
                            />
                            
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Траты (мес.)</span>
                                <span class="text-xl font-black text-[#A66353]">{{ formatMoney(totalSpent) }}</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div v-for="(item, idx) in monthStats.slice(0, 5)" :key="item.name" class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: chartData.datasets[0].backgroundColor[idx] }"></div>
                                    <span class="text-sm text-slate-600">{{ item.name }}</span>
                                </div>
                                <span class="text-sm font-bold text-slate-800">{{ formatMoney(item.total) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#A66353] p-8 rounded-3xl text-white shadow-xl shadow-[#a663532c] relative overflow-hidden group">
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-2">Планирование</h4>
                            <p class="text-sm text-[#F3D9D4] mb-6 leading-relaxed">Установите лимиты на категории, чтобы система предупредила о перерасходе.</p>
                            <Link href="/budgets" class="inline-flex items-center gap-2 bg-white text-[#A66353] px-6 py-3 rounded-xl font-bold text-sm hover:bg-slate-50 transition-colors">
                                Настроить лимиты
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