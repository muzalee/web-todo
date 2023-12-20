<template>
    <AppHead title="Home" />
    <NavBar />
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-5 sm:px-6">
            <div v-for="task in tasks" :key="task.id" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition duration-300 cursor-pointer flex flex-col">
                <div class="px-6 py-4 flex-1">
                    <div class="font-bold text-xl mb-2">{{ task.title }}</div>
                    <p class="text-gray-700 text-base">{{ task.description }}</p>
                    <p class="text-gray-700 text-base">Priority: {{ task.priorityName ?? '-' }}</p>
                    <p class="text-gray-700 text-base">Due Date: {{ task.dueDate ?? '-' }}</p>
                </div>
                <div class="px-6 pb-2">
                    <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">{{ task.archivedAt !== null && task.archivedAt !== '' ? 'Archived' : task.completedAt !== null && task.completedAt !== '' ? 'Completed' : 'Incomplete' }}</span>
                </div>
                <div class="flex justify-end">
                    <button @click.stop="markAsComplete(task)" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded mr-2">Mark as {{ task.completedAt !== null && task.completedAt !== '' ? 'Todo' : 'Complete' }}</button>
                    <button @click.stop="archiveRestore(task)" class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">{{ task.archivedAt !== null && task.archivedAt !== '' ? 'Restore' : 'Restore' }}</button>
                </div>
            </div>
        </div>


        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
                <a href="#" @click.prevent="currentPage > 1 && changePage(currentPage - 1)" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
                <a href="#" @click.prevent="currentPage < meta.last_page && changePage(currentPage + 1)" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
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
                        <a href="#" @click.prevent="currentPage > 1 && changePage(currentPage - 1)" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                           <span class="sr-only">Previous</span>
                           <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                       </a>

                       <a v-for="page in meta.last_page" :key="page" @click="changePage(page)" :class="{ 'bg-indigo-200': currentPage === page, 'text-gray-900': currentPage !== page }" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                           {{ page }}
                       </a>

                       <a href="#" @click.prevent="currentPage < meta.last_page && changePage(currentPage + 1)" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
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

onMounted(async () => {
    await changePage(currentPage.value);
});

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

const handleTaskClick = (task: Task) => {
};

const markAsComplete = (task: Task) => {
};

const archiveRestore = (task: Task) => {
};
</script>
