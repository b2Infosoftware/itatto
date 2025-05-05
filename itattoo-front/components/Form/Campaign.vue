<template>
  <form class="mb-4 card max-w-2xl" @submit.prevent="handleSubmit">
    <div class="flex flex-col gap-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 w-full">
        <input-text
          v-model="form.name"
          :label="$t('marketing.name')"
          name="name"
        ></input-text>
        <input-select
          :label="$t('marketing.is_active')"
          :options="[
            { id: 1, name: $t('general.yes') },
            { id: 0, name: $t('general.no') },
          ]"
          is-object
          name="have_bookings"
          v-model="form.is_active"
          class="flex-grow"
        ></input-select>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
        <input-select
          :label="$t('marketing.campaign_type')"
          :options="sendOptions"
          is-object
          name="type"
          v-model="form.type"
          @change="countCustomers"
          class="flex-grow"
        ></input-select>

        <input-select
          :label="$t('marketing.select_customers_that')"
          :options="bookingType"
          is-object
          name="have_bookings"
          v-model="form.filters.have_bookings"
          @change="countCustomers"
          class="flex-grow"
        ></input-select>

        <input-select
          :label="$t('marketing.in_the_past')"
          :options="months"
          is-object
          name="past_months"
          v-model="form.filters.past_months"
          @change="countCustomers"
          class="flex-grow"
        ></input-select>

        <ui-alert v-if="form.is_birthday" type="info">{{
          $t('marketing.birthday_message')
        }}</ui-alert>
        <ui-datepicker
          v-else
          class="flex-grow"
          name="date"
          :label="$t('marketing.scheduled_on')"
          date-only
          :min-date="new Date()"
          v-model="form.scheduled_date"
        ></ui-datepicker>
        <input-hour
          class="flex-grow"
          name="scheduled_time"
          :label="$t('wizard.start_time')"
          show-quarters
          v-model="form.scheduled_time"
        >
        </input-hour>
        <input-select
          :label="$t('marketing.timezone')"
          :options="timezones"
          is-object
          name="timezone"
          v-model="form.timezone"
          class="flex-grow"
        ></input-select>
      </div>
      <div class="bg-warning-500/40 rounded-lg p-4 text-xs">
        <div class="text-sm font-semibold mb-4">
          {{ $t('marketing.newsletter_info') }}:
        </div>
        <div>{{ $t('marketing.customers_found') }} {{ foundCustomers }}</div>
        <div v-if="form.type == 'sms'">
          {{ $t('marketing.sms_left') }} {{ smsAvailable }}
        </div>
        <button v-if="form.type == 'sms'" class="btn btn-xs outlined mt-4">
          {{ $t('marketing.buy_sms') }}
        </button>
      </div>

      <div
        class="w-full border-b border-slate-400 dark:border-input-dark"
      ></div>

      <input-text-area
        :label="$t('marketing.message')"
        v-if="form.type == 'sms'"
        name="message"
        v-model="form.message"
      >
      </input-text-area>
      <template v-else>
        <input-text
          v-model="form.email_subject"
          name="email_subject"
          :label="$t('marketing.subject')"
        >
        </input-text>
        <input-editor
          v-model="form.message"
          name="message"
          :label="$t('marketing.message')"
        ></input-editor>
      </template>
    </div>

    <span
      class="text-xs text-left items-start w-full p-4 bg-slate-100 dark:bg-slate-200/10 mt-4 rounded-lg"
    >
      <span class="block mt-2 mb-4">
        {{ $t('notificationTemplates.customisation_tags') }}
      </span>
      <ul class="flex flex-col gap-y-0.5">
        <li>
          <code>{company}</code> - {{ $t('notificationTemplates.company') }}
        </li>
        <li>
          <code>{customer}</code> - {{ $t('notificationTemplates.customer') }}
        </li>
      </ul>
    </span>

    <div class="flex mt-4 gap-x-4">
      <input-submit-button :loading="saving" class="btn primary">{{
        $t('general.save')
      }}</input-submit-button>
      <button
        @click.prevent="preview"
        v-if="form.type == 'email'"
        class="btn secondary"
      >
        Preview
      </button>
    </div>
    <div v-if="showPreview" class="appointmentSection">
      <div class="overlay"></div>
      <div class="appointment-details card text-[#475569]">
        <span v-html="previewData"></span>

        <div class="mt-4 flex justify-center">
          <button @click.prevent="showPreview = false" class="btn secondary">
            {{ $t('general.close') }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
const { $t } = useNuxtApp();
const props = defineProps({
  campaign: {
    type: Object,
    default: {},
  },
  edit: {
    type: Boolean,
    default: false,
  },
});
const showPreview = ref(false);
const saving = ref(false);
const foundCustomers = ref(0);
const smsAvailable = ref(useOrganisationStore().defaultOrganisation.sms_left);
const previewData = ref(null);
const form = reactive({
  name: '',
  type: 'sms',
  email_subject: '',
  is_active: false,
  scheduled_date: '',
  scheduled_time: '10:00',
  timezone: 'GMT+00:00',
  message: '',
  filters: {
    have_bookings: true,
    past_months: 2,
  },
});

const sendOptions = [
  { id: 'sms', name: $t('marketing.sms') },
  { id: 'email', name: $t('marketing.email') },
];
const bookingType = [
  { id: 1, name: $t('marketing.have_booked') },
  { id: 0, name: $t('marketing.have_not_booked') },
];
let months = [];
for (let index = 1; index < 13; index++) {
  if (index == 1) {
    months.push({
      id: index,
      name: index + ' ' + $t('general.month'),
    });
  } else {
    months.push({
      id: index,
      name: index + ' ' + $t('general.months'),
    });
  }
}

const preview = async () => {
  useValidationStore().clearErrors();

  try {
    const response = await useApi('POST', 'campaigns/preview', form);
    if (response) {
      previewData.value = response.data;
      showPreview.value = true;
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }
};

const countCustomers = async () => {
  const response = await useApi(
    'POST',
    'campaigns/count-customers',
    form.filters
  );
  foundCustomers.value = response.data;
};

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  const method = props.edit ? 'PATCH' : 'POST';
  const url = props.edit ? 'campaigns/' + props.campaign.id : 'campaigns';
  saving.value = true;
  try {
    const response = await useApi(method, url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      navigateTo({ name: 'campaigns' });
    }
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

const populateForm = async () => {
  if (!props.edit) {
    return;
  }
  form.name = props.campaign.name;
  form.is_birthday = props.campaign.is_birthday;
  form.is_active = props.campaign.is_active;
  form.type = props.campaign.type;
  form.email_subject = props.campaign.email_subject;
  form.scheduled_date = props.campaign.scheduled_date;
  form.scheduled_time = props.campaign.scheduled_time;
  form.timezone = props.campaign.timezone;
  form.message = props.campaign.message;
  form.filters.have_bookings = props.campaign.filters?.have_bookings || 1;
  form.filters.past_months = props.campaign.filters?.past_months || 0;
};

onMounted(() => {
  populateForm();
  countCustomers();
});
</script>
