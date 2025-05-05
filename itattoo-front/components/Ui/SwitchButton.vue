<template>
  <div class="flex items-start">
    <div class="form-switch" :class="size">
      <input
        :id="name"
        :name="name"
        :value="modelValue"
        :disabled="disabled"
        type="checkbox"
        class="sr-only"
        :true-value="true"
        :false-value="false"
        :checked="!!modelValue"
        @input="updateInput"
      />
      <label class="" :for="name">
        <span aria-hidden="true"></span>
      </label>
    </div>
    <div :class="{ 'text-sm': size == 'sm' }">
      <slot></slot>
      <div v-if="info" class="block opacity-50 text-xs w-full leading-none">
        {{ info }}
      </div>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['update:modelValue']);

defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  name: {
    type: String,
    default: '',
    required: true,
  },
  size: {
    type: String,
    default: 'sm',
  },
  info: {
    type: String,
    default: null,
  },
});
const updateInput = (e) => {
  emit('update:modelValue', e.target.checked);
};
</script>
