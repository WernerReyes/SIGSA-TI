<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';

// import { register } from '@/routes';
// import { store } from '@/routes/login';
// import { request } from '@/routes/password';

import { Form } from '@inertiajs/vue3';




</script>

<template>
    <AuthBase
        title="Log in to your account"
        description="Enter your email and password below to log in"
    >
        <Head title="Log in" />

        <div
           
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            
        </div>
        
        <!-- v-bind="store.form()" -->
        <!-- v-bind:action="" -->
        <Form
        :action="'/login'"
        :reset-on-success="['password']"
        method="post"
        class="flex flex-col gap-6"
        v-slot="{ errors, processing }"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="text"
                        name="username"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="username"
                        placeholder="Email, DNI or Username"
                    />
                    <InputError :message="errors.username" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <!-- <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink> -->
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Password"
                    />
                    <!-- <InputError :message="errors.password" /> -->
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <!-- :disabled="processing" -->
                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="4"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    Log in
                </Button>
            </div>

            <!-- v-if="canRegister" -->
            <div
                class="text-center text-sm text-muted-foreground"
            >
                Don't have an account?
                <!-- <TextLink  :tabindex="5">Sign up</TextLink> -->
            </div>
        </Form>
    </AuthBase>
</template>
