<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chirp from '@/Components/Chirp.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Tabs from '@/Components/Tabs.vue';
import { useForm, Head } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

defineProps(['chirps']);

const tabs = [
    {href:route('chirps.index', { filter: 'false'}), active:route().current('chirps.index', { filter: 'false'}), only:['chirps'], text:'All'},
    {href:route('chirps.index', { filter: 'true'}), active:route().current('chirps.index', { filter: 'true'}), only:['chirps'],  text:'Following'},
]

const form = useForm({
    message: '',
});
</script>

<template>
    <Head title="Chirps" />

    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
            <form @submit.prevent="form.post(route('chirps.store'), { onSuccess: () => form.reset() })">
                <textarea
                    v-model="form.message"
                    placeholder="What's on your mind?"
                    class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                ></textarea>
                <InputError :message="form.errors.message" class="mt-2" />
                <PrimaryButton class="mt-4">Chirp</PrimaryButton>
            </form>
            <Tabs :tabs="tabs"/>
            <div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
                <Chirp
                    v-for="chirp in chirps.data"
                    :key="chirp.id"
                    :chirp="chirp"
                />
            </div>
            <Pagination :nextUrl="chirps.next_page_url" :prevUrl="chirps.prev_page_url"/>
        </div>
    </AuthenticatedLayout>
</template>
