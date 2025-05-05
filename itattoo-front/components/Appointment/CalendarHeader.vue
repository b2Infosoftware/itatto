<template>
  <div class="w-full">
    <div class="calendarHeader">
      <div class="left-section">
        <button
          class="btn btn-icon btn-sm secondary"
          @click.prevent="toggleUpcomingSearch()"
        >
          <nuxt-icon
            v-if="appointmentStore.showUpcomingSearch"
            filled
            name="chevron-left"
          ></nuxt-icon>
          <nuxt-icon v-else filled name="chevron-right"></nuxt-icon>
        </button>

        <input-multiselect
          v-model="appointmentStore.filters.staff_ids"
          name="staff_id"
          is-object
          class="sm"
          text-key="full_name"
          :placeholder="$t('wizard.pick_staff')"
          :options="staffOptions"
          @change="setActiveStaff"
          :max="12"
        >
        </input-multiselect>
      </div>

      <div class="right-section">
        <div class="flex justify-between gap-x-2 md:justify-normal">
          <div class="hidden lg:flex gap-x-2">
            <button
              @click.prevent="changeViewType('timeGrid')"
              class="btn btn-sm btn-icon success"
              :class="viewType == 'timeGrid' ? '' : 'tonal'"
            >
              <nuxt-icon filled name="calendar-week"></nuxt-icon>
            </button>
            <button
              @click.prevent="changeViewType('list')"
              class="btn btn-sm btn-icon success"
              :class="viewType == 'list' ? '' : 'tonal'"
            >
              <nuxt-icon filled name="list-bullet"></nuxt-icon>
            </button>
          </div>
        </div>
        <div class="flex gap-x-2">
          <div>
            <input-select
              :options="viewOptions"
              label=""
              @change="changeView"
              class="sm"
              name="viewoptions"
              v-model="viewOption"
              is-object
            ></input-select>
          </div>
          <div class="dates-navigation">
            <button
              @click.prevent="previousInterval"
              class="btn btn-sm btn-icon secondary"
            >
              <nuxt-icon filled name="chevron-left"></nuxt-icon>
            </button>
            <ui-datepicker
              name="calendardate"
              class="sm"
              date-only
              v-model="appointmentStore.calendarDay"
              @changed="emit('dateChanged')"
              custom-trigger
            >
              <input
                type="text"
                readonly
                :value="datePlaceholder"
                class="text-center cursor-pointer"
              />
            </ui-datepicker>
            <button
              @click.prevent="nextInterval"
              class="btn btn-sm btn-icon secondary"
            >
              <nuxt-icon filled name="chevron-right"></nuxt-icon>
            </button>
          </div>
        </div>
        <div class="flex gap-x-2">
          <div class="flex lg:hidden gap-x-2">
            <button
              @click.prevent="changeViewType('timeGrid')"
              class="btn btn-sm btn-icon success"
              :class="viewType == 'timeGrid' ? '' : 'tonal'"
            >
              <nuxt-icon filled name="calendar-week"></nuxt-icon>
            </button>
            <button
              @click.prevent="changeViewType('list')"
              class="btn btn-sm btn-icon success"
              :class="viewType == 'list' ? '' : 'tonal'"
            >
              <nuxt-icon filled name="list-bullet"></nuxt-icon>
            </button>
          </div>
          <button
            @click.prevent="emit('setToday')"
            class="btn btn-sm success tonal"
          >
            {{ $t('general.today') }}
          </button>
          <button
            @click.prevent="toggleFilters"
            class="items-center btn btn-sm success"
            :class="showFilters ? '' : 'tonal'"
          >
            <nuxt-icon filled name="funnel" class="mr-1"></nuxt-icon>
            <span>{{ $t('general.filters') }}</span>
          </button>
          <!-- <button class="btn btn-sm btn-icon success tonal">
            <nuxt-icon name="settings" filled></nuxt-icon>
          </button> -->
        </div>
      </div>
    </div>

    <appointment-filters v-if="showFilters"></appointment-filters>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const emit = defineEmits([
  'changeView',
  'updatedStaff',
  'next',
  'prev',
  'setToday',
  'dateChanged',
]);
const appointmentStore = useAppointmentStore();
const organisationStore = useOrganisationStore();
const dayJs = useDayjs();
const viewOption = ref('Week');
const showFilters = ref(false);
const viewType = ref('timeGrid');

/**
 * Options list for TimeFrame view dropdown
 */
