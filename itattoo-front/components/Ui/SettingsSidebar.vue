<template>
  <div class="accountSidebar">
    <!-- Group 1 -->
    <div>
      <div class="section-title">Settings</div>
      <ul>
        <li v-for="(link, index) in links" :key="index">
          <nuxt-link v-if="link.visible" :to="{ name: link.pathName }">
            <nuxt-icon filled :name="link.icon"></nuxt-icon>
            <span>{{ link.text }}</span>
          </nuxt-link>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const links = [
  {
    pathName: 'settings',
    visible: useCan('view', 'settings'),
    icon: 'cog',
    text: $t('settingsSidebar.general'),
  },
  {
    pathName: 'settings-locations',
    visible: useCan('view', 'locations'),
    icon: 'location',
    text: $t('settingsSidebar.locations'),
  },
  {
    pathName: 'settings-calendar',
    visible: useCan('view', 'calendar-settings'),
    icon: 'calendar-day',
    text: $t('settingsSidebar.calendar'),
  },
  {
    pathName: 'settings-customer',
    visible: useCan('view', 'settings'),
    icon: 'user',
    text: $t('settingsSidebar.customer_card'),
  },
  {
    pathName: 'settings-roles',
    visible: useCan('view', 'roles'),
    icon: 'users',
    text: $t('settingsSidebar.roles'),
  },
  {
    pathName: 'settings-notification-settings',
    visible: useCan('edit', 'notifications'),
    icon: 'send',
    text: $t('settingsSidebar.notification_settings'),
  },
  {
    pathName: 'settings-notification-templates',
    visible: useCan('edit', 'notifications'),
    icon: 'envelope',
    text: $t('settingsSidebar.notification_templates'),
  },
  {
    pathName: 'settings-consent-forms',
    visible: useCan('edit', 'consent-forms'),
    icon: 'document-check',
    text: $t('settingsSidebar.consent_forms'),
  },
  {
    pathName: 'settings-subscription',
    visible: useCan('view', 'settings'),
    icon: 'credit-card',
    text: $t('settingsSidebar.subscription'),
  },
  {
    pathName: 'settings-booking',
    visible: useCan('view', 'settings'),
    icon: 'calendar-day',
    text: $t('settingsSidebar.booking_setting'),
  },
  {
    pathName: 'settings-vip',
    visible: useCan('view', 'settings'),
    icon: 'crown',
    text: $t('settingsSidebar.vip_setting'),
  }
];
</script>
