<template>
  <div class="card">
    <div class="card-title justify-between">
      <span>{{ $t('staff.time_off') }}</span>
      <button @click.prevent="showForm = true" class="btn btn-xs tonal success">
        {{ $t('staff.add_time_off') }}
      </button>
    </div>
    <div class="flex flex-col gap-4">
      <ui-no-data v-if="!items.length">
        <template #cta>
          <button @click.prevent="showForm = true" class="btn primary">
            <span v-if="isConvention">
              {{ $t('staff.add_convention') }}
            </span>
            <span v-else>
              {{ $t('staff.add_time_off') }}
            </span>
          </button>
        </template>
      </ui-no-data>
      <div v-else>
        <div v-for="item in items" :key="item.id" class="timeOffEntry">
          <div class="flex flex-col">
            <span class="font-semibold mb-2">{{ item.reason }}</span>
            <span class="text-xs font-light mt-1 gap-x-2 flex items-center">
              <em>{{ dayjs(item.start_date).format('DD/MM/YYYY') }}</em> to
              <em>{{ dayjs(item.end_date).format('DD/MM/YYYY') }}</em>

            </span>
          </div>
          <button @click.prevent="deleteTimeOff(item)" class="btn btn-icon danger tonal flex-grow-0">
            <nuxt-icon filled name="trash"></nuxt-icon>
          </button>
        </div>
      </div>
    </div>

    <!-- Time Off FORM -->
    <ui-modal @close="showForm = false" :visible="showForm">
      <template #title>
        <span v-if="isConvention">
          {{ $t('staff.add_convention') }}
        </span>
        <span v-else>
          {{ $t('staff.add_time_off') }}
        </span>
      </template>
      <template #description>&nbsp;</template>
      <template #content>
        <form class="grow w-full h-full flex flex-col gap-4 text-left" @submit.prevent="createTimeOff">
          <input-text v-model="form.reason" :label="isConvention ? $t('staff.convention_name') : $t('staff.reason')
            " name="reason" required></input-text>
          <div class="grid grid-cols-2 gap-x-4">
            <ui-datepicker v-model="form.start_date" :label="$t('general.from')" name="start_date"
              required></ui-datepicker>
            <ui-datepicker v-model="form.end_date" :label="$t('general.to')" name="end_date" required></ui-datepicker>
          </div>

          <div class="inline-flex mt-5 justify-between">
            <button @click.prevent="hideForm" class="btn secondary">
              {{ $t('general.cancel') }}
            </button>
            <input-submit-button class="primary" :loading="saving">
              {{ $t('general.save') }}
            </input-submit-button>
          </div>
        </form>
      </template>
    </ui-modal>

    <!-- CONFIRM DELETION -->
    <ui-confirm-modal type="danger" ref="deleteModal">
      <template #confirm>{{ $t('general.yes_delete') }}</template>
    </ui-confirm-modal>
  </div>
</template>

<script setup>
const route = useRoute();
const emit = defineEmits(['update']);
const props = defineProps({
  items: {
    type: Array,
    default: [],
  },
  isConvention: {
    type: Boolean,
    default: false,
  },
  orgId: {
    type: Number,
    required: true,
  },
});

const deleteModal = ref(null);
const showForm = ref(false);
const saving = ref(false);
const dayjs = useDayjs();
const form = reactive({
  reason: '',
  staff_id: route.params.id,
  organisation_id: props.orgId,
  start_date: '',
  end_date: '',
  is_convention: props.isConvention,
});

const deleteTimeOff = async (item) => {
  const confirmed = await deleteModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'time-off/' + item.id);
    if (response) {
      emit('update');
      useAppointmentStore().updateAvilability();
      useSnackbarStore().show(response.message, 'success');
    }
  }
};

const createTimeOff = async () => {
  useValidationStore().clearErrors();

  saving.value = true;
  try {
    const response = await useApi('POST', 'time-off', form);
    if (response) {
      emit('update');
      useAppointmentStore().updateAvilability();
      hideForm();
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};

const hideForm = () => {
  showForm.value = false;
  useValidationStore().clearErrors();
};
</script>
