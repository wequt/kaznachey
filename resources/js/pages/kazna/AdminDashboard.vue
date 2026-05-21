<script setup>
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import NavUser from '@/components/NavUser.vue';

const props = defineProps({
    stats: Object,
    users: Array
});

const toggleBlock = (user) => {
    if (user.role === 'admin') return;

    router.patch(`/admin/users/${user.id}/toggle`, {}, {
        preserveScroll: true
    });
};
</script>

<template>

    <Head title="Панель администратора" />

    <div class="min-h-screen bg-[#FDFCFB] p-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Панель администратора «Казначей»</h1>

                <div class="flex items-center">
                    <NavUser :user="$page.props.auth.user" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-500 uppercase">Пользователей</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ stats.usersCount }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-500 uppercase">Всего транзакций</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ stats.transactionsCount }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-500 uppercase">Общий оборот</p>
                    <p class="text-3xl font-bold text-amber-600">{{ stats.totalVolume }} ₽</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-800">Управление пользователями</h2>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                        <tr>
                            <th class="px-6 py-4">Имя</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4 text-center">Транзакций</th>
                            <th class="px-6 py-4">Статус</th>
                            <th class="px-6 py-4 text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ user.name }}
                                <span v-if="user.role === 'admin'"
                                    class="ml-2 text-[10px] bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded uppercase font-bold">
                                    Admin
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ user.email }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                <span v-if="user.role === 'admin'" class="text-gray-300">—</span>
                                <span v-else>{{ user.transactions_count }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="user.is_blocked ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                    class="px-3 py-1 rounded-full text-xs font-bold">
                                    {{ user.is_blocked ? 'Заблокирован' : 'Активен' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button v-if="user.role !== 'admin'" @click="toggleBlock(user)"
                                    class="px-4 py-2 rounded-lg text-sm font-semibold transition"
                                    :class="user.is_blocked ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-red-500 hover:bg-red-600 text-white'">
                                    {{ user.is_blocked ? 'Разблокировать' : 'Заблокировать' }}
                                </button>
                                <span v-else class="text-xs text-gray-400 italic">Системный аккаунт</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>