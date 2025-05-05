<template>
  <div class="pageSidebarWrapper" :class="{ minified: layout.sidebarMinified }">
    <!-- Sidebar backdrop (mobile only) -->
    <div class="backdrop" aria-hidden="true"></div>

    <!-- Sidebar -->
    <aside>
      <!-- <aside v-click-outside="closeSidebar"> -->
      <!-- Sidebar header -->
      <div
        class="flex justify-between pr-3 mt-4 mb-8 lg:justify-center sm:px-2"
      >
        <!-- Logo -->
        <app-logo></app-logo>
        <button @click.prevent="layout.toggleMinification()" class="minify">
          <nuxt-icon
            v-if="layout.sidebarMinified"
            name="chevron-right"
            filled
          ></nuxt-icon>
          <nuxt-icon v-else name="chevron-left" filled></nuxt-icon>
        </button>
        <!-- Hamburger -->
        <button
          ref="trigger"
          class="lg:hidden text-slate-500 dark:text-slate-200"
          aria-controls="sidebar"
          :aria-expanded="layout.sidebarExpanded"
          @click.stop="layout.toggleSidebar()"
        >
          <span class="sr-only">Close sidebar</span>
          <svg
            class="w-6 h-6 fill-current"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z"
            />
          </svg>
        </button>
      </div>

      <!-- Links -->
      <div class="flex flex-grow space-y-8">
        <!-- Pages group -->
        <div class="flex flex-col justify-start w-full md:justify-between">
          <ul class="mt-3">
            <li v-for="item in topItems" :key="item.icon">
              <nuxt-link
                @click="layout.sidebarIsOpen = false"
                v-if="item.visible"
                :to="{ name: item.pathName }"
              >
                <div class="flex items-center">
                  <nuxt-icon :name="item.icon" filled></nuxt-icon>
                  <span>{{ item.name }}</span>
                </div>
              </nuxt-link>
            </li>
          </ul>
          <ul class="mb-3">
            <li v-for="item in bottomItems" :key="item.icon">
              <nuxt-link
                @click="layout.sidebarIsOpen = false"
                v-if="item.visible"
                :to="{ name: item.pathName }"
              >
                <div class="flex items-center">
                  <nuxt-icon :name="item.icon" filled></nuxt-icon>
                  <span>{{ item.name }} </span>
                </div>
              </nuxt-link>
            </li>
            <li>
              <a class="cursor-pointer" @click.prevent="auth.logout()">
                <div class="flex items-center">
                  <nuxt-icon filled name="logout"></nuxt-icon>
                  <span>{{ $t('auth.log_out') }}</span>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </aside>
  </div>
</template>

<script setup>
const auth = useAuthStore();
const layout = useLayoutStore();
const organisation = useOrganisationStore();
const is_trial = useOrganisationStore().defaultOrganisation.is_trial;
const { $t } = useNuxtApp();
const hasSubscription = Boolean(
  organisation.defaultOrganisation.activeSubscription?.id
);
const topItems = [
  {
    name: $t('sidebar.dashboard'),
    visible: hasSubscription && useCan('view', 'dashboard'),
    icon: 'home',
    pathName: 'index',
  },
  {
    name: $t('sidebar.calendar'),
    visible: true,
    icon: 'calendar-week',
    pathName: 'calendar',
  },
  {
    name: $t('sidebar.book'),
    visible: hasSubscription && useCan('view', 'staff'),
    icon: 'calendar-day',
    pathName: 'booking',
  },
  {
    name: $t('sidebar.services'),
    visible: hasSubscription && useCan('view', 'services'),
    icon: 'briefcase',
    pathName: 'services',
  },
  {
    name: $t('sidebar.staff'),
    visible: hasSubscription && useCan('view', 'staff'),
    icon: 'users',
    pathName: 'staff',
  },
  {
    name: $t('sidebar.customers'),
    visible: hasSubscription && useCan('view', 'customers'),
    icon: 'customers',
    pathName: 'customers',
  },
  {
    name: $t('sidebar.marketing'),
    visible: hasSubscription && !is_trial && useCan('view', 'campaigns'),
    icon: 'send',
    pathName: 'campaigns',
  },
  {
    name: $t('sidebar.subscriptions'),
    visible: useCan('is', 'superadmin'),
    icon: 'banknotes',
    pathName: 'subscriptions',
  },
  {
    name: $t('sidebar.organisations'),
    visible: useCan('is', 'superadmin'),
    icon: 'shop',
    pathName: 'organisations',
  },
  {
    name: $t('sidebar.logs'),
    visible: hasSubscription && useCan('view', 'logs'),
    icon: 'cursor-arrow',
    pathName: 'logs',
  },
  {
    name: $t('sidebar.pick_organisation'),
    visible: useOrganisationStore().organisations.length > 1,
    icon: 'shop',
    pathName: 'pick-organisation',
  },
];
const bottomItems = [
  {
    name: $t('sidebar.settings'),
    visible: useCan('edit', 'settings'),
    icon: 'settings',
    pathName: 'settings',
  },
];
const trigger = ref(null);
</script>
