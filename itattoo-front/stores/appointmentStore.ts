const dayJs = useDayjs();
export const useAppointmentStore = defineStore('appointmentStore', {
  state: () => ({
    items: markRaw([] as Array<Appointment>),
    upcoming: markRaw([] as Array<Appointment>),
    mappedItems: [] as Array<CalendarEvent>,
    staff: [] as Array<Staff>,
    shakeItemId: null as number | null,
    customerNameQuery: null as string | null,
    calendarDay: new Date(),
    showUpcomingSearch: false,
    filters: {
      staff_ids: [],
      from: dayJs(new Date()).format('YYYY-MM-DD'),
      to: null,
      customer_ids: [],
      location_id: null,
      service_ids: [],
      status: null,
      duration: null,
      duration_operator: '=',
    } as AppointmentFilters,
  }),
  getters: {
    list: (state) => state.items,
  },
  actions: {
    breakEvents() {
      const locationId = useOrganisationStore().defaultLocation.id;
      this.staff.forEach((staffItem: Staff) => {
        staffItem.availability?.forEach((avl: Availability) => {
          if (avl.is_available) {
            return;
          }
          if (avl.location_id != locationId) {
            return;
          }
          const date = dayJs(this.calendarDay).day(avl.day);
          const dateFormat = date.format('YYYY-MM-DD');
          const tmp = {
            id: 'avlblt' + avl.id,
            resourceId: staffItem.id,
            title: 'Break Time: ' + staffItem.full_name,
            start: new Date(dateFormat + ' ' + avl.start_time),
            end: new Date(dateFormat + ' ' + avl.end_time),
            display: 'background',
            extendedProps: {
              isBreak: true,
            },
          } as CalendarEvent;

          this.mappedItems.push(tmp);
        });
        staffItem.time_off?.forEach((timeOff: TimeOff) => {
          const tmp = {
            id: 'timeoff' + timeOff.id,
            resourceId: staffItem.id,
            title: timeOff.reason + ': ' + staffItem.full_name,
            start: new Date(timeOff.start_date),
            end: new Date(timeOff.end_date),
            display: 'background',
            extendedProps: {
              isBreak: true,
            },
          } as CalendarEvent;

          this.mappedItems.push(tmp);
        });
      });
    },
    mapEvent(dbEvent: Appointment) {
      const staffColors =
        useOrganisationStore().calendarSettings.use_staff_colors;
      const color = staffColors ? dbEvent.staff.color : dbEvent.service.color;

      const mappedEvent = {
        id: dbEvent.id,
        resourceId: dbEvent.staff_id,
        title: dbEvent.customer.full_name,
        start: new Date(dbEvent.date + ' ' + dbEvent.start_time),
        end: new Date(dbEvent.date + ' ' + dbEvent.end_time),
        backgroundColor: color,
        textColor: useColor().getContrastColor(color),
        borderColor: useColor().adjust(color),
        extendedProps: {
          isBreak: false,
          phone_number: dbEvent.customer.phone_number,
          note: dbEvent.note,
          deposit: dbEvent.deposit,
          price: dbEvent.price,
          status: dbEvent.status,
          serviceName: dbEvent.service.name,
          staff_id: dbEvent.staff_id,
          project_group: dbEvent.project_group,
          is_online: dbEvent.staff.services.find(service => service.id === dbEvent.service_id)?.is_online ?? 0,
          vip_name: dbEvent.customer.vip_name,
          vip_color: dbEvent.customer.vip_color,
        },
      } as CalendarEvent;
      console.log("Mapped Event:", mappedEvent); 
      return mappedEvent;
    },
    updateItemStatus(itemId: number, newStatus: any) {
      (this.mappedItems as any).find((item: any) => item.id == itemId).extendedProps.status =
        newStatus;
    },
    async fetchItems() {
      const response: any = await useApi('GET', 'appointments', this.filters);
      console.log("API Response:", response);
      this.items = response?.data || [];

      this.mappedItems = this.items.map((item) => this.mapEvent(item));
      this.breakEvents();

      this.fetchUpcomingAppointments();
    },

    async fetchUpcomingAppointments() {
      try {
        const filters = {
          customer_name: this.customerNameQuery,
          ...this.filters,
        };
        const response: any = await useApi('GET', 'appointments/upcoming', filters);
        this.upcoming = response?.data || [];
      } catch (error) {
        console.log(error);
      }
    },

    async updateFilters(payload: AppointmentFilters) {
      this.filters = payload;
      await this.fetchItems();
    },

    async updateAvilability() {
      await useOrganisationStore().fetchStaff();
      this.staff.forEach((item) => {
        item.availability = useOrganisationStore().staff.find(
          (i) => i.id == item.id
        ).availability;
      });
    },

    reset() {
      this.items = [];
      this.upcoming = [];
      this.items = [];
      this.upcoming = [];
      this.filters.staff_ids = [];
      this.filters.from = dayJs(new Date()).format('YYYY-MM-DD');
      this.filters.to = null;
      this.filters.customer_ids = [];
      this.filters.location_id = null;
      this.filters.service_ids = [];
      this.filters.status = null;
      this.filters.duration = null;
      this.filters.duration_operator = '=';
    },
  },
});
