<template>
  <div class="card">
    <div class="card-title">
      <div class="flex justify-between w-full">
        <span class="capitalize">{{ title }}</span>
      </div>
    </div>
    <div class="flex flex-col card-body gap-y-4">
      <div class="w-full">
        <form
          class="flex flex-col w-full h-full gap-4 grow"
          @submit.prevent="handleSubmit"
        >
          <span
            class="pb-2 mt-4 mb-2 text-sm font-semibold border-b border-slate-200/20"
          >
            {{ $t('calendarSettings.appearance') }}
          </span>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <input-select
              v-model="form.default_view"
              :label="$t('calendarSettings.predefined')"
              :options="appearanceOptions"
              name="default_view"
              is-object
              required
            ></input-select>

            <input-select
              v-model="form.slot_duration"
              :label="$t('calendarSettings.slot_duration')"
              name="slot_duration"
              :options="slotDurationOptions"
              is-object
              required
            ></input-select>
            <input-select
              v-model="form.snap_duration"
              :label="$t('calendarSettings.snap_duration')"
              name="snap_duration"
              :options="slotDurationOptions"
              is-object
              required
            ></input-select>
          </div>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <input-hour
              v-model="form.from_time"
              :label="$t('calendarSettings.from_time')"
              min="00:00"
              :max="form.to_time"
              name="from_time"
              required
            ></input-hour>
            <input-hour
              v-model="form.to_time"
              :min="form.from_time"
              max="24:00"
              :label="$t('calendarSettings.to_time')"
              name="to_time"
              required
            ></input-hour>
            <input-hour
              v-model="form.start_time"
              :min="form.from_time"
              :max="form.to_time"
              :label="$t('calendarSettings.start_time')"
              name="start_time"
              required
            ></input-hour>
            <input-select
              v-model="form.date_format"
              :label="$t('calendarSettings.date_format')"
              :options="dateFormatOptions"
              name="start_time"
              required
              is-object
            ></input-select>
            <div class="col-span-2 form-group">
              <label>{{ $t('calendarSettings.hidden_days') }}</label>
              <vue-multiselect
                v-model="form.hidden_days"
                multiple
                :options="weekDays.map((i) => i.id)"
                :custom-label="(opt) => weekDays.find((e) => e.id === opt).name"
                :close-on-select="false"
                :searchable="false"
                selectLabel="↩"
                selectedLabel="✔"
                deselectLabel="✕"
                class="col-span-2"
              ></vue-multiselect>
            </div>
          </div>

          <span
            class="pb-2 mt-8 mb-2 text-sm font-semibold border-b border-slate-200/20"
          >
            {{ $t('calendarSettings.other_settings') }}
          </span>
          <ui-switch-button
            name="allow_off_hours_booking"
            v-model="form.allow_off_hours_booking"
            :info="$t('calendarSettings.allow_off_hours_booking_info')"
            >{{ $t('calendarSettings.allow_off_hours_booking') }}
          </ui-switch-button>
          <ui-switch-button
            name="allow_double_booking"
            v-model="form.allow_double_booking"
            :info="$t('calendarSettings.allow_double_booking_info')"
            >{{ $t('calendarSettings.allow_double_booking') }}
          </ui-switch-button>
          <ui-switch-button
            name="apply_staff_appearance"
            v-model="form.apply_staff_appearance"
            :info="$t('calendarSettings.apply_staff_appearance_info')"
            >{{ $t('calendarSettings.apply_staff_appearance') }}
          </ui-switch-button>
          <ui-switch-button
            name="use_staff_colors"
            v-model="form.use_staff_colors"
            :info="$t('calendarSettings.use_staff_colors_info')"
            >{{ $t('calendarSettings.use_staff_colors') }}
          </ui-switch-button>

          <div class="inline-flex justify-between mt-5">
            <input-submit-button
              class="primary"
              :loading="saving"
              :disabled="!useCan('edit', 'calendar-settings')"
            >
              {{ $t('general.save') }}
            </input-submit-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
import VueMultiselect from 'vue-multiselect';
const { $t } = useNuxtApp();
definePageMeta({
  layout: 'settings',
  middleware: [
    'subscribed',
    function () {
      if (!useCan('edit', 'calendar-settings')) {
        return navigateTo({ name: 'index' });
      }
    },
  ],
});
const title = $t('calendarSettings.title');
useHead({
  title: title,
});

const weekDays = [
  { id: 1, name: $t('days.1') },
  { id: 2, name: $t('days.2') },
  { id: 3, name: $t('days.3') },
  { id: 4, name: $t('days.4') },
  { id: 5, name: $t('days.5') },
  { id: 6, name: $t('days.6') },
  { id: 0, name: $t('days.7') },
];

const organisation = useOrganisationStore();
const saving = ref(false);
const form = reactive({});
const slotDurationOptions = [
  { id: 5, name: '5 ' + $t('general.minutes') },
  { id: 15, name: '15 ' + $t('general.minutes') },
  { id: 30, name: '30 ' + $t('general.minutes') },
];
const dateFormatOptions = [
  { id: 'DD/MM/YYYY', name: '25/12/2024' },
  { id: 'DD-MM-YYYY', name: '25-12-2024' },
  { id: 'MM-DD-YYYY', name: '12-25-2024' },
  { id: 'YYYY-MM-DD', name: '2024-12-25' },
  { id: 'DD MMM YYYY', name: '25 Dec 2024' },
  { id: 'MMM DD YYYY', name: 'Dec 25 2024' },
  { id: 'YYYY MMM DD', name: '2024 Dec 25' },
];
const appearanceOptions = [
  { id: 'timeGridDay', name: 'Daily calendar' },
  { id: 'timeGridWeek', name: 'Weekly calendar' },
  { id: 'timeGridFourDay', name: 'Four days calendar' },
  { id: 'dayGridMonth', name: 'Monthly calendar' },
  { id: 'listDay', name: 'Daily agenda' },
  { id: 'listWeek', name: 'Weekly agenda' },
  { id: 'listMonth', name: 'Monthly agenda' },
  { id: 'listYear', name: 'Annual agenda' },
  // { id: 'listRange', name: 'Date range agenda' },
];

const handleSubmit = async () => {
  if (!useCan('edit', 'calendar-settings')) {
    return;
  }
  useValidationStore().clearErrors();
  saving.value = true;
  try {
    const url = 'calendar-settings/' + organisation.calendarSettings.id;
    const response = await useApi('PATCH', url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      organisation.orgCalendarSettings = response.data;
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};

onMounted(() => {
  for (const key in organisation.orgCalendarSettings) {
    form[key] = organisation.orgCalendarSettings[key];
  }
});
</script>
