<template>
  <form @submit.prevent="handleSubmit">
    <ul v-if="form.is_minor" class="tabsHeader mb-4">
      <li v-for="(tab, index) in tabs" :key="index">
        <button
          :class="{ active: tabIsActive(index) }"
          @click.prevent.native="changeTab(index)"
        >
          {{ tab }}
        </button>
      </li>
      <li v-if="tabs.length < 3 && activeTab == 1">
        <button
          @click.prevent="addSecondParent"
          class="btn btn-icon success tonal"
        >
          <nuxt-icon name="plus" filled></nuxt-icon>
        </button>
      </li>
    </ul>
    <div v-show="activeTab == 0">
      <div v-if="edit" class="flex space-x-4">
        <div class="form-group">
          <label>{{ $t('customers.photo') }}</label>
          <form-photo
            v-model="form.avatar"
            @changed="updateAvatar"
            class="w-24"
          ></form-photo>
        </div>
      </div>
      <div class="flex flex-col space-y-4 flex-grow mt-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input-text
            v-if="showField('first_name')"
            v-model="form.first_name"
            :label="$t('customers.first_name')"
            name="first_name"
          ></input-text>
          <input-text
            v-if="showField('last_name')"
            v-model="form.last_name"
            :label="$t('customers.last_name')"
            name="last_name"
          ></input-text>
          <input-text
            v-model="form.email"
            :label="$t('customers.email')"
            name="email"
          ></input-text>
          <input-phone
            :auto-country="!props.edit"
            v-model="form.phone_number"
            :label="$t('customers.phone_number')"
            name="phone_number"
          ></input-phone>
          <ui-datepicker
            v-if="showField('birth_date')"
            v-model="form.birth_date"
            date-only
            :label="$t('customers.birth_date')"
            name="birth_date"
            :max-date="new Date()"
            @changed="checkAge"
          ></ui-datepicker>
          <ui-radio-group
            v-if="showField('birth_date')"
            v-model="form.gender"
            name="gender"
            :label="$t('customers.gender')"
            :options="genderOptions"
          ></ui-radio-group>
          <input-text
            v-if="showField('ssn')"
            v-model="form.ssn"
            :label="$t('customerSettings.ssn')"
            name="ssn"
          ></input-text>
          <input-text
            v-if="showField('address')"
            v-model="form.address"
            :label="$t('customers.address')"
            name="address"
            required
          ></input-text>
          <input-text
            v-if="showField('city')"
            v-model="form.city"
            :label="$t('customers.city')"
            name="city"
          ></input-text>
          <input-text
            v-if="showField('postal_code')"
            v-model="form.postal_code"
            :label="$t('customers.postal_code')"
            name="postal_code"
          ></input-text>
          <input-select
            v-if="showField('country_id')"
            :label="$t('customers.country')"
            name="'country_id'"
            v-model="form.country_id"
            :options="useSettingsStore().countries"
            is-object
          ></input-select>
          <input-select
            v-if="showField('referral')"
            v-model="form.referral"
            :label="$t('customers.referral')"
            name="referral"
            optional
            :options="[
              'Google',
              'Facebook',
              'Instagram',
              'TikTok',
              'Friends',
              'Other',
            ]"
          ></input-select>
          <input-text
            v-if="form.referral == 'Other'"
            name="referral"
            v-model="customReferral"
            :label="$t('customers.custom_referral')"
          ></input-text>

          <input-multiselect
            v-model="form.staff_ids"
            multiple
            name="staff_ids"
            :label="$t('customers.associated_artist')"
            :options="artistOptions.map((i) => i.id)"
            :custom-label="
              (opt) => artistOptions.find((e) => e.id === opt)?.full_name
            "
            :close-on-select="false"
            :searchable="false"
          ></input-multiselect>

          <input-select
            v-if="showField('referral')"
            v-model="form.vip_id"
            :label="$t('customers.change_vip_status')"
            name="vip_id"
            is-object
            textKey="label"
            optional
            :options="vipOptions"
          ></input-select>

          <input-text-area
            v-if="showField('notes')"
            v-model="form.notes"
            :label="$t('customers.notes')"
            name="notes"
            class="md:col-span-2"
            optional
          ></input-text-area>
        </div>

        <ui-switch-button
          disabled
          v-if="showField('is_minor')"
          name="is_minor"
          @input="minorChanged"
          v-model="form.is_minor"
          class="pt-4"
          >{{ $t('customers.is_minor') }}
        </ui-switch-button>
        <ui-switch-button
          name="accepts_newsletter"
          v-model="form.accepts_newsletter"
          class="pt-4"
          >{{ $t('customers.accepts_newsletter') }}
        </ui-switch-button>
      </div>
    </div>
    <div v-if="activeTab == 1">
      <form-parent v-model="form.parent_1"></form-parent>
    </div>
    <div v-if="activeTab == 2">
      <div class="flex md:justify-end">
        <button
          @click.prevent="removeSecondParent()"
          class="btn btn-xs danger tonal mt-4 md:mt-0"
        >
          Remove second parent
        </button>
      </div>
      <form-parent v-model="form.parent_2" entity="parent_2"></form-parent>
    </div>
    <!-- FORM ACTION BUTTONS -->
    <div v-if="!hideButtons" class="inline-flex mt-8 justify-between w-full">
      <nuxt-link :to="{ name: 'customers' }" class="btn secondary">
        {{ $t('general.back') }}
      </nuxt-link>
      <input-submit-button class="primary" :loading="saving">
        {{ $t('general.save') }}
      </input-submit-button>
    </div>
  </form>