const viewOptions = computed(() => {
  if (viewType.value == 'timeGrid') {
    return [
      {
        id: 'Month',
        name: $t('calendarSettings.monthly'),
      },
      {
        id: 'Week',
        name: $t('calendarSettings.weekly'),
      },
      {
        id: 'FourDay',
        name: $t('calendarSettings.four_days'),
      },
      {
        id: 'Day',
        name: $t('calendarSettings.daily'),
      },
    ];
  }
  return [
    {
      id: 'Year',
      name: $t('calendarSettings.yearly'),
    },
    {
      id: 'Month',
      name: $t('calendarSettings.monthly'),
    },
    {
      id: 'Week',
      name: $t('calendarSettings.weekly'),
    },
    {
      id: 'Day',
      name: $t('calendarSettings.daily'),
    },
    // {
    //   id: 'DateInterval',
    //   name: $t('calendarSettings.date_interval'),
    // },
  ];
});

const toggleUpcomingSearch = () => {
  appointmentStore.showUpcomingSearch = !appointmentStore.showUpcomingSearch;
  return;
};

/**
 * Options list for staff dropdown
 */
const staffOptions = computed(() => {
  if (!useCan('manage others', 'appointments')) {
    return organisationStore.staff.filter(
      (item) => item.id == useAuthStore().user.id
    );
  }
  return organisationStore.staff;
});

/**
 * Decides whether to display advanced filters or not
 */
const toggleFilters = () => {
  showFilters.value = !showFilters.value;
};

/**
 * Updates the store with selected staff items
 */
const setActiveStaff = (items) => {
  appointmentStore.staff = items;
  useCookie('selectedStaff').value = items.map((i) => i.id);
  emit('updatedStaff');

  appointmentStore.fetchItems();
};

/**
 * Tells the parent component to move calendar to a different timeframe
 */
const previousInterval = () => {
  emit('prev');
};

/**
 * Tells the parent component to move calendar to a different timeframe
 */
const nextInterval = () => {
  emit('next');
};

/**
 * A nice placeholder to display in the datepicker
 */
const datePlaceholder = computed(() => {
  if (viewOption.value == 'Year') {
    return dayJs(appointmentStore.calendarDay).format('YYYY');
  }
  if (viewOption.value == 'Month') {
    return dayJs(appointmentStore.calendarDay).format('MMM YYYY');
  }
  if (['FourDay', 'Week'].includes(viewOption.value)) {
    const start = dayJs(appointmentStore.calendarDay);
    const end =
      viewOption.value == 'Week'
        ? dayJs(appointmentStore.calendarDay).endOf('week')
        : dayJs(appointmentStore.calendarDay).add(3, 'days');
    if (start.month() == end.month()) {
      return start.format('D') + ' - ' + end.format('D MMM YYYY');
    }
    return start.format('d MMM') + ' - ' + end.format('d MMM YYYY');
  }

  return dayJs(appointmentStore.calendarDay).format('DD MMM YYYY');
});

/**
 * Changes the type of calendar view
 * It can either be list or timeGrid
 */
const changeViewType = (type) => {
  viewType.value = type;
  if (type == 'timeGrid' && viewOption.value == 'Year') {
    viewOption.value = 'Month';
  }
  changeView({ id: viewOption.value });
};

/**
 * Changes the view display.
 * This, combined with the viewType above, generate a custom calendar view
 * This mainly changes timeframes of the view. eg: Day,Month,Year
 */
const changeView = (option) => {
  let viewName = viewType.value + option.id;
  if (option.id == 'Month' && viewType.value == 'timeGrid') {
    viewName = 'dayGridMonth';
  }
  if (option.id == 'Day' && viewType.value == 'timeGrid') {
    viewName = 'timeGridOneDay';
  }

  setDatesIntervalFilters();

  emit('changeView', viewName);
};

const setDatesIntervalFilters = () => {
  const calendarDay = dayJs(appointmentStore.calendarDay);
  if (viewOption.value == 'Day') {
    appointmentStore.filters.from = calendarDay.format('YYYY-MM-DD');
    appointmentStore.filters.to = calendarDay.format('YYYY-MM-DD');
  } else if (viewOption.value == 'FourDay') {
    const start = calendarDay.format('YYYY-MM-DD');
    const end = calendarDay.add(3, 'day').format('YYYY-MM-DD');
    appointmentStore.filters.from = start;
    appointmentStore.filters.to = end;
  } else if (viewOption.value == 'Week') {
    const start = calendarDay
      .startOf('week')
      .add(1, 'day')
      .format('YYYY-MM-DD');
    const end = calendarDay.startOf('week').add(7, 'day').format('YYYY-MM-DD');
    appointmentStore.filters.from = start;
    appointmentStore.filters.to = end;
  } else {
    const start = calendarDay.startOf('month').format('YYYY-MM-DD');
    const end = calendarDay.endOf('month').format('YYYY-MM-DD');
    appointmentStore.filters.from = start;
    appointmentStore.filters.to = end;
  }
};
</script>
