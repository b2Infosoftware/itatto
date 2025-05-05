<template>
  <div class="grow w-full h-full flex flex-col" autocomplete="off">
    <!-- Panel body -->
    <div>
      <div class="text-xl text-slate-800 font-bold border-b border-slate-200">
        <span class="mb-5 block">{{ title }}</span>
      </div>
    </div>
    <div class="p-6 space-y-6">
      <div>
        <div v-if="hasActiveSubscription" class="text-sm">
          Your account's {{ activeSubscription.plan.name }} Plan is set to
          <strong class="font-medium"
            >${{ activeSubscription.plan.price }}</strong
          >
          per year
          <template v-if="activeSubscription.canceled_at">
            and will be terminated on
          </template>
          <template v-else> and will renew on </template>
          <strong class="font-medium">{{ getNextPaymentDate() }}</strong
          >.
        </div>
        <div v-else>
          <div class="max-w-2xl m-auto mt-16">
            <div class="text-center px-4">
              <img
                :src="'/images/no_data.svg'"
                class="block mx-auto w-40 mb-10"
              />
              <h2 class="text-2xl text-slate-800 font-bold mb-2">
                You don't have an active subscription at the moment.
              </h2>
              <div class="mb-6">
                Click on the button below to start check out our plans.
              </div>
              <a class="btn primary"> See subscription plans </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Billing Information -->
      <section v-if="hasActiveSubscription" class="pt-5">
        <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">
          Billing Information
        </h3>
        <ul>
          <!-- PLAN -->
          <li
            class="md:flex md:justify-between md:items-center py-3 border-b border-slate-200"
          >
            <div class="text-sm text-slate-800 font-medium">Active Plan</div>
            <div class="text-sm text-slate-800ml-4">
              <span class="mr-3 font-medium">{{
                activeSubscription.plan.name
              }}</span>
              <a class="btn btn-text btn-sm primary">Edit</a>
            </div>
          </li>
          <!-- Interval -->
          <li
            class="md:flex md:justify-between md:items-center py-3 border-b border-slate-200"
          >
            <div class="text-sm text-slate-800 font-medium">
              Billing Interval
            </div>
            <div class="text-sm text-slate-800ml-4">
              <span class="mr-3">Yearly</span>
            </div>
          </li>
          <li
            class="md:flex md:justify-between md:items-center py-3 border-b border-slate-200"
          >
            <div
              v-if="activeSubscription.canceled_at"
              class="text-sm text-slate-800 font-medium"
            >
              Expires at
            </div>
            <div v-else class="text-sm text-slate-800 font-medium">
              Next payment
            </div>
            <div class="text-sm text-slate-800ml-4">
              <span class="mr-3">{{ getNextPaymentDate() }}</span>
            </div>
          </li>
        </ul>
      </section>
      <!-- Invoices -->
      <section v-if="invoices.length" class="pt-5">
        <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">
          Invoices
        </h3>

        <!-- Table -->
        <table class="table-auto w-full">
          <!-- Table header -->
          <thead class="text-xs uppercase text-slate-400 rounded">
            <tr class="flex flex-wrap md:table-row md:flex-no-wrap">
              <th class="w-full block md:w-auto md:table-cell py-2">
                <div class="font-semibold text-left">Date</div>
              </th>
              <th class="w-full hidden md:w-auto md:table-cell py-2">
                <div class="font-semibold text-left">Amount</div>
              </th>
              <th class="w-full hidden md:w-auto md:table-cell py-2">
                <div class="font-semibold text-left">Status</div>
              </th>
              <th class="w-full hidden md:w-auto md:table-cell py-2">
                <div class="font-semibold text-right"></div>
              </th>
            </tr>
          </thead>
          <!-- Table body -->
          <tbody class="text-sm">
            <!-- Row -->
            <tr
              v-for="(invoice, index) in invoices"
              :key="index"
              class="flex flex-wrap md:table-row md:flex-no-wrap border-b border-slate-200 py-2 md:py-0"
            >
              <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2">
                <div class="text-left font-medium text-slate-800">
                  {{ getInvoiceMonth(invoice) }}
                </div>
              </td>
              <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2">
                <div class="text-left font-medium text-slate-800">
                  {{ getInvoicePrice(invoice) }}
                </div>
              </td>
              <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2">
                <div class="text-left font-medium capitalize">
                  {{ invoice.status }}
                </div>
              </td>
              <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2">
                <div class="text-right flex items-center md:justify-end">
                  <a
                    class="btn btn-xs danger outlined"
                    :href="invoice.invoice_pdf"
                    target="_blank"
                    >PDF</a
                  >
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
    </div>
  </div>
</template>
<script setup>
definePageMeta({
  layout: 'settings',
  middleware: 'auth',
});
const title = 'Billing and invoices';
useHead({
  title: title,
});
const auth = useAuthStore();
const invoices = ref([]);

const activeSubscription = computed(() => {
  return auth.user.activeSubscription;
});
const hasActiveSubscription = computed(() => {
  return Boolean(auth.user?.activeSubscription);
});

onMounted(async () => {
  if (!hasActiveSubscription) {
    return;
  }
  const { data } = await useApi('GET', 'stripe-invoices/' + auth.user.id);
  invoices.value = data.value?.invoices;
});

/**
 * Displays the date of the next invoice in a nice format
 */
const getNextPaymentDate = () => {
  const date = new Date(activeSubscription.value.ends_at);
  return new Intl.DateTimeFormat('en-US').format(date);
};
const getInvoicePrice = (invoice) => {
  return '$' + invoice.amount_due / 100;
};
const getInvoiceMonth = (invoice) => {
  const date = new Date(invoice.finalized_at);
  return new Intl.DateTimeFormat('en-US').format(date);
};
</script>
