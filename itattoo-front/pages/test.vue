<template>
  <div class="lg:w-2/3 2xl:w-1/2">
    <div class="mb-4">
      <h1 class="text-2xl">{{ $t('staff.add_staff') }}</h1>
      <p class="opacity-60 leading-tight text-sm mt-2">
        {{ $t('staff.add_staff_subtitle') }}
      </p>
    </div>
    <appointment-project
      v-model="ok"
      customer-id="4"
      staff-id="4"
    ></appointment-project>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  middleware: 'auth',
});
useHead({
  title: $t('staff.add_staff'),
});

const ok = ref(null);
</script>
