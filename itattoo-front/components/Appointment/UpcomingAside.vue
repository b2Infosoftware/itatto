<template>
  <div class="upcoming-appointments">
    <div class="flex flex-col p-4">
      <span class="title"> Upcoming </span>
      <input-text
        class="sm"
        v-model="clientQuery"
        name="clientName"
        placeholder="customer name"
        @input="searching = true"
      ></input-text>
    </div>
    <div v-if="searching">
      <div v-for="n in 10" class="skeleton-item"></div>
    </div>
    <div v-else-if="!appointmentStore.upcoming.length">
      <ui-no-data>
        <template #text>
          <div class="text-sm">{{ $t('wizard.no_customers') }}</div>
        </template>
      </ui-no-data>
    </div>
    <div v-else>
      <span v-for="(group, date) in groupedItems">
        <div class="date">
        {{
          dayJs(date).format('DD MMMM YYYY')
        }}
      </div>

        <button
          @click.prevent="viewItem(item)"
          v-for="item in group"
          class="item"
          :key="item.id"
        >
          <span
            :style="{
              backgroundColor: item.service.color,
              color: useColor().getContrastColor(item.service.color),
            }"
          >
            <nuxt-icon filled name="calendar-week"></nuxt-icon>
          </span>
          <div class="details">
            <div class="flex items-center">
              <span class="name">
                {{ item.customer.full_name }}
              </span>
              <span class="service">{{ item.service.name }}</span>
            </div>
            <div class="bottom-row">
              <nuxt-icon filled name="clock" class="text-[12px] -mr-[3px]"></nuxt-icon>
              <i>{{ item.start_time }}</i>
              <i> - </i>
              <i>{{ item.duration }} {{ $t('general.minutes') }}</i>
              <i>/</i>
              <i>
                {{ item.staff.full_name }}
              </i>
            </div>
          </div>
        </button>
      </span>
    </div>
  </div>
</template>
<script setup>
import { watchDebounced } from '@vueuse/core';
const appointmentStore = useAppointmentStore();
const emit = defineEmits(['viewItem']);
const searching = ref(false);
const dayJs = useDayjs();
const clientQuery = ref(appointmentStore.customerNameQuery);
const groupedItems = computed(() => {
  return useHelpers().groupBy(appointmentStore.upcoming, 'date');
});
watchDebounced(
  clientQuery,
  async () => {
    await fetchItems();
  },
  { debounce: 500, maxWait: 1000 }
);

onMounted(() => {
  fetchItems();
});

const fetchItems = async () => {
  appointmentStore.customerNameQuery = clientQuery.value;
  await appointmentStore.fetchUpcomingAppointments();
  searching.value = false;
};

const viewItem = (item) => {
  emit('viewItem', {
    id: item.id,
    date: item.date,
  });
};
</script>
