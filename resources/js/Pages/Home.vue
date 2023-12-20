<template>
    <AppHead title="Home" />
    <NavBar @taskCreated="changePage(1, true)" />
    <SortTask @sortOptionSelected="updateSortBy" @sortDirectionToggled="updateSortOrder" />
    <FilterTask @searchTask="searchTask" />
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-5 sm:px-6">
            <div v-for="task in tasks" :key="task.id" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition duration-300 cursor-pointer flex flex-col" @click="handleTaskClick(task)">
                <div class="px-6 py-4 flex-1">
                    <div class="font-bold text-xl mb-1">{{ task.title }}</div>
                    <p class="text-gray-800 text-lg">{{ task.description }}</p>
                    <p class="text-gray-700 text-base">Priority: {{ task.priorityName ?? '-' }}</p>
                    <p class="text-gray-700 text-base">Due Date: {{ task.dueDate ?? '-' }}</p>
                    <p class="text-gray-700 text-base">Status: {{ task.archivedAt !== null && task.archivedAt !== '' ? 'Archived' : task.completedAt !== null && task.completedAt !== '' ? 'Completed' : 'Incomplete' }}</p>
                </div>
                <div v-if="task.tags.length > 0" class="px-6 pb-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tags:</label>
                    <div class="flex flex-wrap space-x-2">
                        <span v-for="tag in task.tags" :key="tag.id" class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mb-1">
                            {{ tag.name }}
                        </span>
                    </div>
                </div>
                <div v-if="task.attachments.length > 0" class="px-6 pb-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Attachments:</label>
                    <div class="flex flex-wrap space-x-2">
                        <span v-for="attachment in task.attachments" :key="attachment.id" class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mb-1">
                            {{ attachment.name }}
                        </span>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button @click.stop="markAsComplete(task)" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded mr-2">Mark as {{ task.completedAt !== null && task.completedAt !== '' ? 'Todo' : 'Complete' }}</button>
                    <button @click.stop="archiveRestore(task)" class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">{{ task.archivedAt !== null && task.archivedAt !== '' ? 'Restore' : 'Archive' }}</button>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
                <a href="#" @click.prevent="currentPage > 1 && changePage(currentPage - 1, false)" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
                <a href="#" @click.prevent="currentPage < meta.last_page && changePage(currentPage + 1, false)" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
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
                        <a href="#" @click.prevent="currentPage > 1 && changePage(currentPage - 1, false)" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                           <span class="sr-only">Previous</span>
                           <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                       </a>

                       <a v-for="page in meta.last_page" :key="page" @click="changePage(page, false)" :class="{ 'bg-indigo-200': currentPage === page, 'text-gray-900': currentPage !== page }" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                           {{ page }}
                       </a>

                       <a href="#" @click.prevent="currentPage < meta.last_page && changePage(currentPage + 1, false)" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                           <span class="sr-only">Next</span>
                           <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                       </a>
                   </nav>
                </div>
            </div>
        </div>
    </div>

    <UpdateTaskModal :show="showUpdateModal" @close="toggleUpdateModal" @taskUpdated="changePage(1, true)" :task="selectedTask!" />
</template>

<script setup lang="ts">
import AppHead from "@/components/AppHead.vue";
import NavBar from "@/components/NavBar.vue";
import { onMounted, ref } from 'vue';
import { Task } from '@/types/task';
import axios from "axios";
import Swal from 'sweetalert2';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';
import UpdateTaskModal from '@/components/UpdateTaskModal.vue';
import SortTask from '@/components/SortTask.vue';
import FilterTask from '@/components/FilterTask.vue';

const tasks = ref<Task[]>([]);
const currentPage = ref(1);
const meta = ref({ total: 0, last_page: 1 });
const showUpdateModal = ref(false);
const selectedTask = ref<Task | null>(null);
const sort = ref<String | null>(null);
const order = ref<String | null>(null);
let filterData: FilterData | null = null;

onMounted(async () => {
    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        await axios.post('api/archive-task/delete', {}, { headers });
    } catch (_) {}

    changePage(currentPage.value, false);
});

const changePage = (page: number, isRefresh: boolean) => {
    currentPage.value = page;
    if (isRefresh) {
        sort.value = null;
        order.value = null;
        filterData = null;
    }
    getTasks();
};

const getTasks = async () => {
    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        const response = await axios.get('api/task', { headers,
            params: {
                page: currentPage.value,
                sort: sort.value,
                order: order.value,
                completed_date_start: filterData?.completedDateStart,
                completed_date_end: filterData?.completedDateEnd,
                priority: filterData?.priority === '' ? null : filterData?.priority,
                due_date_start: filterData?.dueDateStart,
                due_date_end: filterData?.dueDateEnd,
                archived_date_start: filterData?.archivedDateStart,
                archived_date_end: filterData?.archivedDateEnd,
                search: filterData?.searchQuery,
            }
        });
        tasks.value = response.data.data.map((item: any) => new Task(item));
        meta.value = response.data.meta;
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Oops..',
            text: error.response.data.message || error.response.data.error || 'Something went wrong!',
        });
    }
}

const handleTaskClick = (task: Task) => {
    selectedTask.value = task;
    toggleUpdateModal();
};

const markAsComplete = async (task: Task) => {
    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        await axios.put(`/api/task/${task.id}/complete`, {
            is_completed: !task.completedAt
        }, { headers });

        changePage(currentPage.value, false);
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Oops..',
            text: error.response.data.message || error.response.data.error || 'Something went wrong!',
        });
    }
};

const archiveRestore = async (task: Task) => {
    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        await axios.put(`/api/task/${task.id}/archive`, {
            is_archived: !task.archivedAt
        }, { headers });

        changePage(currentPage.value, false);
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Oops..',
            text: error.response.data.message || error.response.data.error || 'Something went wrong!',
        });
    }
};

const toggleUpdateModal = () => {
    showUpdateModal.value = !showUpdateModal.value;
}

const updateSortBy = (val: string) => {
   sort.value = val;
   changePage(1, false)
};

const updateSortOrder = (val: string) => {
   order.value = val;
   changePage(1, false)
};

const searchTask = (val: FilterData) => {
   filterData = val;
   changePage(1, false)
};
</script>
