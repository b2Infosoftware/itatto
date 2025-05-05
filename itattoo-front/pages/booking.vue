<template>
  <div>
    <div class="flex flex-col justify-between space-y-4 md:flex-row">
      <div>
        <h1 class="text-2xl">Booking List</h1>
        <p class="leading-tight opacity-60">
          Manage Booking which your company provides
        </p>
      </div>
    </div>

    <!-- CATEGORY FILTER -->
    <div class="form-group">
      <label class="block pb-2 mt-8">Status</label>
    </div>
    <div class="serviceCategories">
      <button
        @click.prevent="setStatus(0)"
        class="btn btn-sm"
        :class="activeStatus == 0 ? 'primary' : 'tonal slate'"
      >
        {{ $t("general.all") }}
      </button>
      <button
        @click.prevent="setStatus(item.id)"
        v-for="item in status_list"
        :key="item.id"
        class="btn btn-sm"
        :class="item.id == activeStatus ? 'primary' : 'tonal primary'"
      >
        {{ item.name }}
      </button>
    </div>

    <div class="table-wrapper" style="overflow: visible">
      <table>
        <thead>
          <tr>
            <th>User</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Is Reschedule</th>
            <th>Updated By</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="servicesSection">
          <tr v-for="item in filterBooking" :key="item.id">
            <td class="capitalize">
              <span class="th">Name</span>
              <span>{{ item.first_name }} {{ item.last_name }}</span>
              <small class="hidden mt-1 text-sm text-gray-500 md:block">{{
                item.email
              }}</small>
            </td>
            <td>
              <span class="th">Service</span>
              <span>
                {{ item.service.name }}
              </span>
            </td>
            <td>
              <span class="th">Date</span>
                <span>
                  {{ formatDateForInput(item.date) }}
                </span>
            </td>
            <td>
              <span class="th">Time</span>
              <span>
                {{ formatTimeForInput(item.start_time) + " - " + formatTimeForInput(item.end_time) }}
              </span>
            </td>
            <td>
              <span class="th">Is Reschedule</span>
              <span v-if="item.is_reschedule"> Yes </span>
              <span v-else> No </span>
            </td>
            <td>
              <span class="th"> Updated By</span>
              <span v-if="item.status_updated_by">
                {{ item.staff.first_name.concat(" ", item.last_name) }}
              </span>
              <span v-else> No Set Data </span>
            </td>
            <td>
              <span class="th"> Status</span>
              <span
                v-if="item.status == 1"
                class="px-2 py-1 text-white rounded-md bg-success-500"
              >
                Approved
              </span>
              <span
                v-else-if="item.status == 2"
                class="px-2 py-1 text-white rounded-md bg-danger-500"
              >
                Rejected
              </span>
              <span
                v-else
                class="px-2 py-1 text-white rounded-md bg-warning-400"
              >
                Pending
              </span>
            </td>

            <td class="actions">
              <span>
                <booking-action
                  ref="dropdownMenu"

                  :visible="activeDropdownId === item.id"
                  @toggle="toggleDropdown(item.id)"
                  @action="handleDropdownAction($event, item.id)"
                  @hadlerReschedule="handleRescheduleModalShow(item)"
                />
              </span>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="fetchedData && !filterBooking.length" class="mt-5 card">
        <ui-no-data>
          <template #text>You do not have any bookings yet.</template>
        </ui-no-data>
      </div>

      <ui-pagination class="justify-end mt-4" :meta="meta"></ui-pagination>
    </div>

    <ui-modal
      @close="showModalReschedule = false"
      :visible="showModalReschedule"
    >
      <template #title>Reschedule</template>
      <template #description>.</template>
      <template #content>
        <form
          class="flex flex-col w-full h-full gap-4 text-left grow"
          @submit.prevent="handleSubmit"
        >
          <input-text
            v-model="rescheduleForm.date"
            label="Date"
            name="date"
            type="date"
            required
          ></input-text>
          <div class="flex w-full grid-cols-2 gap-3">
            <input-hour
              v-model="rescheduleForm.start_time"
              label="Start Time"
              name="start_time"
              class="w-full"
              required
            ></input-hour>
            <input-hour
              v-model="rescheduleForm.end_time"
              label="End Time"
              name="end_time"
              class="w-full"
              required
            ></input-hour>
          </div>
          <div class="inline-flex justify-between mt-5">
            <button @click.prevent="showModalReschedule" class="btn secondary">
              {{ $t("general.cancel") }}
            </button>
            <input-submit-button class="primary" :loading="isLoddingReschedule">
              {{ $t("general.save") }}
            </input-submit-button>
          </div>
        </form>
      </template>
    </ui-modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
