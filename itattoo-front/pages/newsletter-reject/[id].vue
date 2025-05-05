<template>
  <div class="w-full md:mt-0 sm:max-w-md xl:p-0">
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
      <ui-alert v-if="loading" type="warning">{{
        $t('newsletter.loading')
      }}</ui-alert>
      <ui-alert v-else-if="error" type="danger">{{ error }}</ui-alert>
      <ui-alert v-else-if="done">
        {{ $t('newsletter.saved') }}
      </ui-alert>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const route = useRoute();
const loading = ref(true);
const done = ref(false);
const error = ref(false);
definePageMeta({
  layout: 'authentication',
});
useHead({
  title: $t('newsletter.title'),
});

const handleSubmit = async () => {
  try {
    const response = await useApi(
      'GET',
      'newsletter/reject/' + route.params.id,
      route.query
    );
    if (response) {
      done.value = true;
    }
  } catch (err) {
    error.value = err.message;
  }
  loading.value = false;
};

onMounted(() => {
  handleSubmit();
});
</script>
