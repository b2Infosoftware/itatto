<template>
  <div class="lg:w-2/3 2xl:w-1/2">
    <div class="mb-4">
      <h1 class="text-2xl">{{ $t('customers.add_customer') }}</h1>
      <p class="opacity-60 leading-tight text-sm mt-2">
        {{ $t('customers.add_customer_subtitle') }}
      </p>
    </div>
    <div class="card">
      <div class="card-title">{{ $t('customers.personal_details') }}</div>
      <form-customer></form-customer>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  middleware: 'subscribed',
});
useHead({
  title: $t('customers.add_customer'),
});
</script>
