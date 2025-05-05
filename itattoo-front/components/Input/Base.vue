<template>
  <div
    class="form-group"
    :class="{
      error: errorMessage,
    }"
  >
    <label v-show="label.length">
      {{ label }} <em v-if="optional" class="not-italic">(optional)</em>
    </label>
    <slot></slot>
    <div v-if="errorMessage" class="err-msg">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['update:modelValue']);
const apiErrors = useValidationStore();
const props = defineProps({
  optional: {
    type: Boolean,
    default: false,
  },
  name: {
    type: String,
    required: true,
  },

  label: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
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
</script>
