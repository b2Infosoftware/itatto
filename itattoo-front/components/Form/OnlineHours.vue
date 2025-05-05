<template>
  <div class="card">
    <div class="justify-between card-title min-w">
      <span>{{ $t("staff.online_hours") }}</span>
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
        v-for="(schedules, day) in editableSchedules"
        :key="day"
        class="workingDay"
      >
        <div class="flex pt-1 flex-col">
          {{ $t("dayname." + day) }}
          <button
            class="btn btn-xs primary tonal w-28 mt-4"
            @click="addSlot(day)"
          >
            <span class="text-center w-full py-1 px-1 block">Add Slot</span>
          </button>
        </div>
        <div class="intervals" v-if="schedules.length > 0">
          <div
            v-for="(interval, index) in schedules"
            :key="interval.id || index"
            class="flex mt-5"
          >
            <div class="flex flex-col flex-1 mr-4">
              <div class="flex gap-x-4 flex-grow relative mb-2">
                <input-select
                  class="flex-grow"
                  v-model="interval.start_time"
                  name="from_time"
                  :options="useSchedule().hourIntervals"
                  label=""
                  required
                  @change="onFromTimeChange(day, index, interval.start_time)"
                ></input-select>
                <input-select
                  class="flex-grow"
                  v-model="interval.end_time"
                  name="to_time"
                  :options="getFilteredEndTimeOptions(interval.start_time)"
                  label=""
                  required
                ></input-select>
              </div>
              <input-multiselect
                class="flex-grow z-90"
                v-model="interval.service_ids"
                :options="serviceIsOnline"
                isObject
                name="service"
              ></input-multiselect>
            </div>
            <div class="inline-block">
              <button
                class="btn btn-xs danger tonal"
                @click.prevent="removeSlot(day, index, interval.id)"
                v-if="schedules.length > 1"
              >
                <span class="text-center w-full">Remove</span>
              </button>
              <ui-switch-button
                class="mt-5"
                :name="day + '-' + index"
                v-model="interval.is_available"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
function debounce(fn, delay) {
  let timer;
  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(() => {
      fn(...args);
    }, delay);
  };
}

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const locationId = ref(props.user.default_location_id);
const userAvailabilities = ref([...props.user.online_hours]);

const allDays = [1, 2, 3, 4, 5, 6, 7];
const dayNames = [
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
  "Sunday",
];

const serviceIsOnline = computed(() => {
  return props.user.services
    .filter((item) => item.is_online == 1)
    .map((item) => ({
      id: item.id,
      name: item.name,
    }));
});

const initialAvailabilities = computed(() => {
  const grouped = useHelpers().groupBy(
    userAvailabilities.value.filter(
      (item) => item.location_id == locationId.value
    ),
    "day"
  );

  const result = {};
  allDays.forEach((dayNum) => {
    const dayKey = dayNames[dayNum - 1];
    if (grouped[dayNum]) {
      result[dayKey] = grouped[dayNum].map((item) => ({
        id: item.id,
        day: dayKey,
        day_num: parseInt(item.day, 10),
        start_time: item.start_time.slice(0, 5),
        end_time: item.end_time.slice(0, 5),
        is_available: !!item.is_available,
        service_ids: item.services.map((service) => service.id),
      }));
    } else {
      result[dayKey] = [
        {
          id: null,
          day: dayKey,
          day_num: dayNum,
          start_time: "09:00",
          end_time: "17:00",
          is_available: false,
          service_ids: [],
        },
      ];
    }
  });
  return result;
});
const editableSchedules = ref(
  JSON.parse(JSON.stringify(initialAvailabilities.value))
);

watch(locationId, (newVal) => {
  editableSchedules.value = JSON.parse(
    JSON.stringify(initialAvailabilities.value)
  );
});

// Fungsi untuk filter opsi end_time agar selalu >= start_time
const getFilteredEndTimeOptions = (startTime) => {
  // Asumsi: useSchedule().hourIntervals mengembalikan array string dalam format "HH:MM"
  return useSchedule().hourIntervals.filter(time => time >= startTime);
};

// Handler saat start_time berubah
const onFromTimeChange = (day, index, newStartTime) => {
  const interval = editableSchedules.value[day][index];
  // Kalau end_time sekarang kurang dari start_time baru, set end_time jadi sama dengan start_time
  if (interval.end_time < newStartTime) {
    interval.end_time = newStartTime;
  }
};

const syncOnlineHours = async () => {
  let payload = [];
  Object.keys(editableSchedules.value).forEach((dayKey) => {
    editableSchedules.value[dayKey].forEach((schedule) => {
      payload.push({
        staff_id: props.user.id,
        organisation_id: props.user.default_organisation_id,
        location_id: parseInt(locationId.value),
        start_time:
          schedule.start_time.length === 5
            ? schedule.start_time + ":00"
            : schedule.start_time,
        end_time:
          schedule.end_time.length === 5
            ? schedule.end_time + ":00"
            : schedule.end_time,
        day: schedule.day_num,
        is_available: schedule.is_available,
        service: schedule.service_ids,
      });
    });
  });
  try {
    const response = await useApi("PATCH", "sync-online-hour", payload);
    if (response) {
      useSnackbarStore().show("Online hours synchronized", "success");
    }
  } catch (error) {
    useSnackbarStore().show("Failed to synchronize online hours", "error");
  }
};

const debouncedSync = debounce(syncOnlineHours, 1500);

watch(
  editableSchedules,
  () => {
    debouncedSync();
  },
  { deep: true }
);

const addSlot = (day) => {
  const newSlot = {
    id: null,
    day: day,
    day_num: dayNames.indexOf(day) + 1,
    start_time: "09:00",
    end_time: "17:00",
    is_available: false,
    service_ids: [],
  };
  if (editableSchedules.value[day]) {
    editableSchedules.value[day].push(newSlot);
  } else {
    editableSchedules.value[day] = [newSlot];
  }
};

const removeSlot = (day, index, id) => {
  if (editableSchedules.value[day]) {
    editableSchedules.value[day].splice(index, 1);
  }
};
</script>
