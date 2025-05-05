export const useLayoutStore = defineStore('layoutStore', {
  state: () => ({
    sidebarIsOpen: false,
    sidebarIsMinified: false,
    showLogs: false,
    theme: 'light',
  }),
  getters: {
    sidebarExpanded: (state) => state.sidebarIsOpen,
    sidebarMinified: (state) => state.sidebarIsMinified,
  },
  actions: {
    toggleSidebar() {
      this.sidebarIsMinified = false;
      this.sidebarIsOpen = !this.sidebarIsOpen;
    },
    toggleMinification() {
      this.sidebarIsMinified = !this.sidebarIsMinified;
    },
    toggleLogs() {
      this.showLogs = !this.showLogs;
    },
    initTheme() {
      if (localStorage.theme == 'dark') {
        this.theme = 'dark';
        document.documentElement.classList.add('dark');
      }
    },
    toggleTheme() {
      this.theme = this.theme === 'light' ? 'dark' : 'light';
      localStorage.setItem('theme', this.theme);
      if (this.theme == 'dark') {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    },
  },
});
