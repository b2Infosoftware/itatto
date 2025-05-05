<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between space-y-4">
      <div>
        <h1 class="text-2xl">{{ $t('marketing.edit') }}</h1>
      </div>
    </div>

    <form-campaign edit :campaign="dbCampaign"></form-campaign>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const is_trial = useOrganisationStore().defaultOrganisation.is_trial;
const error = useError();

definePageMeta({
  middleware: 'subscribed',
});

const route = useRoute();
definePageMeta({
  middleware: 'auth',
});
useHead({
  title: $t('marketing.edit'),
});

const { data: dbCampaign } = await useApi(
  'GET',
  'campaigns/' + route.params.id
);

onMounted(() => {
  if (is_trial) {
    error.value = {
      statusCode: 404,
      message: $t('general.page_not_found'),
    };
  }
});
</script>