</template>
<script setup>
const { $t } = useNuxtApp();
const emit = defineEmits(['saved', 'updated']);
const dayJs = useDayjs();
const props = defineProps({
  user: {
    type: Object,
    default: {},
  },
  edit: {
    type: Boolean,
    default: false,
  },
  hideButtons: {
    type: Boolean,
    default: false,
  },
});
const genderOptions = [
  { value: 'not_specified', text: $t('customers.not_specified') },
  { value: 'male', text: $t('customers.male') },
  { value: 'female', text: $t('customers.female') },
];
const tabs = ref([$t('customers.customer'), $t('customers.parent_1')]);
const activeTab = ref(0);
const route = useRoute();
const saving = ref(false);
const customReferral = ref(null);
const vipOptions = ref([]);
const hiddenFields = useOrganisationStore().defaultOrganisation.hidden_fields;
const artistOptions = useOrganisationStore().staff?.filter((i) => {
  if (useCan('manage others', 'appointments')) {
    return i.id > 0;
  }
  return i.id == useAuthStore().user.id;
});

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  photo: '',
  birth_date: '',
  gender: 'not_specified',
  phone_number: '',
  is_minor: false,
  address: '',
  ssn: '',
  postal_code: '',
  doc_type: '',
  issued_by: '',
  city: '',
  notes: '',
  doc_no: '',
  vip_id: null,
  expiry_date: '',
  accepts_newsletter: false,
  staff_ids: [],
  country_id: useOrganisationStore().defaultLocation.country_id,
});

const updateAvatar = async () => {
  if (!props.edit) {
    return;
  }
  await handleSubmit(true);
};

const addSecondParent = () => {
  tabs.value.push($t('customers.parent_2'));
  toggleParentField('parent_2');
};

const showField = (field) => {
  return !hiddenFields.includes(field);
};

const removeSecondParent = () => {
  activeTab.value = 1;
  toggleParentField('parent_2');
  tabs.value.splice(2, 1);
};

const minorChanged = (event) => {
  const value = event.target.value;
  if (value) {
    toggleParentField('parent_1');
  } else {
    delete form.parent_1;
    delete form.parent_2;
  }
};

