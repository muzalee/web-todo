<template>
    <div class="w-full px-4 py-2">
        <button @click="showFilter = !showFilter" class="flex justify-between items-center w-full px-4 py-2 text-sm font-medium text-left text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
            <span>Filter</span>
            <ChevronUpIcon v-if="showFilter" class="w-4 h-4 text-gray-500 transition-transform duration-150 ease-in-out" />
            <ChevronDownIcon v-else class="w-4 h-4 text-gray-500 transition-transform duration-150 ease-in-out" />
        </button>

        <div v-show="showFilter" class="w-full px-4 py-2 text-sm text-gray-500">
            <div class="flex flex-col space-y-4 md:flex-row md:space-y-0 md:space-x-4">
            <div class="flex flex-col w-full md:w-1/3">
                <label for="completedDate">Completed Date</label>
                <div class="flex space-x-4">
                    <input type="date" id="completedDateStart" v-model="completedDateStart" class="border-2 border-gray-300 p-2 rounded-md">
                    <input type="date" id="completedDateEnd" v-model="completedDateEnd" :min="completedDateStart || undefined" class="border-2 border-gray-300 p-2 rounded-md">
                </div>
            </div>

            <div class="flex flex-col w-full md:w-1/3">
                <label for="priority">Priority</label>
                <select id="priority" v-model="priority" class="border-2 border-gray-300 p-2 rounded-md">
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="normal">Normal</option>
                    <option value="low">Low</option>
                    <option value="" selected>None</option>
                </select>
            </div>

            <div class="flex flex-col w-full md:w-1/3">
                <label for="dueDate">Due Date</label>
                <div class="flex space-x-4">
                    <input type="date" id="dueDateStart" v-model="dueDateStart" class="border-2 border-gray-300 p-2 rounded-md">
                    <input type="date" id="dueDateEnd" v-model="dueDateEnd" :min="dueDateStart || undefined" class="border-2 border-gray-300 p-2 rounded-md">
                </div>
            </div>
            </div>

            <div class="flex flex-col space-y-4 md:flex-row md:space-y-0 md:space-x-4">
            <div class="flex flex-col w-full md:w-1/3">
                <label for="archivedDate">Archived Date</label>
                <div class="flex space-x-4">
                    <input type="date" id="archivedDateStart" v-model="archivedDateStart" class="border-2 border-gray-300 p-2 rounded-md">
                    <input type="date" id="archivedDateEnd" v-model="archivedDateEnd" :min="archivedDateStart || undefined" class="border-2 border-gray-300 p-2 rounded-md">
                </div>
            </div>

            <div class="flex flex-col w-full md:w-1/3">
                <label for="searchQuery">Search Query</label>
                <div class="flex">
                <input type="text" id="searchQuery" v-model="searchQuery" class="border-2 border-gray-300 p-2 rounded-md flex-1">
                </div>
            </div>

            <div class="flex flex-col w-full md:w-1/3">
                <label for="searchButton" class="invisible md:visible">&nbsp;</label>
                <div class="flex">
                    <button @click="search" class="bg-indigo-500 text-white py-2 px-4 rounded-md ml-2">Search</button>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';
import Swal from 'sweetalert2';

const showFilter = ref(false);
const completedDateStart = ref<string | null>(null);
const completedDateEnd = ref<string | null>(null);
const priority = ref('');
const dueDateStart = ref<string | null>(null);
const dueDateEnd = ref<string | null>(null);
const archivedDateStart = ref<string | null>(null);
const archivedDateEnd = ref<string | null>(null);
const searchQuery = ref('');

const emit = defineEmits(['searchTask']);

watch(completedDateStart, () => {
    if (completedDateEnd.value && completedDateStart.value && completedDateEnd.value < completedDateStart.value) {
        completedDateEnd.value = completedDateStart.value;
    }
});

watch(dueDateStart, () => {
    if (dueDateEnd.value && dueDateStart.value && dueDateEnd.value < dueDateStart.value) {
        dueDateEnd.value = dueDateStart.value;
    }
});

watch(archivedDateStart, () => {
    if (archivedDateEnd.value && archivedDateStart.value && archivedDateEnd.value < archivedDateStart.value) {
        archivedDateEnd.value = archivedDateStart.value;
    }
});

const search = () => {
    const isArchivedDateSelected = archivedDateStart.value || archivedDateEnd.value;
    const isCompletedDateSelected = completedDateStart.value || completedDateEnd.value;
    const isDueDateSelected = dueDateStart.value || dueDateEnd.value;

    if (isArchivedDateSelected && (!archivedDateStart.value || !archivedDateEnd.value)) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete',
            text: 'Both archived date range must be selected.',
        });
        return;
    }

    if (isCompletedDateSelected && (!completedDateStart.value || !completedDateEnd.value)) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete',
            text: 'Both completed date range must be selected.',
        });
        return;
    }

    if (isDueDateSelected && (!dueDateStart.value || !dueDateEnd.value)) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete',
            text: 'Both due date range must be selected.',
        });
        return;
    }

    emit('searchTask', {
        completedDateStart: completedDateStart.value,
        completedDateEnd: completedDateEnd.value,
        priority: priority.value,
        dueDateStart: dueDateStart.value,
        dueDateEnd: dueDateEnd.value,
        archivedDateStart: archivedDateStart.value,
        archivedDateEnd: archivedDateEnd.value,
        searchQuery: searchQuery.value,
    } as FilterData);
};
</script>
