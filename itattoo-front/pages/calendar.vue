<template>
  <div class="calendarPage">
    <appointment-tooltip
      v-if="visibleTooltip"
      :info="tooltipData"
      :position="tooltipPosition"
    ></appointment-tooltip>

    <appointment-calendar-header
      @updatedStaff="updateResources"
      @changeView="changeView"
      @prev="changeInterval('prev')"
      @next="changeInterval('next')"
      @dateChanged="dateChanged()"
      @setToday="setToday"
    ></appointment-calendar-header>
    <div
      v-if="mounted"
      ref="calendarViewWrapper"
      class="calendarViewWrapper"
      :class="{ 'opacity-0': showOpacity }"
      :style="{ height: 'calc(100dvh - ' + dynamicHeight.y + 'px)' }"
    >
      <appointment-upcoming-aside
        v-show="appointmentStore.showUpcomingSearch"
        @viewItem="goToItem"
      ></appointment-upcoming-aside>

      <full-calendar ref="calendar" :options="calendarOptions">
        <template v-slot:eventContent="item">
          <appointment-event
            @showTooltip="showTooltip"
            @hideTooltip="hideTooltip"
            @goToItem="goToItem"
            :appointment="item.event"
          ></appointment-event>
        </template>
      </full-calendar>
    </div>

    <appointment-wizard
      v-if="wizard.isVisible"
      @hide="unselectEvents"
      @updated="eventUpdated"
    ></appointment-wizard>

    <appointment-details
      v-if="activeAppointment"
      :appointment="activeAppointment"
      @hide="activeAppointment = false"
      @deleted="eventDeleted"
      @edit="editAppointment"
      @statusUpdated="updateAppointmentStatus"
    ></appointment-details>

    <ui-confirm-modal ref="confirmEventModification">
      <template #title>{{ $t('calendar.confirm_reschedule') }}</template>
      <template #description>
        <div class="text-center text-base">
          <p class="mb-4">
            {{ $t('calendar.reschedule_question') }}
            {{ rescheduleDate }}?
          </p>
          <p class="flex items-center justify-center">
            <input-checkbox
              v-model="notifyCustomerOfChange"
              name="notify_customer"
            >
              {{ $t('calendar.send_notification') }}
            </input-checkbox>
          </p>
        </div>
      </template>
      <template #confirm>{{ $t('calendar.reschedule') }}</template>
    </ui-confirm-modal>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid';
import listPlugin from '@fullcalendar/list';
import { useElementBounding } from '@vueuse/core';

definePageMeta({
  middleware: 'subscribed',
});
useHead({
  title: $t('calendar.title'),
});

const mounted = ref(false);
const config = useRuntimeConfig();
const dayJs = useDayjs();
const appointmentStore = useAppointmentStore();
const settings = useOrganisationStore().calendarSettings;
const day = ref('');
const hour = ref('');
const calendar = ref(null);
const calendarViewWrapper = ref(null);
const wizard = useWizardStore();
const visibleTooltip = ref(false);
const tooltipData = ref({});
const confirmEventModification = ref(null);
const tooltipPosition = reactive({
  left: 0,
  top: 0,
});
const rescheduleDate = ref(null);
const notifyCustomerOfChange = ref(true);
const showOpacity = ref(true);

const activeAppointment = ref(false);

await appointmentStore.fetchItems();

const dynamicHeight = reactive(useElementBounding(calendarViewWrapper));

/**
 * Triggered when an event has been updated
 */
const eventUpdated = () => {
  appointmentStore.fetchItems();
  wizard.hide();
};

/**
 * Triggered when an event has been deleted
 */
const eventDeleted = () => {
  appointmentStore.fetchItems();
  activeAppointment.value = false;
};

/**
 * Clears the selection a use made in the calendar.
 */
const unselectEvents = () => {
  calendar.value.getApi().unselect();
};

