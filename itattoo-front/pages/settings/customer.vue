<template>
  <div class="card">
    <div class="card-title">
      <div class="flex justify-between w-full">
        <span class="capitalize">{{ title }}</span>
      </div>
    </div>
    <div class="card-body gap-y-4 flex flex-col">
      <div class="w-full">
        <form
          class="grow w-full h-full flex flex-col gap-4"
          @submit.prevent="handleSubmit"
        >
          <span
            class="border-b border-slate-200/20 pb-2 mt-4 mb-2 text-sm font-semibold"
          >
            {{ $t('customerSettings.hide_fields') }}
          </span>

          <div class="grid gap-4 grid-cols-1 md:grid-cols-3 lg:grid-cols-4">
            <input-checkbox
              v-for="item in customerFields"
              :key="item.prop"
              v-model="form.hidden_fields"
              :value="item.prop"
              :id="item.prop"
              >{{ item.text }}</input-checkbox
            >
          </div>

          <!-- <span
            class="border-b border-slate-200/20 pb-2 mt-4 mb-2 text-sm font-semibold"
          >
            {{ $t('customerSettings.other_settings') }}
          </span>
          <input-select
            v-model="form.country_id"
            :label="$t('customerSettings.default_country')"
            :options="useSettingsStore().countries"
            name="country_id"
            class="lg:w-1/3"
            is-object
            required
          ></input-select> -->

          <div class="inline-flex mt-5 justify-between">
            <input-submit-button class="primary" :loading="saving">
              {{ $t('general.save') }}
            </input-submit-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  layout: 'settings',
  middleware: [
    'subscribed',
    function () {
      if (!useCan('edit', 'settings')) {
        return navigateTo({ name: 'index' });
      }
    },
  ],
});
const title = $t('customerSettings.title');
useHead({
  title: title,
});

const saving = ref(false);
const dbOrg = useOrganisationStore().defaultOrganisation;
const form = reactive({
  country_id: dbOrg.country_id,
  hidden_fields: dbOrg.hidden_fields,
  name: dbOrg.name,
  slug: dbOrg.slug,
});

const customerFields = [
  { prop: 'first_name', text: $t('customerSettings.first_name') },
  { prop: 'last_name', text: $t('customerSettings.last_name') },
  // { prop: 'email', text: $t('customerSettings.email') },
  // { prop: 'phone_number', text: $t('customerSettings.phone_number') },
  { prop: 'birth_date', text: $t('customerSettings.birth_date') },
  { prop: 'gender', text: $t('customerSettings.gender') },
  { prop: 'is_minor', text: $t('customerSettings.is_minor') },
  { prop: 'city', text: $t('customerSettings.city') },
  { prop: 'address', text: $t('customerSettings.address') },
  { prop: 'state', text: $t('customerSettings.state') },
  { prop: 'postal_code', text: $t('customerSettings.postal_code') },
  { prop: 'ssn', text: $t('customerSettings.ssn') },
  { prop: 'notes', text: $t('customerSettings.notes') },
  { prop: 'referral', text: $t('customerSettings.referral') },
];

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  saving.value = true;
  try {
    const response = await useApi('PATCH', 'organisations/' + dbOrg.id, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      useOrganisationStore().defaultOrganisation = response.data;
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};
</script>
