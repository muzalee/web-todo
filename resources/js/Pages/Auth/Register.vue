<template>
    <AppHead title="Register" />
    <AuthLayout
    title="Register"
    description="Sign up to manage your tasks and to-dos with ease"
    >
        <div class="my-6 text-xl">
            TODO WEB APP
        </div>
        <form @submit.prevent="register" class="sm:w-2/3 w-full px-4 lg:px-0 mx-auto" novalidate>
            <div class="pb-2 pt-4">
                <input class="block w-full p-4 text-lg rounded-lg bg-black" type="text" name="name" placeholder="Name" v-model="userName">
                <div v-if="v$.userName.$error" class="text-red-500 text-sm mt-2 text-left">{{ v$.userName.$errors[0].$message }}</div>
            </div>
            <div class="pb-2 pt-4">
                <input class="block w-full p-4 text-lg rounded-lg bg-black" type="email" name="email" placeholder="Email" v-model="userEmail">
                <div v-if="v$.userEmail.$error" class="text-red-500 text-sm mt-2 text-left">{{ v$.userEmail.$errors[0].$message }}</div>
            </div>
            <div class="pb-2 pt-4">
                <input class="block w-full p-4 text-lg rounded-lg bg-black" type="password" name="password" placeholder="Password" v-model="userPassword">
                <div v-if="v$.userPassword.$error" class="text-red-500 text-sm mt-2 text-left">{{ v$.userPassword.$errors[0].$message }}</div>
            </div>
            <div class="pb-2 pt-4">
                <input class="block w-full p-4 text-lg rounded-lg bg-black" type="password" name="confirmPassword" placeholder="Confirm Password" v-model="userConfirmPassword">
                <div v-if="v$.userConfirmPassword.$error" class="text-red-500 text-sm mt-2 text-left">{{ v$.userConfirmPassword.$errors[0].$message }}</div>
            </div>
            <div class="pb-2 pt-4 mt-5">
                <button class="uppercase block w-full p-4 text-lg rounded-xl bg-indigo-500 hover:bg-indigo-600 focus:outline-none">sign up</button>
            </div>
            <div class="text-gray-400 hover:underline hover:text-gray-100 mt-3">
                <a href="/login">Already have an account? Sign in</a>
            </div>
        </form>
    </AuthLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AppHead from "@/components/AppHead.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import { useVuelidate } from '@vuelidate/core'
import { required, email, helpers, sameAs } from '@vuelidate/validators';

const userName = ref('');
const userEmail = ref('');
const userPassword = ref('');
const userConfirmPassword = ref('');

const passwordPattern = helpers.regex(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/)
const passwordPatternWithMessage = helpers.withMessage('Password must contain at least one uppercase letter, one lowercase letter, one digit, one special character and at least 8', passwordPattern);

const rules = {
    userEmail: { required, email },
    userPassword: { required, passwordPatternWithMessage },
    userName: { required },
    userConfirmPassword: { required, sameAs: sameAs(userPassword) }
};

const v$ = useVuelidate(rules, { userName, userEmail, userPassword, userConfirmPassword })

const register = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }

    alert(JSON.stringify({ email: userEmail.value, password: userPassword.value }, null, 2));
    userEmail.value = '';
    userPassword.value = '';
    v$.value.$reset();
};
</script>
