<template>
  <div class="card">
    <div class="flex flex-col md:flex-row gap-4 w-full">
      <input-select
        :options="yearOptions"
        label="Select a year"
        @change="fetchData"
        name="year"
        v-model="form.year"
      ></input-select>
      <input-select
        :options="monthOptions"
        label="Select a month"
        @change="fetchData"
        name="month"
        v-model="form.month"
        is-object
      ></input-select>
    </div>
    <div class="py-4">
      <span class="mt-8 mb-4">{{
        $t('customers.appointments_per_period')
      }}</span>
      <bar
        v-if="loaded"
        id="customerChart"
        :options="chartOptions"
        :data="chartData"
      ></bar>
    </div>
    <span class="mt-8">{{ $t('customers.appointments_by_service') }}</span>
    <div class="flex flex-col w-full mt-4">
      <span v-for="(item, index) in byService" :key="index">
        <span class="flex items-center justify-between text-xs mt-2">
          <div>
            {{ item.name }}
          </div>
          <div class="flex items-baseline h-4">
            {{ item.percent }} %
            <i
              class="h-3 w-3 inline-block rounded-full ml-1"
              :style="{ backgroundColor: colors[index] }"
            ></i>
          </div>
        </span>
        <span
          class="block h-1 rounded"
          :style="{ width: item.percent + '%', backgroundColor: colors[index] }"
        ></span>
      </span>
    </div>
  </div>
</template>
<script setup>
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
const loaded = ref(false);
const yearOptions = [];
const monthOptions = [
  {
    id: 'all',
    name: $t('general.all'),
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
    name: item,
  });
});

const form = reactive({
  year: year,
  month: 'all',
});

const byDate = ref([]);
const byService = ref([]);

const chartData = computed(() => {
  let keys = Object.keys(byDate.value);
  if (form.month == 'all') {
    for (let i = 0; i < keys.length; i++) {
      keys[i] = dayJs('2020-02-03').set('month', i).format('MMM');
    }
  }
  return {
    labels: keys,
    datasets: [
      { data: Object.values(byDate.value), backgroundColor: '#17D2D8' },
    ],
  };
});

const chartOptions = {
  responsive: true,
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
  },
};

const fetchData = async () => {
  loaded.value = false;
  const id = useRoute().params.id;
  const response = await useApi('GET', 'customers/' + id + '/stats', form);
  if (response) {
    byDate.value = response.data.byDate;
    byService.value = response.data.byService;
    loaded.value = true;
  }
};

const colors = [
  '#84cc16',
  '#f97316',
  '#d946ef',
  '#a855f7',
  '#8b5cf6',
  '#f43f5e',
  '#6366f1',
  '#3b82f6',
  '#0ea5e9',
  '#ec4899',
  '#ef4444',
  '#06b6d4',
  '#10b981',
  '#14b8a6',
  '#22c55e',
  '#eab308',
  '#f59e0b',
  '#78716c',
];

await fetchData();
</script>
