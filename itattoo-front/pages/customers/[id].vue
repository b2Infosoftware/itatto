<template>
  <div class="xl:w-2/3 2xl:w-1/2">
    <div class="mb-4 flex justify-between">
      <div>
        <h1 class="text-2xl">{{ $t('customers.edit_customer') }}</h1>
        <p class="opacity-60 leading-tight text-sm mt-2">
          {{ $t('customers.edit_customer_subtitle') }}
        </p>
      </div>
      <div>
        <nuxt-link :to="{ name: 'customers' }" class="btn btn-icon outlined">
          <nuxt-icon filled name="chevron-left"></nuxt-icon>
        </nuxt-link>
      </div>
    </div>
    <ui-tabs v-model="activeTab" :tabs="tabs" class="mb-4"></ui-tabs>
    <div class="tabsContainer">
      <div v-if="activeTab == 0" class="card">
        <form-customer
          @updated="goToListing"
          edit
          :user="useCustomerStore().item"
        ></form-customer>
      </div>
      <customer-appointments v-if="activeTab == 1"></customer-appointments>
      <customer-projects v-if="activeTab == 2"> </customer-projects>

      <customer-statistics v-if="activeTab == 3"> </customer-statistics>
      <div v-if="activeTab == 4" class="card">
        <ui-no-data v-if="!images.length">
          <template #text>{{ $t('customers.no_photo') }}</template></ui-no-data
        >
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <customer-media-card
            @delete="deleteFile(item.id)"
            v-for="item in images"
            :key="item.id"
            :media="item"
          ></customer-media-card>
        </div>
        <div class="flex justify-center mt-4 pt-4">
          <button @click.prevent="mediaInput?.click" class="btn success">
            {{ $t('customers.add_file') }}
          </button>
        </div>
      </div>
      <div v-if="activeTab == 5" class="card">
        <ui-no-data v-if="!videos.length">
          <template #text>{{ $t('customers.no_video') }}</template></ui-no-data
        >
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <customer-media-card
            @delete="deleteFile(item.id)"
            v-for="item in videos"
            :key="item.id"
            :media="item"
          ></customer-media-card>
        </div>
        <div class="flex justify-center mt-4 pt-4">
          <button @click.prevent="mediaInput?.click" class="btn success">
            {{ $t('customers.add_file') }}
          </button>
        </div>
      </div>
      <input
        ref="mediaInput"
        type="file"
        name="file"
        accept=".jpeg,.png,.jpg,.mp4,.webm"
        hidden
        @input="changeImage($event)"
      />
    </div>
    <ui-confirm-modal type="danger" ref="confirmModal">
      <template #confirm>{{ $t('general.yes_delete') }}</template>
    </ui-confirm-modal>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  middleware: 'subscribed',
});
useHead({
  title: $t('customers.edit_customer'),
});
const mediaInput = ref(null);
const activeTab = ref(0);
const customerStore = useCustomerStore();
const confirmModal = ref(null);
const tabs = [
  { text: $t('customers.personal_details'), icon: 'user' },
  { text: $t('customers.appointments'), icon: 'calendar-day' },
  { text: $t('customers.projects'), icon: 'brush' },
  { text: $t('customers.stats'), icon: 'chart-bar' },
  { text: $t('customers.photos'), icon: 'camera' },
  { text: $t('customers.videos'), icon: 'video' },
];
const customerId = useRoute().params.id;
await customerStore.fetchCustomer(customerId);

const images = computed(() => {
  return (
    customerStore.item?.media?.filter((item) => item.type == 'image') || []
  );
});
const videos = computed(() => {
  return (
    customerStore.item?.media?.filter((item) => item.type == 'video') || []
  );
});

const changeImage = async (file) => {
  const { files } = file.target;

  if (!files || !files.length) {
    return;
  }

  await uploadFile(files[0]);
};

const goToListing = () => {
  navigateTo({ name: 'customers' });
};

const uploadFile = async (file) => {
  const formData = new FormData();
  formData.append('attachment', file);
  formData.append('customer_id', customerId);
  try {
    const response = await useApi('POST', 'media/upload', formData);
    if (response) {
      await customerStore.fetchCustomer(customerId);
    }
  } catch (error) {
    return error;
  }
};

const deleteFile = async (itemId) => {
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'media/' + itemId);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      await customerStore.fetchCustomer(customerId);
    }
  }
};
</script>
