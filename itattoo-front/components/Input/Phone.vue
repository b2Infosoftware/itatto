<template>
  <input-base v-bind="{ name: name, label: label, optional: optional, error: error }">
    <vue-tel-input :name="name" :placeholder="placeholder" :disabled="readonly" v-model="model" mode="international"
      :input-options="{
        showDialCode: true,
      }" :auto-default-country="autoCountry" valid-characters-only>
    </vue-tel-input>
  </input-base>
</template>

<script setup>
import { VueTelInput } from 'vue-tel-input';
const apiErrors = useValidationStore();
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {
    required: true,
    default: '',
  },
  isPassword: {
    type: Boolean,
    default: false,
  },
  optional: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String,
    default: 'text',
  },
  name: {
    type: String,
    required: true,
  },
  placeholder: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  autocomplete: {
    type: String,
    default: 'off',
  },
  autoCountry: {
    type: Boolean,
    default: true,
  },
  studioCountry: {
    type: Object,
    required: false, // Making it optional
    default: () => ({}), // Default to an empty object
  }
});

console.log(props)

const model = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
    if (errorMessage && !props.error.length) {
      apiErrors.clearError(props.name);
    }
  },
});

const errorMessage = computed(() => {
  if (props.error.length) {
    return props.error;
  }
  return apiErrors.errorsList[props.name]
    ? apiErrors.errorsList[props.name][0]
    : null;
});

onMounted(async () => {
  if (!props.autoCountry) {
    console.log(props)
    if (!props.modelValue) {
      const countryName = props.studioCountry.name ?? "Italy"
      const response = await fetch(`https://restcountries.com/v3.1/name/${countryName}`);
      if (!response.ok) throw new Error("Failed to fetch country data");

      const data = await response.json();
      const countryData = data[0];


      const phoneCode = countryData.idd.root + (countryData.idd.suffixes[0] || "");
      const countryCode = countryData.cca2.toLowerCase();

      console.log("Phone Code", phoneCode)
      console.log("Country Code", countryCode)

      const flagElement = document.querySelector('.vti__flag');
      if (flagElement) {
        flagElement.className = `vti__flag ${countryCode}`;
      }

      const inputElement = document.querySelector('.vti__input');
      if (inputElement && !inputElement.dataset.initialized) {
        inputElement.value = phoneCode;
        inputElement.dataset.initialized = "true";
      }

    }

    console.log('Auto country detection is disabled');
    return;
  }

  nextTick(async () => {
    try {
      const res = await fetch('https://ipapi.co/json/');
      if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);

      const data = await res.json();
      const countryCode = data.country_code?.toLowerCase() || 'it';
      const callingCode = data.country_calling_code || '+39';

      const flagElement = document.querySelector('.vti__flag');
      if (flagElement) {
        flagElement.className = `vti__flag ${countryCode}`;
      }

      const inputElement = document.querySelector('.vti__input');
      if (inputElement && !inputElement.dataset.initialized) {
        inputElement.value = callingCode;
        inputElement.dataset.initialized = "true";
      }

    } catch (error) {
      console.error('Failed to get location data:', error);
    }
  });
});
</script>