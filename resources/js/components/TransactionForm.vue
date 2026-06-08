<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Account {
    id: number;
    name: string;
    balance: number | string;
}

interface Category {
    id: number;
    name: string;
    type: 'income' | 'expense';
}

const props = defineProps<{
    accounts: Account[];
    categories: Category[];
}>();

const form = useForm({
    account_id: '',
    category_id: '',
    amount: '',
    transaction_date: new Date().toISOString().split('T')[0],
    description: ''
});

const selectedAccountBalance = computed(() => {
    if (!form.account_id) return null;
    const account = props.accounts.find(a => a.id === Number(form.account_id));
    if (!account || account.balance === undefined || account.balance === null) return 0;
    const parsedBalance = Number(account.balance);
    return isNaN(parsedBalance) ? 0 : parsedBalance;
});

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';
};

const submit = () => {
    form.post('/transactions', {
        onSuccess: () => form.reset('amount', 'description'),
        preserveScroll: true
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <div class="space-y-1.5">
                <div class="flex justify-between items-center h-5 px-1">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Счет</label>
                    <span 
                        v-if="selectedAccountBalance !== null" 
                        class="text-[11px] font-black text-[#A66353] bg-[#A66353]/5 px-2.5 py-0.5 rounded-full border border-[#A66353]/10"
                    >
                        Доступно: {{ formatCurrency(selectedAccountBalance) }}
                    </span>
                </div>
                
                <select 
                    v-model="form.account_id" 
                    required
                    class="w-full px-4 text-sm font-medium rounded-2xl border border-slate-200 bg-slate-50/40 text-slate-700 h-12.5 shadow-xs focus:bg-white focus:border-[#A66353] focus:ring-4 focus:ring-[#A66353]/10 transition-all duration-200 outline-hidden cursor-pointer"
                >
                    <option value="" disabled selected>Выберите счет</option>
                    <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                        {{ acc.name }}
                    </option>
                </select>
                <div v-if="form.errors.account_id" class="text-red-500 text-xs px-1">{{ form.errors.account_id }}</div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider h-5 px-1">Категория</label>
                
                <select 
                    v-model="form.category_id" 
                    required
                    class="w-full px-4 text-sm font-medium rounded-2xl border border-slate-200 bg-slate-50/40 text-slate-700 h-12.5 shadow-xs focus:bg-white focus:border-[#A66353] focus:ring-4 focus:ring-[#A66353]/10 transition-all duration-200 outline-hidden cursor-pointer"
                >
                    <option value="" disabled selected>Выберите категорию</option>
                    <optgroup label="Доходы" class="text-slate-400 font-normal bg-white">
                        <option v-for="cat in categories.filter(c => c.type === 'income')" :key="cat.id" :value="cat.id" class="text-slate-700 font-medium">
                            {{ cat.name }}
                        </option>
                    </optgroup>
                    <optgroup label="Расходы" class="text-slate-400 font-normal bg-white">
                        <option v-for="cat in categories.filter(c => c.type === 'expense')" :key="cat.id" :value="cat.id" class="text-slate-700 font-medium">
                            {{ cat.name }}
                        </option>
                    </optgroup>
                </select>
                <div v-if="form.errors.category_id" class="text-red-500 text-xs px-1">{{ form.errors.category_id }}</div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider h-5 px-1">Сумма</label>
                <div class="relative">
                    <input 
                        v-model="form.amount" 
                        type="number" 
                        step="0.01"
                        min="0.01"
                        placeholder="0.00" 
                        required
                        class="w-full pl-4 pr-12 text-base font-black rounded-2xl border border-slate-200 bg-slate-50/40 text-slate-800 h-12.5 shadow-xs focus:bg-white focus:border-[#A66353] focus:ring-4 focus:ring-[#A66353]/10 transition-all duration-200 placeholder:text-slate-300 placeholder:font-normal outline-hidden"
                    />
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">₽</span>
                </div>
                <div v-if="form.errors.amount" class="text-red-500 text-xs px-1">{{ form.errors.amount }}</div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider h-5 px-1">Дата</label>
                <input 
                    v-model="form.transaction_date" 
                    type="date" 
                    required
                    class="w-full px-4 text-sm font-medium rounded-2xl border border-slate-200 bg-slate-50/40 text-slate-700 h-12.5 shadow-xs focus:bg-white focus:border-[#A66353] focus:ring-4 focus:ring-[#A66353]/10 transition-all duration-200 outline-hidden"
                />
                <div v-if="form.errors.transaction_date" class="text-red-500 text-xs px-1">{{ form.errors.transaction_date }}</div>
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider h-5 px-1">Комментарий</label>
            <input 
                v-model="form.description" 
                type="text" 
                placeholder="На что потратили или откуда пришло?" 
                class="w-full px-4 text-sm font-medium rounded-2xl border border-slate-200 bg-slate-50/40 text-slate-700 h-12.5 shadow-xs focus:bg-white focus:border-[#A66353] focus:ring-4 focus:ring-[#A66353]/10 transition-all duration-200 placeholder:text-slate-400/40 outline-hidden"
            />
            <div v-if="form.errors.description" class="text-red-500 text-xs px-1">{{ form.errors.description }}</div>
        </div>

        <div class="flex justify-end pt-2">
            <button 
                type="submit" 
                :disabled="form.processing"
                class="px-8 py-3.5 bg-[#A66353] text-white text-sm font-black rounded-2xl shadow-lg shadow-[#a6635333] hover:bg-[#8e5244] hover:shadow-xl hover:shadow-[#a663534d] active:scale-[0.99] transition-all duration-200 disabled:opacity-50 cursor-pointer tracking-wide"
            >
                <span v-if="form.processing" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Сохранение...
                </span>
                <span v-else>Добавить транзакцию</span>
            </button>
        </div>
    </form>
</template>