<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import { Pencil, X, Check, ArrowLeftRight, Wallet, Plus } from 'lucide-vue-next';
import Header from '@/components/Header.vue';

const props = defineProps<{
    accounts: Array<{ id: number, name: string, balance: number, currency: string }>;
    categories: Array<{ id: number, name: string, type: string, icon: string }>;
}>();

const accountForm = useForm({ name: '', balance: 0, currency: 'RUB' });
const categoryForm = useForm({ name: '', type: 'expense', icon: 'folder' });
const transferForm = useForm({
    account_id: '',
    destination_account_id: '',
    amount: '',
    description: ''
});

const activeTab = ref<'all' | 'expense' | 'income'>('all');

const editingAccountId = ref<number | null>(null);
const editingAccountName = ref('');

const totalBalance = computed(() => {
    return props.accounts.reduce((sum, acc) => sum + Number(acc.balance), 0);
});

const filteredCategories = computed(() => {
    if (activeTab.value === 'all') return props.categories;
    return props.categories.filter(cat => cat.type === activeTab.value);
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

const submitTransfer = () => {
    transferForm.post('/directories/transfer', {
        onSuccess: () => transferForm.reset()
    });
};

const startEditAccount = (acc: { id: number, name: string }) => {
    editingAccountId.value = acc.id;
    editingAccountName.value = acc.name;
};

const cancelEditAccount = () => {
    editingAccountId.value = null;
    editingAccountName.value = '';
};

const saveAccountName = (id: number) => {
    if (!editingAccountName.value.trim()) return;
    router.put(`/directories/accounts/${id}`, { name: editingAccountName.value }, {
        onSuccess: () => editingAccountId.value = null,
        preserveScroll: true
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

const formatNumber = (amount: number) => {
    if (isNaN(amount)) return '0';
    return new Intl.NumberFormat('ru-RU').format(amount);
};
</script>

<template>
    <Head title="Счета и категории" />
    
    <Header />

    <div class="min-h-screen bg-[#FDFCFB] py-6 sm:py-8 px-4 sm:px-6 text-slate-900">
        <div class="max-w-7xl mx-auto space-y-6 sm:space-y-8">
            
            <div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800">Счета и категории</h1>
                <p class="text-sm sm:text-base text-slate-500 italic">Управление справочниками и кошельками</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                
                <div class="bg-white p-5 sm:p-8 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-3 mb-6">
                            <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                                <Wallet class="w-5 h-5 text-[#A66353]" /> Мои счета
                            </h3>
                            <div class="text-left sm:text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Общий баланс</span>
                                <span class="text-xl font-black text-[#A66353]">{{ formatNumber(totalBalance) }} ₽</span>
                            </div>
                        </div>

                        <form @submit.prevent="submitAccount" class="mb-6 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <input 
                                    v-model="accountForm.name" 
                                    type="text" 
                                    placeholder="Название счета"
                                    required
                                    class="w-full h-11 px-4 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50/30 text-slate-700 focus:border-[#A66353] focus:bg-white focus:ring-1 focus:ring-[#A66353] outline-hidden transition-all" 
                                />
                                <input 
                                    v-model="accountForm.balance" 
                                    type="number" 
                                    step="0.01" 
                                    placeholder="Начальный баланс"
                                    required
                                    class="w-full h-11 px-4 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50/30 text-slate-700 focus:border-[#A66353] focus:bg-white focus:ring-1 focus:ring-[#A66353] outline-hidden transition-all" 
                                />
                            </div>
                            <button 
                                type="submit" 
                                :disabled="accountForm.processing"
                                class="w-full h-11 bg-[#A66353] hover:bg-[#925344] text-white text-sm font-bold rounded-xl transition-colors cursor-pointer shadow-xs flex items-center justify-center gap-1.5"
                            >
                                <Plus class="w-4 h-4" /> Создать счет
                            </button>
                        </form>

                        <div class="space-y-2">
                            <div 
                                v-for="acc in accounts" 
                                :key="acc.id"
                                class="flex justify-between items-center p-4 bg-slate-50/40 hover:bg-slate-50/80 border border-slate-50 rounded-2xl group transition-all duration-200 gap-2"
                            >
                                <div class="flex-1 min-w-0">
                                    <div v-if="editingAccountId === acc.id" class="flex items-center gap-1.5">
                                        <input 
                                            v-model="editingAccountName" 
                                            type="text"
                                            class="w-full h-8 px-2 text-sm font-bold rounded-md border border-slate-200 bg-white text-slate-800 focus:border-[#A66353] outline-hidden" 
                                            @keyup.enter="saveAccountName(acc.id)"
                                        />
                                    </div>
                                    <span v-else class="font-bold text-slate-700 truncate block text-sm sm:text-base">{{ acc.name }}</span>
                                </div>

                                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                                    <div class="flex items-center gap-0.5 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <template v-if="editingAccountId === acc.id">
                                            <button @click="saveAccountName(acc.id)" class="text-green-600 hover:bg-green-50 p-1 rounded-md cursor-pointer">
                                                <Check class="w-4 h-4" />
                                            </button>
                                            <button @click="cancelEditAccount" class="text-slate-400 hover:bg-slate-100 p-1 rounded-md cursor-pointer">
                                                <X class="w-4 h-4" />
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button 
                                                @click="startEditAccount(acc)" 
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 cursor-pointer transition-colors"
                                                title="Редактировать"
                                            >
                                                <Pencil class="w-3.5 h-3.5" />
                                            </button>
                                            <button 
                                                @click="deleteAccount(acc.id)" 
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 cursor-pointer transition-colors"
                                                title="Удалить"
                                            >
                                                <X class="w-3.5 h-3.5" />
                                            </button>
                                        </template>
                                    </div>

                                    <span class="font-black text-slate-800 text-sm sm:text-base text-right min-w-15">
                                        {{ formatNumber(acc.balance) }} ₽
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 sm:p-8 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-6">Мои категории</h3>

                        <form @submit.prevent="submitCategory" class="mb-6 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <input 
                                    v-model="categoryForm.name" 
                                    type="text" 
                                    placeholder="Название категории"
                                    required
                                    class="sm:col-span-2 w-full h-11 px-4 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50/30 text-slate-700 focus:border-[#A66353] focus:bg-white focus:ring-1 focus:ring-[#A66353] outline-hidden transition-all" 
                                />
                                <select 
                                    v-model="categoryForm.type"
                                    class="w-full h-11 px-3 text-sm font-bold rounded-xl border border-slate-200 bg-slate-50/30 text-slate-700 focus:border-[#A66353] focus:bg-white focus:ring-1 focus:ring-[#A66353] outline-hidden transition-all cursor-pointer"
                                >
                                    <option value="expense">Расход</option>
                                    <option value="income">Доход</option>
                                </select>
                            </div>
                            <button 
                                type="submit" 
                                :disabled="categoryForm.processing"
                                class="w-full h-11 bg-[#A66353] hover:bg-[#925344] text-white text-sm font-bold rounded-xl transition-colors cursor-pointer shadow-xs flex items-center justify-center gap-1.5"
                            >
                                <Plus class="w-4 h-4" /> Добавить категорию
                            </button>
                        </form>

                        <div class="flex p-1 bg-slate-100/80 rounded-xl mb-4">
                            <button 
                                @click="activeTab = 'all'" 
                                type="button"
                                :class="activeTab === 'all' ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                class="flex-1 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer"
                            >
                                Все
                            </button>
                            <button 
                                @click="activeTab = 'expense'" 
                                type="button"
                                :class="activeTab === 'expense' ? 'bg-white text-red-600 shadow-xs' : 'text-slate-500 hover:text-red-500'"
                                class="flex-1 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer"
                            >
                                Расходы
                            </button>
                            <button 
                                @click="activeTab = 'income'" 
                                type="button"
                                :class="activeTab === 'income' ? 'bg-white text-green-600 shadow-xs' : 'text-slate-500 hover:text-green-500'"
                                class="flex-1 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer"
                            >
                                Доходы
                            </button>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <div 
                                v-for="cat in filteredCategories" 
                                :key="cat.id"
                                :class="cat.type === 'income' 
                                    ? 'bg-green-50/60 text-green-700 border-green-100/70' 
                                    : 'bg-red-50/60 text-red-700 border-red-100/70'"
                                class="flex items-center gap-2 px-3 py-1.5 text-xs sm:text-sm font-bold rounded-xl border transition-all"
                            >
                                <span class="truncate max-w-15 sm:max-w-none">{{ cat.name }}</span>
                                <button 
                                    @click="deleteCategory(cat.id)"
                                    class="text-current opacity-40 hover:opacity-100 transition-opacity text-base leading-none cursor-pointer"
                                >
                                    &times;
                                </button>
                            </div>

                            <div v-if="filteredCategories.length === 0" class="w-full text-center py-6 text-xs text-slate-400 italic">
                                Категорий в этой группе нет
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="bg-white p-5 sm:p-8 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <ArrowLeftRight class="w-5 h-5 text-amber-600" /> Внутренний перевод
                </h3>

                <form @submit.prevent="submitTransfer" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Счет списания</label>
                            <select 
                                v-model="transferForm.account_id" 
                                required
                                class="w-full h-11 px-3 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50/30 text-slate-700 focus:border-[#A66353] focus:bg-white focus:ring-1 focus:ring-[#A66353] outline-hidden transition-all cursor-pointer"
                            >
                                <option value="" disabled>Откуда перевести?</option>
                                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                    {{ acc.name }} ({{ formatNumber(acc.balance) }} ₽)
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Счет зачисления</label>
                            <select 
                                v-model="transferForm.destination_account_id" 
                                required
                                class="w-full h-11 px-3 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50/30 text-slate-700 focus:border-[#A66353] focus:bg-white focus:ring-1 focus:ring-[#A66353] outline-hidden transition-all cursor-pointer"
                            >
                                <option value="" disabled>Куда перевести?</option>
                                <option 
                                    v-for="acc in accounts" 
                                    :key="acc.id" 
                                    :value="acc.id"
                                    :disabled="acc.id === Number(transferForm.account_id)"
                                >
                                    {{ acc.name }} ({{ formatNumber(acc.balance) }} ₽)
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Сумма</label>
                            <input 
                                v-model="transferForm.amount" 
                                type="number" 
                                step="0.01" 
                                placeholder="0.00" 
                                required
                                class="w-full h-11 px-4 text-sm font-bold rounded-xl border border-slate-200 bg-slate-50/30 text-slate-700 focus:border-[#A66353] focus:bg-white focus:ring-1 focus:ring-[#A66353] outline-hidden transition-all" 
                            />
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Комментарий</label>
                            <input 
                                v-model="transferForm.description" 
                                type="text" 
                                placeholder="Например: Между счетами" 
                                class="w-full h-11 px-4 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50/30 text-slate-700 focus:border-[#A66353] focus:bg-white focus:ring-1 focus:ring-[#A66353] outline-hidden transition-all" 
                            />
                        </div>

                    </div>

                    <div class="flex justify-end pt-2">
                        <button 
                            type="submit" 
                            :disabled="transferForm.processing"
                            class="w-full sm:w-auto sm:px-6 h-11 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition-colors cursor-pointer shadow-xs flex items-center justify-center gap-2"
                        >
                            <ArrowLeftRight class="w-4 h-4" /> Выполнить перевод
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</template>

<style scoped>
:deep(b) {
    font-weight: 900;
}
</style>