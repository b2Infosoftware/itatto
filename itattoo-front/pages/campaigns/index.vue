<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between space-y-4">
      <div>
        <h1 class="text-2xl">{{ $t('marketing.title') }}</h1>
        <p class="opacity-60 leading-tight">
          {{ $t('marketing.description') }}
        </p>
      </div>
      <nuxt-link
        v-if="useCan('create', 'campaigns')"
        :to="{ name: 'campaigns-create' }"
        class="btn primary md:h-10 justify-center sm:max-w-60"
        >{{ $t('marketing.create_campaign') }}</nuxt-link
      >
    </div>
    <div class="card mt-8">
      <span class="card-title">{{ $t('marketing.your_campaigns') }}</span>

      <div class="table-wrapper">
        <table class="w-full">
          <thead>
            <tr>
              <th>{{ $t('marketing.name') }}</th>
              <th>{{ $t('marketing.type') }}</th>
              <th>{{ $t('marketing.scheduled_on') }}</th>
              <th>{{ $t('marketing.timezone') }}</th>
              <th>{{ $t('marketing.status') }}</th>
              <th>{{ $t('marketing.active') }}</th>
              <th class="px-4"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in campaigns" :key="item.id">
              <td class="capitalize">
                <span class="th">{{ $t('marketing.name') }}</span>
                <span>{{ item.name }}</span>
              </td>
              <td :class="item.type == 'sms' ? 'uppercase' : 'capitalize'">
                <span class="th">{{ $t('marketing.type') }}</span>
                <span> {{ item.type }}</span>
              </td>
              <td>
                <span class="th">{{ $t('marketing.scheduled_on') }}</span>
                <span>
                  <span v-if="item.is_birthday">
                    {{ $t('calendarSettings.daily') }}
                  </span>
                  <span v-else> {{ item.scheduled_date }} </span> @
                  {{ item.scheduled_time }}
                </span>
              </td>
              <td>
                <span class="th">{{ $t('marketing.timezone') }}</span>
                <span>{{ item.timezone }}</span>
              </td>
              <td>
                <span class="th">{{ $t('marketing.status') }}</span>
                <span>
                  <span v-if="item.is_birthday">
                    {{
                      item.is_active
                        ? $t('marketing.ongoing')
                        : $t('marketing.paused')
                    }}
                  </span>
                  <span v-else>
                    {{
                      item.delivered_on
                        ? $t('marketing.sent')
                        : $t('marketing.upcoming')
                    }}
                  </span>
                </span>
              </td>

              <td>
                <span class="th">{{ $t('marketing.active') }}</span>
                <span>
                  <span
                    v-if="item.is_active"
                    class="w-3 h-3 rounded-full bg-success-500 block ml-4"
                  ></span>
                  <span
                    v-else
                    class="w-3 h-3 rounded-full bg-danger-500 block ml-4"
                  ></span>
                </span>
              </td>

              <td class="text-right">
                <nuxt-link
                  :to="{ name: 'campaigns-id', params: { id: item.id } }"
                  class="btn btn-sm btn-icon primary"
                >
                  <nuxt-icon filled name="pencil"></nuxt-icon>
                </nuxt-link>
                <button
                  @click.prevent="deleteCampaign(item)"
                  class="btn btn-sm btn-icon danger ml-4"
                >
                  <nuxt-icon filled name="trash"></nuxt-icon>
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
const is_trial = useOrganisationStore().defaultOrganisation.is_trial;
const error = useError();

definePageMeta({
  middleware: 'subscribed',
});

useHead({
  title: $t('marketing.title'),
});
const confirmModal = ref(null);
const campaigns = ref([]);

const fetchCampaigns = async () => {
  const response = await useApi('GET', 'campaigns');
  campaigns.value = response.data;
};

await fetchCampaigns();

const deleteCampaign = async (item) => {
  if (item.is_birthday) {
    useSnackbarStore().show($t('marketing.cannot_delete_birthday'), 'warning');
    return false;
  }
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'campaigns/' + item.id);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      fetchCampaigns();
    }
  }
};

onMounted(() => {
  if (is_trial) {
    error.value = {
      statusCode: 404,
      message: $t('general.page_not_found'),
    };
  }
});
</script>
