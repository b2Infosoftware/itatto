<template>
  <div class="public-booking">
    <div class="booking-card">
      <div class="booking-card-limit">

        <div class="booking-header">
          <div class="profil">
            <div class="booking-organisation">
              <img :src="validateMedia(getData?.media?.find(m => m.media_type == '1')?.full_path, 'https://i.imgur.com/fKzbUq7.png')" alt="Profile Media" />
            </div>
            <div class="description">
              <h2>{{ getData?.organisation?.name || " " }}</h2>
              <p>{{ getData?.organisation?.description || " " }}</p>
            </div>

            <div class="booking-logo">
              <nuxt-link to="/">
                <img class="large" src="/images/logo/white.png" alt="iTattoo logo" />
              </nuxt-link>
            </div>
          </div>
          <div class="banner">
            <img :src="validateMedia(getData?.media?.find(m => m.media_type == '2')?.full_path, 'https://i.imgur.com/jxsEUNq.jpeg')" alt="Banner Media" />
          </div>
        </div>

      <div class="wizard-sidebar" v-if="getData?.is_open == 1">
        <div class="wizard-space">
          <ul>
            <li 
              v-for="(step, index) in steps.slice(0, steps.length - 1)"
              :key="index"
              :class="{ active: currentStep === index, clickable: canNavigateToStep(index) }"
            >
              <span>{{ step.id }}</span>
              <strong>{{ step.label }}</strong>
            </li>
          </ul>
        </div>
      </div>
      
        <div class="wizard-content">
          <div class="wizard-space-content">
            <div class="wizard-mobile-space" v-if="getData?.is_open == 1">

              <div v-if="currentStep === 0" class="max-w-[60%] max-md:max-w-[100%] mx-auto">
                <h3>{{ $t("booking.select_location") }}</h3>
                <p>{{ $t("booking.select_location_description") }}</p>
                <select v-model="selectedLocation" @change="fetchCategories">
                  <option v-for="location in locations" :key="location.id" :value="location.id">
                    {{ location.name }}
                  </option>
                </select>
              </div>
            
              <div v-if="currentStep === 1" class="max-w-[60%] max-md:max-w-[100%] mx-auto">
                <h3>{{ $t("booking.select_category") }}</h3>
                <p>{{ $t("booking.select_category_description") }}</p>
                <select v-model="selectedCategory">
                  <option value="" disabled selected>
                    {{ $t("booking.select_category") }}
                  </option>
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    {{ category.name }}
                  </option>
                </select>
              </div>
            
              <div v-if="currentStep === 2" class="max-w-[60%] max-md:max-w-[100%] mx-auto">
                <div v-show="isLoadingServices">Loading services...</div> 
                <h3>{{ $t("booking.service_select_category") }}</h3>
                <p>{{ $t("booking.service_category_description") }}</p>
                <hr />

                <div v-if="!isLoadingServices && services.length > 0" class="radio-box">
                  <div v-for="service in services" :key="service.id">
                    <label
                      class="radio-item"
                      :class="{ active: selectedService === service.id }"
                    >
                      <input
                        type="radio"
                        :value="service.id"
                        v-model="selectedService"
                      />
                      <span class="title">{{ service.name }}</span>
                      <span class="price">$ {{ service.price }}</span>
                      <span class="duration">{{ service.duration }} {{ $t("booking.minutes") }}</span>
                    </label>
                  </div>
                </div>

                <p v-else-if="!isLoadingServices" class="no-service-message">No services available.</p>
                <hr />
              </div>
        
              <div v-if="currentStep === 3" class="max-w-[60%] max-md:max-w-[100%] mx-auto">
                <h3>{{ $t("booking.select_staff") }}</h3>
                <p>{{ $t("booking.select_staff_description") }}</p>
                <hr />
                <div class="radio-box">
                  <div v-for="staff in staff" :key="staff.id">
                    <label
                      class="text-center radio-item"
                      :class="{ active: selectedStaff === staff.id }"
                    >
                      <input type="radio" :value="staff.id" v-model="selectedStaff" />
                      <span class="title">{{ staff.name }}</span>
                    </label>
                  </div>
                </div>
                <hr />
              </div>

              <div v-if="currentStep === 4" class="max-w-[80%] max-md:max-w-[100%] mx-auto">
                <h3>{{ $t("booking.time_slot") }}</h3>
                <p>{{ $t("booking.time_slot_description") }}</p>
                <hr />
                <div class="slot-outer flex flex-row">
                  <div class="slot-left w-3/4">
                    <div class="slot-left-gap mr-10 max-md:mr-0">
                      <ui-calendar
                        :workdays="workdays"
                        :restdays="restdays"
                        :specialdays="specialDay"
                        v-model="selectedDate"
                      />
                    </div>
                  </div>
                  <div class="slot-right w-1/4 max-h-[100%] overflow-auto my-5">
                    <div class="radio-box">
                        <div
                          class="mb-5"
                          v-for="timeSlot in availableTimes"
                          :key="timeSlot"
                        >
                          <label
                            class="text-center radio-item"
                            :class="{ active: selectedTime === timeSlot }"
                          >
                            <input
                              type="radio"
                              :value="timeSlot"
                              v-model="selectedTime"
                            />
                            <span class="title m-auto">{{ timeSlot }}</span>
                          </label>
                      </div>
                    </div>
                  </div>
                </div>
                <hr />
              </div>
            
              <div v-if="currentStep === 5" class="max-w-[60%] max-md:max-w-[100%] mx-auto">
                <h3>{{ $t("booking.personal_information") }}</h3>
                <p>{{ $t("booking.personal_description") }}</p>

                <label class="block font-medium text-gray-500">First Name</label>
                <input type="text" name="first_name" v-model="form.first_name" class="mt-2 border p-2 w-full rounded bg-gray-100 cursor-pointer mb-3" style="color: grey; border-color: grey;"/>

                <label class="block font-medium text-gray-500">Last Name</label>
                <input type="text" name="last_name" v-model="form.last_name" class="mt-2 border p-2 w-full rounded bg-gray-100 cursor-pointer mb-3" style="color: grey; border-color: grey;"/>

                <label class="block font-medium text-gray-500">Email</label>
                <input type="text" name="email" v-model="form.email" class="mt-2 border p-2 w-full rounded bg-gray-100 cursor-pointer mb-3" style="color: grey; border-color: grey;"/>

                <input-phone
                  v-model="form.phone_number"
                  label="Phone Number"
                  name="phone_number"
                  required
                  class="-mb-5 text-gray-600"
                />
                <div class="accept-tos">
                  <div class="accept-tos-list">
                    <input type="checkbox" v-model="acceptPrivacyTerms" />
                    <p>
                      {{ $t("auth.accept_privacy_terms") }}
                      <a href="/privacy" target="_blank">{{
                        $t("auth.privacy_link")
                      }}</a>
                    </p>
                  </div>
                  <div class="accept-tos-list">
                    <input type="checkbox" v-model="acceptTermsCondition" />
                    <p>
                      {{ $t("auth.accept_terms_conditions") }}
                      <a href="/terms" target="_blank">{{ $t("auth.terms_link") }}</a>
                    </p>
                  </div>
                  <div class="accept-tos-list">
                    <input type="checkbox" v-model="subscribeNewsletter" />
                    <p>
                      {{ $t("booking.subscribe") }}
                      <a href="/newsletter" target="_blank">{{
                        $t("booking.newsletter")
                      }}</a>
                    </p>
                  </div>
                </div>
              </div>
            
              <div v-if="currentStep === 6" class="max-w-[60%] max-md:max-w-[100%] mx-auto">
                <h3>{{ $t("booking.confirm") }}</h3>
                <p>{{ $t("booking.confirm_description") }}</p>
                <div class="confirm-block">
                  <h2>{{ $t("booking.booking_details") }}</h2>
                  <ul>
                    <li>
                      <strong>{{ $t("booking.selected_organisation") }}</strong>
                      <span>{{ historydata.name }}</span>
                    </li>
                    <li>
                      <strong>{{ $t("booking.selected_category") }}</strong>
                      <span>{{ historydata.category.name }}</span>
                    </li>
                    <li>
                      <strong>{{ $t("booking.selected_service") }}</strong>
                      <span>{{ historydata.service.name }}</span>
                    </li>
                    <li>
                      <strong>{{ $t("booking.selected_staff") }}</strong>
                      <span>{{ historydata.staff.name }}</span>
                    </li>
                    <li>
                      <strong>{{ $t("booking.selected_date") }}</strong>
                      <span>{{ selectedDate }}</span>
                    </li>
                    <li>
                      <strong>{{ $t("booking.selected_time") }}</strong>
                      <span>{{ selectedTime }}</span>
                    </li>
                  </ul>
                  <h2>{{ $t("booking.personal_data") }}</h2>
                  <ul>
                    <li>
                      <strong>{{ $t("booking.first_name") }}</strong
                      ><span>{{ form.first_name }}</span>
                    </li>
                    <li>
                      <strong>{{ $t("booking.last_name") }}</strong
                      ><span>{{ form.last_name }}</span>
                    </li>
                    <li>
                      <strong>{{ $t("booking.email") }}</strong
                      ><span>{{ form.email }}</span>
                    </li>
                    <li>
                      <strong>{{ $t("booking.phone_number") }}</strong
                      ><span>{{ form.phone_number }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            
              <div class="wizard-navigation max-w-[60%] mx-auto max-md:max-w-[100%]">
                <button
                  class="back"
                  @click="prevStep"
                  :disabled="currentStep === 0"
                  :class="{ 'first-step': currentStep === 0 }"
                  v-if="currentStep < steps.length - 1" 
                >
                  {{ $t("booking.back") }}
                </button>

                <button
                  class="next"
                  @click="nextStep"
                  v-if="currentStep < steps.length - 2" 
                >
                  {{ $t("booking.next") }}
                </button>

                <input-submit-button
                  v-if="currentStep === steps.length - 2" 
                  class="primary"
                  :loading="isLoading"
                  @click.prevent="handleSubmit"
                >
                  {{ $t("general.save") }}
                </input-submit-button>
              </div>

              <div v-if="currentStep === 7" class="single-alert">
                <h3 class="text-success-500">Successfully Booked!</h3>
              </div>
            
            </div>
            <div v-if="getData?.is_open === 0" class="single-alert">
              <h3 class="text-danger-500">Public Booking Disabled</h3>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from "vue-router";

const route = useRoute();
const { $t } = useNuxtApp();

const currentStep = ref(0);
const steps = ref([
  { id: 1, label: $t("booking.select_location") },
  { id: 2, label: $t("booking.step_category") },
  { id: 3, label: $t("booking.step_service") },
  { id: 4, label: $t("booking.step_staff") },
  { id: 5, label: $t("booking.step_time_slot") },
  { id: 6, label: $t("booking.personal_information") },
  { id: 7, label: $t("booking.step_confirm") },
  { id: 8, label: $t("booking.success") },
]);

const isLoadingServices = ref(true);
const isLoading = ref(false);
const getData = ref(null);
const locations = ref([]);
const categories = ref([]);
const services = ref([]);
const staff = ref([]);
const availableTimes = ref([]);
const selectedLocation = ref(null);
const selectedCategory = ref(null);
const selectedService = ref(null);
const selectedStaff = ref(null);
const selectedTime = ref(null);
const selectedDate = ref(null);
const resTime = ref(null)
const workdays = ref([])
const restdays = ref([]);
const specialDay = ref([])
const acceptPrivacyTerms = ref(false);
const acceptTermsCondition = ref(false);
const subscribeNewsletter = ref(false)
const organitation_id = ref(null); 
const historydata = ref(null);

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone_number: ''
});

