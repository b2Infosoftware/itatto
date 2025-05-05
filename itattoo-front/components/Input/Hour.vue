<template>
  <input-base
    v-bind="{ name: name, label: label, optional: optional, error: error }"
  >
    <select
      @input="updateInput"
      :required="!optional"
      :name="name"
      :disabled="disabled"
      :placeholder="placeholder"
    >
      <option v-if="!hideEmpty"></option>
      <option
        v-for="(option, key) in hours"
        :key="key"
        :value="option"
        :selected="optionIsActive(option)"
      >
        {{ option }}
      </option>
    </select>
  </input-base>
</template>

<script setup>
const apiErrors = useValidationStore();
const emit = defineEmits(['update:modelValue', 'change']);
const props = defineProps({
  modelValue: {
    required: true,
    default: '',
    type: [String, Number, null],
  },
  min: {
    type: [String, null],
    default: null,
  },
  max: {
    type: [String, null],
    default: null,
  },
  name: {
    type: String,
    required: true,
  },
  optional: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  hideEmpty: {
    type: Boolean,
    default: false,
  },
  showQuarters: {
    type: Boolean,
    default: false,
  },
  label: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
});
const calendarSettings = useOrganisationStore().calendarSettings;
const optionIsActive = (option) => {
  return option == props.modelValue;
};

const makeInt = (hour) => {
  return parseInt(hour.replace(':', ''));
};

const hours = computed(() => {
  let hourIntervals = [];

  const min = props.min || calendarSettings.from_time;
  const max = props.max || calendarSettings.to_time;
  for (let index = 0; index < 24; index++) {
    let hour = index < 10 ? '0' + index + ':00' : index + ':00';
    let qtr = index < 10 ? '0' + index + ':15' : index + ':15';

    let half = index < 10 ? '0' + index + ':30' : index + ':30';
    if (makeInt(hour) >= makeInt(min) && makeInt(hour) <= makeInt(max)) {
      hourIntervals.push(hour);
    }
    if (
      props.showQuarters &&
      makeInt(hour) >= makeInt(min) &&
      makeInt(half) <= makeInt(max)
    ) {
      hourIntervals.push(qtr);
    }
    if (makeInt(half) >= makeInt(min) && makeInt(half) <= makeInt(max)) {
      hourIntervals.push(half);
    }
  }
  return hourIntervals;
});

const errorMessage = computed(() => {
  if (props.error.length) {
    return props.error;
  }
  return apiErrors.errorsList[props.name]
    ? apiErrors.errorsList[props.name][0]
    : null;
});

const updateInput = (e) => {
  emit('update:modelValue', e.target.value);

  if (errorMessage && !props.error.length) {
    apiErrors.clearError(props.name);
  }
};
</script>
