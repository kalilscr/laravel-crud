<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Tabs from '@/Components/Tabs.vue';
import  UserCard  from '@/Components/UserCard.vue'
import { Link, Head } from '@inertiajs/vue3';

const props = defineProps(['user', 'users', 'auth']);
console.log(props.user);
console.log(props.users);
//console.log(props.auth);

const tabs = [
    {href:route('followers', props.user.id), active:route().current('followers', props.user.id), text:'Followers'},
    {href:route('follow.index', props.user.id), active:route().current('follow.index', props.user.id), text:'Following'},
 ]

</script>
<template>
     <Head>
        <title v-if="route().current('followers')">{{ user.name }} Followers</title>
        <title v-else>{{ user.name }} Follows</title>
     </Head>

    <AuthenticatedLayout>

        <h1 class="bg-white p-6 lg:p-8">
            <Link :href="route('profile.show', user.id)" class="text-2xl font-bold text-gray-900 capitalize hover:text-gray-500 hover:underline focus:text-gray-500 active:text-gray-950">{{ user.name }}</Link>
        </h1>
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
            <Tabs :tabs="tabs" />
            <div class="divide-y bg-white mt-6 rounded-lg">
                <UserCard
                     v-for="user in users"
                     :user="user"
                 />
            </div>
        </div>

    </AuthenticatedLayout>
</template>
