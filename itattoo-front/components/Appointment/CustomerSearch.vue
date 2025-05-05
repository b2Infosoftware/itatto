<template>
  <div class="w-full max-w-screen-sm mx-auto">
    <div class="pb-4">
      <form-customer hide-buttons ref="customerForm" @saved="customerSaved" v-if="wizard.newCustomer"></form-customer>

      <div v-else-if="wizard.customer" class="form-group">
        <label for="">Selected Customer</label>
        <span
          class="relative bg-transparent border border-gray-400 text-gray-700 sm:text-sm rounded-md block w-full p-2.5 focus:outline-none focus:border-gray-800 dark:text-gray-400 dark:focus:border-gray-300">{{
            wizard.customer.full_name }}
          <span class="absolute right-2 top-2">
            <button @click.prevent="removeCustomer" class="btn btn-xs danger">
              {{ $t('general.remove') }}
            </button>
          </span>
        </span>
      </div>

      <div v-else class="relative">
        <div class="form-group">
          <label class="block text-sm font-medium mb-1" for="tooltip" @click="focusInput">
            {{ $t('wizard.search_for_customer') }}
          </label>
          <span v-if="loading"
            class="absolute right-4 top-9 w-4 h-4 animate-spin border-2 border-dotted block rounded-full"></span>

          <input class="form-input w-full" name="customer_search" type="text"
            :placeholder="$t('wizard.customer_search_placeholder')" autocomplete="off" v-model="searchQuery"
            :disabled="loading" @focus="showDropdown = true" />
        </div>

        <!-- List rendering  -->
        <ul v-if="showDropdown" ref="customersDropdown" class="customers-dropdown" @scroll="handleScroll">
          <div v-if="!customers.length" class="flex flex-col items-center justify-center p-4">
            <span>{{ $t('wizard.no_customers') }}</span>
            <button @click.prevent="wizard.newCustomer = true" class="primary btn mt-5 btn-sm">
              {{ $t('wizard.add_customer') }}
            </button>
          </div>
          <template v-else>
            <div ref="scrollContainer" class="overflow-y-auto" >
              <li v-for="(item, key) in customers" :key="key" class="" @click.prevent="selectItem(item)">
                <div class="flex flex-col">
                  <span>{{ item.full_name }}</span>
                  <span class="flex flex-col text-xs opacity-70">
                    <span class="flex items-center">
                      <nuxt-icon filled name="phone" class="mr-1"></nuxt-icon>
                      {{ item.phone_number }}
                    </span>
                    <div class="flex items-center">
                      <nuxt-icon filled name="envelope" class="mr-1"></nuxt-icon>
                      {{ item.email }}
                    </div>
                  </span>
                </div>

              </li>
            </div>
            <div v-if="loading" class="text-center my-4">
              <span>Loading...</span>
            </div>
          </template>
        </ul>

        <div class="flex justify-center my-4 lg:my-8">
          <button @click.prevent="wizard.newCustomer = true" class="btn primary">
            {{ $t('customers.add_customer') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { watchDebounced, onClickOutside } from '@vueuse/core';
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {
    type: [Number, null],
    required: true,
  },
});
const wizard = useWizardStore();
const customerForm = ref(null);
const selectedCustomer = ref(null);
const loading = ref(false);
const showDropdown = ref(false);
const customers = ref([]);
const nextUrl = ref(null);
const searchQuery = ref('');
const customersDropdown = ref(null);
const scrollContainer = ref(null);

const handleScroll = () => {
  const el = scrollContainer.value;
  console.log("scrolling")
  if (!el) return;

  const maxScroll = el.scrollHeight - el.clientHeight;
  const scrollPos = el.scrollTop;


  const lowerThreshold = maxScroll * 0.01; // 1%
  const upperThreshold = maxScroll * 0.60; // 60%
  console.log(lowerThreshold)
  console.log(upperThreshold)
  console.log(scrollPos)

  if (scrollPos >= lowerThreshold && scrollPos <= upperThreshold) {
    if (nextUrl.value && !loading.value) {
      fetchCustomers();
    }
  }
};




onClickOutside(customersDropdown, (event) => {
  showDropdown.value = false;
});

const model = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  },
});

const selectItem = (item) => {
  wizard.customer = item;
  selectedCustomer.value = item;
  model.value = item.id;
  showDropdown.value = false;
  wizard.fetchProjects();
};

const customerSaved = (item) => {
  wizard.newCustomer = false;
  selectItem(item);
  customers.value.unshift(item);
};

const removeCustomer = () => {
  wizard.customer = null;
  selectedCustomer.value = null;
  model.value = null;
  wizard.fetchProjects();
};

watchDebounced(
  searchQuery,
  () => {
    fetchCustomers();
  },
  { debounce: 500, maxWait: 1000 }
);

const fetchCustomers = async () => {
  loading.value = true;
  const url = searchQuery.value ? 'customers' : nextUrl.value ?? 'customers';
  const response = await useApi('GET', url, {
    query: searchQuery.value,
  });
  customers.value = searchQuery.value
    ? response.data // If searching, replace list
    : [...customers.value, ...response.data]; // Otherwise, append results
  nextUrl.value = searchQuery.value ? null : response.links?.next;
  loading.value = false;
};

const createCustomer = () => {
  customerForm.value.handleSubmit();
};

onMounted(() => {
  fetchCustomers();
});

defineExpose({ createCustomer });
</script>
