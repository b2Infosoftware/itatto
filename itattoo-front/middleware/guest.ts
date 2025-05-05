export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuthStore();

  if (auth.loggedIn) {
    return navigateTo('/');
  }
});
