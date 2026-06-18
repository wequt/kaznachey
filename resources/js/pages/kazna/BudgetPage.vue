<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { PiggyBank, AlertCircle, Plus, CheckCircle2, Calendar, Trash2 } from 'lucide-vue-next';
import Header from '@/components/Header.vue';

const props = defineProps<{
    budgets: Array<{
        id: number;
        category_name: string;
        limit_amount: number;
        spent_amount: number;
        remaining: number;
        progress: number;
        is_over_limit: boolean;
    }>;
    categories: Array<{ id: number; name: string }>;
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

const deleteBudget = (id: number) => {
    if (confirm('Вы уверены, что хотите удалить этот лимит?')) {
        router.delete(`/budgets/${id}`);
    }
};

const formatMoney = (amount: number) => new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';

const formatMonthLabel = (monthStr: string) => {
    const [year, month] = monthStr.split('-');
    const months = [
        'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
        'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
    ];
    return `${months[parseInt(month) - 1]} ${year}`;
};
</script>

<template>
    <Head title="Лимиты расходов" />

    <Header />

    <div class="min-h-screen bg-[#FDFCFB] py-8 px-6 text-slate-900">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 pb-2">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-800">Бюджетное планирование</h1>
                    <div class="flex items-center gap-2 text-slate-500 italic mt-1">
                        <Calendar class="w-4 h-4 text-[#A66353]" />
                        <span>Контроль лимитов на {{ formatMonthLabel(currentMonth) }}</span>
                    </div>
                </div>
                
                <form @submit.prevent="submit" class="flex flex-col sm:flex-row items-stretch gap-3 bg-white p-2 rounded-2xl border border-slate-100 shadow-xs w-full lg:w-auto">
                    <div class="relative flex-1 sm:w-60">
                        <select 
                            v-model="form.category_id" 
                            required
                            class="w-full text-sm rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 font-medium text-slate-700 focus:border-[#A66353] focus:ring-[#A66353] focus:bg-white outline-hidden cursor-pointer"
                        >
                            <option value="" disabled>Выберите категорию</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <div class="relative flex-1 sm:w-36">
                        <input 
                            v-model="form.limit_amount" 
                            type="number" 
                            required
                            min="1"
                            placeholder="Сумма лимита" 
                            class="w-full text-sm rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 font-medium text-slate-700 focus:border-[#A66353] focus:ring-[#A66353] focus:bg-white outline-hidden"
                        />
                    </div>

                    <button 
                        :disabled="form.processing" 
                        type="submit"
                        class="bg-[#A66353] text-white px-5 py-2.5 rounded-xl hover:bg-[#8c5245] transition-all font-bold text-xs shadow-xs flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <Plus class="w-4 h-4 stroke-3" />
                        <span>Установить</span>
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="budget in budgets" 
                    :key="budget.id" 
                    class="bg-white p-6 rounded-3xl border transition-all duration-300 shadow-sm flex flex-col justify-between group/card"
                    :class="budget.is_over_limit ? 'border-red-100 hover:border-red-200' : 'border-slate-100 hover:border-slate-200/80'"
                >
                    <div>
                        <div class="flex justify-between items-start mb-5 relative">
                            <div class="flex items-center gap-3.5">
                                <div 
                                    class="w-11 h-11 rounded-2xl flex items-center justify-center transition-colors"
                                    :class="budget.is_over_limit ? 'bg-red-50 text-red-500' : 'bg-[#fdf5f3] text-[#A66353]'"
                                >
                                    <PiggyBank class="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-base">
                                        {{ budget.category_name }}
                                    </h3>
                                    <p class="text-[10px] uppercase font-black text-slate-400 tracking-wider mt-0.5">
                                        Лимит: {{ formatMoney(budget.limit_amount) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div 
                                    v-if="budget.is_over_limit" 
                                    class="text-red-500 bg-red-50 px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider flex items-center gap-1 group-hover/card:opacity-0 transition-opacity duration-150"
                                >
                                    <AlertCircle class="w-3.5 h-3.5" />
                                    <span>Превышен</span>
                                </div>

                                <button 
                                    @click="deleteBudget(budget.id)"
                                    type="button"
                                    class="absolute right-0 top-0 lg:opacity-0 group-hover/card:opacity-100 p-2 text-slate-400 hover:text-red-500 rounded-xl hover:bg-red-50/50 transition-all cursor-pointer"
                                    title="Удалить лимит"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2.5">
                            <div class="flex justify-between text-xs items-end">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-slate-400 font-medium text-[11px]">Потрачено</span>
                                    <span class="font-bold text-slate-700 text-sm">{{ formatMoney(budget.spent_amount) }}</span>
                                </div>
                                <span 
                                    class="font-black text-sm" 
                                    :class="budget.is_over_limit ? 'text-red-500' : 'text-slate-800'"
                                >
                                    {{ budget.progress }}%
                                </span>
                            </div>
                            
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div 
                                    class="h-2 rounded-full transition-all duration-700 shadow-xs" 
                                    :class="budget.is_over_limit ? 'bg-linear-to-r from-red-400 to-red-500' : 'bg-[#A66353]'"
                                    :style="{ width: budget.progress + '%' }"
                                >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div 
                        class="mt-6 pt-4 border-t flex justify-between items-center text-xs font-medium"
                        :class="budget.is_over_limit ? 'border-red-50/60' : 'border-slate-50'"
                    >
                        <span :class="budget.is_over_limit ? 'text-red-400 font-semibold' : 'text-slate-400 italic'">
                            {{ budget.is_over_limit ? 'Превышение бюджета!' : 'Доступно еще ' + formatMoney(budget.remaining) }}
                        </span>
                        <CheckCircle2 v-if="!budget.is_over_limit" class="w-4 h-4 text-green-500 stroke-[2.5]" />
                    </div>
                </div>

                <div 
                    v-if="budgets.length === 0" 
                    class="col-span-full py-24 text-center bg-white rounded-3xl border-2 border-dashed border-slate-100 flex flex-col items-center justify-center gap-3"
                >
                    <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400">
                        <PiggyBank class="w-6 h-6" />
                    </div>
                    <div class="max-w-xs mx-auto">
                        <p class="text-slate-700 font-bold text-base mb-0.5">Лимиты не установлены</p>
                        <p class="text-slate-400 text-xs">Добавьте категории расходов в верхнюю форму, чтобы контролировать перерасход средств.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>