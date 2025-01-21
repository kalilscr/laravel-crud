<script setup>
import { Link } from '@inertiajs/vue3'

</script>

<template>
    <nav class="isolate flex divide-x mt-6 divide-gray-200 rounded-lg shadow overflow-hidden" aria-label="Tabs">
        <Link
            :href="route('chirps.index', { filter: 'false'})"
            :only="['chirps']"
            :class="[route().current('chirps.index', { filter: 'false'}) ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700','group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-center text-sm font-medium hover:bg-gray-50 focus:z-10']"
            :aria-current="route().current('chirps.index', { filter: 'false'}) ? 'page' : undefined">
            <span> All </span>
            <span aria-hidden="true" :class="[route().current('chirps.index', { filter: 'false'}) ? 'bg-indigo-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']" />
        </Link>
        <Link
            :href="route('chirps.index', { filter: 'true'})"
            :only="['chirps']"
            :class="[route().current('chirps.index', { filter: 'true'}) ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700', 'group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-center text-sm font-medium hover:bg-gray-50 focus:z-10']"
            :aria-current="route().current('chirps.index', { filter: 'true'}) ? 'page' : undefined">
            <span>Following </span>
            <span aria-hidden="true" :class="[route().current('chirps.index', { filter: 'true'}) ? 'bg-indigo-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']" />
        </Link>
    </nav>
</template>
