<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import TransactionForm from '@/components/TransactionForm.vue';
import { 
    Search, 
    Filter, 
    Trash2, 
    ArrowUpRight, 
    ArrowDownLeft, 
    History,
    PlusCircle
} from 'lucide-vue-next';

interface Account {
    id: number;
    name: string;
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
    category: Category;
}

interface Props {
    transactions: {
        data: Transaction[];
        links: any[];
    };
    accounts: Account[];
    categories: Category[];
    filters: {
        search?: string;
        account_id?: string | number;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const accountId = ref(props.filters.account_id || '');

watch([search, accountId], ([newSearch, newAccount]) => {
    router.get('/transactions', 
        { search: newSearch, account_id: newAccount }, 
        { preserveState: true, replace: true }
    );
});

const deleteTransaction = (id: number) => {
    if (confirm('Вы уверены, что хотите отменить эту операцию? Баланс счета будет пересчитан.')) {
        router.delete(`/transactions/${id}`, {
            preserveScroll: true,
        });
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

    <div class="min-h-screen bg-[#FDFCFB] py-12 px-6">
        <div class="max-w-6xl mx-auto space-y-8">
            
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h1 class="text-3xl font-black text-slate-800 tracking-tight">Журнал операций</h1>
                    <p class="text-slate-500 italic">Полная история ваших финансовых потоков</p>
                </div>
                <div class="hidden md:block">
                    <History class="w-12 h-12 text-[#A66353] opacity-20" />
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center gap-3 mb-6">
                    <PlusCircle class="w-6 h-6 text-[#A66353]" />
                    <h2 class="text-xl font-bold text-slate-800">Новая запись</h2>
                </div>
                <TransactionForm 
                    :accounts="props.accounts" 
                    :categories="props.categories" 
                />
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-linear-to-r from-white to-slate-50">
                    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        Последние операции
                    </h2>
                    
                    <div class="flex flex-wrap gap-3">
                        <div class="relative">
                            <Search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Поиск..." 
                                class="pl-10 text-sm rounded-xl border-slate-200 bg-white shadow-xs focus:border-[#A66353] focus:ring-[#A66353] w-full md:w-64"
                            />
                        </div>
                        <div class="relative">
                            <Filter class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                            <select 
                                v-model="accountId" 
                                class="pl-10 text-sm rounded-xl border-slate-200 bg-white shadow-xs focus:border-[#A66353] focus:ring-[#A66353]"
                            >
                                <option value="">Все счета</option>
                                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                    {{ acc.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Дата</th>
                                <th class="px-8 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Детали</th>
                                <th class="px-8 py-4 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Категория</th>
                                <th class="px-8 py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest">Сумма</th>
                                <th class="px-8 py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest">Действие</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="item in transactions.data" :key="item.id" class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-6 whitespace-nowrap text-sm text-slate-500">
                                    {{ formatDate(item.transaction_date) }}
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-800">{{ item.description || 'Без описания' }}</div>
                                    <div class="text-[10px] font-bold uppercase text-slate-400 tracking-tight bg-slate-100 px-2 py-0.5 rounded inline-block mt-1">
                                        {{ item.account.name }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-sm font-medium text-slate-600 bg-slate-50 px-3 py-1 rounded-lg">
                                        {{ item.category.name }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <ArrowDownLeft v-if="item.category.type === 'expense'" class="w-4 h-4 text-red-400" />
                                        <ArrowUpRight v-else class="w-4 h-4 text-green-400" />
                                        <span class="text-lg font-black" 
                                              :class="item.category.type === 'expense' ? 'text-slate-800' : 'text-green-600'">
                                            {{ item.category.type === 'expense' ? '-' : '+' }} {{ formatCurrency(Number(item.amount)) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right">
                                    <button 
                                        @click="deleteTransaction(item.id)" 
                                        class="p-2 rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100"
                                        title="Удалить"
                                    >
                                        <Trash2 class="w-5 h-5 ml-auto" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="transactions.data.length === 0" class="text-center py-20 bg-white">
                    <History class="w-12 h-12 text-slate-100 mx-auto mb-4" />
                    <p class="text-slate-400 font-medium text-lg">Записей пока нет</p>
                    <p class="text-slate-300 text-sm">Попробуйте изменить параметры поиска или добавьте операцию</p>
                </div>

                <div class="p-8 border-t border-slate-50 flex justify-center">
                    <div class="flex gap-1">
                        <Link 
                            v-for="link in transactions.links" 
                            :key="link.label"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="px-4 py-2 rounded-xl text-sm font-bold transition-all border border-transparent"
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
</template>

<style scoped>
:deep(b) {
    font-weight: 900;
}
</style>