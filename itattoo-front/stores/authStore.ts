export const useAuthStore = defineStore('authStore', {
  state: () => ({
    authUserData: markRaw({}) as AuthUserData,
    serverError: null,
  }),
  getters: {
    loggedIn: (state) => Boolean(state.authUserData.id),
    emailVerified: (state) => Boolean(state.authUserData?.email_verified_at),
    user: (state) => state.authUserData,
  },
  actions: {
    /** Fetches the logged-in user */
    async fetchUser() {
      this.serverError = null;
      try {
        const response = await useApi('GET', 'me');
        const user = response.data || {};
        this.authUserData = user;
      } catch (error: any) {
        // most likely there's a problem with the token so we remove the auth_token the user
        useCookie('auth_token').value = null;
      }
    },

    /**
     * Attempts to login a user if we have a token. Usually used after a registration.
     */
    async loginWithToken(token: string) {
      useCookie('auth_token').value = token;
      await new Promise((r) => setTimeout(r, 1000));
      await this.fetchUser();
      navigateTo({ name: 'index' });
    },

    /**
     * Logs out a user and deletes its token from cookies.
     */
    async logout() {
      await useApi('POST', 'logout');
      this.authUserData = {};
      // clear all stores
      useAppointmentStore().reset();
      useCustomerStore().reset();
      useOrganisationStore().reset();
      useValidationStore().clearErrors();
      useWizardStore().reset();

      useCookie('auth_token').value = null;
      useCookie('selectedStaff').value = null;
      navigateTo({ name: 'login' });
    },
  },
});
