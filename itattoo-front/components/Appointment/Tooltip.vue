<template>
  <div
    class="event-tooltip"
    :style="{
      left: position.left,
      top: position.top,
    }"
  >
    <span
      v-if="info.extendedProps.status"
      class="status"
      :style="{
        backgroundColor: info.backgroundColor,
        color: useColor().getContrastColor(info.backgroundColor),
      }"
      >{{ $t('statuses.' + info.extendedProps.status) }}</span
    >
    <span class="font-semibold">{{ info.title }}</span>
    <ul>
      <li>
        <nuxt-icon name="phone" filled></nuxt-icon>
        <span>{{ info.extendedProps.phone_number }}</span>
      </li>
      <li>
        <nuxt-icon name="briefcase" filled></nuxt-icon>
        <span>{{ info.extendedProps.serviceName }}</span>
      </li>
      <li>
        <nuxt-icon name="credit-card" filled></nuxt-icon>
        <span>&euro;{{ info.extendedProps.price }}</span>
      </li>
      <li>
        <nuxt-icon name="banknotes" filled></nuxt-icon>
        <span>&euro;{{ info.extendedProps.deposit || 0 }}</span>
      </li>
      <li v-if="info.extendedProps.note">
        <nuxt-icon name="document" filled></nuxt-icon>
        <span>{{ info.extendedProps.note }}</span>
      </li>
      <li>
        <nuxt-icon name="clock" filled></nuxt-icon>
        <span>
          {{ dayJs(info.start).format('HH:mm') }} -
          {{ dayJs(info.end).format('HH:mm') }}
        </span>
      </li>
    </ul>
  </div>
</template>

<script setup>
const props = defineProps({
  position: {
    type: Object,
    default: {
      left: 0,
      right: 0,
    },
  },
  info: {
    type: Object,
    required: true,
  },
});

const dayJs = useDayjs();
</script>
