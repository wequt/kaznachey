<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { PiggyBank, AlertCircle, Plus, CheckCircle2 } from 'lucide-vue-next';

const props = defineProps<{
    budgets: Array<any>;
    categories: Array<any>;
    currentMonth: string;
}>();

const form = useForm({
    category_id: '',
    limit_amount: '',
});

const submit = () => {
    form.post('/budgets', {
        onSuccess: () => form.reset(),
    });
};

const formatMoney = (amount: number) => new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';
</script>

<template>
    <Head title="Лимиты расходов" />

    <div class="min-h-screen bg-[#FDFCFB] py-12 px-6">
        <div class="max-w-5xl mx-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <h1 class="text-3xl font-black text-slate-800">Бюджетное планирование</h1>
                    <p class="text-slate-500">Контроль лимитов на {{ currentMonth }}</p>
                </div>
                
                <form @submit.prevent="submit" class="flex flex-wrap gap-3 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
                    <select v-model="form.category_id" class="rounded-xl border-slate-200 text-sm focus:ring-[#A66353]">
                        <option value="" disabled>Категория</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <input v-model="form.limit_amount" type="number" placeholder="Лимит" class="w-32 rounded-xl border-slate-200 text-sm focus:ring-[#A66353]">
                    <button :disabled="form.processing" class="bg-[#A66353] text-white px-4 py-2 rounded-xl hover:bg-[#8c5245] transition flex items-center gap-2">
                        <Plus class="w-4 h-4" />
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="budget in budgets" :key="budget.id" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                    
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-[#A66353]">
                                <PiggyBank class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">{{ budget.category_name }}</h3>
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Лимит: {{ formatMoney(budget.limit_amount) }}</p>
                            </div>
                        </div>
                        <div v-if="budget.is_over_limit" class="text-red-500 animate-pulse">
                            <AlertCircle class="w-6 h-6" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-500 font-medium">Потрачено: {{ formatMoney(budget.spent_amount) }}</span>
                            <span class="font-bold" :class="budget.is_over_limit ? 'text-red-500' : 'text-slate-800'">{{ budget.progress }}%</span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-1000 rounded-full" 
                                 :class="budget.is_over_limit ? 'bg-red-500' : 'bg-[#A66353]'"
                                 :style="{ width: budget.progress + '%' }">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-50 flex justify-between items-center text-xs">
                        <span class="text-slate-400 italic">
                            {{ budget.is_over_limit ? 'Превышение лимита!' : 'Доступно еще ' + formatMoney(budget.remaining) }}
                        </span>
                        <CheckCircle2 v-if="!budget.is_over_limit" class="w-4 h-4 text-green-500" />
                    </div>
                </div>

                <div v-if="budgets.length === 0" class="col-span-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-slate-100">
                    <p class="text-slate-400">У вас пока нет установленных лимитов.</p>
                </div>
            </div>
        </div>
    </div>
</template>