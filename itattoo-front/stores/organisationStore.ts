export const useOrganisationStore = defineStore('organisationStore', {
  state: () => ({
    services: markRaw([]),
    staff: markRaw([]),
    locations: markRaw([]),
    organisations: markRaw([]),
    tags: markRaw([]),
    categories: markRaw([]),
    defaultOrganisation: markRaw({} as Organisation),
    defaultLocation: markRaw({} as StudioLocation),
    orgCalendarSettings: markRaw({} as CalendarSettings),
    notificationSettings: markRaw({} as NotificationSettings),
  }),
  getters: {
    calendarSettings: (state) => {
      const settings = { ...state.orgCalendarSettings };
      settings.from_time = state.defaultLocation.from_time;
      settings.to_time = state.defaultLocation.to_time;
      return settings;
    },
  },
  actions: {
    /**
     * Fetches the services associated with the default active organisation
     */
    async fetchServices() {
      try {
        const response = await useApi('GET', 'services');
        this.services =
          response.data.sort(
            (a, b) => parseInt(a.position) - parseInt(b.position)
          ) || [];
      } catch (error: any) {
        console.log(error);
      }
    },
    /**
     * Fetches the staff associated with the default active organisation
     */
    async fetchStaff() {
      try {
        const response = await useApi('GET', 'staff');
        this.staff = response.data || [];
      } catch (error: any) {
        console.log(error);
      }
    },
    /**
     * Fetches the organisations associated with the logged-in user
     */
    async fetchOrganisations() {
      try {
        const response = await useApi('GET', 'organisations');
        this.organisations = toRaw(response.data) || [];
        this.defaultOrganisation =
          response.data.find(
            (item: Organisation | null) => item.default_for_auth_user
          ) || {};
        this.orgCalendarSettings = this.defaultOrganisation.calendarSettings;
        this.notificationSettings =
          this.defaultOrganisation.notificationSettings;
      } catch (error: any) {
        console.log(error);
      }
    },
    /**
     * Gets all locations the auth user is associated with, WITHIN the default organisation/workspace
     */
    async fetchLocations() {
      try {
        const response = await useApi('GET', 'locations');
        this.locations = response.data || [];
        this.defaultLocation =
          response.data.find(
            (item: StudioLocation | null) => item.default_for_auth_user
          ) || {};
      } catch (error: any) {
        console.log(error);
      }
    },

    updateStaff(user: Staff) {
      const staffList = this.staff;
      this.staff.forEach((item: Staff, index) => {
        if (item.id == user.id) {
          staffList[index] = user;
        }
      });
      this.staff = staffList;
    },

    /**
     * Gets all the tags within an organisation
     */
    async fetchTags() {
      try {
        const response = await useApi('GET', 'tags');
        this.tags = response.data || [];
      } catch (error: any) {
        console.log(error);
      }
    },
    /**
     * Gets all the categories within an organisation
     */
    async fetchCategories() {
      try {
        const response = await useApi('GET', 'categories');
        this.categories = response.data || [];
      } catch (error: any) {
        console.log(error);
      }
    },
    /**
     * Fetch all needed data. Ran once on first app load
     */
    async fetchData() {
      await Promise.all([
        this.fetchOrganisations(),
        this.fetchCategories(),
        this.fetchLocations(),
        this.fetchServices(),
        this.fetchStaff(),
        this.fetchTags(),
      ]);
    },

    reset() {
      this.services = [];
      this.staff = [];
      this.locations = [];
      this.tags = [];
      this.organisations = [];
      this.categories = [];
      this.defaultOrganisation = {} as Organisation;
      this.defaultLocation = {} as StudioLocation;
      this.orgCalendarSettings = {} as CalendarSettings;
      this.notificationSettings = {} as NotificationSettings;
    },
  },
});
