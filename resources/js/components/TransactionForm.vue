<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

defineProps<{
    accounts: Array<{ id: number; name: string }>;
    categories: Array<{ id: number; name: string; type: string }>;
}>();

interface TransactionFields {
    account_id: number | string;
    category_id: number | string;
    amount: number | null;
    transaction_date: string;
    description: string;
}

const form = useForm<TransactionFields>({
    account_id: '',
    category_id: '',
    amount: null,
    transaction_date: new Date().toISOString().split('T')[0],
    description: ''
});

const submit = () => {
    form.post('/transactions', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Счет</label>
                <select 
                    v-model="form.account_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#A66353] focus:border-[#A66353]"
                    :class="{ 'border-red-500': form.errors.account_id }"
                >
                    <option value="" disabled>Выберите счет</option>
                    <option v-for="account in accounts" :key="account.id" :value="account.id">
                        {{ account.name }}
                    </option>
                </select>
                <div v-if="form.errors.account_id" class="text-red-500 text-xs mt-1">{{ form.errors.account_id }}</div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Категория</label>
                <select 
                    v-model="form.category_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#A66353] focus:border-[#A66353]"
                    :class="{ 'border-red-500': form.errors.category_id }"
                >
                    <option value="" disabled>Выберите категорию</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }} ({{ category.type === 'expense' ? 'Расход' : 'Доход' }})
                    </option>
                </select>
                <div v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Сумма</label>
                <input 
                    v-model="form.amount" 
                    type="number" 
                    step="0.01"
                    placeholder="0.00"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#A66353] focus:border-[#A66353]"
                    :class="{ 'border-red-500': form.errors.amount }"
                >
                <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Дата</label>
                <input 
                    v-model="form.transaction_date" 
                    type="date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#A66353] focus:border-[#A66353]"
                >
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Комментарий</label>
            <input 
                v-model="form.description" 
                type="text"
                placeholder="На что потратили?"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-[#A66353] focus:border-[#A66353]"
            >
        </div>

        <div class="flex justify-end">
            <button 
                type="submit" 
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#A66353] hover:bg-[#8e5446] focus:outline-none disabled:opacity-50 transition-colors"
            >
                {{ form.processing ? 'Сохранение...' : 'Добавить транзакцию' }}
            </button>
        </div>
    </form>
</template>