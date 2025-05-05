import * as langs from '@/lang';
export default defineNuxtPlugin(async (NuxtApp) => {
  useLayoutStore().initTheme();
  const settingsStore = useSettingsStore();
  const organisationStore = useOrganisationStore();
  await settingsStore.getSettings();
  if (useAuthStore().loggedIn) {
    await organisationStore.fetchData();
  }

  let userLang = useCookie('usrLang').value;
  if (!userLang) {
    const orgLang = organisationStore.defaultOrganisation?.language;
    userLang = orgLang?.locale || 'en';
    useCookie('usrLang').value = userLang;
  }

  const translations = langs[userLang];

  const translate = (key: string) => {
    if (key.indexOf('.') > 0) {
      const keys = key.split('.') as Array<string>;
      if (!translations[keys[0]]) {
        return key;
      }
      if (!translations[keys[0]][keys[1]]) {
        return key;
      }
      return translations[keys[0]][keys[1]];
    }
    // not nested
    return translations[key] || key;
  };

  return {
    provide: {
      t: translate,
    },
  };
});
