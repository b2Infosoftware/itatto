<template>
  <input-base
    v-bind="{ name: name, label: label, optional: optional, error: error }"
  >
    <div class="flex duration" :class="{ 'with-operator': withOperator }">
      <div v-if="withOperator" class="operator-dropdown">
        <button
          ref="trigger"
          class="btn btn-icon btn-sm secondary"
          aria-haspopup="true"
          :aria-expanded="showOperatorDropdown"
          @click.prevent="showOperatorDropdown = !showOperatorDropdown"
        >
          {{ operator }}
        </button>
        <transition
          enter-active-class="transition ease-out duration-200 transform"
          enter-from-class="opacity-0 -translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition ease-out duration-200"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="showOperatorDropdown" class="dropdown-items-wrapper">
            <ul>
              <li
                v-for="(item, index) in filterOptions"
                :key="index"
                class="dropdown-item"
              >
                <button @click.native="setOperator(item)">
                  {{ item.text }}
                </button>
              </li>
            </ul>
          </div>
        </transition>
      </div>
      <input
        ref="baseInput"
        class=""
        :name="name"
        type="number"
        :placeholder="placeholder"
        :readonly="readonly"
        :value="displayValue"
        :autocomplete="autocomplete"
        :class="{
          disabled: readonly,
        }"
        @input="updateInput"
      />

      <select
        @change="updateUnit"
        v-model="unit"
        name="min_dropdown"
        id="mindr"
      >
        <option value="minutes">{{ $t('general.minutes') }}</option>
        <option value="hours">{{ $t('general.hours') }}</option>
      </select>
    </div>
  </input-base>
</template>

<script setup>
const { $t } = useNuxtApp();
const apiErrors = useValidationStore();
const emit = defineEmits(['update:minutes', 'update:operator', 'changed']);
const props = defineProps({
  minutes: {
    required: true,
    default: '',
  },
  operator: {
    type: String,
    default: '=',
  },
  withOperator: {
    type: Boolean,
    default: false,
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

const unit = ref('hours');
const baseInput = ref(null);
const showOperatorDropdown = ref(false);
const displayValue = ref('');
const operator = ref('=');
const filterOptions = [
  { key: '=', text: $t('duration.equal') },
  { key: '<', text: $t('duration.less_than') },
  { key: '<=', text: $t('duration.less_or_equal') },
  { key: '>', text: $t('duration.more_than') },
  { key: '>=', text: $t('duration.more_or_equal') },
];

const setOperator = (item) => {
  operator.value = item.key;
  emit('update:operator', item.key);
  showOperatorDropdown.value = false;
};

const updateInput = (e) => {
  const minutesValue =
    unit.value == 'minutes' ? e.target.value : e.target.value * 60;
  emit('update:minutes', Number(minutesValue));
  emit('changed', Number(minutesValue));
  if (errorMessage && !props.error.length) {
    apiErrors.clearError(props.name);
  }
};

watch(
  () => props.minutes,
  (val) => {
    if (unit.value == "minutes") {
      unit.value = 'minutes';
      displayValue.value = val;
    } else {
      unit.value = 'hours';
      displayValue.value = val / 60;
    }
  }
);

const updateUnit = () => {
  displayValue.value = '';
  emit('update:minutes', null);
};
const errorMessage = computed(() => {
  if (props.error.length) {
    return props.error;
  }
  return apiErrors.errorsList[props.name]
    ? apiErrors.errorsList[props.name][0]
    : null;
});

onMounted(() => {
  if(props.minutes == 0){
    unit.value = 'hours';
    displayValue.value = '';
  } else if (props.minutes < 60) {
    unit.value = 'minutes';
    displayValue.value = props.minutes;
  } else {
    unit.value = 'hours';
    displayValue.value = props.minutes / 60;
  }
});
</script>
