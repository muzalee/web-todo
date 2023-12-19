<template>
    <AppHead title="Login" />
    <AuthLayout
    title="Login"
    description="Organize your day – Log in to manage your to-dos effortlessly!"
    >
        <div class="my-6 text-xl">
            TODO WEB APP
        </div>
        <form action="" class="sm:w-2/3 w-full px-4 lg:px-0 mx-auto">
            <div class="pb-2 pt-4">
                <input class="block w-full p-4 text-lg rounded-lg bg-black" type="email" name="email" id="email" placeholder="Email" required>
            </div>
            <div class="pb-2 pt-4">
                <input class="block w-full p-4 text-lg rounded-lg bg-black" type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <div class="text-right text-gray-400 hover:underline hover:text-gray-100">
                <a href="#">Forgot your password?</a>
            </div>
            <div class="pb-2 pt-4 mt-5">
                <button class="uppercase block w-full p-4 text-lg rounded-xl bg-indigo-500 hover:bg-indigo-600 focus:outline-none">sign in</button>
            </div>
        </form>
    </AuthLayout>
</template>

<script setup lang="ts">
import AppHead from "@/components/AppHead.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";
</script>
