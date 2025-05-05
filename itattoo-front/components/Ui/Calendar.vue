<template>
    <div class="calendar">
      <div class="calendar-month">
        <button @click="previousMonth">❮</button>
        <strong>{{ monthName }} {{ year }}</strong>
        <button @click="nextMonth">❯</button>
      </div>
      <table class="calendar-days">
        <thead>
          <tr>
            <th>MON</th>
            <th>TUE</th>
            <th>WED</th>
            <th>THU</th>
            <th>FRI</th>
            <th>SAT</th>
            <th>SUN</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(week, index) in weeks" :key="index">
            <td v-for="(day, i) in week" :key="i">
              <button
                :class="['day-btn', day.class, { active: isActive(day) }]"
                @click="handleClick(day)"
                :disabled="!isSelectable(day)"
              >
                {{ day.date }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, defineProps, defineEmits } from 'vue';
  
  const props = defineProps({
    workdays: {
      type: Array,
      default: () => [1, 2, 3, 4, 5],
    },
    restdays: {
      type: Array,
      default: () => [6, 7],
    },
    specialdays: {
      type: Array,
      default: () => []  
    }
  });
  const emit = defineEmits(['update:modelValue']);
  
  const formatDate = (year, month, day) => {
    const formattedMonth = month < 9 ? `0${month + 1}` : month + 1;
    const formattedDay = day < 10 ? `0${day}` : day;
    return `${year}-${formattedMonth}-${formattedDay}`;
  };
  
  const getFirstDayOfMonth = (month, year) => {
    const firstDay = new Date(year, month, 1).getDay();
    return firstDay === 0 ? 7 : firstDay;
  };
  
  const getDaysInMonth = (month, year) => {
    return new Date(year, month + 1, 0).getDate();
  };
  
  const today = new Date();
  const todayYear = today.getFullYear();
  const todayMonth = today.getMonth();
  const todayDate = today.getDate();
  
  const currentDate = ref(new Date());
  const month = computed(() => currentDate.value.getMonth());
  const year = computed(() => currentDate.value.getFullYear());
  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];
  const monthName = computed(() => monthNames[month.value]);
  
  const computeDayClass = (dayObj) => {
    const d = new Date(dayObj.actualYear, dayObj.actualMonth, dayObj.date);
    let dayOfWeek = d.getDay();
    const adjustedDay = dayOfWeek === 0 ? 7 : dayOfWeek;
    if (props.restdays.includes(adjustedDay)) return 'rest';
    if (props.workdays.includes(adjustedDay)) return 'work';
    return '';
  };
  
  const weeks = computed(() => {
    const daysInDisplayedMonth = getDaysInMonth(month.value, year.value);
    const firstDay = getFirstDayOfMonth(month.value, year.value);
    let days = [];
  
    for (let i = 0; i < firstDay - 1; i++) {
      const prevMonth = month.value - 1 < 0 ? 11 : month.value - 1;
      const prevYear = month.value - 1 < 0 ? year.value - 1 : year.value;
      const daysInPrevMonth = getDaysInMonth(prevMonth, prevYear);
      days.push({
        date: daysInPrevMonth - (firstDay - i - 2),
        source: 'prev',
        actualMonth: prevMonth,
        actualYear: prevYear,
        class: 'disable'
      });
    }
  
    for (let i = 1; i <= daysInDisplayedMonth; i++) {
      days.push({
        date: i,
        source: 'current',
        actualMonth: month.value,
        actualYear: year.value,
        class: '' 
      });
    }
  
    const weeksArray = [];
    while (days.length) {
      weeksArray.push(days.splice(0, 7));
    }
  
    weeksArray.forEach(week => {
      week.forEach(day => {
        if (day.source === 'prev') {
            day.class = 'disable';
        } else if (day.source === 'current') {
        if (day.actualYear === todayYear && day.actualMonth === todayMonth) {
            if (day.date < todayDate) {
            day.class = 'disable';
            } else {
            day.class = computeDayClass(day);
            }
        } else if (
            day.actualYear < todayYear ||
            (day.actualYear === todayYear && day.actualMonth < todayMonth)
        ) {
            day.class = 'disable';
        } else {
            day.class = computeDayClass(day);
        }
        } else if (day.source === 'next') {
        day.class = computeDayClass(day);
        }
  
        const formatted = formatDate(day.actualYear, day.actualMonth, day.date);
        if (props.specialdays.includes(formatted)) {
        const nowdayClass = day.class.includes('nowday') ? ' nowday' : '';
        day.class = 'rest disable' + nowdayClass;
        }

        
        if (
        day.actualYear === todayYear &&
        day.actualMonth === todayMonth &&
        day.date === todayDate
        ) {
        day.class = 'nowday';
        } else {
        const formatted = formatDate(day.actualYear, day.actualMonth, day.date);
        if (props.specialdays.includes(formatted)) {
            day.class = 'rest disable';
        }
        }
      });
    });
  
    return weeksArray;
  });
  
  const isSelectable = (dayObj) => {
    return dayObj.class.indexOf('disable') === -1;
  };
  
  const selectedDate = ref(getFirstSelectableDate());
  
  const isActive = (dayObj) => {
    return isSelectable(dayObj) && dayObj.date === selectedDate.value;
  };
  
  const previousMonth = () => {
    currentDate.value = new Date(year.value, month.value - 1, 1);
  };
  
  const nextMonth = () => {
    currentDate.value = new Date(year.value, month.value + 1, 1);
  };
  
  const selectDay = (dayObj) => {
    selectedDate.value = dayObj.date;
    const formattedDate = formatDate(dayObj.actualYear, dayObj.actualMonth, dayObj.date);
    emit('update:modelValue', formattedDate);
  };
  
  function getFirstSelectableDate() {
    let d = new Date();
    let dayOfWeek = d.getDay();
    let adjustedDay = dayOfWeek === 0 ? 7 : dayOfWeek;
    if (!props.restdays.includes(adjustedDay)) {
      return todayDate; 
    }
    for (let i = 1; i <= 7; i++) {
      d.setDate(d.getDate() + 1);
      dayOfWeek = d.getDay();
      adjustedDay = dayOfWeek === 0 ? 7 : dayOfWeek;
      if (!props.restdays.includes(adjustedDay)) {
        return d.getDate();
      }
    }
    return todayDate;
  }
  
  const firstSelectableDay = weeks.value
    .flat()
    .find(day => isSelectable(day) && day.date === selectedDate.value);
  
  if (firstSelectableDay) {
    selectDay(firstSelectableDay);
  }
  
  const handleClick = (dayObj) => {
    if (!isSelectable(dayObj)) return;
    selectDay(dayObj);
  };
  </script>