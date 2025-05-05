<template>
  <div class="lg:w-2/3 2xl:w-1/2">
    <div class="mb-4">
      <h1 class="text-2xl">{{ $t('staff.add_staff') }}</h1>
      <p class="opacity-60 leading-tight text-sm mt-2">
        {{ $t('staff.add_staff_subtitle') }}
      </p>
    </div>
    <form-staff></form-staff>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  middleware: 'subscribed',
});
useHead({
  title: $t('staff.add_staff'),
});
</script>
