<template>
  <div class="relative">
    <div class="flex items-center justify-between">
      <label
        v-show="label.length"
        class="block text-sm font-medium mb-1"
        for="tooltip"
        @click="focusInput"
      >
        {{ label }}
        <span v-if="required" class="text-danger-500">*</span>
      </label>
    </div>
    <nuxt-icon
      v-if="loading"
      name="x-circle"
      class="absolute right-2 top-8 animate-spin mt-0.5 text-success-600"
    ></nuxt-icon>
    <input
      class="form-input w-full"
      :name="name"
      type="text"
      :placeholder="placeholder"
      :required="required"
      :readonly="readonly"
      :value="value"
      autocomplete="off"
      :class="{
        error: errors.length,
        disabled: readonly,
      }"
      @input="update"
      @focus="showDropdown = true"
    />
    <div v-if="errors.length" class="text-xs mt-1 text-danger-500">
      {{ errors[0] }}
    </div>

    <!-- List rendering  -->
    <ul
      v-if="showDropdown && items.length"
      class="absolute bg-white w-full shadow-xl py-2 rounded-sm z-10 border border-slate-100 overflow-auto max-h-96"
    >
      <li
        v-for="(item, key) in items"
        :key="key"
        class="cursor-pointer hover:bg-slate-100 p-2 border-b border-slate-50"
        @click.prevent="itemSelected(item)"
      >
        <slot name="locationText" :item="item"></slot>
      </li>
    </ul>
  </div>
</template>

<script setup>
const emit = defineEmits(['update', 'input:modelValue', 'itemSelected']);
const props = defineProps({
  value: {
    default: '',
    type: String,
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
  errors: {
    type: Array,
    default: () => {
      return [];
    },
  },
  required: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  items: {
    type: Array,
    default: () => {
      return [];
    },
  },
});

const showDropdown = ref(false);

const focusInput = (e) => {
  const labelParent = e.target.parentElement.parentElement;
  const input = labelParent.getElementsByTagName('input')[0];
  if (input) {
    input.focus();
  }
};
const update = (e) => {
  emit('update');
  emit('input:modelValue', e.target.value);
};
const itemSelected = (item) => {
  emit('itemSelected', item);
  hideDropdown();
};
const hideDropdown = () => {
  showDropdown.value = false;
};
</script>
