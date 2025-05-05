<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between space-y-4">
      <div>
        <h1 class="text-2xl">{{ title }}</h1>
        <p class="opacity-60 leading-tight">
          {{ $t('staff.manage_staff') }}
        </p>
      </div>
      <nuxt-link
        v-if="useCan('create', 'staff')"
        :to="{ name: 'staff-create' }"
        class="btn primary md:h-10 justify-center sm:max-w-60"
        >{{ $t('staff.add_staff') }}</nuxt-link
      >
    </div>

    <div class="table-wrapper mt-4">
      <table>
        <thead>
          <tr>
            <th>{{ $t('staff.name') }}</th>
            <th>{{ $t('staff.role') }}</th>
            <th>{{ $t('staff.services') }}</th>
            <th>{{ $t('staff.status') }}</th>
            <th>{{ $t('general.actions') }}</th>
          </tr>
        </thead>
        <tbody id="servicesSection">
          <tr v-for="item in users" :key="item.id">
            <td class="capitalize">
              <span class="th">{{ $t('staff.name') }}</span>
              <div class="inline-flex items-center">
                <span
                  class="h-8 w-8 rounded-full overflow-hidden block bg-contain bg-center mr-3"
                  :style="{
                    backgroundImage: 'url(' + getImage(item) + ')',
                    backgroundColor: item.color,
                  }"
                ></span>
                <span class="inline-flex flex-col">
                  <span>
                    {{ item.full_name }}
                  </span>
                  <span class="lowercase text-xs opacity-70">
                    {{ item.email }}
                  </span>
                </span>
              </div>
            </td>
            <td>
              <span class="th">{{ $t('staff.role') }}</span>
              <span>
                {{ item.role?.name || 'N/A' }}
              </span>
            </td>
            <td>
              <span class="th">{{ $t('staff.services') }}</span>
              <span>
                <div
                  class="badge sm mr-2"
                  v-for="(s, i) in item.services"
                  :key="i"
                >
                  {{ s.name }}
                </div>
              </span>
            </td>
            <td>
              <span class="th">{{ $t('staff.status') }}</span>
              <span>
                <span v-if="item.is_guest" class="badge warning">{{
                  $t('staff.guest')
                }}</span>
                <span v-else class="badge success">{{
                  $t('staff.resident')
                }}</span>
              </span>
            </td>

            <td class="actions">
              <span>
                <nuxt-link
                  :to="{ name: 'staff-id', params: { id: item.id } }"
                  class="btn btn-sm btn-icon"
                >
                  <nuxt-icon filled name="pencil"></nuxt-icon>
                </nuxt-link>
                <button
                  v-if="useCan('delete', 'staff')"
                  @click.prevent="deleteStaff(item)"
                  class="btn btn-sm btn-icon mx-4"
                >
                  <nuxt-icon filled name="trash"></nuxt-icon>
                </button>
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- CONFIRM DELETION -->
    <ui-confirm-modal type="danger" ref="confirmModal">
      <template #confirm>{{ $t('general.yes_delete') }}</template>
    </ui-confirm-modal>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const title = $t('staff.staff_management');

useHead({
  title: title,
});
definePageMeta({
  middleware: 'subscribed',
});

const confirmModal = ref(null);
const users = ref([]);

const getImage = (staff) => {
  return staff.avatar || '';
};

const fetchStaff = async () => {
  const response = await useApi('GET', 'staff');
  if (response) {
    users.value = response.data;
  }
};

const deleteStaff = async (item) => {
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'staff/' + item.id);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      fetchStaff();
    }
  }
};

onMounted(async () => {
  await fetchStaff();
});
</script>
