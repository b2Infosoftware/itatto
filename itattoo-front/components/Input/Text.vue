<template>
  <input-base
    v-bind="{ name: name, label: label, optional: optional, error: error }"
  >
    <input
      ref="baseInput"
      class=""
      :name="name"
      :type="type"
      :placeholder="placeholder"
      :readonly="readonly"
      :value="modelValue"
      :autocomplete="autocomplete"
      min="0"
      :class="{
        disabled: readonly,
      }"
      @input="updateInput"
    />

    <div v-if="isPassword" class="password-toggle" @click="togglePassword">
      <nuxt-icon filled name="eye-slash" v-if="showPassword"></nuxt-icon>
      <nuxt-icon filled name="eye" v-else></nuxt-icon>
    </div>
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
  isPassword: {
    type: Boolean,
    default: false,
  },
  min: {
    type: Number,
    default: 0,
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
});

const showPassword = ref(false);
const baseInput = ref(null);

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const updateInput = (e) => {
  if (props.type == 'number') {
    if (e.target.value < props.min) {
      e.target.value = props.min;
    }
    emit('update:modelValue', e.target.value);
  } else {
    emit('update:modelValue', e.target.value);
  }
  if (errorMessage && !props.error.length) {
    apiErrors.clearError(props.name);
  }
};
const errorMessage = computed(() => {
  if (props.error.length) {
    return props.error;
  }
  return apiErrors.errorsList[props.name]
    ? apiErrors.errorsList[props.name][0]
    : null;
});
watch(
  () => showPassword.value,
  (val) => {
    val ? (baseInput.value.type = 'text') : (baseInput.value.type = 'password');
  }
);
</script>
