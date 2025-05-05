<template>
  <div class="card">
    <ui-no-data v-if="!customerStore.appointments.length">
      <template #text>{{ $t('customers.no_appointments') }}</template>
    </ui-no-data>
    <span
      v-for="item in customerStore.appointments"
      :key="item.id"
      class="appointmentCard"
      @click.prevent="setActiveAppointment(item)"
    >
      <nuxt-icon name="calendar-day" filled></nuxt-icon>
      <div>
        <div class="bold">
          <i
            >{{ dayJs(item.date).format(calendarSettings.date_format) }}
            {{ item.start_time }}</i
          >
          <i>{{ useHelpers().niceTime(item.duration) }}</i>
        </div>
        <div class="details">
          <i>{{ item.staff.full_name }} - {{ item.service.name }}</i>
          <i>{{ $t('wizard.price') }}: {{ item.price }}</i>
          <i>{{ $t('wizard.deposit') }}: {{ item.deposit || 0 }}</i>
        </div>
      </div>
    </span>

    <div class="flex justify-center mt-4 pt-4">
      <button @click.prevent="startWizard" class="btn success">
        {{ $t('customers.add_appointment') }}
      </button>
    </div>

    <appointment-wizard
      v-if="wizard.isVisible"
      @updated="eventUpdated"
    ></appointment-wizard>

    <appointment-details
      v-if="activeAppointment"
      :appointment="activeAppointment"
      @hide="activeAppointment = false"
      @deleted="eventUpdated"
      @edit="editAppointment"
      @statusUpdated="eventUpdated"
    ></appointment-details>
  </div>
</template>
<script setup>
const customerStore = useCustomerStore();
const calendarSettings = useOrganisationStore().calendarSettings;
const dayJs = useDayjs();
const wizard = useWizardStore();
const activeAppointment = ref(false);

const setActiveAppointment = (item) => {
  activeAppointment.value = item;
};

const eventUpdated = () => {
  customerStore.fetchCustomer(customerStore.item.id);
  wizard.hide();
  activeAppointment.value = false;
};

const editAppointment = () => {
  wizard.edit(activeAppointment.value);
  activeAppointment.value = false;
};

const startWizard = () => {
  const date = dayJs().format('YYYY-MM-DD');
  const hour = dayJs().add(1, 'hour').format('HH:00');
  wizard.customer = customerStore.item;
  wizard.form.customer_id = customerStore.item.id;
  wizard.start(date, hour);
  wizard.fetchProjects();
};
</script>
