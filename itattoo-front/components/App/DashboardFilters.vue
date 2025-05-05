<template>
  <div class="mb-4 flex flex-col w-full flex-grow -mt-4">
    <span class="block md:hidden self-end">
      <button
        @click.prevent="showFilters = !showFilters"
        class="btn btn-sm btn-icon"
        :class="showFilters ? 'primary' : 'secondary'"
      >
        <nuxt-icon name="settings" filled></nuxt-icon>
      </button>
    </span>
    <div
      class="md:flex mt-4 flex-col md:mt-0 md:flex-row gap-4 w-full flex-grow md:justify-end"
      :class="showFilters ? 'flex' : 'hidden'"
    >
      <input-select
        v-model="dashboardStore.form.location_id"
        name="location_id"
        @change="fetchData"
        is-object
        :options="locationOptions"
      >
      </input-select>
      <input-select
        v-model="dashboardStore.form.year"
        name="year"
        @change="fetchData"
        :options="yearOptions"
      >
      </input-select>
      <input-select
        v-model="dashboardStore.form.month"
        name="month"
        is-object
        @change="fetchData"
        :options="monthOptions"
      >
      </input-select>
      <input-select
        v-model="dashboardStore.form.service_id"
        name="service_id"
        is-object
        @change="fetchData"
        :options="serviceOptions"
      >
      </input-select>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const dayJs = useDayjs();
const dashboardStore = useDashboardStore();
const showFilters = ref(false);
const serviceOptions = [
  {
    id: null,
    name: $t('services.all_services'),
  },
  ...useOrganisationStore().services,
];
const yearOptions = [];
const monthOptions = [
  {
    id: null,
    name: $t('general.all_months'),
  },
];
const year = new Date().getFullYear();
const months = dayJs.months();

for (let index = year; index > year - 10; index--) {
  yearOptions.push(index);
}
months.forEach((item, index) => {
  monthOptions.push({
    id: index + 1,
    name: $t(`month_names.${item.toLowerCase()}`),
  });
});

const locationOptions = [
  {
    id: null,
    name: $t('dashboard.all_locations'),
  },
  ...useOrganisationStore().locations.map((i) => {
    return {
      id: i.id,
      name: i.name,
    };
  }),
];

const fetchData = async () => {
  await useDashboardStore().fetchData();
};
</script>
