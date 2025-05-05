<template>
  <form
    class="grow w-full h-full flex flex-col xl:w-2/3 2xl:1/2"
    @submit.prevent="handleSubmit"
  >
    <div class="card">
      <div class="card-title">General Settings</div>
      <div class="card-body gap-y-4 flex flex-col">
        <input-text
          v-model="form.name"
          :label="$t('settings.business_name')"
          name="business_name"
          required
        ></input-text>
        <input-select
          v-model="form.language_id"
          :options="languages"
          :label="$t('settings.language')"
          name="language"
          is-object
          required
        ></input-select>
        <input-select
          v-model="form.currency_id"
          :options="currencies"
          is-object
          :label="$t('settings.currency')"
          name="currency"
          required
        ></input-select>
        <input-select
          :label="$t('marketing.timezone')"
          :options="timezones"
          is-object
          name="timezone"
          v-model="form.timezone"
          class="flex-grow"
        ></input-select>
        <input-select
          v-model="form.cancellation_buffer_days"
          :options="cancellationPeriods"
          is-object
          :label="$t('settings.dissallow_appointment_cancellation')"
          name="cancellation_buffer_days"
          required
        ></input-select>
        <input-select
          v-model="form.autodelete_period_days"
          :options="deletePeriods"
          is-object
          :label="$t('settings.autodelete')"
          name="autodelete_period_days"
          required
        ></input-select>
        <ui-alert type="warning">
          {{ $t('settings.appointments_delete_warning') }}
        </ui-alert>

        <div class="flex justify-between">
          <span></span>
          <button type="submit" class="btn primary">
            {{ $t('general.update') }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>
definePageMeta({
  layout: 'settings',
  middleware: [
    'subscribed',
    function () {
      if (!useCan('edit', 'settings')) {
        return navigateTo({ name: 'index' });
      }
    },
  ],
});
const title = 'Dashboard';
useHead({
  title: title,
});
const { $t } = useNuxtApp();

const saving = ref(false);
const deletePeriods = [
  { id: null, name: $t('settings.never') },
  { id: 365, name: $t('settings.prev_year') },
  { id: 30, name: $t('settings.prev_month') },
  { id: 7, name: $t('settings.prev_week') },
  { id: 1, name: $t('settings.prev_day') },
];
const cancellationPeriods = [
  { id: 0, name: $t('settings.allow_anytime') },
  { id: 2, name: $t('settings.two_days') },
  { id: 3, name: $t('settings.three_days') },
  { id: 4, name: $t('settings.four_days') },
  { id: 5, name: $t('settings.five_days') },
  { id: 365, name: $t('settings.always') },
];

const languages = useSettingsStore().languages;

const currencies = useSettingsStore().currencies;

const dbOrg = useOrganisationStore().defaultOrganisation;

const form = {
  name: dbOrg.name,
  slug: dbOrg.slug,
  timezone: dbOrg.timezone,
  language_id: dbOrg.language_id,
  currency_id: dbOrg.currency_id,
  cancellation_buffer_days: dbOrg.cancellation_buffer_days,
  autodelete_period_days: dbOrg.autodelete_period_days,
};

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  const url = 'organisations/' + useOrganisationStore().defaultOrganisation.id;
  saving.value = true;
  try {
    const response = await useApi('PATCH', url, form);
    useOrganisationStore().fetchData();
    useSnackbarStore().show(response.message, 'success');
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};

const timezones = [
  { id: 'GMT-12:00', name: 'Dateline Standard Time (GMT-12:00)' },
  { id: 'GMT-11:00', name: 'Samoa Standard Time (GMT-11:00)' },
  { id: 'GMT-10:00', name: 'Hawaiian Standard Time (GMT-10:00)' },
  { id: 'GMT-09:00', name: 'Alaskan Standard Time (GMT-09:00)' },
  { id: 'GMT-08:00', name: 'Pacific Standard Time (GMT-08:00)' },
  { id: 'GMT-07:00', name: 'U.S. Mountain Standard Time (GMT-07:00)' },
  { id: 'GMT-06:00', name: 'Central Standard Time (GMT-06:00)' },
  { id: 'GMT-05:00', name: 'Eastern Standard Time (GMT-05:00)' },
  { id: 'GMT-04:00', name: 'Atlantic Standard Time (GMT-04:00)' },
  { id: 'GMT-03:00', name: 'Greenland Standard Time (GMT-03:00)' },
  { id: 'GMT-02:00', name: 'Mid-Atlantic Standard Time (GMT-02:00)' },
  { id: 'GMT-01:00', name: 'Azores Standard Time (GMT-01:00)' },
  { id: 'GMT+00:00', name: 'GMT Standard Time (GMT)' },
  { id: 'GMT+01:00', name: 'Central Europe Standard Time (GMT+01:00)' },
  { id: 'GMT+02:00', name: 'E. Europe Standard Time (GMT+02:00)' },
  { id: 'GMT+03:00', name: 'Russian Standard Time (GMT+03:00)' },
  { id: 'GMT+03:30', name: 'Iran Standard Time (GMT+03:30)' },
  { id: 'GMT+04:00', name: 'Arabian Standard Time (GMT+04:00)' },
  { id: 'GMT+05:30', name: 'India Standard Time (GMT+05:30)' },
  { id: 'GMT+05:45', name: 'Nepal Standard Time (GMT+05:45)' },
  { id: 'GMT+06:00', name: 'Central Asia Standard Time (GMT+06:00)' },
  { id: 'GMT+06:30', name: 'Myanmar Standard Time (GMT+06:30)' },
  { id: 'GMT+07:00', name: 'S.E. Asia Standard Time (GMT+07:00)' },
  { id: 'GMT+08:00', name: 'China Standard Time (GMT+08:00)' },
  { id: 'GMT+09:00', name: 'Tokyo Standard Time (GMT+09:00)' },
  { id: 'GMT+09:30', name: 'A.U.S. Central Standard Time (GMT+09:30)' },
  { id: 'GMT+10:00', name: 'West Pacific Standard Time (GMT+10:00)' },
  { id: 'GMT+11:00', name: 'Central Pacific Standard Time (GMT+11:00)' },
  { id: 'GMT+12:00', name: 'New Zealand Standard Time (GMT+12:00)' },
  { id: 'GMT+13:00', name: 'Tonga Standard Time (GMT+13:00)' },
];
</script>
