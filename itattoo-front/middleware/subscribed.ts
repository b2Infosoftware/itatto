export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuthStore();
  const organisationStore = useOrganisationStore();

  if (!auth.loggedIn) {
    return navigateTo('/login');
  }
  if (!auth.emailVerified) {
    return navigateTo({ name: 'verify-email' });
  }
  if (!organisationStore.defaultOrganisation?.activeSubscription?.id) {
    return navigateTo({ name: 'settings-subscription' });
  }
});
