<template>
  <div>
    <div class="card">
      <span class="card-title justify-between">
        {{ title }}
      </span>
      <div class="card-body">
        <customer-filters></customer-filters>
        <div class="flex justify-between pt-4 my-4 border-t border-divider-light dark:border-divider-dark">
          <span></span>
          <nuxt-link v-if="useCan('create', 'customers')" :to="{ name: 'customers-create' }"
            class="btn primary md:h-10 justify-center sm:max-w-60">{{ $t('customers.add_customer') }}</nuxt-link>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>{{ $t('customers.name') }}</th>
                <th>{{ $t('customers.vip_status') }}</th>
                <th>{{ $t('customers.email') }}</th>
                <th>{{ $t('customers.phone_number') }}</th>
                <th>{{ $t('customers.gender') }}</th>
                <th>{{ $t('general.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in customerStore.list" :key="item.id">
                <td class="capitalize">
                  <span class="th">{{ $t('customers.name') }}</span>
                  <span> {{ item.first_name }} {{ item.last_name }} </span>
                </td>
                <td class="capitalize">
                  <strong v-show="item.vip" class="inline-block w-3 h-3 rounded-full mr-3"
                    :style="{ backgroundColor: item?.vip?.color || '#facc15' }"></strong>
                  <span> {{ item?.vip?.label }} </span>
                </td>
                <td>
                  <span class="th">{{ $t('customers.email') }}</span>
                  <span>
                    {{ item.email }}
                  </span>
                </td>
                <td>
                  <span class="th">{{ $t('customers.phone_number') }}</span>
                  <span>
                    {{ item.phone_number }}
                  </span>
                </td>
                <td>
                  <span class="th">{{ $t('customers.gender') }}</span>
                  <span>
                    {{ $t('customers.' + item.gender) }}
                  </span>
                </td>
                <td class="actions">
                  <span>
                    <nuxt-link :to="{ name: 'customers-id', params: { id: item.id } }" class="btn btn-sm btn-icon">
                      <nuxt-icon filled name="pencil"></nuxt-icon>
                    </nuxt-link>
                    <button @click.prevent="deleteCustomer(item)" class="btn btn-sm btn-icon ml-4">
                      <nuxt-icon filled name="trash"></nuxt-icon>
                    </button>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Loading Indicator -->
        <div v-if="customerStore.loading" class="text-center my-4">
          <span>Loading...</span>
        </div>
      </div>

      <!-- Load More button -->
      <div v-if="customerStore.nextPageUrl && !customerStore.loading" class="text-center my-8">
        <button @click="loadMore" class="btn primary">
          {{ $t('general.load_more') }}
        </button>
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
const title = $t('customers.customers_management');

useHead({
  title: title,
});

definePageMeta({
  middleware: 'subscribed',
});

const customerStore = useCustomerStore();
const confirmModal = ref(null);

const deleteCustomer = async (item) => {
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'customers/' + item.id);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      customerStore.fetchItems();
    }
  }
};

onMounted(() => {
  customerStore.fetchItems(null, true);
});

const loadMore = () => {
  if (!customerStore.loading && customerStore.nextPageUrl) {
    customerStore.fetchItems(null, true);
  }
};


// customerStore.fetchItems();
</script>