const checkAge = () => {
  const dob = dayJs(form.birth_date);
  const now = dayJs(new Date());
  if (now.diff(dob, 'years') < 16) {
    form.is_minor = true;
    toggleParentField('parent_1');
  } else {
    form.is_minor = false;
    delete form.parent_1;
    delete form.parent_2;
  }
};

const toggleParentField = (key) => {
  const parentForm = {
    first_name: '',
    last_name: '',
    phone_number: '',
    birth_date: '',
    gender: 'not_specified',
    country_id: useOrganisationStore().defaultLocation.country_id,
    city: '',
    address: '',
    state: '',
    postal_code: '',
    doc_type: '',
    issued_by: '',
    doc_no: '',
    expiry_date: '',
  };
  if (!form[key]) {
    form[key] = parentForm;
  } else {
    delete form[key];
  }
};

const fetchVipOptions = async () => {
  const { data } = await useApi('GET', 'vip');
  if(data) {
    vipOptions.value = data;
  }
}
const parentErrors = computed(() => {
  const list = Object.keys(useValidationStore().errorsList);
  if (!list.length) {
    return 0;
  }
  const parent1Errors = list.filter((item) => item.indexOf('parent_1') > -1);
  const parent2Errors = list.filter((item) => item.indexOf('parent_2') > -1);
  if (parent1Errors.length == list.length) {
    return 1;
  }
  if (parent2Errors.length == list.length) {
    return 2;
  }

  return 0;
});

const handleSubmit = async (justAvatar = false) => {
  useValidationStore().clearErrors();
  if (customReferral.value?.length) {
    form.referral = customReferral.value;
  }
  const method = props.edit ? 'PATCH' : 'POST';
  const url = props.edit ? 'customers/' + route.params.id : 'customers';
  saving.value = true;
  try {
    const response = await useApi(method, url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      if (method == 'POST') {
        if (props.hideButtons) {
          emit('saved', response.data);
        } else {
          navigateTo({ name: 'customers' });
        }
      } else {
        if (!justAvatar) {
          emit('updated');
        }
      }
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  activeTab.value = parentErrors.value;
  saving.value = false;
};

/**
 * Quite self explainatory
 */
const changeTab = (index) => {
  if (index === activeTab.value) {
    return false;
  }
  activeTab.value = index;
};

/**
 * Quite self explainatory
 */
const tabIsActive = (index) => {
  return index === activeTab.value;
};

defineExpose({ handleSubmit });

const populateForm = async () => {
  if (!props.edit) {
    return;
  }

  form.first_name = props.user.first_name;
  form.last_name = props.user.last_name;
  form.email = props.user.email;
  form.avatar = props.user.avatar;
  form.gender = props.user.gender;
  form.phone_number = props.user.phone_number;
  form.is_minor = props.user.is_minor;
  form.birth_date = props.user.birth_date;
  form.address = props.user.address;
  form.ssn = props.user.ssn;
  form.postal_code = props.user.postal_code;
  form.doc_type = props.user.doc_type;
  form.issued_by = props.user.issued_by;
  form.city = props.user.city;
  form.expiry_date = props.user.expiry_date;
  form.notes = props.user.notes;
  form.country_id = props.user.country_id;
  form.vip_id = props.user.vip ? props.user.vip.id : null;
  form.staff_ids = props.user.staff_ids;
  form.accepts_newsletter = props.user.accepts_newsletter;
  if (
    ['Google', 'Facebook', 'Instagram', 'TikTok', 'Friends'].includes(
      props.user.referral
    )
  ) {
    form.referral = props.user.referral;
  } else {
    form.referral = 'Other';
    customReferral.value = props.user.referral;
  }
  if (props.user.parent_1) {
    form.parent_1 = props.user.parent_1;
    form.parent_2 = props.user.parent_2;
  }
};

onMounted(() => {
  populateForm();
  fetchVipOptions();
});
</script>
