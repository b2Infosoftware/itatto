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
                <!-- <th>{{ $t('general.actions') }}</th> -->
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in subscriptions" :key="item.id">
                <td class="capitalize">
                  <span class="th">{{ $t('subscriptions.company') }}</span>
                  <span>{{ item.organisation.name }}</span>
                </td>
                <td>
                  <span class="th">{{ $t('subscriptions.person') }}</span>
                  <span class="flex flex-col">
                    <em class="not-italic">
                      {{ item.organisation.owner.first_name }}
                      {{ item.organisation.owner.last_name }}
                    </em>
                    <em class="not-italic opacity-50">
                      {{ item.organisation.owner.email }}
                    </em>
                  </span>
                </td>
                <td>
                  <span class="th">{{ $t('subscriptions.plan') }}</span>
                  <span>
                    {{ item.plan.name }}
                  </span>
                </td>
                <td>
                  <span class="th">{{ $t('subscriptions.ends_at') }}</span>
                  <span>
                    {{ dayJs(item.ends_at).format('YYYY-MM-DD HH:mm') }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
const title = $t('subscriptions.title');

useHead({
  title: title,
});

const dayJs = useDayjs();
const subscriptions = ref([]);

const fetchData = async () => {
  const response = await useApi('GET', 'subscriptions');
  if (response) {
    subscriptions.value = response.data;
  }
};

onMounted(async () => {
  await fetchData();
});
</script>
