<template>
  <div class="w-full md:mt-0 sm:max-w-md xl:p-0">
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8"></div>
  </div>
</template>

<script setup>
const route = useRoute();
definePageMeta({
  layout: 'authentication',
});

await useApi('GET', 'accept-invitation', route.query);
navigateTo({ name: 'login' });
</script>
