<template>
  <div class="card">
    <div class="card-title">
      <div class="flex justify-between w-full">
        <span class="capitalize">{{ $t('locations.title') }}</span>
        <div>
          <button
            v-if="!showForm && useCan('create', 'locations')"
            class="btn btn-sm primary mr-2"
            @click.prevent="showForm = !showForm"
          >
            {{ $t('locations.add_location') }}
          </button>
        </div>
      </div>
    </div>
    <div class="card-body gap-y-4 flex flex-col">
      <div class="locationCards">
        <span v-for="item in locations" :key="item.id" class="location-card">
          <nuxt-icon filled name="shop"></nuxt-icon>
          <span>{{ item.name }}</span>
          <div class="details">
            <div class="info-line">
              <nuxt-icon name="location" filled></nuxt-icon>
              <div>
                <div>{{ item.address }}</div>
                <div>
                  {{ item.city }},
                  {{ item.state ? item.state + ',' : '' }}
                  {{ item.post_code }}
                </div>
                <div>{{ item.country?.name }}</div>
              </div>
            </div>
            <div class="info-line">
              <nuxt-icon name="phone" filled></nuxt-icon>
              <div>{{ item.phone_number }}</div>
            </div>
            <div class="info-line">
              <nuxt-icon name="calculator" filled></nuxt-icon>
              <div>{{ item.vat_number || '-' }}</div>
            </div>
            <div class="info-line">
              <nuxt-icon name="link" filled></nuxt-icon>
              <div>{{ item.website || '-' }}</div>
            </div>
            <div class="info-line">
              <nuxt-icon name="clock" filled></nuxt-icon>
              <div>{{ item.from_time }} - {{ item.to_time }}</div>
            </div>
          </div>

          <div class="flex justify-between w-full">
            <button
              @click.prevent="setActive(item)"
              :disabled="!useCan('edit', 'locations')"
              class="btn btn-sm secondary"
            >
              {{ $t('general.edit') }}
            </button>
            <button
              v-if="locations.length > 1 && useCan('delete', 'locations')"
              @click.stop.prevent="deleteLocation(item)"
              class="btn danger btn-sm outlined"
            >
              {{ $t('general.delete') }}
            </button>
          </div>
        </span>
      </div>
      <form-location
        @saved="fetchLocations"
        @close="hideForm"
        :location="activeLocation"
        v-if="showForm"
        class="mt-5"
      ></form-location>
    </div>
    <ui-confirm-modal type="danger" ref="confirmModal">
      <template #confirm>{{ $t('general.yes_delete') }}</template>
    </ui-confirm-modal>
  </div>
</template>
<script setup>
definePageMeta({
  layout: 'settings',
  middleware: [
    'subscribed',
    function () {
      if (!useCan('edit', 'locations')) {
        return navigateTo({ name: 'index' });
      }
    },
  ],
});
const title = 'Dashboard';
useHead({
  title: title,
});

const showForm = ref(false);
const confirmModal = ref(null);
const activeLocation = ref(location);
const locations = ref([]);

const setActive = (item) => {
  activeLocation.value = item;
  showForm.value = true;
};

const hideForm = () => {
  activeLocation.value = null;
  showForm.value = false;
};

const fetchLocations = async () => {
  const response = await useApi('GET', 'locations');
  locations.value = response.data;
  hideForm();
};

const deleteLocation = async (item) => {
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'locations/' + item.id);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      fetchLocations();
    }
  }
};

onMounted(() => {
  fetchLocations();
});
</script>
