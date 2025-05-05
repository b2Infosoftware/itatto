<template>
  <div class="input-checkbox">
    <label :class="{ 'disabled-label': disabled }">
      <input
        type="checkbox"
        :disabled="disabled"
        v-model="model"
        :value="value"
      />
      <span class="checkmark"></span>
      <span>
        <slot></slot>
      </span>
    </label>
  </div>
</template>
<script setup>
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {
    type: [Array, Boolean, String],
  },

  value: {
    type: [Boolean, Object, String],
  },

  disabled: {
    type: Boolean,
    default: false,
  },
});

const model = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  },
});
</script>

<style scoped>
.disabled-label {
  cursor: not-allowed;
  opacity: 0.6; 
}
</style>
