<template>
  <header class="appHeader">
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 -mb-px">
        <!-- Header: Left side -->
        <div class="flex">
          <!-- Hamburger button -->
          <button
            class="text-slate-500 hover:text-slate-600 lg:hidden"
            aria-controls="sidebar"
            :aria-expanded="layout.sidebarExpanded"
            @click.stop="layout.toggleSidebar()"
          >
            <span class="sr-only">Open sidebar</span>
            <svg
              class="w-6 h-6 fill-current"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <rect x="4" y="5" width="16" height="2" />
              <rect x="4" y="11" width="16" height="2" />
              <rect x="4" y="17" width="16" height="2" />
            </svg>
          </button>
          <appointment-staff-shortlist
            v-if="
              route.name == 'calendar' &&
              useCan('manage others', 'appointments')
            "
          >
          </appointment-staff-shortlist>
        </div>

        <!-- Header: Right side -->
        <div class="flex items-center space-x-3">
          <app-theme-switcher></app-theme-switcher>
          <app-change-language></app-change-language>
          <app-location-switcher></app-location-switcher>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
const layout = useLayoutStore();
const route = useRoute();
</script>
