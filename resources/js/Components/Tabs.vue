<script setup>
import TabLink from './TabLink.vue';

defineProps(['tabs'])

</script>

<template>
    <nav class="isolate flex divide-x mt-6 divide-gray-200 rounded-lg shadow overflow-hidden" aria-label="Tabs">
        <template v-for="(tab, index) in tabs">
          <TabLink :tab="tab" :tabIdx="index" :tabLength="tabs.length">
               <span> {{ tab.text }}</span>
          </TabLink>
        </template>
    </nav>
</template>
