<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import TransactionForm from '@/components/TransactionForm.vue';
import Header from '@/components/Header.vue';
import { Trash2, ArrowUpRight, ArrowDownLeft, ArrowLeftRight, History, PlusCircle } from 'lucide-vue-next';

interface Account {
    id: number;
    name: string;
    balance: number;
}

interface Category {
    id: number;
    name: string;
    type: 'income' | 'expense';
}

interface Transaction {
    id: number;
    amount: number;
    transaction_date: string;
    description: string | null;
    account: Account;
    category: Category | null;
    destination_account_id?: number | null;
    destination_account?: Account | null;
}

interface Props {
    transactions: {
        data: Transaction[];
        links: any[];
    };
    accounts: Account[];
    categories: Category[];
    filters: {
        account_id?: string | number;
        category_id?: string | number;
        date_from?: string;
        date_to?: string;
        type?: string;
    };
}

const props = defineProps<Props>();

const accountId = ref(props.filters.account_id || '');
const categoryId = ref(props.filters.category_id || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const transactionType = ref(props.filters.type || ''); 

watch([accountId, categoryId, dateFrom, dateTo, transactionType], ([newAccount, newCategory, newFrom, newTo, newType]) => {
    router.get('/transactions', 
        { 
            account_id: newAccount,
            category_id: newCategory,
            date_from: newFrom,
            date_to: newTo,
            type: newType
        }, 
        { 
            preserveState: true, 
            replace: true,
            preserveScroll: true
        }
    );
});

const toggleType = (type: 'income' | 'expense') => {
    transactionType.value = transactionType.value === type ? '' : type;
};

const deleteTransaction = (id: number) => {
    if (confirm('Вы уверены, что хотите отменить эту операцию? Баланс счета будет пересчитан.')) {
        router.delete(`/transactions/${id}`, { preserveScroll: true });
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';
};
</script>

<template>
    <Head title="Журнал операций" />

    <div class="min-h-screen bg-[#FDFCFB]">
        <Header />

        <div class="py-6 sm:py-8 px-4 sm:px-6">
            <div class="max-w-7xl mx-auto space-y-6 sm:space-y-8">
                
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Журнал операций</h1>
                        <p class="text-sm sm:text-base text-slate-500 italic">Полная история ваших финансовых потоков</p>
                    </div>
                </div>

                <div class="bg-white p-5 sm:p-8 rounded-3xl sm:rounded-4xl border border-slate-100 shadow-xs">
                    <div class="flex items-center gap-3 mb-5 sm:mb-6">
                        <PlusCircle class="w-5 h-5 sm:w-6 sm:h-6 text-[#A66353]" />
                        <h2 class="text-lg sm:text-xl font-bold text-slate-800">Новая запись</h2>
                    </div>
                    <TransactionForm 
                        :accounts="props.accounts" 
                        :categories="props.categories" 
                    />
                </div>

                <div class="bg-white rounded-3xl sm:rounded-4xl border border-slate-100 shadow-xs overflow-hidden">
                    
                    <div class="p-5 sm:p-6 border-b border-slate-50 flex flex-col gap-4 bg-slate-50/30">
                        <h2 class="text-lg sm:text-xl font-bold text-slate-800">Последние операции</h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 w-full">
                            
                            <div class="flex bg-slate-100 p-1 rounded-xl w-full h-10.5">
                                <button 
                                    @click="toggleType('income')"
                                    type="button"
                                    :class="transactionType === 'income' ? 'bg-green-600 text-white shadow-xs' : 'text-slate-600 hover:bg-white/60'"
                                    class="flex-1 text-xs font-bold rounded-lg transition-all cursor-pointer"
                                >
                                    Пополнение
                                </button>
                                <button 
                                    @click="toggleType('expense')"
                                    type="button"
                                    :class="transactionType === 'expense' ? 'bg-red-500 text-white shadow-xs' : 'text-slate-600 hover:bg-white/60'"
                                    class="flex-1 text-xs font-bold rounded-lg transition-all cursor-pointer"
                                >
                                    Списание
                                </button>
                            </div>

                            <select 
                                v-model="accountId"
                                class="w-full h-10.5 px-3 text-sm font-medium rounded-xl border border-slate-200 bg-white text-slate-700 focus:border-[#A66353] focus:ring-1 focus:ring-[#A66353] transition-all outline-hidden cursor-pointer"
                            >
                                <option value="">Все счета</option>
                                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                    {{ acc.name }}
                                </option>
                            </select>

                            <select 
                                v-model="categoryId"
                                class="w-full h-10.5 px-3 text-sm font-medium rounded-xl border border-slate-200 bg-white text-slate-700 focus:border-[#A66353] focus:ring-1 focus:ring-[#A66353] transition-all outline-hidden cursor-pointer"
                            >
                                <option value="">Все категории</option>
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

                            <div class="flex items-center gap-2 h-10.5 w-full">
                                <input 
                                    v-model="dateFrom" 
                                    type="date" 
                                    class="flex-1 min-w-0 text-sm rounded-xl border border-slate-200 bg-white p-2 h-full font-medium text-slate-700 focus:border-[#A66353] focus:ring-[#A66353] outline-hidden"
                                />
                                <span class="text-slate-400 text-sm select-none">—</span>
                                <input 
                                    v-model="dateTo" 
                                    type="date" 
                                    class="flex-1 min-w-0 text-sm rounded-xl border border-slate-200 bg-white p-2 h-full font-medium text-slate-700 focus:border-[#A66353] focus:ring-[#A66353] outline-hidden"
                                />
                            </div>

                        </div>
                    </div>
                    
                    <div class="hidden md:block overflow-x-auto selection:bg-[#A66353]/10">
                        <table class="min-w-full align-middle">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Дата</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Детали</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Категория</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest">Сумма</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest">Действие</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="item in transactions.data" :key="item.id" class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-5 whitespace-nowrap text-sm text-slate-500">
                                        {{ formatDate(item.transaction_date) }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-slate-800 text-sm sm:text-base">
                                            {{ item.destination_account_id ? 'Внутренний перевод' : (item.description || 'Без описания') }}
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                            <span class="text-[10px] font-bold uppercase text-slate-400 tracking-tight bg-slate-100 px-2 py-0.5 rounded">
                                                {{ item.account.name }}
                                            </span>
                                            <template v-if="item.destination_account_id && item.destination_account">
                                                <span class="text-slate-400 text-xs">→</span>
                                                <span class="text-[10px] font-bold uppercase text-amber-700 tracking-tight bg-amber-50 px-2 py-0.5 rounded">
                                                    {{ item.destination_account.name }}
                                                </span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span class="text-xs sm:text-sm font-medium text-slate-600 bg-slate-50 px-3 py-1 rounded-lg">
                                            {{ item.destination_account_id ? 'Перевод между счетами' : (item.category?.name || 'Без категории') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <ArrowLeftRight v-if="item.destination_account_id" class="w-4 h-4 text-amber-500" />
                                            <ArrowDownLeft v-else-if="item.category?.type === 'expense'" class="w-4 h-4 text-red-400" />
                                            <ArrowUpRight v-else class="w-4 h-4 text-green-400" />
                                            <span class="text-base sm:text-lg font-black" :class="item.destination_account_id ? 'text-slate-600' : (item.category?.type === 'expense' ? 'text-slate-800' : 'text-green-600')">
                                                {{ item.destination_account_id ? '' : (item.category?.type === 'expense' ? '-' : '+') }} {{ formatCurrency(Number(item.amount)) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <button 
                                            @click="deleteTransaction(item.id)" 
                                            class="p-2 rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all md:opacity-0 group-hover:opacity-100 cursor-pointer"
                                            title="Удалить"
                                        >
                                            <Trash2 class="w-5 h-5 ml-auto" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="block md:hidden divide-y divide-slate-100">
                        <div v-for="item in transactions.data" :key="item.id" class="p-4 space-y-3 bg-white hover:bg-slate-50/40 transition">
                            <div class="flex justify-between items-center text-xs text-slate-400">
                                <span>{{ formatDate(item.transaction_date) }}</span>
                                <button 
                                    @click="deleteTransaction(item.id)" 
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition active:scale-95"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div>
                                <div class="font-bold text-slate-800 text-sm">
                                    {{ item.destination_account_id ? 'Внутренний перевод' : (item.description || 'Без описания') }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    <span class="text-[9px] font-bold uppercase text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                        {{ item.account.name }}
                                    </span>
                                    <template v-if="item.destination_account_id && item.destination_account">
                                        <span class="text-slate-400 text-xs">→</span>
                                        <span class="text-[9px] font-bold uppercase text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded">
                                            {{ item.destination_account.name }}
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-1">
                                <span class="text-xs font-medium text-slate-500 bg-slate-50 px-2 py-1 rounded-md">
                                    {{ item.destination_account_id ? 'Перевод' : (item.category?.name || 'Без категории') }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <ArrowLeftRight v-if="item.destination_account_id" class="w-3.5 h-3.5 text-amber-500" />
                                    <ArrowDownLeft v-else-if="item.category?.type === 'expense'" class="w-3.5 h-3.5 text-red-400" />
                                    <ArrowUpRight v-else class="w-3.5 h-3.5 text-green-400" />
                                    <span class="text-base font-black" :class="item.destination_account_id ? 'text-slate-600' : (item.category?.type === 'expense' ? 'text-slate-800' : 'text-green-600')">
                                        {{ item.destination_account_id ? '' : (item.category?.type === 'expense' ? '-' : '+') }} {{ formatCurrency(Number(item.amount)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="transactions.data.length === 0" class="text-center py-16 sm:py-20 bg-white px-4">
                        <History class="w-10 h-10 sm:w-12 sm:h-12 text-slate-100 mx-auto mb-4" />
                        <p class="text-slate-400 font-medium text-base sm:text-lg">Записей пока нет</p>
                        <p class="text-slate-300 text-xs sm:text-sm">Выбранная комбинация фильтров не дала результатов.</p>
                    </div>

                    <div class="p-4 sm:p-8 border-t border-slate-50 flex justify-center overflow-x-auto">
                        <div class="flex gap-1">
                            <Link 
                                v-for="link in transactions.links" 
                                :key="link.label"
                                :href="link.url || '#'"
                                v-html="link.label"
                                class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs sm:text-sm font-bold transition-all border border-transparent whitespace-nowrap"
                                :class="{ 
                                    'bg-[#A66353] text-white shadow-lg shadow-[#a663534d]': link.active, 
                                    'text-slate-400 hover:bg-slate-50': !link.active && link.url,
                                    'text-slate-200 cursor-not-allowed': !link.url
                                }"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
:deep(b) {
    font-weight: 900;
}
</style>