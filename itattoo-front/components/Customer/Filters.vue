<template>
  <div class="flex-col flex md:flex-row gap-4 w-full py-4">
    <div class="form-group flex-grow">
      <label for="">
        {{ $t('customers.customer') }}
      </label>
      <span
        v-if="loading"
        class="absolute right-4 top-9 w-4 h-4 animate-spin border-2 border-dotted block rounded-full"
      ></span>

      <input
        class="form-input w-full"
        name="customer_search"
        type="text"
        :placeholder="$t('wizard.customer_search_placeholder')"
        autocomplete="off"
        v-model="searchQuery"
        @input="loading = true"
      />
    </div>
    <input-multiselect
      :label="$t('wizard.artist')"
      name="staff_id"
      text-key="full_name"
      v-model="customerStore.filters.staff_ids"
      :options="staffMembers"
      @change="customerStore.fetchItems()"
      is-object
      class="flex-grow"
    ></input-multiselect>

    <input-select
      :label="$t('customers.order_by')"
      v-model="customerStore.filters.order_by"
      name="status"
      :options="orderByOptions"
      hide-empty
      is-object
      @change="customerStore.fetchItems()"
    ></input-select>

    <input-select
      :label="$t('customers.sort_by')"
      v-model="customerStore.filters.sort_by"
      name="status"
      :options="sortByOptions"
      hide-empty
      is-object
      @change="customerStore.fetchItems()"
    ></input-select>
  </div>
</template>
<script setup>
import { watchDebounced } from '@vueuse/core';
const { $t } = useNuxtApp();
const customerStore = useCustomerStore();
const organisationStore = useOrganisationStore();
const staffMembers = organisationStore.staff;
const searchQuery = ref('');
const loading = ref(false);
const orderByOptions = [
  {
    id: 'first_name',
    name: $t('customers.first_name'),
  },
  {
    id: 'created_at',
    name: $t('customers.date_added'),
  },
];
const sortByOptions = [
  {
    id: 'asc',
    name: $t('customers.ascending'),
  },
  {
    id: 'desc',
    name: $t('customers.descending'),
  },
];

watchDebounced(
  searchQuery,
  async (value) => {
    await customerStore.fetchItems(value);
    loading.value = false;
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
