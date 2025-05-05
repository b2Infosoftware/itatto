export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuthStore();

  if (!auth.loggedIn) {
    return navigateTo('/login');
  }
  if (!auth.emailVerified) {
    return navigateTo({ name: 'verify-email' });
  }
});
