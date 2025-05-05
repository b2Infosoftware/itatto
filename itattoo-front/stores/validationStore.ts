type ErrorMessageList = {
  [key: string]: any;
};
export const useValidationStore = defineStore('validationStore', {
  state: () => ({
    errorMessages: {} as ErrorMessageList,
    apiOverallMessage: '' as string,
  }),
  getters: {
    errorsList: (state) => state.errorMessages,
    generalMessage: (state) => state.apiOverallMessage,
  },
  actions: {
    clearError(name: string) {
      if (this.errorMessages[name]) {
        delete this.errorMessages[name];
      }
    },
    clearErrors() {
      this.errorMessages = {};
      this.apiOverallMessage = '';
    },
    populateErrors(apiErrors: Record<string, never>) {
      // it's a 403 or something
      if (apiErrors.message) {
        return;
      }
      this.errorMessages = apiErrors || {};
    },

    getError(key: string) {
      const errorArr = this.errorMessages[key] || [];
      return errorArr[0] || '';
    },
  },
});
