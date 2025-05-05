<template>
  <div class="lg:w-2/3">
    <div class="card">
      <div class="card-title">{{ $t('notificationTemplates.edit_title') }}</div>

      <form @submit.prevent="handleSubmit">
        <input-text
          v-if="dbForm.channel == 'email'"
          v-model="form.subject"
          :label="$t('notificationTemplates.subject')"
          name="subject"
          class="mb-4"
        ></input-text>

        <input-editor
          v-if="dbForm.channel == 'email'"
          v-model="form.message"
          name="message"
          :label="$t('notificationTemplates.email_message')"
        ></input-editor>

        <input-text-area
          v-else
          v-model="form.message"
          name="message"
          :label="$t('notificationTemplates.sms_message')"
        ></input-text-area>

        <div class="flex justify-end">
          <input-submit-button class="primary mx-auto mt-4" :loading="saving">{{
            $t('general.update')
          }}</input-submit-button>
        </div>
      </form>
      <span
        class="text-xs text-left items-start w-full p-4 bg-slate-100 dark:bg-slate-200/10 mt-4 rounded-lg"
      >
        <span class="block mt-2 mb-4 text-sm">
          {{ $t('notificationTemplates.customisation_tags') }}
        </span>
        <ul class="flex flex-col gap-y-0.5">
          <li>
            <code>{staff}</code> - {{ $t('notificationTemplates.staff') }}
          </li>
          <li>
            <code>{company}</code> - {{ $t('notificationTemplates.company') }}
          </li>
          <li>
            <code>{service}</code> - {{ $t('notificationTemplates.service') }}
          </li>
          <li>
            <code>{customer}</code> - {{ $t('notificationTemplates.customer') }}
          </li>
          <li><code>{date}</code> - {{ $t('notificationTemplates.date') }}</li>
          <li>
            <code>{start_time}</code> -
            {{ $t('notificationTemplates.start_time') }}
          </li>
          <li>
            <code>{end_time}</code> - {{ $t('notificationTemplates.end_time') }}
          </li>
          <li>
            <code>{duration}</code> - {{ $t('notificationTemplates.duration') }}
          </li>
        </ul>
      </span>
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
  title: $t('notificationTemplates.edit_title'),
});

const route = useRoute();
const saving = ref(false);
const { data: dbForm } = await useApi(
  'GET',
  'notification-templates/' + route.params.id
);
const form = reactive({
  subject: dbForm.subject,
  message: dbForm.message,
});

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  const url = 'notification-templates/' + route.params.id;
  saving.value = true;
  try {
    const response = await useApi('PATCH', url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};
</script>
