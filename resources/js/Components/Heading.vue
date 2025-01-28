<script setup>
import dayjs from 'dayjs';
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps(['user', 'following']);

const follow = useForm({
    id: props.user.id,
});

const unfollow = useForm({});

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
                    <div class="mt-3 sm:mt-3">
                       <Link :href="route('follow.index', user.id)">
                           <span>{{ user.following_count }}</span>
                           <span class="text-sm font-medium text-gray-500"> Following</span>
                       </Link>
                       <Link :href="route('followers', user.id)">
                           <span class="ml-4">{{ user.followers_count }}</span>
                           <span class="text-sm font-medium text-gray-500"> Followers</span>
                       </Link>
                   </div>
                 </div>
           </div>
           <div v-if="user.id === $page.props.auth.user.id" class="my-3 flex flex-col-reverse justify-stretch sm:mt-0 sm:pr-3">
               <Link :href="route('profile.edit')" class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Edit Profile</Link>
           </div>
           <div v-else class="my-3 flex flex-col-reverse justify-stretch space-y-4 space-y-reverse sm:justify-end sm:space-x-3 sm:space-y-0 sm:mt-0 sm:flex-row sm:pr-3">
                <form v-if="!following" class="flex flex-col" @submit.prevent="follow.post(route('follow.store'), {

                    preserveScroll: true,
                    only:['following', 'user']
                })">
                <PrimaryButton class="justify-center">Follow</PrimaryButton>
                </form>
                <form v-else class="flex flex-col" @submit.prevent="unfollow.delete(route('follow.destroy', user.id), {
                    preserveScroll: true,
                    only:['following', 'user']
                })">
                <SecondaryButton class="justify-center" type='submit'>Unfollow</SecondaryButton>
                </form>
            </div>
       </div>
   </div>
</template>
