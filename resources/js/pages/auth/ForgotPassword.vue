<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Восстановление пароля',
        description: 'Введите ваш email, чтобы получить ссылку для сброса пароля',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Восстановление пароля" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-[#FDFCFB] p-4">
        
        <div
            v-if="status"
            class="mb-4 w-full max-w-md text-center text-sm font-medium text-green-600 bg-green-50 p-4 rounded-xl border border-green-100"
        >
            {{ status }}
        </div>

        <div class="w-full max-w-md space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-[#E5E5E1]">
            <div class="flex flex-col space-y-2 text-center">
                <h1 class="text-2xl font-semibold tracking-tight text-[#3A3A3A]">Забыли пароль?</h1>
                <p class="text-sm text-[#8C8C8C]">
                    Ничего страшного. Введите адрес электронной почты, и мы отправим вам ссылку для создания нового.
                </p>
            </div>

            <Form v-bind="email.form()" v-slot="{ errors, processing }" class="space-y-6">
                <div class="grid gap-2">
                    <Label for="email" class="text-[#3A3A3A] font-medium">Электронная почта</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="off"
                        autofocus
                        placeholder="email@example.com"
                        class="bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353]"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="flex flex-col gap-4">
                    <Button
                        class="w-full bg-[#A66353] hover:bg-[#8E5346] text-white transition-colors py-6 text-base font-medium rounded-xl shadow-none"
                        :disabled="processing"
                        data-test="email-password-reset-link-button"
                    >
                        <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                        Отправить ссылку
                    </Button>

                    <div class="text-center text-sm text-[#8C8C8C]">
                        <span>Или вернуться к</span>
                        <TextLink 
                            :href="login()" 
                            class="ml-1 text-[#A66353] font-medium hover:underline underline-offset-4"
                        >
                            входу в систему
                        </TextLink>
                    </div>
                </div>
            </Form>
        </div>
    </div>
</template>