const slug = route.params.slug

const getCurrentTime = () => {
  const now = new Date();
  
  const year = now.getFullYear();
  const month = (now.getMonth() + 1).toString().padStart(2, "0"); 
  const day = now.getDate().toString().padStart(2, "0");
  const hours = now.getHours().toString().padStart(2, "0");
  const minutes = now.getMinutes().toString().padStart(2, "0");
  const seconds = now.getSeconds().toString().padStart(2, "0");

  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
};

const fetchLocations = async () => {
  const res = await useApi("GET", `book/${slug}?&current_time=${encodeURIComponent(getCurrentTime())}`)
  organitation_id.value = res.data.organisation.id
  locations.value = res.data.locations;
};

const fetchCategories = async () => {
  if (!selectedLocation.value) return;
  const res = await useApi("GET", `book/${slug}?location_id=${selectedLocation.value}&current_time=${encodeURIComponent(getCurrentTime())}`);
  categories.value = res.data.categories;
};

const fetchServices = async () => {
  isLoadingServices.value = true;
  if (!selectedCategory.value || !selectedLocation.value) {
    services.value = []; 
    isLoadingServices.value = false;
    return;
  }
  try {
    const res = await useApi(
      "GET",
      `book/${slug}?category_id=${selectedCategory.value}&location_id=${selectedLocation.value}&current_time=${encodeURIComponent(getCurrentTime())}`
    );
    services.value = res.data.services || []; 
  } catch (error) {
    console.error("Error fetching services:", error);
    services.value = []; 
  } finally {
    isLoadingServices.value = false;
  }
};

