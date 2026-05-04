<script setup lang="ts">
import { useForm, Head, router } from '@inertiajs/vue3';

const props = defineProps<{
    accounts: Array<{ id: number, name: string, balance: number, currency: string }>;
    categories: Array<{ id: number, name: string, type: string, icon: string }>;
}>();

const accountForm = useForm({ name: '', balance: 0, currency: 'RUB' });
const categoryForm = useForm({
    name: '',
    type: 'expense',
    icon: 'folder'
});

const submitAccount = () => {
    accountForm.post('/directories/accounts', { 
        onSuccess: () => accountForm.reset() 
    });
};

const submitCategory = () => {
    categoryForm.post('/directories/categories', { 
        onSuccess: () => categoryForm.reset() 
    });
};

const deleteAccount = (id: number) => {
    if (confirm('Вы уверены? Все операции по этому счету также могут быть затронуты.')) {
        router.delete(`/directories/accounts/${id}`, {
            preserveScroll: true,
        });
    }
};

const deleteCategory = (id: number) => {
    if (confirm('Удалить категорию?')) {
        router.delete(`/directories/categories/${id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Счета и категории" />

    <div class="min-h-screen bg-gray-50 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="font-semibold text-2xl text-gray-800 mb-8">Управление счетами и категориями</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-100">
                    <h3 class="text-lg font-medium mb-6" style="color: #A66353;">Мои счета</h3>
                    <form @submit.prevent="submitAccount" class="mb-8 space-y-4">
                        <div>
                            <input v-model="accountForm.name" type="text" placeholder="Название счета"
                                class="w-full rounded-lg border-gray-300 focus:ring-[#A66353] focus:border-[#A66353]" />
                            <div v-if="accountForm.errors.name" class="text-red-500 text-xs mt-1">{{
                                accountForm.errors.name }}</div>
                        </div>

                        <div class="flex gap-2">
                            <div class="flex-1">
                                <input v-model="accountForm.balance" type="number" step="0.01" placeholder="Баланс"
                                    class="w-full rounded-lg border-gray-300 focus:ring-[#A66353] focus:border-[#A66353]" />
                                <div v-if="accountForm.errors.balance" class="text-red-500 text-xs mt-1">{{
                                    accountForm.errors.balance }}</div>
                            </div>
                            <div>
                                <select v-model="accountForm.currency"
                                    class="w-full rounded-lg border-gray-300 focus:ring-[#A66353] focus:border-[#A66353]">
                                    <option value="RUB">RUB (₽)</option>
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="KZT">KZT (₸)</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" :disabled="accountForm.processing"
                            class="w-full bg-[#A66353] text-white py-2.5 rounded-lg hover:opacity-90 transition font-medium">
                            Создать счет
                        </button>
                    </form>

                    <div class="space-y-2">
                        <div v-for="acc in accounts" :key="acc.id"
                            class="flex justify-between items-center p-3 bg-gray-50 rounded-lg group">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-700">{{ acc.name }}</span>
                                <span class="text-xs text-gray-400">{{ acc.currency }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-gray-800">{{ acc.balance }}</span>
                                <button @click="deleteAccount(acc.id)" 
                                    class="text-gray-300 hover:text-red-500 transition-colors text-xl leading-none">
                                    &times;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-100">
                    <h3 class="text-lg font-medium mb-6" style="color: #A66353;">Мои категории</h3>
                    <form @submit.prevent="submitCategory" class="mb-8 space-y-4">
                        <div>
                            <input v-model="categoryForm.name" type="text" placeholder="Название категории"
                                class="w-full rounded-lg border-gray-300 focus:ring-[#A66353] focus:border-[#A66353]" />
                            <div v-if="categoryForm.errors.name" class="text-red-500 text-xs mt-1">{{
                                categoryForm.errors.name }}</div>
                        </div>

                        <div>
                            <select v-model="categoryForm.type"
                                class="w-full rounded-lg border-gray-300 focus:ring-[#A66353] focus:border-[#A66353]">
                                <option value="expense">Расход</option>
                                <option value="income">Доход</option>
                            </select>
                        </div>

                        <button type="submit" :disabled="categoryForm.processing"
                            class="w-full bg-[#A66353] text-white py-2.5 rounded-lg hover:opacity-90 transition font-medium">
                            Добавить категорию
                        </button>
                    </form>

                    <div class="grid grid-cols-2 gap-3">
                        <div v-for="cat in categories" :key="cat.id"
                            class="flex justify-between items-center px-4 py-2.5 rounded-lg text-sm font-medium border group"
                            :class="cat.type === 'income' ? 'bg-green-50 border-green-100 text-green-700' : 'bg-red-50 border-red-100 text-red-700'">
                            <div class="flex items-center truncate">
                                <span class="mr-2">📁</span>
                                <span class="truncate">{{ cat.name }}</span>
                            </div>
                            <button @click="deleteCategory(cat.id)" 
                                class="ml-2 text-current opacity-40 hover:opacity-100 transition-opacity text-lg">
                                &times;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>