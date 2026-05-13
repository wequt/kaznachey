<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Вход в аккаунт',
        description: 'Введите вашу почту и пароль для входа в систему',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Вход" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-[#FDFCFB] p-4">
        
        <div
            v-if="status"
            class="mb-4 w-full max-w-md text-center text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg border border-green-100"
        >
            {{ status }}
        </div>

        <div class="w-full max-w-md space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-[#E5E5E1]">
            <div class="flex flex-col space-y-2 text-center">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3A3A3A]">Вход в систему</h1>
                <p class="text-sm text-[#8C8C8C]">Добро пожаловать</p>
            </div>

            <Form
                v-bind="store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-5"
            >
                <div class="grid gap-5">
                    <div class="grid gap-1">
                        <Label for="email" class="text-[#3A3A3A] font-medium">Электронная почта</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="email@example.com"
                            class="bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353]"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-1">
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-[#3A3A3A] font-medium">Пароль</Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                class="text-xs text-[#A66353] hover:underline"
                                :tabindex="5"
                            >
                                Забыли пароль?
                            </TextLink>
                        </div>
                        <PasswordInput
                            id="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="Введите пароль"
                            class="bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353]"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="flex items-center">
                        <Label for="remember" class="flex items-center space-x-2 cursor-pointer group">
                            <Checkbox 
                                id="remember" 
                                name="remember" 
                                :tabindex="3"
                                class="border-[#D1D1CB] data-[state=checked]:bg-[#A66353] data-[state=checked]:border-[#A66353]"
                            />
                            <span class="text-sm text-[#3A3A3A] group-hover:text-[#A66353] transition-colors">Запомнить меня</span>
                        </Label>
                    </div>

                    <Button
                        type="submit"
                        class="mt-2 w-full bg-[#A66353] hover:bg-[#8E5346] text-white transition-colors py-6 text-base font-medium rounded-xl shadow-none"
                        :tabindex="4"
                        :disabled="processing"
                        data-test="login-button"
                    >
                        <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                        Войти
                    </Button>
                </div>

                <div
                    class="text-center text-sm text-[#8C8C8C] mt-2"
                    v-if="canRegister"
                >
                    Нет аккаунта?
                    <TextLink 
                        :href="register()" 
                        class="text-[#A66353] font-medium hover:underline underline-offset-4"
                        :tabindex="6"
                    >
                        Зарегистрироваться
                    </TextLink>
                </div>
            </Form>
        </div>
    </div>
</template>