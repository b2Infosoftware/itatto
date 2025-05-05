<template>
  <div
    class="radio-group-wrapper"
    :class="[size, getOrientationClasses(), { error: errorMessage }]"
  >
    <label v-if="label.length" :class="labelClasses()">
      {{ label }} <em v-if="optional" class="not-italic">(optional)</em>
    </label>
    <div class="buttons-wrapper">
      <button
        v-for="(item, key) in options"
        :key="key"
        class="capitalize"
        :class="{ active: isActive(item) }"
        :disabled="readonly"
        @click.prevent="changeValue(item)"
      >
        {{ itemText(item) }}
      </button>
    </div>
    <div v-if="errorMessage" class="err-msg">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['update:modelValue', 'change']);
const apiErrors = useValidationStore();
const props = defineProps({
  modelValue: {
    type: [String, Object, Number, Boolean],
    default: '',
  },
  name: {
    type: String,
    required: true,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  options: {
    type: Array,
    default: () => {
      return [];
    },
  },
  optional: {
    type: Boolean,
    default: false,
  },
  labelPosition: {
    type: String,
    default: 'top',
  },
  label: {
    type: String,
    default: '',
  },
  size: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  error: {
    type: Boolean,
    default: false,
  },
});

const isObject = (item) => {
  return typeof item === 'object' && !Array.isArray(item) && item !== null;
};

const errorMessage = computed(() => {
  if (props.error.length) {
    return props.error;
  }
  return apiErrors.errorsList[props.name]
    ? apiErrors.errorsList[props.name][0]
    : null;
});

const itemText = (item) => {
  if (isObject(item)) {
    return item.text;
  }
  return item;
};

const isActive = (item) => {
  const value = isObject(item)
    ? String(item.value).toLowerCase()
    : String(item).toLowerCase();
  return String(value).toLowerCase() === String(props.modelValue).toLowerCase();
};

const changeValue = (item) => {
  const value = isObject(item) ? item.value : item;
  emit('update:modelValue', value);
  emit('change');
  if (errorMessage && !props.error.length) {
    apiErrors.clearError(props.name);
  }
};
const getOrientationClasses = () => {
  const orientations = {
    left: 'flex items-center',
    right: 'flex-reverse items-center',
    top: 'flex flex-col',
    bottom: 'flex flex-col-reverse',
  };
  return orientations[props.labelPosition];
};
const labelClasses = () => {
  let cls = [];
  if (props.error) {
    cls = ['text-danger-500'];
  }
  const orientations = {
    left: 'mr-2',
    right: 'ml-2',
    top: 'mb-1',
    bottom: 'mt-2',
  };
  cls.push(orientations[props.labelPosition]);
  return cls;
};
</script>
