export const useSchedule = () => {
  // const settings = useAuthStore().calendarSettings;
  const { $t } = useNuxtApp();
  type mappedAvailability = {
    day: number;
    startTime: string;
    endTime: string;
    is_available: boolean;
  };
  type businessHourItem = {
    daysOfWeek: Array<number>;
    startTime: string;
    endTime: string;
  };
  let hourIntervals = [];
  for (let index = 0; index < 24; index++) {
    if (index < 10) {
      hourIntervals.push('0' + index + ':00');
      hourIntervals.push('0' + index + ':30');
    } else {
      hourIntervals.push(index + ':00');
      hourIntervals.push(index + ':30');
    }
  }

  const groupAvailability = (
    items: Array<Availability>
  ): Array<mappedAvailability> => {
    let days = items.map((item) => {
      return {
        day: item.day == 7 ? 0 : item.day,
        startTime: item.start_time,
        endTime: item.end_time,
        is_available: item.is_available,
      };
    });

    days = useHelpers().groupBy(days, 'day');

    return Object.values(days);
  };

  const generateBusinessHours = (availability: Array<Availability>) => {
    const locationId = useOrganisationStore().defaultLocation?.id;
    const filtered = availability.filter(
      (item) => item.location_id == locationId
    );
    const fullDays = [] as Array<businessHourItem>;
    const groupedDays = groupAvailability(filtered);

    groupedDays.forEach((daySet: Array) => {
      // just one day
      if (daySet.length == 1 && daySet[0].is_available) {
        fullDays.push({
          startTime: daySet[0].startTime,
          endTime: daySet[0].endTime,
          daysOfWeek: [daySet[0].day],
        });
      }
      // more than one in a day means there's a break

      if (daySet.length > 1) {
        const sortedSet = daySet.sort(
          (a: any, b: any) => parseInt(a.startTime) - parseInt(b.startTime)
        );
        sortedSet.forEach((item: any, index: number) => {
          const prevItem = sortedSet[index - 1];
          const nextItem = sortedSet[index + 1];
          let startTime = item.endTime;

          if (index == 0) {
            startTime = item.startTime;
          } else if (index == daySet.length - 1) {
            startTime = item.endTime;
          }

          let endTime = nextItem ? nextItem.startTime : sortedSet[0].endTime;

          if (endTime != item.endTime) {
            fullDays.push({
              daysOfWeek: [item.day],
              startTime: startTime,
              endTime: endTime,
            });
          }
        });
      }
    });
    return fullDays.sort(
      (a, b) => parseInt(a.startTime) - parseInt(b.startTime)
    );
  };

  return { hourIntervals, generateBusinessHours };
};
