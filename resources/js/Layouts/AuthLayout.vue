<template>
    <section class="min-h-screen flex items-stretch text-white ">
        <div class="lg:flex w-1/2 hidden bg-gray-500 bg-no-repeat bg-cover relative items-center">
            <div class="w-full px-24 z-10">
                <h1 class="text-5xl font-bold text-left tracking-wide"> {{ title }} </h1>
                <p class="text-3xl my-4"> {{ description }} </p>
            </div>
        </div>
        <div class="lg:w-1/2 w-full flex items-center justify-center text-center md:px-16 px-0 z-0 bg-gray-900">
            <div class="w-full py-6 z-20">
                <slot />
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
defineProps({
    title: String,
    description: String,
});
</script>
