<template>
  <div class="calendarFilters">
    <div class="wrapper">
      <input-multiselect
        v-model="appointmentStore.filters.service_ids"
        label="Service"
        name="service_id"
        :options="services"
        @change="setActiveService"
        hide-empty
        is-object
        :placeholder="$t('general.all')"
        class="sm"
      ></input-multiselect>
      <input-select
        v-model="appointmentStore.filters.status"
        label="Status"
        name="status"
        :options="statusOptions"
        hide-empty
        is-object
        class="sm"
        @change="setActiveStatus"
      ></input-select>
      <input-duration
        v-model:minutes="appointmentStore.filters.duration"
        v-model:operator="appointmentStore.filters.duration_operator"
        label="Duration"
        name="duration"
        with-operator
        class="sm"
        @change="applyDurationFilter"
      ></input-duration>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const appointmentStore = useAppointmentStore();
const organisationStore = useOrganisationStore();
const statusOptions = [
  {
    id: null,
    name: $t('statuses.all'),
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

const setActiveService = (item) => {
  appointmentStore.fetchItems();
};

const setActiveStatus = (item) => {
  appointmentStore.fetchItems();
};
const applyDurationFilter = (item) => {
  appointmentStore.fetchItems();
};

const services = computed(() => {
  if (appointmentStore.staff.length) {
    let allServices = [];
    appointmentStore.staff.forEach((item) => {
      return item.services.forEach((srv) => {
        allServices.push(srv);
      });
    });

    return allServices;
  }
  return organisationStore.services;
});
</script>
