<template>
  <div class="flex flex-col space-y-4 flex-grow mt-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <input-text
        v-model="model.first_name"
        :label="$t('customers.first_name')"
        :name="entity + '.first_name'"
        required
      ></input-text>
      <input-text
        v-model="model.last_name"
        :label="$t('customers.last_name')"
        :name="entity + '.last_name'"
        required
      ></input-text>
      <input-phone
        :key="inputKey"
        v-model="model.phone_number"
        :label="$t('customers.phone_number')"
        name="phone_number"
        required
        :auto-country="!model.id"
        :default-country="model.country_id"
        preferred-countries="['it']"
      />
      <ui-radio-group
        v-model="model.gender"
        :name="entity + '.gender'"
        :label="$t('customers.gender')"
        :options="genderOptions"
      ></ui-radio-group>

      <ui-datepicker
        v-model="model.birth_date"
        date-only
        :label="$t('customers.birth_date')"
        :name="entity + '.birth_date'"
        required
        :max-date="new Date()"
      ></ui-datepicker>
      <input-select
        :label="$t('customers.country')"
        :name="entity + '.country_id'"
        v-model="model.country_id"
        :options="useSettingsStore().countries"
        is-object
      ></input-select>

      <input-text
        v-model="model.address"
        :label="$t('customers.address')"
        :name="entity + '.address'"
        required
        class="md:col-span-2"
      ></input-text>

      <div class="grid md:col-span-2 grid-cols-1 md:grid-cols-3 gap-4">
        <input-text
          v-model="model.city"
          :label="$t('customers.city')"
          :name="entity + '.city'"
          required
        ></input-text>
        <input-text
          v-model="model.state"
          :label="$t('customers.state')"
          :name="entity + '.state'"
          required
        ></input-text>
        <input-text
          v-model="model.postal_code"
          :label="$t('customers.postal_code')"
          :name="entity + '.postal_code'"
          required
        ></input-text>
      </div>
      <div
        class="grid md:col-span-2 grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
      >
        <input-select
          :label="$t('customers.doc_type')"
          :name="entity + '.doc_type'"
          v-model="model.doc_type"
          :options="documentOptions"
          is-object
        ></input-select>

        <input-text
          v-model="model.doc_no"
          :label="$t('customers.doc_no')"
          :name="entity + '.doc_no'"
          required
        ></input-text>
        <input-text
          v-model="model.issued_by"
          :label="$t('customers.issued_by')"
          :name="entity + '.issued_by'"
          required
        ></input-text>
        <ui-datepicker
          v-model="model.expiry_date"
          date-only
          :label="$t('customers.expiry_date')"
          :name="entity + '.expiry_date'"
          required
        ></ui-datepicker>
      </div>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  entity: {
    type: String,
    default: 'parent_1',
  },
});

const emit = defineEmits(['update:modelValue']);
const inputKey = ref(Date.now());
const loadingCountry = ref(true);

const setCountryData = async () => {
  try {
    const res = await fetch('https://ipapi.co/json/');
    const data = await res.json();

    const countryCode = data.country_code?.toLowerCase() ?? 'it';
    model.value.country_id = countryCode; 
    model.value.phone_number = data.country_calling_code ?? '+39'; 

    loadingCountry.value = false;
    inputKey.value = Date.now(); 

    const flagElement = document.querySelector('.vti__flag');
    if (flagElement) {
      flagElement.classList.remove(...flagElement.classList);
      flagElement.classList.add(countryCode); 
    }

    const selectionElement = document.querySelector('.vti__selection');
    if (selectionElement) {
      const flagSpan = selectionElement.querySelector('.vti__flag');
      if (flagSpan) {
        flagSpan.className = `vti__flag ${countryCode}`;
      }
    }

    const inputElement = document.querySelector('.vti__input');
    if (inputElement && !inputElement.dataset.initialized) {
      inputElement.value = data.country_calling_code ?? '+39';
      inputElement.dataset.initialized = "true"; 
    }
  } catch (err) {
    console.error('Failed to fetch country:', err);
    loadingCountry.value = false;
  }
};

onMounted(() => {
  if (!model.value.id) setCountryData();
  if (!model.value.gender) model.value.gender = 'not_specified';
});

const genderOptions = [
  { value: 'not_specified', text: $t('customers.not_specified') },
  { value: 'male', text: $t('customers.male') },
  { value: 'female', text: $t('customers.female') },
];

const documentOptions = [
  { id: 'card_id', name: $t('customers.identity_card') },
  { id: 'driving_license', name: $t('customers.driving_license') },
  { id: 'passport', name: $t('customers.passport') },
  { id: 'other', name: $t('customers.other') },
];

const model = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  },
});
</script>