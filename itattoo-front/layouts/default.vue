<template>
  <div
    class="pageWrapper"
    :class="{ 'sidebar-expanded': layout.sidebarExpanded }"
  >
    <!-- UI stuff -->
    <app-offline-mode></app-offline-mode>
    <ui-snackbar></ui-snackbar>
    <app-sidebar></app-sidebar>
    <!-- Content area -->
    <div
      class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden"
    >
      <app-main-header></app-main-header>
      <main>
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
const route = useRoute();
const layout = useLayoutStore();
</script>