const fetchStaff = async () => {
  if (!selectedService.value || !selectedCategory.value || !selectedLocation.value) return;
  const res = await useApi("GET", `book/${slug}?category_id=${selectedCategory.value}&location_id=${selectedLocation.value}&service_id=${selectedService.value}&current_time=${encodeURIComponent(getCurrentTime())}`)
  staff.value = res.data.staff;
};

const fetchTimes = async () => {
  if (!selectedStaff.value || !selectedService.value || !selectedCategory.value || !selectedLocation.value) return;

  const history = await useApi("GET", `book/${slug}?category_id=${selectedCategory.value}&location_id=${selectedLocation.value}&service_id=${selectedService.value}&staff_id=${selectedStaff.value}&current_time=${encodeURIComponent(getCurrentTime())}`)
  historydata.value = history.data.organisation 
};

const fetchTimeSeletedDate = async () => {
  if (!selectedStaff.value || !selectedService.value || !selectedCategory.value || !selectedLocation.value) return ;

  const response = await useApi(
  "GET",
  `book/show-staff-time/${selectedService.value}?selected_date=${selectedDate.value}&organitation_id=${organitation_id.value  }&location_id=${selectedLocation.value}&staff_id=${selectedStaff.value}&current_time=${encodeURIComponent(getCurrentTime())}`
);

  if(response) {
    availableTimes.value = response.slots
    workdays.value = response.active
    restdays.value = response.in_active
  }
}

