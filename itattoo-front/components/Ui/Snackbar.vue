<template>
  <div class="snackbar-wrapper">
    <transition name="snack">
      <div
        v-if="snackbarStore.content"
        class="snackbar"
        :class="snackbarStore.type"
      >
        {{ snackbarStore.content }}
      </div>
    </transition>
  </div>
</template>

<script setup>
const snackbarStore = useSnackbarStore();
</script>
