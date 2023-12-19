<template>
    <Head>
        <title>{{ props.title ?? 'TODO' }}</title>
    </Head>
</template>

<script setup lang="ts">
import { Head } from "@inertiajs/vue3";

const props = defineProps<{
    title?: string
}>();
</script>
