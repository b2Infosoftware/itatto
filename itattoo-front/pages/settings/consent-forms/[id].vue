<template>
  <div class="lg:w-2/3 2xl:w-1/2">
    <div class="mb-4">
      <h1 class="text-2xl">{{ $t('consentSettings.edit_title') }}</h1>
    </div>
    <div class="card">
      <div class="card-title">{{ $t('consentSettings.card_title') }}</div>
      <form-consent edit></form-consent>
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
  title: $t('consentSettings.edit_title'),
});
</script>
