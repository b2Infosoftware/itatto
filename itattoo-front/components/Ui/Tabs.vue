<template>
  <ul class="tabsHeader">
    <li v-for="(tab, index) in tabs">
      <button
        :class="{ active: tabIsActive(index) }"
        @click.prevent.native="changeTab(index)"
      >
        <nuxt-icon filled :name="tab.icon"></nuxt-icon>
        <span>{{ tab.text }}</span>
      </button>
    </li>
  </ul>
</template>

<script setup>
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {
    type: [Number, null],
    default: 0,
  },
  tabs: {
    type: Array,
    default: [],
  },
});

/**
 * Quite self explainatory
 */
const changeTab = (index) => {
  if (index === props.modelValue) {
    return false;
  }
  model.value = index;
};

/**
 * Quite self explainatory
 */
const tabIsActive = (index) => {
  return index === props.modelValue;
};

const model = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  },
});
</script>
