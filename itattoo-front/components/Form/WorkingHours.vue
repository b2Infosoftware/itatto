<template>
  <div class="card">
    <div class="justify-between card-title min-w">
      <span>{{ $t('staff.working_hours') }}</span>
      <span class="min-w-48">
        <input-select
          name="location_id"
          v-model="locationId"
          :options="user.locations"
          is-object
          class="sm"
        ></input-select>
      </span>
    </div>
    <div>
      <div
        v-for="(schedule, day) in availabilities"
        :key="day"
        class="workingDay"
      >
        <div class="flex pt-1">
          <ui-switch-button
            :name="day"
            v-model="availabilities[day][0].is_available"
            @change="toggleDay(day)"
            >{{ $t('days.' + day) }}</ui-switch-button
          >
        </div>
        <div class="intervals">
          <div
            class="flex gap-x-4 flex-grow relative"
            v-for="(interval, key) in schedule"
            :key="interval.id"
          >
            <span v-if="!interval.is_available" class="is-break">Break</span>
            <input-select
              class="flex-grow"
              v-model="availabilities[day][key].start_time"
              :options="useSchedule().hourIntervals"
              name="to_time"
              label=""
              :disabled="!availabilities[day][0].is_available"
              required
              @change="updateHours(interval)"
            ></input-select>
            <input-select
              class="flex-grow"
              v-model="availabilities[day][key].end_time"
              :options="useSchedule().hourIntervals"
              name="to_time"
              label=""
              :disabled="!availabilities[day][0].is_available"
              required
              @change="updateHours(interval)"
            ></input-select>
            <button
              class="btn btn-xs primary tonal w-20"
              v-if="key == 0"
              :disabled="!interval.is_available"
              @click.prevent="addBreak(day)"
            >
              <span class="text-center w-full">Add break</span>
            </button>
            <button
              class="btn btn-xs danger tonal w-20"
              v-else
              @click.prevent="removeBreak(day, key)"
            >
              <span class="text-center w-full">Remove</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const locationId = ref(props.user.default_location_id);
const userAvailabilities = props.user.availability;

const availabilities = computed(() => {
  const locationAvailability = userAvailabilities.filter((item) => {
    return item.location_id == locationId.value;
  });
  return useHelpers().groupBy(locationAvailability, 'day');
});

const addBreak = async (day) => {
  const mainSlot = availabilities.value[day][0];
  const form = {
    start_time: mainSlot.start_time,
    end_time: mainSlot.end_time,
    day: mainSlot.day,
    location_id: parseInt(locationId.value),
    staff_id: mainSlot.staff_id,
    is_available: false,
  };

  try {
    const response = await useApi('POST', 'availabilities', form);
    if (response) {
      userAvailabilities.push(response.data);
      useSnackbarStore().show(response.message, 'success');
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }
};

const removeBreak = async (day, index) => {
  const id = availabilities.value[day][index].id;
  try {
    const response = await useApi('DELETE', 'availabilities/' + id);
    if (response) {
      userAvailabilities.forEach((item, key) => {
        if (item.id == id) {
          userAvailabilities.splice(key, 1);
        }
      });
      useSnackbarStore().show(response.message, 'success');
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }
};

const toggleDay = async (day) => {
  const form = availabilities.value[day][0];
  const url = 'availabilities/' + form.id;
  try {
    const response = await useApi('PATCH', url, form);
    if (response) {
      useAppointmentStore().updateAvilability();
      useSnackbarStore().show(response.message, 'success');
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }
};

const updateHours = async (interval) => {
  const url = 'availabilities/' + interval.id;
  try {
    const response = await useApi('PATCH', url, interval);
    if (response) {
      useAppointmentStore().updateAvilability();
      useSnackbarStore().show(response.message, 'success');
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }
};
</script>