/**
 * Switches the calendar to an appointment in the list.
 * It sets the date interval of the appointment and wiggles the appointment item to make it visible.
 */
const goToItem = async (item) => {
  calendar.value.getApi().gotoDate(item.date);
  updateFromToFilters();
  await appointmentStore.fetchItems();
  appointmentStore.shakeItemId = item.id;
  setTimeout(() => {
    appointmentStore.shakeItemId = null;
  }, 2000);
};

/**
 * Changes the status of an appointment.
 */
const updateAppointmentStatus = (newStatus) => {
  appointmentStore.updateItemStatus(activeAppointment.value.id, newStatus);
  activeAppointment.value = false;
};

/**
 * Changes the calendar view
 */
const changeView = async (viewName) => {
  calendar.value.getApi().changeView(viewName);
  updateFromToFilters();
  await appointmentStore.fetchItems();
  updateResources();
};

/**
 * Triggers when the date filters have changed.
 */
const dateChanged = () => {
  calendar.value.getApi().gotoDate(appointmentStore.calendarDay);
  updateFromToFilters();
  appointmentStore.fetchItems();
};

/**
 * Sets the calendar dates interval to today.
 */
const setToday = () => {
  calendar.value.getApi().today();
  appointmentStore.calendarDay = calendar.value.getApi().getDate();
  updateFromToFilters();
  appointmentStore.fetchItems();
};

/**
 * Changes the interval of the calendar.
 * Triggered by using prev/next arrows.
 */
const changeInterval = (direction) => {
  if (direction == 'prev') {
    calendar.value.getApi().prev();
  } else {
    calendar.value.getApi().next();
  }
  updateFromToFilters();
  appointmentStore.fetchItems();
};

/**
 * Updates the date interval filters in the store.
 */
const updateFromToFilters = () => {
  const start = calendar.value.getApi().view.activeStart;
  const end = calendar.value.getApi().view.activeEnd;
  appointmentStore.calendarDay = calendar.value.getApi().getDate();
  appointmentStore.filters.from = dayJs(start).format('YYYY-MM-DD');
  appointmentStore.filters.to = dayJs(end)
    .subtract(1, 'day')
    .format('YYYY-MM-DD');
};

/**
 * Quite self explainatory...
 */
const showTooltip = (info) => {
  tooltipData.value = info[1];
  if (tooltipData.value.extendedProps.isBreak) {
    return;
  }
  if (!tooltipData.value.id) {
    return;
  }
  tooltipPosition.left = info[0].elementPositionX.value + 'px';
  tooltipPosition.top = info[0].elementPositionY.value + 'px';
  visibleTooltip.value = true;
};

/**
 * Quite self explainatory...
 */
const hideTooltip = () => {
  visibleTooltip.value = false;
};

/**
 * Calls the calendar's API to refetch resources.
 */
const updateResources = () => {
  calendar.value.getApi().refetchResources();
};

/**
 * Checks whether a start/end date overlaps with time-off
 * for ANY of the selected staff.
 */
const dateOverlapsWithTimeOff = (startDate, endDate = null) => {
  if (settings.allow_off_hours_booking) {
    return false;
  }
  let isBooked = false;
  appointmentStore.staff.forEach((item) => {
    item.time_off.forEach((timeOff) => {
      const offDateStart = dayJs(timeOff.start_date);
      const offDateEnd = dayJs(timeOff.end_date);
      if (startDate.isBefore(offDateEnd) && startDate.isAfter(offDateStart)) {
        isBooked = true;
      }
    });
    item.availability
      .filter((i) => !i.is_available)
      .forEach((avlblt) => {
        const day = startDate.day();
        if (day == avlblt.day) {
          const offDateStart = startDate
            .hour(avlblt.start_time.split(':')[0])
            .minute(avlblt.start_time.split(':')[1]);
          const offDateEnd = startDate
            .hour(avlblt.end_time.split(':')[0])
            .minute(avlblt.end_time.split(':')[1]);
          if (
            startDate.isSame(offDateStart) ||
            (startDate.isBefore(offDateEnd) && startDate.isAfter(offDateStart))
          ) {
            isBooked = true;
          } else if (
            endDate &&
            (endDate.isSame(offDateEnd) ||
              (endDate.isBefore(offDateEnd) && endDate.isAfter(offDateStart)))
          ) {
            isBooked = true;
          }
        }
      });
  });
  return isBooked;
};

