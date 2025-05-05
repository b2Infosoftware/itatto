<template>
  <button
    :disabled="buttonIsDisabled"
    class="google-button"
    type="button"
    @click="handleClick"
  >
    <div v-show="loading" class="loading-ring"></div>
    <slot />
  </button>
</template>

<script setup>
import { computed, defineProps, defineEmits } from "vue";

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

const emit = defineEmits(["click"]);

const buttonIsDisabled = computed(() => props.loading || props.disabled);

const handleClick = (event) => {
  if (!buttonIsDisabled.value) {
    emit("click", event);
  }
};
</script>
