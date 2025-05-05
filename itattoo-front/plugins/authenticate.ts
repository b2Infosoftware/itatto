export default defineNuxtPlugin(async (NuxtApp) => {
  const authToken = useCookie('auth_token').value;
  if (authToken) {
    const authStore = useAuthStore();
    await authStore.fetchUser();
  }
});
