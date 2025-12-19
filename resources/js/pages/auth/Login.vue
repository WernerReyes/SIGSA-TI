<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useAppearance } from '@/composables/useAppearance';
import AuthBase from '@/layouts/AuthLayout.vue';

import { Form } from '@inertiajs/vue3';
import { toast, Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';

const { appearance } = useAppearance();


</script>

<template>
    <Toaster position="top-right" :theme="appearance" :closeButton="true" closeButtonPosition="top-right" />
    <AuthBase title="Iniciar sesión" description="Ingresa tus credenciales para acceder a tu cuenta">

        <Head title="Log in" />

        <div class="mb-4 text-center text-sm font-medium text-green-600">

        </div>

        <!-- v-bind="store.form()" -->
        <!-- v-bind:action="" -->
        <Form action="/login" :reset-on-success="['password']" method="post" class="flex flex-col gap-6"
        
            v-slot="{ errors, processing }" @error="toast.error($event.username)">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="text" name="username" required autofocus :tabindex="1"
                        default-value="isaac.ramos" autocomplete="username" placeholder="Email, DNI or Username" />

                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>

                    </div>
                    <Input id="password" type="password" name="password" required :tabindex="2"
                        default-value="isaac.ramos" autocomplete="current-password" placeholder="Password" />

                </div>


                <Button :disabled="processing" type="submit" class="mt-4 w-full" :tabindex="4" data-test="login-button">
                    <Spinner v-if="processing" />
                    Ingresar
                </Button>
            </div>
        </Form>



    </AuthBase>
</template>
