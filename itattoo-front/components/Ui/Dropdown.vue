<template>
  <div class="relative inline-flex">
    <button
      ref="trigger"
      class="btn btn-icon btn-sm"
      aria-haspopup="true"
      :aria-expanded="showDropdown"
      @click.prevent="showDropdown = !showDropdown"
    >
      <span class="flex truncate text-xl">
        <nuxt-icon filled :name="props.icon"></nuxt-icon>
      </span>
    </button>
    <transition
      enter-active-class="transition ease-out duration-200 transform"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-out duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showDropdown" class="dropdown-items-wrapper">
        <slot></slot>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { onClickOutside } from '@vueuse/core';
const props = defineProps({
  icon: {
    type: String,
    default: 'ellipsis-vertical',
  },
});
const trigger = ref(null);
const showDropdown = ref(false);

onClickOutside(trigger, (event) => {
  showDropdown.value = false;
});
</script>
