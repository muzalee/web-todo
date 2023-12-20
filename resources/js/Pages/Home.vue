<template>
    <AppHead title="Home" />
    <NavBar />
    <div>
    <ul>
        <li v-for="task in tasks" :key="task.id">
        <h2>{{ task.title }}</h2>
        <p>{{ task.description }}</p>
        </li>
    </ul>
 </div>
</template>

<script setup lang="ts">
import AppHead from "@/components/AppHead.vue";
import NavBar from "@/components/NavBar.vue";
import { onMounted, ref } from 'vue';
import { Task } from '@/types/Task';
import axios from "axios";
import Swal from 'sweetalert2';

const tasks = ref<Task[]>([]);

onMounted(async () => {
    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        const response = await axios.get('api/task', { headers });
        tasks.value = response.data.data.map((item: any) => new Task(item));
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Oops..',
            text: error.response.data.message || error.response.data.error || 'Something went wrong!',
        });
    }
});
</script>
