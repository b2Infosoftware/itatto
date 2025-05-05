<template>
  <div class="appointment-details duplicate">
    <div>
      <div class="header">
        <span>{{ $t('wizard.duplicate_appointment') }}</span>
        <button
          @click.prevent="emit('hide')"
          class="btn tonal btn-icon secondary h-8"
        >
          <nuxt-icon name="x-mark" filled></nuxt-icon>
        </button>
      </div>
      <div class="overflow-auto p-4 flex flex-col md:p-8 max-h-[60vh]">
        <div v-for="(item, index) in form" :key="index" class="duplicate-form">
          <div class="flex justify-between items-center -mb-2">
            <span class="text-sm font-semibold">{{
              $t('wizard.create_on')
            }}</span>
            <span
              @click.prevent="removeDate(index)"
              v-if="form.length > 1"
              class="remove-btn"
            >
              <nuxt-icon name="trash" filled></nuxt-icon>
            </span>
          </div>
          <div class="flex gap-4 w-full">
            <ui-datepicker
              v-model="item.date"
              date-only
              name="date"
              :min-date="new Date()"
              class="flex-grow"
            ></ui-datepicker>
            <input-hour name="hour" show-quarters v-model="item.start_time">
            </input-hour>
          </div>
          <input-duration name="duration" v-model:minutes="item.duration">
          </input-duration>
          <input-text-area
            :placeholder="$t('wizard.note')"
            name="note"
            v-model="item.note"
          ></input-text-area>
        </div>
        <div class="justify-between flex mt-4">
          <i></i>
          <button @click.prevent="addDate" class="btn btn-xs secondary">
            {{ $t('wizard.add_date') }}
          </button>
        </div>
      </div>
    </div>

    <div class="actions">
      <div>
        <button @click.prevent="emit('cancel')" class="btn secondary">
          {{ $t('general.cancel') }}
        </button>
      </div>
      <input-submit-button
        :loading="saving"
        :disabled="saving"
        @click.prevent="save"
        class="btn primary"
      >
        {{ $t('general.save') }}
      </input-submit-button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  appointment: {
    type: [Object, Boolean],
    default: false,
  },
});
const { $t } = useNuxtApp();
const emit = defineEmits(['hide', 'cancel', 'saved']);
const saving = ref(false);
const form = ref([
  {
    date: props.appointment.date,
    start_time: props.appointment.start_time,
    duration: props.appointment.duration,
    note: '',
  },
]);

const addDate = () => {
  form.value.push({
    date: props.appointment.date,
    start_time: props.appointment.start_time,
    duration: props.appointment.duration,
    note: '',
  });
};

const removeDate = (index) => {
  if (form.value.length == 1) {
    return;
  }
  form.value.splice(index, 1);
};
const save = async () => {
  saving.value = true;
  try {
    const response = await useApi(
      'POST',
      'appointments/duplicate/' + props.appointment.id,
      {
        dates: form.value,
      }
    );
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      form.value = [
        {
          date: props.appointment.date,
          start_time: props.appointment.start_time,
          duration: props.appointment.duration,
          note: '',
        },
      ];
      emit('saved');
    }
  } catch (error) {
  } finally {
    saving.value = false;
  }
};
</script>
