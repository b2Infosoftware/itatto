<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between space-y-4">
      <div>
        <h1 class="text-2xl">{{ title }}</h1>
      </div>
    </div>

    <div class="organisationsList">
      <div
        v-for="org in useOrganisationStore().organisations"
        :key="org.id"
        class="card"
      >
        <div
          class="org"
          @click.prevent="setActive(org)"
          :class="{ selected: org.id == currentId }"
        >
          <i class="">
            <em></em>
          </i>

          <span>
            {{ org.name }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const title = $t('general.pick_organisation');
definePageMeta({
  middleware: 'auth',
});
useHead({
  title: title,
});

const orgStore = useOrganisationStore();
const currentId = orgStore.defaultOrganisation.id;

const setActive = async (org) => {
  try {
    const response = await useApi('POST', '/change-organisation/' + org.id);

    useSnackbarStore().show(response.message, 'success');
    reloadNuxtApp({ ttl: 1000 });
  } catch (error) {
    console.log(err);
  }
};

onMounted(async () => {});
</script>
