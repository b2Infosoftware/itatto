  <template>
    <input-base
      v-bind="{ name: name, label: label, optional: optional, error: error }"
    >
      <select
        class="form-select"
        @input="updateInput"
        :required="!optional"
        :name="name"
        :disabled="disabled"
        :placeholder="placeholder"
      >
        <option v-if="!hideEmpty"></option>
        <option
          v-for="(option, key) in options"
          :key="key"
          :value="optionValue(option)"
          :selected="optionIsActive(option)"
        >
          {{ optionText(option) }}
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
      type: [String, Number, Boolean, null],
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
    valueKey: {
      type: String,
      default: 'id',
    },
    textKey: {
      type: String,
      default: 'name',
    },
  });

  const optionValue = (option) => {
    return props.isObject ? option[props.valueKey] : option;
  };

  const optionText = (option) => {
    return props.isObject ? option[props.textKey] : option;
  };

  const optionIsActive = (option) => {
    return optionValue(option) == props.modelValue;
  };

  const errorMessage = computed(() => {
    if (props.error.length) {
      return props.error;
    }
    return apiErrors.errorsList[props.name]
      ? apiErrors.errorsList[props.name][0]
      : null;
  });

  const updateInput = (e) => {
    const pickedOption = props.options.find(
      (item) => optionValue(item) == e.target.value
    );
    if (!pickedOption) {
      emit('update:modelValue', null);
    } else {
      emit('update:modelValue', e.target.value);
    }
    emit('change', pickedOption);

    if (errorMessage && !props.error.length) {
      apiErrors.clearError(props.name);
    }
  };
  </script>