watch(selectedDate, async () => {
  await fetchTimeSeletedDate()
})

const nextStep = () => {
  if (currentStep.value === 0) {if (!selectedLocation.value) {useSnackbarStore().show("Please select a location to continue.", 'error'); return;}}
  if (currentStep.value === 1) {if (!selectedCategory.value) {useSnackbarStore().show("Please select a category to continue.", 'error'); return;}}
  if (currentStep.value === 2) {if (!selectedService.value) {useSnackbarStore().show("Please select a service to continue.", 'error'); return;}}
  if (currentStep.value === 3) {if (!selectedStaff.value) {useSnackbarStore().show("Please select a staff member to continue.", 'error'); return;}}
  if (currentStep.value === 4) {if (!selectedDate.value || !selectedTime.value) {useSnackbarStore().show("Please select a date and time to continue.", 'error');return;}}
  if (currentStep.value === 5) {if (!form.first_name || !form.last_name || !form.email || !form.phone_number || form.phone_number.length < 4 || !acceptPrivacyTerms.value || !acceptTermsCondition.value) {
    useSnackbarStore().show("Please fill out all required personal information and accept the terms to continue.", 'error');
      return;
  }}
  if (currentStep.value < steps.value.length - 1) {
    currentStep.value++;
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

const canNavigateToStep = (index) => {
  return index <= currentStep.value;
};

const handleSubmit = async () => {
  isLoading.value = true;
  
  try {
    const [start_time, end_time] = selectedTime.value.split(" - ");
    const res = await useApi("POST", 'book/store', {
      first_name: form.first_name,
      last_name: form.last_name,
      email: form.email,
      phone: form.phone_number,
      phone_number: form.phone_number,
      organisations_id: organitation_id.value,
      staff_id: selectedStaff.value,
      service_id: selectedService.value,
      categories_id: selectedCategory.value,
      accept_privacy_terms: acceptPrivacyTerms.value,
      accept_terms_conditions: acceptTermsCondition.value,
      subscribe_newsletter: subscribeNewsletter.value,
      date: selectedDate.value,
      start_time,
      end_time
    });

    if (res.status == 201) {
      currentStep.value = 7; 
      useSnackbarStore().show("Successfully Booked!", 'success');
    }
  } catch (error) {
    console.error("Error occurred during the submission:", error);
  } finally {
    isLoading.value = false; 
  }
}
const selectedDayNumber = computed(() => {
  if (!selectedDate.value) return null; 
  const dateObj = new Date(selectedDate.value);
  const day = dateObj.getDay();
  return day === 0 ? 7 : day;
});

const formatTimeOff = (timeOffArray) => {
  const formattedDates = new Set();

  timeOffArray.forEach(off => {
    const startDateStr = off.start_date.split(' ')[0];
    const endDateStr = off.end_date.split(' ')[0];

    let currentDate = new Date(startDateStr);
    const endDate = new Date(endDateStr);
    endDate.setDate(endDate.getDate() - 1);

    while (currentDate <= endDate) {
      const formatted = currentDate.toISOString().split('T')[0];
      formattedDates.add(formatted);

      currentDate.setDate(currentDate.getDate() + 1);
    }
  });
  return Array.from(formattedDates).sort();
};

const fetchData = async () => {
  try {
    const response = await useApi('GET', `book/is-open/${slug}`);
    getData.value = response; 
  } catch (err) {
    console.error("Failed to fetch is-open data:", err);
  }
};

const validateMedia = (url, fallback) => {
  const baseUrl = 'https://pub-f18ab521731b47e48c9442c9f05683fa.r2.dev/';
  if (!url || url.startsWith(baseUrl) && url === baseUrl) {
    return fallback;
  }
  return url; 
};

onMounted(async () => {
  try {
    await fetchData();
    await fetchLocations();
  } catch (err) {
    console.error("Failed to fetch booking data:", err);
  }
});

watch([selectedCategory], async () => {
  if (selectedCategory.value) {
    await fetchServices();
  }
}, { immediate: true });

watch(selectedService, async () => {
  if (selectedService.value) {
    await fetchStaff();
  }
});

watch(selectedStaff, async () => {
  if (selectedStaff.value) {
    await fetchTimes(); 
  }
});
</script>