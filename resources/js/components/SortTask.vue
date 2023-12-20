<template>
    <div class="flex justify-end items-center w-full px-6 py-2">
      <Menu as="div" class="relative mr-3">
            <div>
            <MenuButton class="relative flex max-w-xs items-center font-bold focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2">
                Sort By
            </MenuButton>
            </div>
            <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
            >
            <MenuItems
                class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            >
                <MenuItem v-for="option in sortingOptions" :key="option.val">
                <a href="#" @click="() => selectSortOption(option.val)" class="block px-4 py-2 text-sm text-gray-700">{{ option.name }}</a>
                </MenuItem>
            </MenuItems>
            </transition>
        </Menu>

        <button @click="toggleSortDirection" type="button" class="relative rounded-full bg-gray-800 p-1 text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
            <span class="absolute -inset-0.5" />
            <span class="sr-only">Add Task</span>
            <ArrowUpIcon v-if="sortDirection === 'asc'" class="block h-6 w-6" aria-hidden="true" />
            <ArrowDownIcon v-else class="block h-6 w-6" aria-hidden="true" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ref } from 'vue'
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/24/outline';

const sortingOptions = [
    { name: 'Title', val: 'title' },
    { name: 'Description', val: 'description' },
    { name: 'Due Date', val: 'due_date' },
    { name: 'Created Date', val: 'created_at' },
    { name: 'Completed Date', val: 'completed_at' },
    { name: 'Priority', val: 'priority_name' },
]

const sortDirection = ref('asc')
const emit = defineEmits(['sortOptionSelected', 'sortDirectionToggled']);

const toggleSortDirection = () => {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    emit('sortDirectionToggled', sortDirection.value);
}

const selectSortOption = (option: string) => {
   emit('sortOptionSelected', option);
};

</script>
