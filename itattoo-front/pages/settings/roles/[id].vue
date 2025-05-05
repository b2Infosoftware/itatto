<template>
  <div class="lg:w-2/3">
    <div class="card">
      <div class="card-title">{{ $t('roles.edit') }}</div>

      <form-role edit :role="dbRole"></form-role>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const route = useRoute();
definePageMeta({
  middleware: 'subscribed',
  layout: 'settings',
});
useHead({
  title: $t('roles.edit'),
});
const { data: dbRole } = await useApi('GET', 'roles/' + route.params.id);
</script>
