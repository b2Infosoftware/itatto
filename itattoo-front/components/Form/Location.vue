<template>
  <div class="locationFormWrapper">
    <div class="overlay"></div>
    <div class="locationFormCard">
      <form @submit.prevent="handleSubmit">
        <div class="overflow-auto grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="title md:col-span-2">
            {{ $t('locations.add_location') }}
          </div>
          <input-text
            v-model="form.name"
            :label="$t('locations.name')"
            name="name"
            required
          ></input-text>
          <input-text
            v-model="form.email"
            :label="$t('locations.email')"
            name="email"
            type="email"
            required
          ></input-text>
          <input-text
            v-model="form.address"
            :label="$t('locations.address')"
            name="address"
            required
          ></input-text>
          <input-text
            v-model="form.city"
            :label="$t('locations.city')"
            name="city"
            required
          ></input-text>
          <input-text
            v-model="form.state"
            :label="$t('locations.state')"
            name="state"
            required
          ></input-text>
          <input-select
            v-model="form.country_id"
            :label="$t('locations.country')"
            :options="useSettingsStore().countries"
            name="country_id"
            is-object
            required
          ></input-select>
          <input-phone
            v-model="form.phone_number"
            :label="$t('locations.phone_number')"
            name="phone_number"
            required
            :auto-country="!location?.id"
          ></input-phone>
          <input-text
            v-model="form.post_code"
            :label="$t('locations.post_code')"
            name="post_code"
            required
          ></input-text>
          <input-text
            v-model="form.vat_number"
            :label="$t('locations.vat_number')"
            name="vat_number"
            required
          ></input-text>
          <input-text
            v-model="form.website"
            :label="$t('locations.website')"
            name="website"
            required
          ></input-text>
          <div class="col-span-2 text-sm font-semibold uppercase mt-2">
            {{ $t('locations.working_hours') }}
          </div>
          <input-select
            v-model="form.from_time"
            :label="$t('locations.from_time')"
            :options="useSchedule().hourIntervals"
            name="from_time"
            required
          ></input-select>
          <input-select
            v-model="form.to_time"
            :label="$t('locations.to_time')"
            :options="useSchedule().hourIntervals"
            name="to_time"
            required
          ></input-select>

          <div class="col-span-2 text-sm font-semibold uppercase mt-2">
            Upload Avatar 
          </div>
          <form-photo @changed="updateAvatar" v-model="form.avatar" class="w-24 col-span-2" :disabled="isFormEmpty"></form-photo>
        </div>

        <div class="flex mt-5 justify-between">
          <button @click.prevent="hideForm()" class="btn secondary">
            {{ $t('general.cancel') }}
          </button>
          <input-submit-button class="primary" :loading="saving">
            {{ $t('general.save') }}
          </input-submit-button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  location: {
    default: null,
  },
});
const emit = defineEmits(['saved', 'close']);
const defaultOrganisation = useOrganisationStore().defaultOrganisation;
const saving = ref(false);
const form = reactive({
  organisation_id: defaultOrganisation.id,
  country_id: defaultOrganisation.country_id,
  phone_number: '',
  name: '',
  email: '',
  city: '',
  address: '',
  state: '',
  avatar: '',
  post_code: '',
  vat_number: '',
  website: '',
  from_time: defaultOrganisation.calendarSettings.from_time,
  to_time: defaultOrganisation.calendarSettings.to_time,
});

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  const method = props.location?.id ? 'PATCH' : 'POST';
  const url = props.location?.id
    ? 'locations/' + props.location.id
    : 'locations';
  saving.value = true;
  try {
    const response = await useApi(method, url, form);
    useOrganisationStore().fetchLocations();
    if (response) {
      emit('saved');
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};

const { name, email, phone_number, city, country_id, address, state, post_code, vat_number, website, from_time, to_time } = toRefs(form);


const isFormEmpty = computed(() => {
  const empty = !name.value ||
                !email.value ||
                !phone_number.value ||
                !city.value ||
                !country_id.value ||
                !address.value ||
                !state.value ||
                !post_code.value ||
                !vat_number.value ||
                !website.value ||
                !from_time.value ||
                !to_time.value;

  console.log('isFormEmpty computed:', empty); // Log setiap kali computed dievaluasi
  return empty;
});


watch(isFormEmpty, (value) => {
  console.log('form is empty', value);
}, { deep: true, immediate: true });


const updateAvatar = async () => {
  if(!props.location?.id) {
    return;
  }
  
  await handleSubmit();
};

const populateForm = () => {
  if (props.location?.id) {
    form.name = props.location.name;
    form.email = props.location.email;
    form.phone_number = props.location.phone_number || '';
    form.city = props.location.city;
    form.country_id = props.location.country_id;
    form.address = props.location.address;
    form.state = props.location.state;
    form.post_code = props.location.post_code;
    form.vat_number = props.location.vat_number || '';
    form.website = props.location.website;
    form.avatar = props.location.avatar;
    form.from_time = props.location.from_time;
    form.to_time = props.location.to_time;
  }
};

const hideForm = () => {
  resetForm();
  emit('close');
};

const resetForm = () => {
  form.organisation_id = useOrganisationStore().defaultOrganisation.id;
  form.country_id = useOrganisationStore().defaultOrganisation.country_id;
  form.phone_number = '';
  form.name = '';
  form.email = '';
  form.city = '';
  form.address = '';
  form.state = '';
  form.post_code = '';
  form.vat_number = '';
  form.website = '';
  form.avatar = '';
  form.from_time = defaultOrganisation.calendarSettings.from_time;
  form.to_time = defaultOrganisation.calendarSettings.to_time;
};

onMounted(() => {
  populateForm();
  console.log('location', props.location);
  
});
</script>
