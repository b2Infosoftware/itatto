export const useSettingsStore = defineStore('settingsStore', {
  state: () => ({
    allCountries: markRaw([]),
    allCurrencies: markRaw([]),
    allLanguages: markRaw([]),
  }),
  getters: {
    countries: (state) => state.allCountries,
    currencies: (state) => state.allCurrencies,
    languages: (state) => state.allLanguages,
  },
  actions: {
    /** Attempts to login a user */
    async getSettings() {
      const response = await useApi('GET', 'settings');
      this.allCountries = response.countries;
      this.allCurrencies = response.currencies;
      this.allLanguages = response.languages;
    },
  },
});