/**
 * Opens the wizard when a calendar slot is clicked
 */
const handleDateClick = (arg) => {
  const date = dayJs(arg.date);
  let isBooked = dateOverlapsWithTimeOff(date);

  if (
    arg.jsEvent.target.classList.contains('fc-non-business') &&
    !settings.allow_off_hours_booking
  ) {
    isBooked = true;
  }
  const now = new Date();
  const isPast = arg.date.getTime() < now.getTime();
  if (isPast) {
    useSnackbarStore().show($t('calendar.no_past_reschedule'), 'error');
    return;
  }
  if (isBooked) {
    useSnackbarStore().show($t('calendar.enable_timeoff_booking'), 'error');
    return;
  }
  day.value = date.format('YYYY-MM-DD');
  hour.value = date.format('HH:mm');
  wizard.start(day.value, hour.value);
};

/**
 * Handles the timeslot selections.
 * Similar to handleDateClick() but with an ending-hour selected as well
 */
const timeslotSelected = (e) => {
  if (wizard.isVisible) {
    return;
  }
  if (e.end.getDate() != e.start.getDate()) {
    calendar.value.getApi().unselect();
    return;
  }
  const start = dayJs(e.start);
  const end = dayJs(e.end);
  const isBooked = dateOverlapsWithTimeOff(start, end);
  if (isBooked) {
    calendar.value.getApi().unselect();
    useSnackbarStore().show($t('calendar.enable_timeoff_booking'), 'error');
    return;
  }

  day.value = start.format('YYYY-MM-DD');
  hour.value = start.format('HH:mm');

  const duration = dayJs(e.end).diff(start, 'minute');
  wizard.start(day.value, hour.value, duration);
};

/**
 * Opens the event details when a calendar event is clicked
 */
const handleEventClick = (e) => {
  activeAppointment.value = appointmentStore.items.find(
    (item) => item.id == e.event.id
  );
};

/**
 *  Opens a pop-up to confirm the slot-change for an event
 */
const handleEventChange = async (payload) => {
  hideTooltip();
  const startDate = dayJs(payload.event.start);
  if (startDate.isBefore(dayJs())) {
    payload.revert();
    return useSnackbarStore().show($t('calendar.no_past_reschedule'), 'error');
  }
  rescheduleDate.value = startDate.format('dddd, DD MMM YYYY HH:mm');

  const confirmed = await confirmEventModification.value.open();
  let editedEvent = appointmentStore.items.find(
    (item) => item.id == payload.oldEvent.id
  );

  if (!Boolean(confirmed)) {
    payload.revert();
    return;
  }

  const date = startDate.format('YYYY-MM-DD');
  const start = startDate.format('HH:mm');
  const duration = dayJs(payload.event.end).diff(startDate, 'minute');

  editedEvent.start_time = start;
  editedEvent.duration = duration;
  editedEvent.date = date;
  editedEvent.send_notification = notifyCustomerOfChange.value;
  await updateEvent(editedEvent);
  rescheduleDate.value = null;
  notifyCustomerOfChange.value = true;
};

/**
 * Updates an event in the DB.
 */
