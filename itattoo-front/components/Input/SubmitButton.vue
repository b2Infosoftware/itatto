<template>
  <button :disabled="buttonIsDisabled" class="btn" type="submit">
    <div v-show="loading" class="loading-ring"></div>
    <slot />
  </button>
</template>
<script setup>
const props = defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const buttonIsDisabled = computed(() => {
  return props.loading || props.disabled;
});
</script>
