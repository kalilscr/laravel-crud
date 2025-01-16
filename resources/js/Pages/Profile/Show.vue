<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Heading from '@/Components/Heading.vue';
import Chirp from '@/Components/Chirp.vue';
import { Head } from '@inertiajs/vue3';

defineProps(['user','chirps']);
</script>

<template>
    <Head :title="user.name" />

    <AuthenticatedLayout>
        <Heading :user="user"/>
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
            <div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
                <Chirp
                    v-for="chirp in chirps"
                    :key="chirp.id"
                    :chirp="chirp"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
