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
      <main
        class="px-4 sm:px-6 lg:px-8 py-0 w-full max-w-9xl mx-auto flex flex-col lg:flex-row"
      >
        <ui-settings-sidebar></ui-settings-sidebar>
        <div class="grow py-6 pl-0 lg:pl-6">
          <slot></slot>
        </div>
      </main>
    </div>
  </div>
</template>
<script setup>
const route = useRoute();
const layout = useLayoutStore();

// watch(
//   () => route.name,
//   (value) => {
//     useValidationStore().clearErrors();
//   }
// );
</script>
