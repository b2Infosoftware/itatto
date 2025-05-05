<template>
  <div class="card">
    <div class="card-title">
      <div class="flex justify-between w-full">
        <div class="flex justify-between w-full">
          <span class="capitalize">{{ title }}</span>
          <div>
            <nuxt-link
              class="btn btn-sm primary mr-2"
              :to="{ name: 'settings-roles-create' }"
            >
              {{ $t('roles.create') }}
            </nuxt-link>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="w-full table-wrapper">
        <table class="w-full borderedTable">
          <thead class="text-left">
            <tr>
              <th>{{ $t('roles.name') }}</th>
              <th>{{ $t('roles.permissions_count') }}</th>
              <th>{{ $t('roles.staff_count') }}</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in roles" :key="item.id">
              <td class="capitalize">
                {{ item.name }}
              </td>
              <td class="capitalize">{{ item.permissions.length }}</td>
              <td class="capitalize">{{ item.staff_count }}</td>
              <td class="text-right">
                <nuxt-link
                  v-if="item.editable && useCan('edit', 'roles')"
                  :to="{
                    name: 'settings-roles-id',
                    params: { id: item.id },
                  }"
                  class="btn btn-xs primary"
                >
                  <span class="px-2">{{ $t('general.edit') }}</span>
                </nuxt-link>
                <button
                  v-if="item.editable && useCan('delete', 'roles')"
                  @click.prevent="deleteRole(item)"
                  class="btn btn-xs danger ml-4"
                >
                  {{ $t('general.delete') }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CONFIRM DELETION -->
    <ui-confirm-modal type="danger" ref="confirmModal">
      <template #confirm>{{ $t('general.yes_delete') }}</template>
    </ui-confirm-modal>
  </div>
</template>
<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  layout: 'settings',
  middleware: ['subscribed'],
  function() {
    if (!useCan('edit', 'roles')) {
      return navigateTo({ name: 'index' });
    }
  },
});
const title = $t('roles.title');
useHead({
  title: title,
});
const roles = ref([]);
const confirmModal = ref(null);

const fetchRoles = async () => {
  const response = await useApi('GET', 'roles');
  roles.value = response.data;
};

const deleteRole = async (item) => {
  if (item.staff_count > 0) {
    return useSnackbarStore().show($t('roles.delete_error'), 'error');
  }
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'roles/' + item.id);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      fetchRoles();
    }
  }
};

await fetchRoles();
</script>
