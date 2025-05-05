<template>
  <div class="flex w-full">
    <form class="lg:w-2/3" @submit.prevent="handleSubmit">
      <ui-tabs v-model="activeTab" :tabs="tabs" class="mb-8"></ui-tabs>
      <div class="card">
        <div class="font-semibold mb-4 pb-4 border-b dark:border-input-dark">
          {{ $t('notificationsSettings.what_to_send') }}
        </div>

        <!-- CUSTOMERS -->
        <div v-if="activeTab == 0">
          <span>
            <input-checkbox
              v-for="item in events"
              :key="item.value"
              :value="item.value"
              v-model="form.customer_events"
              name="customer_events"
              >{{ item.text }}</input-checkbox
            >
          </span>
          <div
            v-if="form.customer_events.includes('remind')"
            class="flex flex-col mt-10"
          >
            <div
              class="font-semibold mb-4 pb-4 border-b dark:border-input-dark"
            >
              {{ $t('notificationsSettings.reminders') }}
            </div>

            <input-checkbox
              v-model="form.customer_ics_file"
              name="customer_ics_file"
              >{{ $t('notificationsSettings.add_ics') }}</input-checkbox
            >
            <input-checkbox
              name="customer_link_to_cancel"
              v-model="form.customer_link_to_cancel"
              >{{ $t('notificationsSettings.link_to_cancel') }}</input-checkbox
            >
            <input-duration
              class="max-w-xs"
              name="customer_pre_appointment_minutes"
              v-model:minutes="form.customer_pre_appointment_minutes"
              :label="$t('notificationsSettings.time_before_appointment')"
            ></input-duration>
            <div class="form-group mt-4">
              <label for="">{{ $t('notificationsSettings.remind_via') }}</label>
            
              <!-- Alert jika is_trial true -->
              <ui-alert v-if="is_trial === true" type="warning" class="mb-2">
                {{ $t('notificationsSettings.upgrade_required') }}
              </ui-alert>
            
              <div class="flex flex-col gap-2">
                <input-checkbox
                  name="customer_sms_reminders"
                  v-model="form.customer_sms_reminders"
                  :disabled="is_trial"
                  :value="false"
                >
                  {{ $t('notificationsSettings.sms') }}
                </input-checkbox>
            
                <input-checkbox
                  name="customer_email_reminders"
                  v-model="form.customer_email_reminders"
                  :disabled="is_trial"
                  :value="false"
                >
                  {{ $t('notificationsSettings.email') }}
                </input-checkbox>
            
                <input-checkbox
                  v-if="form.customer_sms_reminders"
                  name="customer_deposit_only_sms_reminder"
                  v-model="form.customer_deposit_only_sms_reminder"
                  :disabled="is_trial"
                  :value="false"
                >
                  {{ $t('notificationsSettings.sms_only_on_deposit') }}
                </input-checkbox>
              </div>
            </div>
            
          </div>
          <div
            v-if="form.customer_events.includes('after')"
            class="flex flex-col mt-10"
          >
            <div
              class="font-semibold mb-4 pb-4 border-b dark:border-input-dark"
            >
              {{ $t('notificationsSettings.post_appointment') }}
            </div>

            <input-duration
              class="max-w-xs"
              name="customer_post_appointment_minutes"
              v-model:minutes="form.customer_post_appointment_minutes"
              :label="$t('notificationsSettings.send_aftercare_after')"
            ></input-duration>
            <div class="form-group mt-4">
              <label for="">{{ $t('notificationsSettings.send_via') }}</label>
              <ui-alert v-if="is_trial === true" type="warning" class="mb-2">
                {{ $t('notificationsSettings.upgrade_required') }}
              </ui-alert>
            
              <div class="flex flex-col gap-2">
                <input-checkbox
                  name="customer_post_appointment_sms"
                  v-model="form.customer_post_appointment_sms"
                  :disabled="is_trial"
                  :value="false"
                >
                  {{ $t('notificationsSettings.sms') }}
                </input-checkbox>
            
                <input-checkbox
                  name="customer_post_appointment_email"
                  v-model="form.customer_post_appointment_email"
                  :disabled="is_trial"
                  :value="false"
                >
                  {{ $t('notificationsSettings.email') }}
                </input-checkbox>
              </div>
            </div>
            
          </div>
        </div>

        <!-- STAFF -->
        <div v-else>
          <span>
            <input-checkbox
              v-for="item in events"
              :key="item.value"
              :value="item.value"
              v-model="form.staff_events"
              name="staff_events"
              >{{ item.text }}</input-checkbox
            >
          </span>
          <div
            v-if="form.staff_events.includes('remind')"
            class="flex flex-col mt-10"
          >
            <div
              class="font-semibold mb-4 pb-4 border-b dark:border-input-dark"
            >
              {{ $t('notificationsSettings.reminders') }}
            </div>

            <input-checkbox
              v-model="form.staff_ics_file"
              name="staff_ics_file"
              >{{ $t('notificationsSettings.add_ics') }}</input-checkbox
            >

            <input-duration
              class="max-w-xs"
              name="staff_pre_appointment_minutes"
              v-model:minutes="form.staff_pre_appointment_minutes"
              :label="$t('notificationsSettings.time_before_appointment')"
            ></input-duration>
            <div class="form-group mt-4">
              <label for="">{{ $t('notificationsSettings.remind_via') }}</label>
              <ui-alert v-if="is_trial === true" type="warning" class="mb-2">
                {{ $t('notificationsSettings.upgrade_required') }}
              </ui-alert>
              <div class="flex flex-col gap-2">
                <input-checkbox
                  name="staff_sms_reminders"
                  :disabled="is_trial"
                  v-model="form.staff_sms_reminders"
                  >{{ $t('notificationsSettings.sms') }}</input-checkbox
                >
                <input-checkbox
                  name="staff_email_reminders"
                  :disabled="is_trial"
                  v-model="form.staff_email_reminders"
                  >{{ $t('notificationsSettings.email') }}</input-checkbox
                >
              </div>
            </div>
          </div>
          <div
            v-if="form.staff_events.includes('after')"
            class="flex flex-col mt-10"
          >
            <div
              class="font-semibold mb-4 pb-4 border-b dark:border-input-dark"
            >
              {{ $t('notificationsSettings.post_appointment') }}
            </div>

            <input-duration
              class="max-w-xs"
              name="staff_post_appointment_minutes"
              v-model:minutes="form.staff_post_appointment_minutes"
              :label="$t('notificationsSettings.send_aftercare_after')"
            ></input-duration>
            <div class="form-group mt-4">
              <label for="">{{ $t('notificationsSettings.send_via') }}</label>
              <ui-alert v-if="is_trial === true" type="warning" class="mb-2">
                {{ $t('notificationsSettings.upgrade_required') }}
              </ui-alert>
              <div class="flex flex-col gap-2">
                <input-checkbox
                  name="staff_post_appointment_sms"
                  :disabled="is_trial"
                  v-model="form.staff_post_appointment_sms"
                  >{{ $t('notificationsSettings.sms') }}</input-checkbox
                >
                <input-checkbox
                  name="staff_post_appointment_email"
                  :disabled="is_trial"
                  v-model="form.staff_post_appointment_email"
                  >{{ $t('notificationsSettings.email') }}</input-checkbox
                >
              </div>
            </div>
          </div>
        </div>

        <div class="pt-8">
          <input-submit-button class="primary" :loading="saving">
            {{ $t('general.save') }}
          </input-submit-button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  middleware: [
    'subscribed',
    function () {
      if (!useCan('edit', 'notifications')) {
        return navigateTo({ name: 'index' });
      }
    },
  ],
  layout: 'settings',
});
useHead({
  title: $t('notificationsSettings.title'),
});
const saving = ref(false);
const events = [
  { text: $t('notificationsSettings.created_event'), value: 'created' },
  { text: $t('notificationsSettings.edited_event'), value: 'edited' },
  { text: $t('notificationsSettings.canceled_event'), value: 'canceled' },
  { text: $t('notificationsSettings.remind_event'), value: 'remind' },
  { text: $t('notificationsSettings.after_event'), value: 'after' },
];
const tabs = [
  { text: $t('notificationsSettings.customer_settings'), icon: 'customers' },
  { text: $t('notificationsSettings.staff_settings'), icon: 'briefcase' },
];
const activeTab = ref(0);
const form = reactive({});
const settings = useOrganisationStore().notificationSettings;
const is_trial = useOrganisationStore().defaultOrganisation.is_trial;
for (const key in settings) {
  form[key] = settings[key];
}

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  const url = 'organisation-notifications/' + form.id;
  saving.value = true;
  try {
    const response = await useApi('PATCH', url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      useOrganisationStore().notificationSettings = response.data;
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};
</script>