const { $t } = useNuxtApp();
const route = useRoute();
const status_list = [
  { id: 1, name: "Approved", color: "bg-green-500" },
  { id: 2, name: "Rejected", color: "bg-red-500" },
  { id: 3, name: "Pending", color: "bg-yellow-500" },
];

const calendarSettings = ref(null);
const bookingList = ref([]);
const filterBooking = ref([]);
const activeStatus = ref(0);
const fetchedData = ref(false);
const activeDropdownId = ref(null);
const showModalReschedule = ref(false);
const isLoddingReschedule = ref(false);
const rescheduleForm = reactive({
  id: null,
  date: "",
  start_time: "",
  end_time: "",
});

const meta = reactive({
  current_page: route.query?.page || 1,
  from: 1,
  last_page: 1,
  per_page: 5,
  to: 1,
  total: 1,
});

const fetchBookList = async () => {
  const page =  route.query?.page || 1;
  const result = await useApi("GET", `/book-list?page=${page}`);
  bookingList.value = result.data
  for(const key in meta) {
    meta[key] = result[key];
  }
  await filteredBookingList();
};

const filteredBookingList = () => {
  if (activeStatus.value === 0) {
    filterBooking.value = bookingList.value;
  } else {
    filterBooking.value = bookingList.value.filter(
      (item) => item.status == activeStatus.value
    );
  }
};

const toggleDropdown = (index) => {
  activeDropdownId.value = activeDropdownId.value === index ? null : index;
};

const handleDropdownAction = async (data, id) => {
  const actions = {
    1: "/book/approved/",
    2: "/book/rejected/",
  };
  const response = await useApi("PATCH", actions[data] + id);
  useSnackbarStore().show(response.message, "success");
  await fetchBookList();
  activeDropdownId.value = null;
};

watch(
  () => route.query,
  (value) => {
    if (value.page != meta.page) {
      fetchBookList();
    }
  }
);

const setStatus = (id) => {
  activeStatus.value = id;
  filteredBookingList();
};

const handleRescheduleModalShow = (item) => {
  Object.assign(rescheduleForm, {
    date: formatDateForInput(item.date),
    start_time: formatTimeForInput(item.start_time),
    end_time: formatTimeForInput(item.end_time),
    id: item.id,
  });
  toggleDropdown(item.id)
  showModalReschedule.value = !showModalReschedule.value;
};

const handleSubmit = async () => {
  useValidationStore().clearErrors();

  isLoddingReschedule.value = true;
  try {
    const res = await useApi("PATCH", `/book/reschedule/${rescheduleForm.id}`, {
      date: rescheduleForm.date,
      start_time: rescheduleForm.start_time,
      end_time: rescheduleForm.end_time,
    });
    if (res) {
      useSnackbarStore().show(res.message, "success");
      await fetchBookList();
      showModalReschedule.value = false;
      Object.assign(rescheduleForm, {
        date: '',
        start_time: '',
        end_time: '',
        id: null,
      });
    }
  } catch (error) {
    useValidationStore().populateErrors(error);
  }
  isLoddingReschedule.value = false;
};

const formatDateForInput = (dateString) => {
  const date = new Date(dateString);
  const day = ("0" + date.getDate()).slice(-2);
  const monthNumber = ("0" + (date.getMonth() + 1)).slice(-2);
  const monthAbbr = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"][date.getMonth()];
  const year = date.getFullYear();
  const format = calendarSettings.value?.date_format || "DD-MM-YYYY";
  return format.replace(/(YYYY|YY|MMM|MM|DD|D)/g, (token) => {
    switch (token) {
      case "YYYY":
        return year;
      case "YY":
        return String(year).slice(-2);
      case "MMM":
        return monthAbbr;
      case "MM":
        return monthNumber;
      case "DD":
        return day;
      case "D":
        return String(date.getDate());
      default:
        return token;
    }
  });
};

const formatTimeForInput = (timeString) => {
  return timeString?.split("T")[1].slice(0, 5) ?? ''; 
};

const fetchCalendarSettings = async () => {
  try {
    const response = await useApi("GET", "/calendar-settings");
    calendarSettings.value = response.data;
  } catch (error) {
    console.error("Gagal fetch calendar settings:", error);
  }
};

onMounted(async () => {
  await fetchBookList();
  await fetchCalendarSettings();
  fetchedData.value = true;
});
</script>
