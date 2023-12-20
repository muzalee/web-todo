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

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
                <a href="#" @click="changePage(currentPage - 1)" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
                <a href="#" @click="changePage(currentPage + 1)" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                   <p class="text-sm text-gray-700">
                       <span class="font-medium">{{ meta.total }}</span>
                       {{ ' ' }}
                       results
                   </p>
                </div>
                <div>
                   <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                       <a href="#" @click="changePage(1)" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                           <span class="sr-only">Previous</span>
                           <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                       </a>

                       <a v-for="page in meta.last_page" :key="page" @click="changePage(page)" :class="{ 'bg-indigo-600': currentPage === page, 'text-gray-900': currentPage !== page }" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                           {{ page }}
                       </a>

                       <a href="#" @click="changePage(meta.last_page)" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                           <span class="sr-only">Next</span>
                           <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                       </a>
                   </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import AppHead from "@/components/AppHead.vue";
import NavBar from "@/components/NavBar.vue";
import { onMounted, ref } from 'vue';
import { Task } from '@/types/Task';
import axios from "axios";
import Swal from 'sweetalert2';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

const tasks = ref<Task[]>([]);
const currentPage = ref(1);
const meta = ref({ total: 0, last_page: 1 });

const changePage = async (page: number) => {
    currentPage.value = page;
    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        const response = await axios.get('api/task', { headers, params: { page: currentPage.value } });
        tasks.value = response.data.data.map((item: any) => new Task(item));
        meta.value = response.data.meta;
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Oops..',
            text: error.response.data.message || error.response.data.error || 'Something went wrong!',
        });
    }
};

onMounted(async () => {
    await changePage(currentPage.value);
});
</script>
