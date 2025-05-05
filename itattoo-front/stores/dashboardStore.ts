export const useDashboardStore = defineStore('dashboardStore', {
  state: () => ({
    processing: true,
    form: {
      service_id: null,
      year: new Date().getFullYear(),
      month: null,
      location_id: null,
    },
    by_date: markRaw([]),
    by_service: markRaw([]),
    active_projects: 0,
    clients: 0,
    sms: 0,
    today_appointments: 0,
    today_clients: 0,
    total_appointments: 0,
    total_income: 0,
    total_projects: 0,
    upcoming_appointments: 0,
  }),
  getters: {
    graphData: (state) => state.by_date,
    serviceData: (state) => state.by_service,
    showChart: (state) => !state.processing,
  },
  actions: {
    async fetchData() {
      try {
        this.processing = true;
        const response = await useApi('GET', 'dashboard', this.form);
        this.by_date = response.data.by_date || [];
        this.by_service = response.data.by_service || [];
        this.active_projects = response.data.active_projects;
        this.clients = response.data.clients;
        this.sms = response.data.sms;
        this.today_appointments = response.data.today_appointments;
        this.today_clients = response.data.today_clients;
        this.total_appointments = response.data.total_appointments;
        this.total_income = response.data.total_income;
        this.total_projects = response.data.total_projects;
        this.upcoming_appointments = response.data.upcoming_appointments;
        this.processing = false;
      } catch (error: any) {
        console.log(error);
      }
    },
  },
});
