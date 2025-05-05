<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between space-y-4">
      <div>
        <h1 class="text-2xl">{{ title }}</h1>
        <!-- <p class="opacity-60 leading-tight">
          {{ $t('customers.manage_customers') }}
        </p> -->
      </div>
      <!-- <nuxt-link
        :to="{ name: 'customers-create' }"
        class="btn primary md:h-10 justify-center sm:max-w-60"
        >{{ $t('customers.add_customer') }}</nuxt-link
      > -->
    </div>

    <div class="card mt-4 md:mt-8">
      <span class="card-title">{{ $t('customers.customers_list') }}</span>
      <div class="card-body">
        <div class="table-wrapper">
          <table class="logsTable">
            <thead>
              <tr>
                <th>{{ $t('logs.action') }}</th>
                <th>{{ $t('logs.for') }}</th>
                <th>{{ $t('logs.when') }}</th>
                <th>{{ $t('logs.by') }}</th>
                <th>IP</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in logs" :key="item.id">
                <td class="capitalize">
                  <span class="th">{{ $t('logs.action') }}</span>
                  <span class="flex items-center">
                    <div class="icon" :class="getBg(item)">
                      <nuxt-icon filled :name="getIcon(item)"></nuxt-icon>
                    </div>
                    <span class="">
                      {{ getAction(item) }}
                    </span>
                  </span>
                </td>
                <td>
                  <span class="th">{{ $t('logs.for') }}</span>
                  <span>
                    <div>
                      {{ getText(item) }}
                    </div>
                  </span>
                </td>
                <td>
                  <span class="th">{{ $t('logs.when') }}</span>
                  <span>
                    <span>
                      <em>{{
                        dayJs(item.created_at).format('YYYY-MM-DD HH:mm')
                      }}</em>
                    </span>
                  </span>
                </td>
                <td>
                  <span class="th">{{ $t('logs.by') }}</span>
                  <span>
                    <span>
                      <em>{{
                        item.type == 'login' ? item.staff_name : item.by
                      }}</em>
                    </span>
                  </span>
                </td>
                <td>
                  <span class="th">IP</span>
                  <span>
                    <span>
                      <em>{{ item.ip }}</em>
                    </span>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <ui-pagination class="mt-4 justify-end" :meta="meta"></ui-pagination>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const title = $t('logs.title');
definePageMeta({
  middleware: 'subscribed',
});
useHead({
  title: title,
});
const route = useRoute();
const dayJs = useDayjs();
const actions = {
  appointment_rescheduled: $t('logs.appointment_rescheduled'),
  appointment_canceled: $t('logs.appointment_canceled'),
  appointment_created: $t('logs.appointment_created'),
  client_created: $t('logs.client_created'),
  client_updated: $t('logs.client_updated'),
  login: $t('logs.login'),
};
const backgrounds = {
  appointment_rescheduled: 'bg-warning-500/50',
  appointment_canceled: 'bg-danger-500/50',
  appointment_created: 'bg-success-500/50',
  client_created: 'bg-success-500/50',
  client_updated: 'bg-sky-500/50',
  login: 'bg-success-500/50',
};
const icons = {
  appointment_rescheduled: 'calendar-week',
  appointment_canceled: 'x-mark',
  appointment_created: 'calendar-day',
  client_created: 'user-plus',
  client_updated: 'user',
  login: 'login',
};
const logs = ref([]);
const getBg = (item) => {
  return backgrounds[item.type];
};
const generateDate = (item) => {
  return item.date + ' ' + item.start_time + ' ' + item.end_time;
};
watch(
  () => route.query,
  (value) => {
    if (value.page != meta.page) {
      fetchData();
    }
  }
);
const getText = (item) => {
  if (item.type == 'login') {
    return item.staff_name;
  }
  if (item.type == 'client_created') {
    return item.client_name + ' ' + $t('logs.was_created');
  }
  if (item.type == 'client_updated') {
    return item.client_name + ' ' + $t('logs.was_updated');
  }
  if (item.type == 'appointment_created') {
    return (
      item.client_name +
      ' ' +
      $t('logs.for') +
      ' ' +
      item.service_name +
      ' ' +
      $t('logs.on_date') +
      ' ' +
      generateDate(item) +
      ' ' +
      $t('logs.with') +
      ' ' +
      item.staff_name
    );
  }
  if (item.type == 'appointment_rescheduled') {
    return (
      item.client_name +
      ' ' +
      $t('logs.for') +
      ' ' +
      item.service_name +
      ' ' +
      $t('logs.with') +
      ' ' +
      item.staff_name +
      ' ' +
      $t('logs.was_rescheduled') +
      ' ' +
      generateDate(item)
    );
  }
  if (item.type == 'appointment_canceled') {
    return (
      item.client_name +
      ' ' +
      $t('logs.for') +
      ' ' +
      item.service_name +
      ' ' +
      $t('logs.with') +
      ' ' +
      item.staff_name +
      ' ' +
      $t('logs.was_canceled')
    );
  }
  return 'bg-success-500';
};

const getIcon = (item) => {
  return icons[item.type];
};
const getAction = (item) => {
  return actions[item.type];
};

const meta = reactive({
  current_page: route.query?.page || 1,
  from: 1,
  last_page: 1,
  per_page: 5,
  to: 1,
  total: 1,
});

const fetchData = async () => {
  const params = {
    page: route.query?.page || 1,
    ...meta,
  };
  const response = await useApi('GET', 'logs', params);
  logs.value = response.data;
  for (const key in meta) {
    meta[key] = response.meta[key];
  }
};

onMounted(async () => {
  await fetchData();
});
</script>
