<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NavUser from '@/components/NavUser.vue';
import { Menu, X } from 'lucide-vue-next';

const page = usePage();
const isMobileMenuOpen = ref(false);

const isActive = (url: string) => {
    if (url === '/dashboard') return page.url === '/dashboard';
    return page.url.startsWith(url);
};
</script>

<template>
    <header class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="px-6">
            <div class="max-w-7xl mx-auto h-16 flex items-center justify-between relative">
                
                <div class="flex items-center md:hidden">
                    <button 
                        @click="isMobileMenuOpen = !isMobileMenuOpen" 
                        type="button" 
                        class="text-slate-500 hover:text-[#A66353] p-1 transition-colors cursor-pointer outline-hidden"
                    >
                        <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>

                <nav class="hidden md:flex items-center gap-6 lg:gap-8">
                    <Link href="/dashboard" 
                        :class="isActive('/dashboard') ? 'text-[#A66353]' : 'text-slate-400 hover:text-slate-900'" 
                        class="text-sm md:text-base font-bold uppercase tracking-wider transition-colors duration-150">
                        Обзор
                    </Link>

                    <Link href="/transactions" 
                        :class="isActive('/transactions') ? 'text-[#A66353]' : 'text-slate-400 hover:text-slate-900'" 
                        class="text-sm md:text-base font-bold uppercase tracking-wider transition-colors duration-150">
                        Операции
                    </Link>

                    <Link href="/directories" 
                        :class="isActive('/directories') ? 'text-[#A66353]' : 'text-slate-400 hover:text-slate-900'" 
                        class="text-sm md:text-base font-bold uppercase tracking-wider transition-colors duration-150">
                        Счета
                    </Link>

                    <Link href="/analytics" 
                        :class="isActive('/analytics') ? 'text-[#A66353]' : 'text-slate-400 hover:text-slate-900'" 
                        class="text-sm md:text-base font-bold uppercase tracking-wider transition-colors duration-150">
                        Аналитика
                    </Link>

                    <Link href="/budgets" 
                        :class="isActive('/budgets') ? 'text-[#A66353]' : 'text-slate-400 hover:text-slate-900'" 
                        class="text-sm md:text-base font-bold uppercase tracking-wider transition-colors duration-150">
                        Бюджеты
                    </Link>
                </nav>

                <div class="flex items-center">
                    <NavUser :user="page.props.auth.user" />
                </div>

            </div>
        </div>

        <div 
            v-if="isMobileMenuOpen" 
            class="md:hidden bg-white border-b border-slate-100 absolute top-full left-0 w-full shadow-md z-40 transition-all duration-200"
        >
            <nav class="flex flex-col p-4 space-y-1">
                <Link href="/dashboard" 
                    @click="isMobileMenuOpen = false"
                    :class="isActive('/dashboard') ? 'text-[#A66353] bg-[#A66353]/5' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" 
                    class="text-sm font-bold uppercase tracking-wider p-3 rounded-xl transition-all">
                    Обзор
                </Link>

                <Link href="/transactions" 
                    @click="isMobileMenuOpen = false"
                    :class="isActive('/transactions') ? 'text-[#A66353] bg-[#A66353]/5' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" 
                    class="text-sm font-bold uppercase tracking-wider p-3 rounded-xl transition-all">
                    Операции
                </Link>

                <Link href="/directories" 
                    @click="isMobileMenuOpen = false"
                    :class="isActive('/directories') ? 'text-[#A66353] bg-[#A66353]/5' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" 
                    class="text-sm font-bold uppercase tracking-wider p-3 rounded-xl transition-all">
                    Счета
                </Link>

                <Link href="/analytics" 
                    @click="isMobileMenuOpen = false"
                    :class="isActive('/analytics') ? 'text-[#A66353] bg-[#A66353]/5' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" 
                    class="text-sm font-bold uppercase tracking-wider p-3 rounded-xl transition-all">
                    Аналитика
                </Link>

                <Link href="/budgets" 
                    @click="isMobileMenuOpen = false"
                    :class="isActive('/budgets') ? 'text-[#A66353] bg-[#A66353]/5' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'" 
                    class="text-sm font-bold uppercase tracking-wider p-3 rounded-xl transition-all">
                    Бюджеты
                </Link>
            </nav>
        </div>
    </header>
</template>