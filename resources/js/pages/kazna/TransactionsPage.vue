<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import TransactionForm from '@/components/TransactionForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';

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
    return new Date(dateString).toLocaleDateString('ru-RU');
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(amount);
};
</script>

<template>
    <Head title="Журнал операций" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900 mb-4" style="color: #A66353;">
                    Добавить новую операцию
                </h2>
                <TransactionForm 
                    :accounts="props.accounts" 
                    :categories="props.categories" 
                />
            </div>

            <div class="p-6 bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
                    <h2 class="text-lg font-medium text-gray-900">Последние операции</h2>
                    
                    <div class="flex flex-wrap gap-2">
                        <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Поиск по описанию..." 
                            class="text-sm rounded-md border-gray-300 shadow-sm focus:border-[#A66353] focus:ring-[#A66353]"
                        />
                        <select 
                            v-model="accountId" 
                            class="text-sm rounded-md border-gray-300 shadow-sm focus:border-[#A66353] focus:ring-[#A66353]"
                        >
                            <option value="">Все счета</option>
                            <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                {{ acc.name }}
                            </option>
                        </select>
                    </div>
                </div>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Счет / Описание</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Категория</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Сумма</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действие</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in transactions.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(item.transaction_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium">{{ item.account.name }}</div>
                                <div class="text-xs text-gray-400">{{ item.description || 'Нет описания' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ item.category.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold" 
                                :class="item.category.type === 'expense' ? 'text-red-600' : 'text-green-600'">
                                {{ item.category.type === 'expense' ? '-' : '+' }}
                                {{ formatCurrency(Number(item.amount)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button 
                                    @click="deleteTransaction(item.id)" 
                                    class="text-gray-400 hover:text-red-600 transition-colors"
                                    title="Удалить"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="transactions.data.length === 0" class="text-center py-8 text-gray-500">
                    Транзакции не найдены.
                </div>

                <div class="mt-6 flex justify-center space-x-2">
                    <Link 
                        v-for="link in transactions.links" 
                        :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1 border rounded text-sm"
                        :class="{ 'bg-[#A66353] text-white border-[#A66353]': link.active, 'text-gray-500': !link.url }"
                    />
                </div>
            </div>
        </div>
    </div>
</template>