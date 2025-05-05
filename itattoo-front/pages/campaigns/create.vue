<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between space-y-4">
      <div>
        <h1 class="text-2xl mb-4">{{ $t('marketing.create_campaign') }}</h1>
      </div>
    </div>

    <form-campaign></form-campaign>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const is_trial = useOrganisationStore().defaultOrganisation.is_trial;
const error = useError();

definePageMeta({
  middleware: 'subscribed',
});

useHead({
  title: $t('marketing.create_campaign'),
});

onMounted(() => {
  if (is_trial) {
    error.value = {
      statusCode: 404,
      message: $t('general.page_not_found'),
    };
  }
});
</script>