const updateEvent = async (dbEvent) => {
  const form = {
    staff_id: dbEvent.staff_id,
    project_id: dbEvent.project_id,
    service_id: dbEvent.service_id,
    customer_id: dbEvent.customer_id,
    location_id: dbEvent.location_id,
    start_time: dbEvent.start_time,
    date: dbEvent.date,
    duration: dbEvent.duration,
    price: dbEvent.price,
    note: dbEvent.note,
    status: dbEvent.status,
    send_notification: dbEvent.send_notification,
  };

  const url = 'appointments/' + dbEvent.id;
  try {
    const response = await useApi('PATCH', url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }
};

/**
 * Returns a mapped list of staff members with their business hours.
 */
const resources = () => {
  return appointmentStore.staff.map((item) => {
    return {
      id: item.id,
      title: item.full_name,
      businessHours: useSchedule().generateBusinessHours(item.availability),
    };
  });
};

/**
 * Generates business hours for staff members.
 */
const generateBusinessHours = computed(() => {
  const staffMembers = resources();
  return staffMembers.length ? staffMembers[0].businessHours : [];
});

/**
 * A set of options for the calendar.
 */
const calendarOptions = computed(() => {
  let sd = settings.slot_duration;
  if (sd < 10) {
    sd = '00:0' + sd + ':00';
  } else {
    sd = '00:' + sd + ':00';
  }
  return {
    plugins: [
      dayGridPlugin,
      interactionPlugin,
      timeGridPlugin,
      listPlugin,
      resourceTimeGridPlugin,
    ],
    schedulerLicenseKey: config.public.fullCalendarKey,
    initialView: settings.default_view,
    dateClick: handleDateClick,
    eventClick: handleEventClick,
    eventDrop: handleEventChange,
    eventResize: handleEventChange,
    slotDuration: sd,
    slotLabelInterval: { hours: 1 },
    allDaySlot: false,
    nowIndicator: true,
    selectAllow: (arg) => {
      const now = new Date();
      return (
        arg.start.getTime() > now.getTime() &&
        arg.end.getDate() == arg.start.getDate()
      );
    },
    height: 'auto',
    slotMinTime: settings.from_time,
    slotMaxTime: settings.to_time,
    firstDay: 1,
    editable: true,
    slotLabelFormat: {
      hour: '2-digit',
      minute: '2-digit',
      meridiem: false,
      hour12: false,
    },
    selectable: true,
    selectMirror: true,
    selectConstraint: settings.allow_off_hours_booking ? null : 'businessHours',
    select: timeslotSelected,
    events: appointmentStore.mappedItems,
    headerToolbar: false,
    views: {
      timeGridFourDay: {
        datesAboveResources: true,
        type: 'resourceTimeGridDay',
        duration: { days: 4 },
      },
      dayGridMonth: {
        dayMaxEventRows: 5,
        dayHeaderFormat: {
          weekday: 'short',
        },
      },
      timeGridOneDay: {
        datesAboveResources: true,
        type: 'resourceTimeGridDay',
      },
    },
    resources: resources(),
    businessHours: generateBusinessHours.value,
    hiddenDays: settings.hidden_days || [],
    dayHeaderFormat: {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
    },
  };
});
/**
 * Opens the edit-wizard for an appointment
 */
const editAppointment = () => {
  wizard.edit(activeAppointment.value);
  activeAppointment.value = false;
};

/**
 * Lifecycle hook.
 */
onMounted(async () => {
  if (!useAuthStore().user?.preselected_staff_id) {
    return;
  }
  if (!appointmentStore.filters.staff_ids.length) {
    const allStaff = useOrganisationStore().staff.map((item) => item.id);
    if (allStaff.includes(useAuthStore().user.preselected_staff_id)) {
      appointmentStore.filters.staff_ids = [
        useAuthStore().user.preselected_staff_id,
      ];
    }
  }
  if (!appointmentStore.staff.length) {
    useAppointmentStore().staff.push(useAuthStore().user);
  }
  mounted.value = true;
  await appointmentStore.fetchItems();

  updateResources();
  setTimeout(() => {
    calendar.value.getApi().updateSize();
    showOpacity.value = false;
  }, 250);
});
</script>
