<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';

const passwordInput = useTemplateRef('passwordInput');
</script>

<template>
    <div class="space-y-6 ">
        <Heading variant="small" title="Удаление аккаунта"
            description="Полное удаление вашего профиля и всех накопленных данных без возможности восстановления"
            class="text-[#3A3A3A] ml-7 mt-4" />

        <div class="space-y-4 rounded-2xl border border-red-100 bg-red-50/50 p-6">
            <div class="flex items-start gap-3 text-[#B91C1C]">
                <div class="mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m12 13 2-2-2-2-2 2 2 2Z" />
                        <circle cx="12" cy="12" r="10" />
                        <path d="m9 9 6 6" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <p class="font-bold text-sm uppercase tracking-wider">Внимание</p>
                    <p class="text-sm leading-relaxed">
                        Пожалуйста, будьте осторожны. Данное действие является необратимым. После удаления все ваши
                        финансовые записи, категории и настройки будут стерты навсегда.
                    </p>
                </div>
            </div>

            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive"
                        class="rounded-xl px-6 font-medium shadow-sm transition-all active:scale-[0.98]"
                        data-test="delete-user-button">
                        Удалить аккаунт
                    </Button>
                </DialogTrigger>

                <DialogContent class="sm:max-w-[425px] rounded-2xl border-[#E5E5E1] bg-white p-8">
                    <Form v-bind="ProfileController.destroy.form()" reset-on-success
                        @error="() => passwordInput?.focus()" :options="{
                            preserveScroll: true,
                        }" class="space-y-6" v-slot="{ errors, processing, reset, clearErrors }">
                        <DialogHeader class="space-y-4">
                            <DialogTitle class="text-xl font-bold text-[#3A3A3A]">
                                Подтвердите удаление
                            </DialogTitle>
                            <DialogDescription class="text-[#8C8C8C] leading-relaxed">
                                Для окончательного подтверждения операции введите ваш текущий пароль. Это необходимо для
                                обеспечения безопасности ваших данных.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-3">
                            <Label for="password" class="text-[#3A3A3A] font-semibold text-sm">
                                Ваш пароль
                            </Label>
                            <PasswordInput id="password" name="password" ref="passwordInput"
                                placeholder="Введите пароль для подтверждения"
                                class="bg-[#FDFDFB] border-[#D1D1CB] focus:ring-[#A66353] focus:border-[#A66353] rounded-lg h-12" />
                            <InputError :message="errors.password" />
                        </div>

                        <DialogFooter class="flex flex-col sm:flex-row gap-3 pt-2">
                            <DialogClose as-child>
                                <Button variant="secondary"
                                    class="flex-1 rounded-xl bg-[#F5F5F5] hover:bg-[#EBEBEB] text-[#3A3A3A] border-none h-12"
                                    @click="
                                        () => {
                                            clearErrors();
                                            reset();
                                        }
                                    ">
                                    Отмена
                                </Button>
                            </DialogClose>

                            <Button type="submit" variant="destructive" class="flex-1 rounded-xl h-12 font-medium"
                                :disabled="processing" data-test="confirm-delete-user-button">
                                <span v-if="!processing">Удалить навсегда</span>
                                <span v-else>Удаление...</span>
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>