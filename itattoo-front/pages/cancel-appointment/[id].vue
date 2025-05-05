<template>
  <div class="w-full md:mt-0 sm:max-w-md xl:p-0">
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
      <ui-alert v-if="canceled">
        {{ $t('general.appointment_canceled') }}
      </ui-alert>
      <form
        v-else
        class="space-y-4 md:space-y-6"
        @submit.prevent="handleSubmit"
      >
        <input-text
          v-model="form.email"
          :label="$t('auth.your_email')"
          name="email"
          placeholder="johndoe@example.com"
          required
        ></input-text>
        <input-submit-button
          class="primary w-full justify-center"
          :loading="loading"
          >{{ $t('general.cancel') }}</input-submit-button
        >
      </form>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const route = useRoute();
const loading = ref(false);
const canceled = ref(false);
definePageMeta({
  layout: 'authentication',
});
useHead({
  title: $t('general.cancel_appointment'),
});
let form = reactive({
  email: '',
});

const handleSubmit = async () => {
  loading.value = true;
  try {
    const response = await useApi(
      'POST',
      'cancel-appointment/' + route.params.id,
      form
    );
    if (response) {
      canceled.value = true;
    }
  } catch (error) {
    // useSnackbarStore().show(error.message.)
  }
  loading.value = false;
};
</script>
