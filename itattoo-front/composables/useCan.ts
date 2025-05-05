export const useCan = (action: string, entity: string) => {
  const authStore = useAuthStore();
  const permissions = authStore.user.permissions;
  if (!authStore.loggedIn) {
    return false;
  }

  if (entity == 'superadmin') {
    return Boolean(authStore.user?.is_super_admin);
  }

  return Boolean(
    permissions.find(
      (item: any) =>
        item.action.toLowerCase() == action.toLowerCase() &&
        item.entity.toLowerCase() == entity.toLowerCase()
    )
  );
};
