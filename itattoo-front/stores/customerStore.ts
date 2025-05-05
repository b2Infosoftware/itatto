export const useCustomerStore = defineStore('customerStore', {
  state: () => ({
    customers: markRaw([] as Array<Appointment>),
    loading: false as boolean,
    customer: null as Customer | null,
    filters: {
      staff_ids: [],
      query: null,
      order_by: 'first_name',
      sort_by: 'asc',
      // location_id: null,
    } as CustomerFilters,
    nextPageUrl: null as string | null,
  }),
  getters: {
    list: (state) => state.customers,
    item: (state) => state.customer,
    projects: (state) => state.customer?.projects || [],
    appointments: (state) => state.customer?.appointments || [],
  },
  actions: {
    // Updates the projects list
    updateProject(payload: Project) {
      if (
        this.customer.projects.filter((item) => item.id == payload.id).length
      ) {
        this.customer.projects.forEach((item, index) => {
          if (item.id == payload.id) {
            this.customer.projects[index] = payload;
          }
        });
        return;
      }
      this.customer.projects.push(payload);
    },

    async fetchItems(searchQuery: string | null = null, infinite: boolean = false) {
      this.filters.query = searchQuery;
      this.loading = true;

      // Check if it's infinite scroll and if nextPageUrl exists
      const url = infinite && this.nextPageUrl ? this.nextPageUrl : 'customers';

      const response = await useApi('GET', url, this.filters);
      console.log(response)
      if (response) {
        if (infinite) {
          this.customers.push(...response.data); // Append data for infinite scrolling
        } else {
          this.customers = response.data; // Replace data for normal pagination
        }
        this.nextPageUrl = response.links?.next || null; // Update next page URL
      }

      this.loading = false;
    },

    async fetchCustomer(id: number) {
      const response = await useApi('GET', 'customers/' + id);
      this.customer = response?.data || [];
    },

    reset() {
      this.customers = [];
      this.customer = null;
    },
  },
});
