<template>
  <div class="w-full flex flex-col space-y-4 max-w-screen-sm mx-auto">
    <input-select
      :label="$t('wizard.location')"
      name="location_id"
      v-model="wizard.form.location_id"
      :options="locationOptions"
      @change="pickLocation"
      is-object
    ></input-select>

    <template v-if="wizard.form.location_id">
      <div class="w-full grid grid-cols-2 gap-4">
        <input-select
          :label="$t('wizard.artist')"
          name="staff_id"
          text-key="full_name"
          v-model="wizard.form.staff_id"
          :options="staffMembers"
          @change="prePopulate"
          is-object
        ></input-select>
        <input-select
          :label="$t('wizard.service')"
          name="service_id"
          v-model="wizard.form.service_id"
          :options="serviceOptions"
          @change="populateDurationAndPrice"
          is-object
        ></input-select>
        <input-duration
          :label="$t('wizard.duration')"
          name="duration"
          v-model:minutes="wizard.form.duration"
        >
        </input-duration>

        <input-text
          :label="$t('wizard.price')"
          name="price"
          type="number"
          v-model="wizard.form.price"
        ></input-text>

        <ui-datepicker
          name="date"
          :label="$t('wizard.date')"
          date-only
          :min-date="new Date()"
          v-model="wizard.form.date"
        ></ui-datepicker>
        <input-hour
          name="hour"
          :min="minHour"
          :label="$t('wizard.start_time')"
          show-quarters
          v-model="wizard.form.start_time"
        >
        </input-hour>
      </div>
      <input-text-area
        optional
        name="notes"
        v-model="wizard.form.note"
        :label="$t('wizard.note')"
      ></input-text-area>

      <ui-switch-button
        v-model="wizard.hasProject"
        @change="handleProjectData"
        class="py-4"
        name="has_project"
        >{{ $t('wizard.has_project') }}</ui-switch-button
      >
    </template>
  </div>
</template>

<script setup>
const wizard = useWizardStore();

const { data: locationOptions } = await useApi('GET', 'locations');

const { data: staffMembers } = await useApi('GET', 'staff');

const prePopulate = (pickedStaff) => {
  wizard.staff = pickedStaff;
  if (pickedStaff.services.length == 1) {
    wizard.form.service_id = pickedStaff.services[0].id;
    populateDurationAndPrice(pickedStaff.services[0]);
  }
  if (locationOptions.length == 1) {
    wizard.form.location_id = locationOptions[0].id;
  }
};

const minHour = computed(() => {
  const appointmentDate = new Date(wizard.form.date);
  const todaysDate = new Date();
  if (appointmentDate.setHours(0, 0, 0, 0) == todaysDate.setHours(0, 0, 0, 0)) {
    return new Date().getHours() + ':00';
  }
  return useOrganisationStore().calendarSettings.from_time;
});

const populateDurationAndPrice = (service) => {
  if (!service) {
    return;
  }

  wizard.form.duration = service.duration;

  wizard.service = service;
  const hours = Math.ceil(service.duration / 60);
  wizard.form.price = service.price * hours;

  handleProjectData();
};

const serviceOptions = computed(() => {
  if (!wizard.form.staff_id) {
    return [];
  }

  return (
    staffMembers.find((item) => item.id == wizard.form.staff_id).services || []
  );
});

const pickLocation = async (location) => {
  wizard.location = location;
  const response = await useApi('GET', 'staff', { location_id: location.id });
  // staffMembers = response.data;
  // fetch customers based on this later
};

const handleProjectData = () => {
  if (wizard.hasProject) {
    wizard.fetchConsentForm();
  }
};

onMounted(async () => {
  wizard.form.location_id = useOrganisationStore().defaultLocation.id;
  await pickLocation(useOrganisationStore().defaultLocation);

  if (
    !wizard.editMode &&
    parseFloat(minHour.value) > parseFloat(wizard.form.start_time)
  ) {
    wizard.form.start_time = null;
  }
});
</script>
