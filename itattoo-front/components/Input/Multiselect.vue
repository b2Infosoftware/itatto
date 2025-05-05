<template>
  <input-base
    v-bind="{ name: name, label: label, optional: optional, error: error }"
  >
    <vue-multiselect
      v-model="model"
      multiple
      :options="optionValues"
      :close-on-select="false"
      :custom-label="optionText"
      :searchable="searchable"
      :taggable="taggable"
      :limit="limit"
      :limitText="limitText"
      :placeholder="placeholder"
      @tag="saveTag"
      v-bind="$attrs"
    >
    </vue-multiselect>
  </input-base>
</template>

<script setup>
const { $t } = useNuxtApp();
import VueMultiselect from 'vue-multiselect';
const apiErrors = useValidationStore();
const emit = defineEmits(['update:modelValue', 'change', 'addTag']);
const props = defineProps({
  modelValue: {
    required: true,
    default: [],
    type: Array,
  },
  name: {
    type: String,
    required: true,
  },
  limit: {
    type: Number,
    default: 1,
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
  disableOptions: {
    type: Array,
    default: [],
  },
  label: {
    type: String,
    default: '',
  },
  options: {
    type: Array,
    default: () => {
      return [];
    },
  },
  placeholder: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  isObject: {
    type: Boolean,
    default: false,
  },
  searchable: {
    type: Boolean,
    default: false,
  },
  taggable: {
    type: Boolean,
    default: false,
  },
  valueKey: {
    type: String,
    default: 'id',
  },
  textKey: {
    type: String,
    default: 'name',
  },
  max: {
    type: Number,
    default: 10,
  },
});

const limitText = (count) => {
  return '+' + count + ' more';
};

const saveTag = (tag) => {
  emit('addTag', tag);
};

const optionValues = computed(() => {
  return props.options.map((option) =>
    props.isObject ? option[props.valueKey] : option
  );
});

const optionValue = (option) => {
  return props.isObject ? option[props.valueKey] : option;
};

const optionText = (option) => {
  if (props.isObject) {
    const sel = props.options.find((item) => item[props.valueKey] == option);
    return sel ? sel[props.textKey] : 'N/A';
  }
  return props.options.find((item) => item == option);
};

const model = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    const pickedOptions = props.options.filter((item) =>
      value.includes(optionValue(item))
    );

    if (pickedOptions.length > props.max) {
      useSnackbarStore().show($t('calendar.maximum_staff_selected'), 'error');
      return;
    }

    emit('update:modelValue', value);

    emit('change', pickedOptions);

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
</script>
