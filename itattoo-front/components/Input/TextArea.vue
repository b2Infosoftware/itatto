<template>
  <input-base
    v-bind="{ name: name, label: label, optional: optional, error: error }"
  >
    <textarea
      :name="name"
      :placeholder="placeholder"
      :required="!optional"
      :readonly="readonly"
      :value="modelValue"
      :autocomplete="autocomplete"
      :class="{
        disabled: readonly,
      }"
      @input="updateInput"
      :rows="rows"
    ></textarea>
  </input-base>
</template>

<script setup>
const apiErrors = useValidationStore();
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {
    required: true,
    default: '',
  },
  rows: {
    type: Number,
    default: 4,
  },
  optional: {
    type: Boolean,
    default: false,
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
