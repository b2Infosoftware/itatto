<template>
  <input-base
    v-bind="{ name: name, label: label, optional: optional, error: error }"
  >
    <vue-date-picker
      ref="dpRef"
      close-on-auto-apply
      :enable-time-picker="!dateOnly"
      :name="name"
      v-model="model"
      :range="range"
      auto-apply
      :disabled="disabled"
      format="dd MMM yyyy"
      :placeholder="placeholder"
      :max-date="maxDate"
      :min-date="minDate"
    >
      <template v-if="customTrigger" #trigger>
        <slot></slot>
      </template>
      <template v-else #input-icon>
        <nuxt-icon filled name="calendar-week"></nuxt-icon>
      </template>
    </vue-date-picker>
  </input-base>
</template>

<script setup>
import VueDatePicker from '@vuepic/vue-datepicker';
const emit = defineEmits(['update:modelValue', 'changed']);
const dayJs = useDayjs();
const apiErrors = useValidationStore();
const props = defineProps({
  modelValue: {
    required: true,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Pick a date',
  },
  optional: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  range: {
    type: Boolean,
    default: false,
  },
  customTrigger: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: '',
  },
  name: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: '',
  },
  dateOnly: {
    type: Boolean,
    default: false,
  },
  minDate: {
    default: null,
  },
  maxDate: {
    default: null,
  },
});
const dpRef = ref(null);

const errorMessage = computed(() => {
  if (props.error.length) {
    return props.error;
  }
  return apiErrors.errorsList[props.name]
    ? apiErrors.errorsList[props.name][0]
    : null;
});

const model = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    const strVal = dayJs(value).format('YYYY-MM-DD');
    emit('update:modelValue', strVal);
    if (errorMessage && !props.error.length) {
      apiErrors.clearError(props.name);
    }
    dpRef.value.closeMenu();
    emit('changed');
  },
});
</script>
