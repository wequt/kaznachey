<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Казначей" />

    <div class="min-h-screen bg-[#FDFBF7] text-[#3A3A3A] selection:bg-[#A66353] selection:text-white">
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-[#A66353]/5 blur-[120px]"></div>
            <div class="absolute top-[20%] right-[-5%] w-[30%] h-[30%] rounded-full bg-[#A66353]/10 blur-[100px]"></div>
        </div>

        <div class="relative z-10 flex flex-col min-h-screen">
            <header class="w-full max-w-7xl mx-auto px-6 py-8">
                <nav class="flex items-center justify-between bg-white/50 backdrop-blur-md border border-white/20 p-4 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-3 ml-2">
                        <span class="text-lg font-bold tracking-tight text-[#3A3A3A] uppercase">Казначей</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <template v-if="$page.props.auth.user">
                            <Link :href="dashboard()" class="px-5 py-2 text-sm font-semibold text-white bg-[#3A3A3A] rounded-xl hover:bg-[#2A2A2A] transition-all">
                                Войти в кабинет
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="login()" class="px-4 py-2 text-sm font-medium hover:text-[#A66353] transition-colors">Войти</Link>
                            <Link v-if="canRegister" :href="register()" class="px-5 py-2 text-sm font-semibold text-white bg-[#A66353] rounded-xl hover:bg-[#8E5346] shadow-md shadow-brown-500/10 transition-all">
                                Создать аккаунт
                            </Link>
                        </template>
                    </div>
                </nav>
            </header>

            <main class="flex-1 flex flex-col items-center justify-center px-6 py-12">
                <div class="max-w-4xl text-center space-y-10">

                    <h1 class="text-6xl md:text-8xl font-black tracking-tighter text-[#3A3A3A] leading-[0.9]">
                        Твои деньги <br/>
                        <span class="text-transparent bg-clip-text bg-linear-to-r from-[#A66353] to-[#D4A398]">под контролем.</span>
                    </h1>

                    <p class="text-lg md:text-xl text-[#8C8C8C] max-w-xl mx-auto font-medium">
                        Личный учет финансов, который помогает копить, анализировать и планировать будущее без лишних усилий.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 pt-6">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="register()"
                            class="group relative px-10 py-5 rounded-2xl bg-[#3A3A3A] text-white text-lg font-bold hover:bg-[#2A2A2A] transition-all overflow-hidden"
                        >
                            <span class="relative z-10">Попробовать бесплатно</span>
                            <div class="absolute inset-0 bg-linear-to-r from-[#A66353] to-[#C48477] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </Link>
                    </div>
                </div>

                <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-5xl w-full opacity-60 grayscale hover:grayscale-0 transition-all duration-700">
                    <div class="h-32 bg-white rounded-3xl border border-[#E5E5E1] p-4 flex flex-col justify-between shadow-sm">
                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">↓</div>
                        <div class="h-2 w-16 bg-gray-100 rounded"></div>
                        <div class="h-4 w-24 bg-gray-200 rounded"></div>
                    </div>
                    <div class="h-32 bg-white rounded-3xl border border-[#E5E5E1] p-4 flex flex-col justify-between shadow-sm translate-y-4">
                        <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center">↑</div>
                        <div class="h-2 w-16 bg-gray-100 rounded"></div>
                        <div class="h-4 w-24 bg-gray-200 rounded"></div>
                    </div>
                    <div class="h-32 bg-white rounded-3xl border border-[#E5E5E1] p-4 flex flex-col justify-between shadow-sm">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">📊</div>
                        <div class="h-2 w-16 bg-gray-100 rounded"></div>
                        <div class="h-4 w-24 bg-gray-200 rounded"></div>
                    </div>
                    <div class="h-32 bg-white rounded-3xl border border-[#E5E5E1] p-4 flex flex-col justify-between shadow-sm translate-y-4">
                        <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">⭐</div>
                        <div class="h-2 w-16 bg-gray-100 rounded"></div>
                        <div class="h-4 w-24 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>