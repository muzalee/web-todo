<template>
    <div v-if="props.show" aria-hidden="true" class="modal overflow-y-auto overflow-x-hidden fixed z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Update Task
                </h3>
                <button @click="close" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <form @submit.prevent="updateTask" class="p-4 md:p-5" novalidate>
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
                    <div class="col-span-2">
                        <label for="tags" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tags (Optional)</label>
                        <Multiselect
                        v-model="tags"
                        :options="tags"
                        mode="tags"
                        placeholder="Select tags"
                        :createTag="true"
                        :searchable="true"
                        class="multiselect bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        />
                    </div>
                    <div class="col-span-2">
                        <label for="attachments" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Attachments (Optional)</label>
                        <input type="file" name="attachments[]" id="attachments" multiple accept=".svg, .png, .jpg, .mp4, .csv, .txt, .doc, .docx" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                    </div>
                    <div v-if="(props.task?.attachments ?? []).length > 0" class="pb-2 col-span-2">
                        <label class="block text-white text-sm font-bold mb-2">Existing Attachments:</label>
                        <div class="flex flex-wrap space-x-2">
                            <div v-for="attachment in props.task!.attachments" :key="attachment.id" class=" bg-white rounded-full px-3 py-1 text-sm font-semibold text-gray-700 flex items-center mb-1">
                                {{ attachment.name }}
                                <button @click.prevent="() => deleteAttachment(attachment.id)" class="ml-2">
                                    <XCircleIcon class="h-5 w-5" />
                                </button>
                                <a :href="attachment.path" download class="ml-2">
                                    <ArrowDownTrayIcon class="h-5 w-5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between">
                    <button @click.prevent="deleteTask" type="button" class="text-white inline-flex items-center bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        <TrashIcon class="h-3 w-3 mr-1" aria-hidden="true" />
                        Delete
                    </button>
                    <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <PencilIcon class="h-3 w-3 mr-1" aria-hidden="true" />
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
</template>

<script setup lang="ts">
import useVuelidate from '@vuelidate/core';
import { required } from '@vuelidate/validators';
import axios from 'axios';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';
import { PencilIcon, TrashIcon, ArrowDownTrayIcon, XCircleIcon } from '@heroicons/vue/24/outline';
import { Task } from '@/types/task';
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'

const props = defineProps({
    show: Boolean,
    task: Task,
});

const emit = defineEmits(['close', 'taskUpdated']);

const close = () => {
    emit('close');
}

const title = ref('');
const description = ref('');
const dueDate = ref('');
const priorityId = ref('');
const tags = ref<string[]>([]);

const rules1 = {
    title: { required },
    description: { required },
}

watch(() => props.task, (newTask) => {
    if (newTask) {
        title.value = newTask.title;
        description.value = newTask.description;
        dueDate.value = newTask.dueDate ?? '';
        priorityId.value = newTask.priorityId?.toString() ?? '';
        tags.value = newTask.tags.map(e => e.name);
    }
});

const v1$ = useVuelidate(rules1, { title, description })

const updateTask = async () => {
    v1$.value.$touch();

    if (v1$.value.$invalid) {
        return;
    }

    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        await axios.put(`/api/task/${props.task?.id ?? 0}`, {
            title: title.value,
            description: description.value,
            priority_id: priorityId.value === '' ? null : Number(priorityId.value),
            due_date: dueDate.value === '' ? null : dueDate.value,
        }, { headers });

        updateTag();
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Oops..',
            text: error.response.data.error || 'Something went wrong!',
        });
    }
};

const deleteTask = () => {
   Swal.fire({
       title: 'Are you sure?',
       text: "You won't be able to revert this!",
       icon: 'warning',
       showCancelButton: true,
       confirmButtonColor: '#3085d6',
       cancelButtonColor: '#d33',
       confirmButtonText: 'Yes, delete it!',
       cancelButtonText: 'No, cancel!',
   }).then((result) => {
       if (result.isConfirmed) {
           confirmDelete();
       }
   });
};

const confirmDelete = async () => {
   try {
       const token = localStorage.getItem('token');
       const headers = {
           'Authorization': `Bearer ${token}`
       };

       await axios.delete(`/api/task/${props.task?.id ?? 0}`, { headers });

       emit('taskUpdated');
       emit('close');

       Swal.fire({
           icon: 'success',
           title: 'Success',
           text: `Task has been deleted successfully.`,
       });
   } catch (error: any) {
       Swal.fire({
           icon: 'error',
           title: 'Oops..',
           text: error.response.data.error || 'Something went wrong!',
       });
   }
};

const updateTag = async () => {
    await addAttachment();
    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        const response = await axios.put(`api/task/${props.task?.id ?? 0}/tag`, {
            tags: tags.value,
        }, { headers });

        title.value = '';
        description.value = '';
        dueDate.value = '';
        priorityId.value = '';
        v1$.value.$reset();
        emit('taskUpdated');
        emit('close');

        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: `${response.data.data.title} has been updated successfully.`,
        });
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Oops..',
            text: error.response.data.error || 'Something went wrong!',
        });
    }
}

const addAttachment = async () => {
    const fileInput = document.getElementById('attachments') as HTMLInputElement;
    const files = fileInput.files;

    if (files?.length == 0) return;

    try {
        const formData = new FormData();

        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'multipart/form-data'
        };

        for (let i = 0; i < files!.length; i++) {
            formData.append('attachments[]', files![i]);
        }

        await axios.post(`api/task/${props.task?.id ?? 0}/attach`, formData, { headers });
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Oops..',
            text: error.response.data.error || 'Something went wrong!',
        });
    }
}

const deleteAttachment = (id: number) => {
   Swal.fire({
       title: 'Are you sure?',
       text: "You won't be able to revert this!",
       icon: 'warning',
       showCancelButton: true,
       confirmButtonColor: '#3085d6',
       cancelButtonColor: '#d33',
       confirmButtonText: 'Yes, delete it!',
       cancelButtonText: 'No, cancel!',
   }).then((result) => {
       if (result.isConfirmed) {
        confirmDeleteAttachment(id);
       }
   });
};

const confirmDeleteAttachment = async (id: number) => {
   try {
       const token = localStorage.getItem('token');
       const headers = {
           'Authorization': `Bearer ${token}`
       };

       await axios.delete(`/api/task/${props.task?.id ?? 0}/attach/${id}`, { headers });

       props.task!.attachments = props.task!.attachments.filter(attachment => attachment.id !== id);

       emit('taskUpdated');

       Swal.fire({
           icon: 'success',
           title: 'Success',
           text: `Attachment has been deleted successfully.`,
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
