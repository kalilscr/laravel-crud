<script setup>

import { Link } from '@inertiajs/vue3';

defineProps(['nextUrl', 'prevUrl'])

</script>
<template>
   <nav v-if="prevUrl || nextUrl" class="flex place-content-around bg-white px-4 py-3 sm:px-6 mt-6 rounded-lg" aria-label="Pagination">        <Link v-if="prevUrl" :href="prevUrl" class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-offset-0">Previous</Link>
        <Link v-if="nextUrl" :href="nextUrl" class="relative ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-offset-0">Next</Link>
    </nav>
</template>
