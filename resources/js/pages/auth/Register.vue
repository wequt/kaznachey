<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineOptions({
    layout: {
        title: 'Создать аккаунт',
        description: 'Введите ваши данные ниже, чтобы создать личный кабинет',
    },
});
</script>

<template>
    <Head title="Регистрация" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-[#FDFCFB] p-4">
        
        <div class="w-full max-w-md space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-[#E5E5E1]">
            <div class="flex flex-col space-y-2 text-center">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3A3A3A]">Создание аккаунта</h1>
                <p class="text-sm text-[#8C8C8C]">Введите свои данные для регистрации</p>
            </div>

            <Form
                v-bind="store.form()"
                :reset-on-success="['password', 'password_confirmation']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-5"
            >
                <div class="grid gap-5">
                    <div class="grid gap-1">
                        <Label for="name" class="text-[#3A3A3A] font-medium">Имя</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            name="name"
                            placeholder="Иван Иванов"
                            class="bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353]"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-1">
                        <Label for="email" class="text-[#3A3A3A] font-medium">Электронная почта</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            :tabindex="2"
                            autocomplete="email"
                            name="email"
                            placeholder="email@example.com"
                            class="bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353]"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-1">
                        <Label for="password" class="text-[#3A3A3A] font-medium">Пароль</Label>
                        <PasswordInput
                            id="password"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            name="password"
                            placeholder="Минимум 8 символов"
                            class="bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353]"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-1">
                        <Label for="password_confirmation" class="text-[#3A3A3A] font-medium">Подтверждение пароля</Label>
                        <PasswordInput
                            id="password_confirmation"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            name="password_confirmation"
                            placeholder="Повторите пароль"
                            class="bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353]"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <Button
                        type="submit"
                        class="mt-2 w-full bg-[#A66353] hover:bg-[#8E5346] text-white transition-colors py-6 text-base font-medium rounded-xl shadow-none"
                        tabindex="5"
                        :disabled="processing"
                        data-test="register-user-button"
                    >
                        <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                        Зарегистрироваться
                    </Button>
                </div>

                <div class="text-center text-sm text-[#8C8C8C] mt-2">
                    Уже есть аккаунт?
                    <TextLink
                        :href="login()"
                        class="text-[#A66353] hover:underline underline-offset-4"
                        :tabindex="6"
                    >
                        Войти
                    </TextLink>
                </div>
            </Form>
        </div>
    </div>
</template>