<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { dashboard } from '@/routes';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Настройки профиля',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const userInitials = computed(() => {
    return user.value.name
        ? user.value.name.split(' ').map((n: string) => n[0]).join('').toUpperCase().substring(0, 2)
        : 'УЗ';
});
</script>

<template>

    <Head title="Настройки профиля" />

    <div class="max-w-2xl mx-auto space-y-6 py-8 px-4">

        <div class="flex items-center justify-start">
            <Link :href="dashboard()"
                class="group flex items-center gap-2 text-sm font-medium text-[#8C8C8C] hover:text-[#A66353] transition-colors">
                <div
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-white border border-[#E5E5E1] group-hover:border-[#A66353] transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </div>
            </Link>
        </div>

        <section class="bg-white rounded-2xl shadow-sm border border-[#E5E5E1] overflow-hidden">
            <div class="bg-[#FDFBF7] border-b border-[#E5E5E1] p-8 flex items-center gap-6">
                <div
                    class="h-20 w-20 rounded-full bg-[#A66353] flex items-center justify-center text-white text-2xl font-bold shadow-inner">
                    {{ userInitials }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#3A3A3A]">{{ user.name }}</h2>
                    <p class="text-sm text-[#8C8C8C]">Личный профиль в системе «Казначей»</p>
                    <div v-if="user.email_verified_at"
                        class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                        Аккаунт подтвержден
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="mb-8">
                    <Heading variant="small" title="Личная информация"
                        description="Эти данные будут использоваться для связи и отображения в системе"
                        class="text-[#3A3A3A]" />
                </div>

                <Form v-bind="ProfileController.update.form()" class="space-y-6" v-slot="{ errors, processing }">
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="name" class="text-[#3A3A3A] font-semibold text-sm">Ваше имя</Label>
                            <Input id="name"
                                class="h-11 bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353] rounded-lg transition-all"
                                name="name" :default-value="user.name" required autocomplete="name"
                                placeholder="Иван Иванов" />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email" class="text-[#3A3A3A] font-semibold text-sm">Электронная почта</Label>
                            <Input id="email" type="email"
                                class="h-11 bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353] rounded-lg transition-all"
                                name="email" :default-value="user.email" required autocomplete="username"
                                placeholder="mail@example.com" />
                            <InputError :message="errors.email" />
                        </div>
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at"
                        class="bg-amber-50 p-4 rounded-xl border border-amber-100 flex gap-3">
                        <div class="text-amber-500 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-amber-800 font-medium">Почта не подтверждена</p>
                            <Link :href="send()" as="button"
                                class="text-xs text-amber-700 underline underline-offset-2 hover:text-amber-900 transition-colors">
                                Отправить ссылку для подтверждения еще раз
                            </Link>
                        </div>
                    </div>

                    <div class="flex items-center pt-4">
                        <Button
                            class="bg-[#A66353] hover:bg-[#8E5346] text-white px-10 py-6 rounded-xl shadow-md shadow-brown-500/10 transition-all active:scale-[0.98]"
                            :disabled="processing" data-test="update-profile-button">
                            <Spinner v-if="processing" class="mr-2" />
                            Сохранить изменения
                        </Button>
                    </div>
                </Form>
            </div>
        </section>

        <div class="bg-red-50/30 rounded-2xl border border-red-100/50 p-1">
            <DeleteUser />
        </div>
    </div>
</template>