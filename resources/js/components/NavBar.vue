<template>
    <Disclosure as="nav" class="bg-gray-800" v-slot="{ open }">
      <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="relative flex h-16 items-center justify-between">

            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                    <span class="absolute -inset-0.5" />
                    <span class="sr-only">Add Task</span>
                    <PlusCircleIcon class="block h-6 w-6" aria-hidden="true" />
                </button>
            </div>

            <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex flex-shrink-0 items-center">
                    <img class="h-8 w-auto" :src="NoteIcon" alt="Todo Icon" />
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:block">
                <div class="flex space-x-4 justify-end">
                    <a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Add Task</a>
                </div>
            </div>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                <button @click="logout" type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                    <span class="absolute -inset-1.5" />
                    <span class="sr-only">Logout</span>
                    <ArrowRightStartOnRectangleIcon class="h-6 w-6" aria-hidden="true" />
                </button>
            </div>
        </div>
      </div>
    </Disclosure>
</template>

<script setup lang="ts">
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ArrowRightStartOnRectangleIcon, PlusCircleIcon } from '@heroicons/vue/24/outline'
import NoteIcon from '@/images/note.png';
import Swal from 'sweetalert2';
import axios from 'axios';

const logout = async () => {
    try {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`
        };

        const response = await axios.post('api/auth/logout', {}, { headers });
        if (response.status !== 204) {
            throw new Error('Logout failed');
        }
        localStorage.clear();
        window.location.href = '/login';
    } catch (error: any) {
        Swal.fire({
            icon: 'error',
            title: 'Logout Failed',
            text: error.response.data.message || error.response.data.error || 'Something went wrong!',
        });
    }
};
</script>
