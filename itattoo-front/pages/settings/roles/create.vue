<template>
  <div class="lg:w-2/3">
    <div class="card">
      <div class="card-title">{{ $t('roles.create') }}</div>

      <form-role></form-role>
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
  title: $t('roles.create'),
});
</script>
