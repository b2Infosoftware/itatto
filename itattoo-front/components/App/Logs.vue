<template>
  <div class="logsBar" :class="{ show: layout.showLogs }">
    <span class="header">
      <button @click.prevent="layout.toggleLogs()" class="btn secondary btn-sm">
        <nuxt-icon name="chevron-right" filled></nuxt-icon>
      </button>
      <span class="text-sm">{{ $t('logs.name') }}</span>
      <div>
        <button @click.prevent="layout.toggleLogs()" class="btn success btn-sm">
          <nuxt-icon name="refresh" filled></nuxt-icon>
        </button>
      </div>
    </span>
    <div class="p-4">
      <div v-for="item in logs" :key="item.id" class="flex mb-2">
        <span class="icon" :class="getBg(item)">
          <nuxt-icon filled name="login"></nuxt-icon>
        </span>
        <div class="flex flex-col">
          <div class="text-xs">
            <span class="font-semibold inline"> {{ getAction(item) }}: </span>
            {{ getText(item) }}
          </div>
          <span class="text-[10px] flex space-x-1 items-center opacity-50">
            <nuxt-icon name="clock" filled></nuxt-icon>
            <em>{{ dayJs(item.created_at).format('YYYY-MM-DD HH:mm') }}</em>
            <em>&#9642;</em>
            <em>{{ item.by }}</em>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
const { $t } = useNuxtApp();
// TODO:load more & pagination

const layout = useLayoutStore();
const dayJs = useDayjs();
const actions = {
  appointment_rescheduled: $t('logs.appointment_rescheduled'),
  appointment_canceled: $t('logs.appointment_canceled'),
  appointment_created: $t('logs.appointment_created'),
  client_created: $t('logs.client_created'),
  login: $t('logs.login'),
};
const backgrounds = {
  appointment_rescheduled: 'bg-warning-500/50',
  appointment_canceled: 'bg-danger-500/50',
  appointment_created: 'bg-success-500/50',
  client_created: 'bg-success-500/50',
  login: 'bg-success-500/50',
};
const { data: logs } = await useApi('GET', 'logs');
const getBg = (item) => {
  return backgrounds[item.type];
};

const generateDate = (item) => {
  return item.date + ' ' + item.start_time + ' ' + item.end_time;
};

const getText = (item) => {
  if (item.type == 'login') {
    return item.by;
  }
  if (item.type == 'client_created') {
    return item.client_name + ' ' + $t('logs.was_created');
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

const getAction = (item) => {
  return actions[item.type];
};
</script>
