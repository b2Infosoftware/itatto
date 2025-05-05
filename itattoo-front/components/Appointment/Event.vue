<template>
  <div
    class="event-item-wrapper"
    :class="{
      'not-upcoming': appointment.extendedProps.status != null,
      'is-break': appointment.extendedProps.isBreak,
      shake: appointmentStore.shakeItemId == appointment.id,
    }"
    :style="{
      backgroundColor: appointment.backgroundColor,
      color: appointment.textColor,
    }"
    @mouseenter="mouseOver"
    @mouseleave="mouseOut"
  >
    <span
      :style="{
        backgroundColor: appointment.textColor,
        color: appointment.backgroundColor,
        fontSize: '12px',
        fontWeight: '400',
        borderRadius: '4px',
        marginBottom: '2px',
      }"
      class="event-status"
      v-if="appointment.extendedProps?.status"
    >
      {{ getStatus() }}
    </span>
    <span class="event-title">{{ appointment.title }}</span>
    <div v-if="!appointment.extendedProps.isBreak" class="event-details">
      <ul>
        <li v-if="appointment.extendedProps?.project_group?.length">
          <nuxt-icon name="brush" filled></nuxt-icon>
          <span>
            {{ getProjectPosition() }}
          </span>
        </li>
        <li>
          <nuxt-icon name="phone" filled></nuxt-icon>
          <span>{{ appointment.extendedProps.phone_number }}</span>
        </li>
        <li>
          <nuxt-icon name="briefcase" filled></nuxt-icon>
          <span>{{ appointment.extendedProps.serviceName }}</span>
        </li>
        <li v-if="appointment.extendedProps.note">
          <nuxt-icon name="document" filled></nuxt-icon>
          <span>{{ appointment.extendedProps.note }}</span>
        </li>
        <li>
          <nuxt-icon name="credit-card" filled></nuxt-icon>
          <span>&euro;{{ appointment.extendedProps.price }}</span>
        </li>
        <li>
          <nuxt-icon name="banknotes" filled></nuxt-icon>
          <span>&euro;{{ appointment.extendedProps.deposit }}</span>
        </li>
        <li>
          <!-- {{ appointment.extendedProps }} -->
          <span v-if="appointment.extendedProps.is_online" class="px-1 mt-2 py-0.5 rounded-md text-white bg-black uppercase">Online Booking</span>
        </li>
        <li v-show="appointment.extendedProps.vip_name">
          <span
            class="pl-1 pr-1.5 mt-2 py-1 rounded-md text-white text-[12px] font-bold uppercase"
            :style="{ backgroundColor: appointment.extendedProps.vip_color }"
          >
            <nuxt-icon name="crown" class="float-left mr-2" filled></nuxt-icon>
            {{ appointment.extendedProps.vip_name }}
          </span>
        </li>
        <template v-if="appointment.extendedProps?.project_group?.length">
          <li
            v-for="item in appointment.extendedProps.project_group"
            :key="item.id"
          >
            <a
              :class="{ 'hover:underline': item.id != appointment.id }"
              @click.prevent.stop="goToItem(item)"
            >
              {{ item.index }}/{{
                props.appointment.extendedProps.project_group.length
              }}
              • &euro;
              {{ item.deposit || 0 }}
              •
              {{ dayJs(item.date).format('DD MMM YYYY') }}
            </a>
          </li>
        </template>
      </ul>
    </div>
    <span
      class="fc-event-time"
      :style="{ backgroundColor: appointment.backgroundColor }"
    >
      {{ dayJs(appointment.start).format('HH:mm') }} -
      {{ dayJs(appointment.end).format('HH:mm') }}
    </span>
  </div>
</template>

<script setup>
import { useMouseInElement } from '@vueuse/core';
const { $t } = useNuxtApp();
const emit = defineEmits(['showTooltip', 'hideTooltip', 'goToItem']);
const appointmentStore = useAppointmentStore();

const props = defineProps({
  appointment: {
    required: true,
  },
});
const dayJs = useDayjs();

const getStatus = () => {
  return $t('statuses.' + props.appointment.extendedProps.status);
};

const mouseOver = (e) => {
  const position = useMouseInElement(e.target);

  emit('showTooltip', [position, props.appointment]);
};

const getProjectPosition = () => {
  const positionArr = props.appointment.extendedProps.project_group;
  const current = positionArr.find((item) => item.id == props.appointment.id);
  return (
    current.index +
    '/' +
    positionArr.length +
    ' • ' +
    current.project_name +
    ' • ' +
    current.project_category
  );
};

const goToItem = (item) => {
  emit('goToItem', {
    date: item.date,
    id: item.id,
  });
  emit('hideTooltip');
};

const mouseOut = () => {
  emit('hideTooltip');
};
</script>
