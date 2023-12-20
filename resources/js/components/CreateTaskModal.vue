<template>
    <div v-if="show" aria-hidden="true" class="modal overflow-y-auto overflow-x-hidden fixed z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Create New Task
                </h3>
                <button @click="close" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <form @submit.prevent="createTask" class="p-4 md:p-5" novalidate>
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2">
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
                        <input v-model="title" type="text" name="title" id="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <div v-if="v1$.title.$error" class="text-red-500 text-sm mt-2 text-left">{{ v1$.title.$errors[0].$message }}</div>
                    </div>
                    <div class="col-span-2">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                        <textarea v-model="description" id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                        <div v-if="v1$.description.$error" class="text-red-500 text-sm mt-2 text-left">{{ v1$.description.$errors[0].$message }}</div>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="dueDate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Due Date</label>
                        <input v-model="dueDate" type="date" name="dueDate" id="dueDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" :min="new Date().toISOString().split('T')[0]">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="priority" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Priority (Optional)</label>
                        <select v-model="priorityId" id="priority" name="priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option value="" selected>Select category</option>
                            <option value="1">Urgent</option>
                            <option value="2">High</option>
                            <option value="3">Normal</option>
                            <option value="4">Low</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 ml-auto">
                        <PlusIcon class="h-3 w-3 mr-1" aria-hidden="true" />
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</template>

<script setup lang="ts">
import useVuelidate from '@vuelidate/core';
import { required, email } from '@vuelidate/validators';
import axios from 'axios';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { PlusIcon } from '@heroicons/vue/24/outline'

defineProps({
    show: Boolean,
});

const emit = defineEmits(['close', 'taskCreated']);

const close = () => {
    emit('close');
}

const title = ref('');
const description = ref('');
const dueDate = ref('');
const priorityId = ref('');

const rules1 = {
    title: { required },
    description: { required },
}

const v1$ = useVuelidate(rules1, { title, description })

const createTask = async () => {
    v1$.value.$touch();

    if (v1$.value.$invalid) {
        return;
    }

    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        const response = await axios.post('/api/task', {
            title: title.value,
            description: description.value,
            priority_id: priorityId.value === '' ? null : Number(priorityId.value),
            due_date: dueDate.value === '' ? null : dueDate.value,
        }, { headers });

        title.value = '';
        description.value = '';
        dueDate.value = '';
        priorityId.value = '';
        v1$.value.$reset();
        emit('taskCreated');
        emit('close');

        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: `${response.data.data.title} has been created successfully.`,
        });
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Oops..',
            text: error.response.data.error || 'Something went wrong!',
        });
    }
};
</script>
