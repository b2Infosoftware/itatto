<template>
  <div :class="getClasses">
    <span
      class="mx-auto block text-center justify-center"
      :class="`text-${size}`"
    >
      <slot></slot>
    </span>
  </div>
</template>

<script setup>
const props = defineProps({
  type: {
    type: String,
    default: 'info',
  },
  size: {
    type: String,
    default: 'xs',
  },
});

const getClasses = computed(() => {
  return [props.type, 'alert'];
});
</script>
