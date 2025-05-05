<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between space-y-4">
      <div>
        <h1 class="text-2xl">{{ title }}</h1>
        <!-- <p class="opacity-60 leading-tight">
          {{ $t('customers.manage_customers') }}
        </p> -->
      </div>
      <!-- <nuxt-link
        :to="{ name: 'customers-create' }"
        class="btn primary md:h-10 justify-center sm:max-w-60"
        >{{ $t('customers.add_customer') }}</nuxt-link
      > -->
    </div>

    <div class="card mt-4 md:mt-8">
      <span class="card-title">{{ $t('customers.customers_list') }}</span>
      <div class="card-body">
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>{{ $t('subscriptions.company') }}</th>
                <th>{{ $t('subscriptions.person') }}</th>
                <th>{{ $t('subscriptions.plan') }}</th>
                <th>{{ $t('subscriptions.ends_at') }}</th>
                <th>{{ $t('general.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in organisations" :key="item.id">
                <td class="capitalize">
                  <span class="th">{{ $t('subscriptions.company') }}</span>
                  <span> {{ item.name }} </span>
                </td>
                <td>
                  <span class="th">{{ $t('subscriptions.person') }}</span>
                  <span>
                    <em v-if="!item.owner" class="text-danger-500 opacity-70"
                      >Deleted</em
                    >
                    {{ item.owner?.first_name }}
                    {{ item.owner?.last_name }}
                  </span>
                </td>
                <td>
                  <span class="th">{{ $t('subscriptions.plan') }}</span>
                  <span>
                    {{ item.active_subscription?.plan?.name || 'N/A' }}
                  </span>
                </td>
                <td>
                  <span class="th">{{ $t('subscriptions.ends_at') }}</span>
                  <span v-if="item.active_subscription">
                    {{
                      dayJs(item.active_subscription.ends_at).format(
                        'YYYY-MM-DD HH:mm'
                      )
                    }}
                  </span>
                  <span v-else>N/A</span>
                </td>

                <td class="actions">
                  <span class="space-x-2 flex items-center">
                    <button
                      :disabled="
                        item.id == useOrganisationStore().defaultOrganisation.id
                      "
                      @click.prevent="setDefault(item)"
                      class="btn btn-xs primary"
                    >
                      Use as GOD
                    </button>
                    <span
                      v-if="item.suspended_at"
                      @click.prevent="suspend(item)"
                      class="btn btn-xs blue"
                    >
                      Re-Enable
                    </span>
                    <button
                      v-else
                      @click.prevent="suspend(item)"
                      class="btn btn-xs danger"
                    >
                      Suspend
                    </button>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- CONFIRM DELETION -->
    <ui-confirm-modal type="danger" ref="confirmModal"> </ui-confirm-modal>
  </div>
</template>

<script setup>
const title = 'All organisations';

useHead({
  title: title,
});

const dayJs = useDayjs();
const organisations = ref([]);
const confirmModal = ref(null);

const fetchData = async () => {
  const response = await useApi('GET', 'organisations/super-admin');
  if (response) {
    organisations.value = response.data;
  }
};

const orgStore = useOrganisationStore();

const setDefault = async (item) => {
  if (orgStore.defaultOrganisation.id == item.id) {
    return useSnackbarStore().show('Already being used...', 'warning');
  }
  const response = await useApi('POST', 'change-organisation/' + item.id);
  useSnackbarStore().show(response.message, 'success');

  reloadNuxtApp({ ttl: 1000 });
};

const suspend = async (item) => {
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('POST', 'organisations/suspend/' + item.id);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      fetchData();
    }
  }
};

onMounted(async () => {
  await fetchData();
});
</script>
