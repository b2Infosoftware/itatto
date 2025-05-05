<template>
  <div class="card">
    <div class="card-title">
      <div class="flex justify-between w-full">
        <div>
          <h1 class="text-2xl">{{ title }}</h1>
        </div>
        <nuxt-link
          v-if="forms.length"
          :to="{ name: 'settings-consent-forms-create' }"
          class="btn primary md:h-10 justify-center sm:max-w-60"
          >{{ $t('consentSettings.create_title') }}</nuxt-link
        >
      </div>
    </div>
    <div class="card-body">
      <ui-no-data v-if="!forms.length">
        <template #text>{{ $t('consentSettings.no_data') }}</template>
        <template #cta>
          <div class="flex justify-center gap-x-4">
            <nuxt-link
              class="btn primary"
              :to="{ name: 'settings-consent-forms-create' }"
            >
              {{ $t('consentSettings.create_title') }}
            </nuxt-link>
          </div>
        </template>
      </ui-no-data>
      <div v-else class="w-full table-wrapper">
        <table class="w-full borderedTable">
          <thead class="text-left">
            <tr>
              <th>Name</th>
              <th>Category</th>
              <th>Active</th>
              <th>Infant</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in forms" :key="item.id">
              <td class="capitalize">
                {{ item.name }}
              </td>
              <td>{{ item.category.name }}</td>
              <td class="text-center">
                <nuxt-icon
                  v-if="item.is_active"
                  name="bold-checked"
                  filled
                  class="text-success-500"
                ></nuxt-icon>
                <nuxt-icon
                  v-else
                  name="bold-x"
                  class="text-danger-500"
                  filled
                ></nuxt-icon>
              </td>
              <td class="text-center">
                <nuxt-icon
                  v-if="item.is_infant"
                  name="bold-checked"
                  class="text-success-500"
                  filled
                ></nuxt-icon>
                <nuxt-icon
                  v-else
                  name="bold-x"
                  class="text-danger-500"
                  filled
                ></nuxt-icon>
              </td>

              <td class="text-right">
                <nuxt-link
                  :to="{
                    name: 'settings-consent-forms-id',
                    params: { id: item.id },
                  }"
                  class="btn btn-sm btn-icon primary"
                >
                  <nuxt-icon filled name="pencil"></nuxt-icon>
                </nuxt-link>
                <button
                  @click.prevent="deleteForm(item)"
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
definePageMeta({
  layout: 'settings',
  middleware: ['subscribed'],
  function() {
    if (!useCan('edit', 'consent-forms')) {
      return navigateTo({ name: 'index' });
    }
  },
});
const title = $t('consentSettings.page_title');
useHead({
  title: title,
});
const confirmModal = ref(null);
const forms = ref([]);

const fetchForms = async () => {
  const response = await useApi('GET', 'consent-forms');
  forms.value = response.data;
};

await fetchForms();

const deleteForm = async (item) => {
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'consent-forms/' + item.id);
    if (response) {
      useSnackbarStore().show(response.message, 'success');

      fetchForms();
    }
  }
};
</script>
