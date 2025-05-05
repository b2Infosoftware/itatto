<template>
  <div class="card md:w-2/3 lg:w-2/3 2xl:w-1/2">
    <span class="card-title">{{ $t('services.edit_service') }}</span>
    <form-service edit></form-service>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  middleware: 'subscribed',
});
useHead({
  title: $t('services.edit_service'),
});
</script>
