<script setup>

import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';

import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps(['user']);

const follow = useForm({
    id: props.user.id,
});

const unfollow = useForm({});

</script>

<template>
    <div class="flex items-center justify-between space-x-6 p-6">
        <div class="flex gap-x-4">
            <div class="min-w-0 flex-auto">
                <Link :href="route('profile.show', user.id)" class="text-md font-semibold leading-6 text-gray-800 capitalize hover:text-gray-500 hover:underline focus:text-gray-500 active:text-gray-900">{{ user.name }}</Link>
            </div>
        </div>
        <template v-if="user.id !== $page.props.auth.user.id">
            <form v-if="!user.following" @submit.prevent="follow.post(route('follow.store'), {
                preserveScroll: true,
                only:['following']
            })">
                <PrimaryButton>Follow</PrimaryButton>
            </form>
            <form v-if="user.following" @submit.prevent="unfollow.delete(route('follow.destroy', user.id), {
                preserveScroll: true,
                only:['following']
            })">
                <SecondaryButton type="submit">Unfollow</SecondaryButton>
            </form>
        </template>
    </div>
</template>
