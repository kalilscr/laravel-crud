<script setup>
import dayjs from 'dayjs';
import { Link } from '@inertiajs/vue3'
const props = defineProps(['user']);
</script>
<template>
    <div class="bg-white">
        <div class="max-w-7xl mx-auto pb-1 px-4 sm:px-6 lg:px-8 p-4 sm:py-6 lg:py-8 sm:flex sm:items-center sm:justify-between sm:space-x-5">
            <div class="flex items-start space-x-5">
                <div class="pt-1.5">
                <h1 class="text-2xl font-bold text-gray-900 capitalize">{{ user.name }}</h1>
                <p class="text-sm font-medium text-gray-500">
                    Joined
                    <time :datetime="dayjs(user.created_at).format('YYYY-MM')">
                        {{ dayjs(user.created_at).format('MMMM YYYY') }}
                    </time>
                </p>
                </div>
            </div>
            <div v-if="user.id === $page.props.auth.user.id" class="my-3 flex flex-col-reverse justify-stretch sm:mt-0 sm:pr-3">
                <Link :href="route('profile.edit')" class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Edit Profile</Link>
            </div>
        </div>
    </div>
  </template>
