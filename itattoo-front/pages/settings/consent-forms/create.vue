<template>
  <div class="lg:w-2/3 2xl:w-1/2">
    <div class="mb-4">
      <h1 class="text-2xl">{{ $t('consentSettings.create_title') }}</h1>
    </div>
    <div class="card">
      <div class="card-title">{{ $t('consentSettings.card_title') }}</div>
      <form-consent></form-consent>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  middleware: 'subscribed',
  layout: 'settings',
});
useHead({
  title: $t('consentSettings.create_title'),
});
</script>
