<template>
    <AppHead title="Login" />
    <AuthLayout
    title="Login"
    description="Organize your day. Log in to manage your to-dos effortlessly!"
    >
        <div class="my-6 text-xl">
            TODO WEB APP
        </div>
        <form @submit.prevent="login" class="sm:w-2/3 w-full px-4 lg:px-0 mx-auto" novalidate>
            <div class="pb-2 pt-4">
                <input class="block w-full p-4 text-lg rounded-lg bg-black" type="email" name="email" placeholder="Email" v-model="userEmail">
                <div v-if="v$.userEmail.$error" class="text-red-500 text-sm mt-2 text-left">{{ v$.userEmail.$errors[0].$message }}</div>
            </div>
            <div class="pb-2 pt-4">
                <input class="block w-full p-4 text-lg rounded-lg bg-black" type="password" name="password" placeholder="Password" v-model="userPassword">
                <div v-if="v$.userPassword.$error" class="text-red-500 text-sm mt-2 text-left">{{ v$.userPassword.$errors[0].$message }}</div>
            </div>
            <div class="pb-2 pt-4 mt-5">
                <button class="uppercase block w-full p-4 text-lg rounded-xl bg-indigo-500 hover:bg-indigo-600 focus:outline-none">sign in</button>
            </div>
            <div class="text-gray-400 hover:underline hover:text-gray-100 mt-3">
                <a href="/register">Don't have an account? Sign up</a>
            </div>
        </form>
    </AuthLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AppHead from "@/components/AppHead.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import { useVuelidate } from '@vuelidate/core'
import { required, email } from '@vuelidate/validators'
import Swal from 'sweetalert2';
import axios from 'axios';

const userEmail = ref('');
const userPassword = ref('');

const rules = {
    userEmail: { required, email },
    userPassword: { required }
}

const v$ = useVuelidate(rules, { userEmail, userPassword })

const login = async () => {
    v$.value.$touch();

    if (v$.value.$invalid) {
        return;
    }

    try {
        const response = await axios.post('/api/auth/login', {
            email: userEmail.value,
            password: userPassword.value,
        });

        localStorage.setItem('user_name', response.data.data.name);
        localStorage.setItem('token', response.data.data.token);
        localStorage.setItem('token_expires_at', response.data.data.token_expires_at);

        window.location.href = '/home';

        userEmail.value = '';
        userPassword.value = '';
        v$.value.$reset();
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: error.response.data.error || 'Something went wrong!',
        });
    }
};
</script>
