<template>
  <div class="w-full flex flex-col">
    <app-dashboard-filters></app-dashboard-filters>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
      <div class="dashboard-card">
        <div>
          <span class="icon-wrapper bg-success-500/20 text-success-500">
            <nuxt-icon name="customers" filled></nuxt-icon>
          </span>
          <em>{{ dashboardStore.clients }}</em>
        </div>
        <div>
          <span class="stat">{{ $t('dashboard.customers') }}</span>
        </div>
      </div>
      <div class="dashboard-card">
        <div>
          <span class="icon-wrapper bg-warning-500/20 text-warning-500">
            <nuxt-icon name="calendar-week" filled></nuxt-icon>
          </span>
          <em>
            {{ dashboardStore.upcoming_appointments }} /

            <div class="text-xs ml-1">
              {{ dashboardStore.total_appointments }}
            </div>
          </em>
        </div>
        <div>
          <span class="flex items-baseline"> </span>
          <span class="stat">{{ $t('dashboard.appointments') }}</span>
        </div>
      </div>
      <div class="dashboard-card">
        <div>
          <span class="icon-wrapper bg-purple-500/20 text-purple-500">
            <nuxt-icon name="banknotes" filled></nuxt-icon>
          </span>
          <em>{{ dashboardStore.total_income }}</em>
        </div>
        <div>
          <span class="stat">{{ $t('dashboard.income') }}</span>
        </div>
      </div>
      <div class="dashboard-card">
        <div>
          <span class="icon-wrapper bg-sky-500/20 text-sky-500">
            <nuxt-icon name="chat-bubble" filled></nuxt-icon>
          </span>

          <em>{{ dashboardStore.sms }}</em>
        </div>
        <div>
          <span class="stat">{{ $t('dashboard.sms') }}</span>
        </div>
        <button @click.prevent="showSMS = true" class="btn btn-xs outlined">
          {{ $t('dashboard.buy_sms') }}
        </button>
      </div>
    </div>
    <div class="mt-4 md:mt-8 statsCard">
      <div class="w-full lg:w-2/3">
        <span class="text-lg mb-4 block">{{
          $t('dashboard.appointments_per_month')
        }}</span>
        <div class="flex flex-col justify-end h-96">
          <bar
            v-if="dashboardStore.showChart"
            id="timelineChart"
            :options="chartOptions"
            :data="chartData"
          ></bar>
        </div>
      </div>
      <div class="w-full lg:w-1/3">
        <span class="text-lg mb-4 block">{{
          $t('dashboard.service_stats')
        }}</span>
        <div class="flex flex-col overflow-auto h-96 pr-1">
          <span
            v-if="!dashboardStore.serviceData.length"
            class="flex flex-grow items-center justify-center"
          >
            <ui-no-data>
              <template #text>{{ $t('dashboard.no_service_data') }}</template>
            </ui-no-data>
          </span>

          <span
            v-for="(item, index) in dashboardStore.serviceData"
            :key="index"
          >
            <span class="flex items-center justify-between mt-2">
              <div>
                {{ item.name }}
              </div>
              <div class="flex items-baseline h-4">
                <em class="text-xs not-italic ml-1">{{ percentage(item) }}%</em>
              </div>
            </span>
            <div class="percentage-bar">
              <span
                class="block h-2 rounded"
                :style="{
                  width: percentage(item) + '%',
                  backgroundColor: item.color,
                }"
              ></span>
            </div>
          </span>
        </div>
      </div>
    </div>

    <app-sms-modal v-if="showSMS" @close="showSMS = false"></app-sms-modal>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'subscribed',
});
useHead({
  title: 'iTattoo',
});

const { $t } = useNuxtApp();
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
);
const dayJs = useDayjs();
const showSMS = ref(false);
const dashboardStore = useDashboardStore();

const chartData = computed(() => {
  let keys = Object.keys(dashboardStore.graphData);
  for (let i = 0; i < keys.length; i++) {
    if (keys.length > 12) {
      keys[i] = i + 1;
    } else {
      keys[i] = dayJs('2020-02-03').set('month', i).format('MMM');
    }
  }

  return {
    labels: keys,
    datasets: [
      {
        data: Object.values(dashboardStore.graphData),
        backgroundColor: 'rgba(40, 199, 111, .4)',
        hoverBackgroundColor: 'rgb(40, 199, 111)',
        borderRadius: 8,
      },
    ],
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
    tooltip: {
      enabled: true,
      usePointStyle: true,
      callbacks: {
        // To change label in tooltip
        label: (data) => {
          return data.parsed.y + ' ' + $t('customers.appointments');
        },
      },
    },
  },
  xAxes: [
    {
      type: 'time',
      position: 'bottom',
      time: {
        displayFormats: { day: 'MM/YY', month: 'MMMM' },
        tooltipFormat: 'DD/MM/YY',
        unit: 'month',
      },
    },
  ],
  scales: {
    y: {
      display: false,
    },
    x: {
      grid: {
        drawOnChartArea: false,
      },
    },
  },
};

const percentage = (item) => {
  const total = dashboardStore.total_appointments || 1;
  return ((item.appointments_count / total) * 100).toFixed(2);
};

const fetchData = async () => {
  await dashboardStore.fetchData();
};

onMounted(async () => {
  await fetchData(0);
});
</script>
