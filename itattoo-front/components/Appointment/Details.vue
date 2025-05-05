<template>
  <div class="appointmentSection">
    <div class="overlay"></div>

    <!-- DUPLICATE FORM -->
    <appointment-duplicate
      v-if="duplicate"
      @cancel="duplicate = false"
      @hide="close"
      @saved="fetchAppointments"
      :appointment="appointment"
    ></appointment-duplicate>

    <!-- APPOINTMENT INFO -->
    <div v-else class="appointment-details">
      <div>
        <div class="header">
          <span>{{ $t('wizard.appointment_details') }}</span>
          <button
            @click.prevent="emit('hide')"
            class="btn tonal btn-icon secondary h-8"
          >
            <nuxt-icon name="x-mark" filled></nuxt-icon>
          </button>
        </div>
        <div class="overflow-hidden p-4 md:p-8 grid grid-cols-1 md:grid-cols-2">
          <div>
            <div class="apt-info-line">
              <span>Status:</span>
              <input-select
                label=""
                v-model="appointment.status"
                name="status"
                :options="statusOptions"
                hide-empty
                is-object
                class="sm"
                @change="setActiveStatus"
              ></input-select>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.date') }}:</span>
              <span>{{
                dayJs(appointment.date).format(
                  useOrganisationStore().calendarSettings.date_format
                )
              }}</span>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.start_time') }}:</span>
              <span>{{ appointment.start_time }}</span>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.duration') }}:</span>
              <span>{{ appointment.duration }} min</span>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.price') }}:</span>
              <span>&euro;{{ appointment.price }}</span>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.deposit') }}:</span>
              <span>&euro;{{ appointment.deposit || 0 }}</span>
            </div>
          </div>
          <div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.artist') }}:</span>
              <span>
                {{ appointment.staff.first_name }}
                {{ appointment.staff.last_name }}
                <i
                  v-if="appointment.staff.is_deleted"
                  class="not-italic text-danger-700 bg-danger-500/20 px-2 py-0.5 rounded-md text-xs ml-1"
                  >{{ $t('general.deleted') }}</i
                >
              </span>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.location') }}:</span>
              <span>{{ appointment.location.name }}</span>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.service') }}:</span>
              <span>{{ appointment.service.name }}</span>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.customer') }}:</span>
              <span>
                {{ appointment.customer.first_name }}
                {{ appointment.customer.last_name }}
              </span>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.note') }}:</span>
              <span>{{ appointment.note }}</span>
            </div>
            <div class="apt-info-line">
              <span>{{ $t('wizard.reminders') }}:</span>
              <span class="flex items-center space-x-4">
                <nuxt-icon
                  filled
                  name="phone"
                  :class="
                    appointment.sms_reminder_sent
                      ? 'text-success-500'
                      : 'text-gray-500'
                  "
                ></nuxt-icon>
                <nuxt-icon
                  :class="
                    appointment.email_reminder_sent
                      ? 'text-success-500'
                      : 'text-gray-500'
                  "
                  filled
                  name="envelope"
                ></nuxt-icon>
              </span>
            </div>
            <div class="apt-info-line gap-5">
              <div v-show="appointment.staff.services.find(service => service.id === appointment.service_id)?.is_online">
                <span class="pt-0.5 bg-success-600 uppercase px-2 pb-1 rounded-md bg-black text-white">Online Booking</span>
              </div>
            </div>
            <div v-show="appointment.customer.vip_name">
              <span
                class="pt-0.5 px-2 pb-1.5 rounded-md text-white uppercase text-[12px] font-bold"
                :style="{ backgroundColor: appointment.customer.vip_color }"
              >
                {{ appointment.customer.vip_name }}
              </span>
            </div>
          </div>
        </div>
      </div>
      <!-- CONFIRM DELETION -->
      <ui-confirm-modal type="danger" ref="confirmModal">
        <template #confirm>{{ $t('general.yes_delete') }}</template>
      </ui-confirm-modal>
      <div class="actions">
        <div>
          <button
            @click.prevent="deleteAppointment"
            class="btn danger outlined mr-4"
          >
            {{ $t('general.delete') }}
          </button>
          <button
            v-if="appointment.project_id"
            @click.prevent="duplicateAppointment"
            class="btn secondary"
          >
            {{ $t('general.duplicate') }}
          </button>
        </div>
        <button @click.prevent="emit('edit')" class="btn primary">
          {{ $t('general.edit') }}
        </button>
      </div>
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
const emit = defineEmits(['hide', 'deleted', 'edit', 'statusUpdated']);
const confirmModal = ref(null);
const dayJs = useDayjs();
const statusOptions = [
  {
    id: null,
    name: $t('statuses.null'),
  },
  {
    id: 'canceled',
    name: $t('statuses.canceled'),
  },
  {
    id: 'deposit',
    name: $t('statuses.deposit'),
  },
  {
    id: 'not_presented',
    name: $t('statuses.not_presented'),
  },
  {
    id: 'completed_unpaid',
    name: $t('statuses.completed_unpaid'),
  },
  {
    id: 'completed_paid',
    name: $t('statuses.completed_paid'),
  },
];
const duplicate = ref(false);

const fetchAppointments = async () => {
  await useAppointmentStore().fetchItems();
  duplicate.value = false;
};

const duplicateAppointment = async () => {
  duplicate.value = true;
};
const setActiveStatus = async (newStatus) => {
  const response = await useApi(
    'PATCH',
    'appointments/change-status/' + props.appointment.id,
    {
      status: newStatus.id,
    }
  );
  if (response) {
    useSnackbarStore().show(response.message, 'success');
    emit('statusUpdated', newStatus.id);
  }
};

const close = () => {
  duplicate.value = false;
  emit('hide');
};

const deleteAppointment = async () => {
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi(
      'DELETE',
      'appointments/' + props.appointment.id
    );
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      emit('deleted');
    }
  }
};
</script>
