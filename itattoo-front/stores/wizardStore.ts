export const useWizardStore = defineStore('wizardStore', {
  state: () => ({
    allSteps: markRaw([
      {
        order: 1,
        key: 'appointment',
        icon: 'calendar-week',
      },
      {
        order: 2,
        key: 'customer',
        icon: 'user',
      },
      {
        order: 3,
        key: 'project',
        icon: 'brush',
      },
      {
        order: 4,
        key: 'payment',
        icon: 'credit-card',
      },
    ]),
    activeStep: 'appointment',
    hasProject: false as boolean,
    staff: null as Staff | null,
    newCustomer: false as boolean,
    project: null as Project | null,
    service: null as Service | null,
    customer: null as Customer | null,
    location: null as StudioLocation | null,
    editMode: false as boolean,
    projects: [] as Project[],
    consentForm: null as Object | null,
    isVisible: false,
    fetchingForm: false,
    editingId: null as number | null,
    appointment: {
      staff_id: null as number | null,
      project_id: null as number | null,
      service_id: null as number | null,
      customer_id: null as number | null,
      location_id: null as number | null,
      start_time: null as string | null,
      date: null as string | null,
      duration: 0 as number | null,
      price: 0 as number | null,
      note: '' as string | null,
      status: null as any,
      deposit: 0 as number | null,
    },
  }),

  getters: {
    steps: (state) => state.allSteps,
    form: (state) => state.appointment,
    showNoConsentFormWarning: (state) =>
      !state.fetchingForm && !state.consentForm,
  },
  actions: {
    hide() {
      this.reset();
      this.isVisible = false;
    },
    start(day: string, hour: string, duration: number = 0) {
      if (this.isVisible) {
        this.form.duration = 0;
        return;
      }
      try {
        this.form.date = day;
        this.form.start_time = hour;
        if (duration) {
          this.form.duration = duration;
        }
        this.isVisible = true;
      } catch (error) {
        console.log(error);
      }
    },
    edit(dbAppointment: Appointment) {
      this.staff = dbAppointment.staff || null;
      this.project = dbAppointment.project || null;
      this.service = dbAppointment.service || null;
      this.customer = dbAppointment.customer || null;
      this.location = dbAppointment.location || null;
      this.projects = dbAppointment.customer?.projects || [];
      this.appointment.staff_id = dbAppointment.staff_id;
      this.appointment.project_id = dbAppointment.project_id;
      this.appointment.service_id = dbAppointment.service_id;
      this.appointment.customer_id = dbAppointment.customer_id;
      this.appointment.location_id = dbAppointment.location_id;
      this.appointment.start_time = dbAppointment.start_time;
      this.appointment.date = dbAppointment.date;
      this.appointment.duration = dbAppointment.duration;
      this.appointment.price = dbAppointment.price;
      this.appointment.note = dbAppointment.note;
      this.appointment.status = dbAppointment.status;
      this.appointment.deposit = dbAppointment.deposit;
      this.hasProject = Boolean(dbAppointment.project_id);
      this.editMode = true;
      this.editingId = dbAppointment.id || null;
      this.isVisible = true;
      this.fetchConsentForm();
    },
    async fetchProjects() {
      if (!this.appointment.customer_id) {
        this.projects = [];
        return;
      }
      const response = await useApi('GET', 'projects', {
        customer_id: this.appointment.customer_id,
      });
      this.projects = response.data;
    },
    async fetchConsentForm() {
      if (!this.project) {
        this.consentForm = null;
        return;
      }
      this.fetchingForm = true;
      const response = await useApi(
        'GET',
        'consent-forms/for-category/' + this.project.category_id
      );
      this.consentForm = response.data;
      this.fetchingForm = false;
    },
    async signDocument(payload) {
      const response = await useApi('POST', 'projects/save-document', payload);
      this.project!.signed_document = response.data;
    },
    reset() {
      this.activeStep = 'appointment';
      this.editMode = false;
      this.hasProject = false;
      this.staff = null;
      this.project = null;
      this.service = null;
      this.customer = null;
      this.location = null;
      this.newCustomer = false;
      this.appointment = {
        staff_id: null,
        project_id: null,
        service_id: null,
        customer_id: null,
        location_id: null,
        start_time: null,
        date: null,
        duration: 0,
        price: 0,
        note: '',
        status: null,
      };
    },
  },
});
