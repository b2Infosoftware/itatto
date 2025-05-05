export const useSnackbarStore = defineStore('snackbarStore', {
  state: () => ({
    text: '' as string,
    alertType: '' as string,
  }),
  getters: {
    content: (state) => state.text,
    type: (state) => state.alertType,
  },
  actions: {
    show(text: string, type = 'info') {
      this.text = text;
      this.alertType = type;
      setTimeout(() => {
        this.text = '';
        this.alertType = '';
      }, 3000);
    },
  },
});
