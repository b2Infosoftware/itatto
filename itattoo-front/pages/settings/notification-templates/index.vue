<template>
  <div class="card">
    <div class="card-title">
      <div class="flex justify-between w-full">
        <div>
          <h1 class="text-2xl">{{ title }}</h1>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="w-full table-wrapper">
        <table class="w-full borderedTable">
          <thead class="text-left">
            <tr>
              <th>{{ $t('notificationTemplates.email_type') }}</th>
              <th>{{ $t('notificationTemplates.entity') }}</th>
              <th>{{ $t('notificationTemplates.channel') }}</th>
              <!-- <th>{{ $t('notificationTemplates.enabled') }}</th> -->
              <th class="text-right">{{ $t('general.actions') }}</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in notificationTemplates" :key="item.id">
              <td class="capitalize">
                {{ item.name }}
              </td>
              <td class="capitalize">{{ item.entity }}</td>
              <td class="capitalize">{{ item.channel }}</td>
              <!-- <td class="capitalize">
                {{ isEnabled(item) }}
              </td> -->
              <td class="text-right">
                <nuxt-link
                  :to="{
                    name: 'settings-notification-templates-id',
                    params: { id: item.id },
                  }"
                  class="btn btn-xs primary"
                >
                  <span class="px-2">{{ $t('general.edit') }}</span>
                </nuxt-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  layout: 'settings',
  middleware: ['subscribed'],
  function() {
    if (!useCan('edit', 'notifications')) {
      return navigateTo({ name: 'index' });
    }
  },
});
const title = $t('notificationTemplates.title');
useHead({
  title: title,
});
const notificationTemplates = ref([]);
const settings = useOrganisationStore().notificationSettings;

const fetchTemplates = async () => {
  const response = await useApi('GET', 'notification-templates');
  notificationTemplates.value = response.data;
};

// const isEnabled = (item) => {
//   const mapping = {
//     created: '_appointment_created',
//     edited: '_appointment_rescheduled',
//     canceled: '_appointment_canceled',
//     remind: '_appointment_reminder',
//     after: '_post_appointment',
//   };
//   const type = item.entity +
//   return 'YES';
//   // if(item.entity == 'staff'){
//   //   return settings.staff_events.includes(item.)
//   // }
//   return item.entity == 'customer';
// };

await fetchTemplates();
</script>
