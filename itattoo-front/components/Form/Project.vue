<template>
  <div>
    <div v-if="edit" class="flex justify-between">
      <ul class="tabsHeader">
        <li v-for="(tab, index) in tabs">
          <button
            :class="{ active: index == activeTab }"
            @click.prevent.native="activeTab = index"
          >
            <span>{{ tab.text }}</span>
          </button>
        </li>
      </ul>
      <button
        v-if="activeTab == 1"
        @click.prevent="mediaInput?.click"
        class="btn secondary btn-icon"
      >
        <nuxt-icon filled name="upload"></nuxt-icon>
      </button>
    </div>
    <div v-if="activeTab == 0">
      <form class="w-full" @submit.prevent="saveProject">
        <div class="flex flex-col space-y-4 flex-grow mt-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input-text
              v-model="form.name"
              label="Project name"
              name="name"
            ></input-text>
            <input-select
              label="Category"
              name="category_id"
              v-model="form.category_id"
              :options="useOrganisationStore().categories"
              is-object
            ></input-select>
            <input-text-area
              optional
              v-model="form.description"
              label="Project description"
              name="description"
              class="col-span-2"
            ></input-text-area>
          </div>
        </div>
        <div class="inline-flex mt-8 justify-between w-full">
          <button @click.prevent="emit('cancel')" class="btn secondary">
            {{ $t('general.cancel') }}
          </button>
          <input-submit-button class="primary" :loading="saving">
            {{ $t('general.save') }}
          </input-submit-button>
        </div>
      </form>
    </div>
    <div v-if="activeTab == 1">
      <!-- NO File UI -->
      <ui-no-data v-if="noFiles">
        <template #text>
          <div class="text-sm">
            {{ $t('projects.no_files') }}
          </div>
        </template>
        <template #cta>
          <button @click.prevent="mediaInput?.click" class="btn primary">
            <span> {{ $t('projects.upload') }} </span>
          </button>
        </template>
      </ui-no-data>
      <ul v-else class="project-files h-72">
        <li v-if="props.project.signed_document" class="relative">
          <span class="flex pb-4">
            <nuxt-icon filled name="document"></nuxt-icon>
            {{ $t('wizard.view_signed_form') }}
          </span>
          <span class="actions pb-4">
            <a
              :href="props.project.signed_document.path"
              download
              class="btn btn-sm"
              target="_blank"
            >
              <nuxt-icon filled name="download"></nuxt-icon>
            </a>
            <em
              class="absolute bottom-2 left-4 ml-6 not-italic text-[10px] w-full block"
              >{{
                dayJs(props.project.signed_document.created_at).format(
                  'YYYY-MM-DD HH:mm'
                )
              }}</em
            >
          </span>
        </li>
        <li v-for="item in files" :key="item.id">
          <span class="flex">
            <nuxt-icon filled name="document"></nuxt-icon>
            {{ getFileName(item) }}
          </span>
          <span class="actions">
            <a
              :href="item.full_path"
              download
              class="btn btn-sm"
              target="_blank"
            >
              <nuxt-icon filled name="download"></nuxt-icon>
            </a>
            <button @click.prevent="deleteFile(item.id)" class="btn btn-sm">
              <nuxt-icon filled name="trash"></nuxt-icon>
            </button>
          </span>
        </li>
      </ul>
      <ui-confirm-modal type="danger" ref="confirmModal">
        <template #confirm>{{ $t('general.yes_delete') }}</template>
      </ui-confirm-modal>
    </div>
    <input
      ref="mediaInput"
      type="file"
      name="file"
      accept=".jpeg,.png,.jpg,.mp4,.webm,.psd"
      hidden
      @input="changeFile($event)"
    />
  </div>
</template>

<script setup>
const customerStore = useCustomerStore();
const { $t } = useNuxtApp();
const emit = defineEmits(['cancel']);
const props = defineProps({
  customer: {
    type: Object,
    default: {},
  },
  isModal: {
    type: Boolean,
    default: false,
  },
  project: {
    type: Object,
    default: {},
  },
  edit: {
    type: Boolean,
    default: false,
  },
});
const confirmModal = ref(null);
const dayJs = useDayjs();
const mediaInput = ref(null);
const saving = ref(false);
const activeTab = ref(0);
const tabs = [{ text: $t('projects.details') }, { text: $t('projects.files') }];

const form = {
  name: props.project?.name || '',
  description: props.project?.description || '',
  category_id: props.project?.category_id || '',
  staff_id: useAuthStore().user?.id || null,
  customer_id: props.customer.id,
};

const files = computed(() => {
  return (
    customerStore.item?.media?.filter(
      (item) => item.project_id == props.project?.id
    ) || []
  );
});

const noFiles = computed(() => {
  if (props.project?.signed_document) {
    return false;
  }
  if (files.value.length) {
    return false;
  }
  return true;
});

const getFileName = (item) => {
  const arr = item.full_path.split('/');
  return arr[arr.length - 1];
};

const saveProject = async () => {
  useValidationStore().clearErrors();

  const method = props.edit ? 'PATCH' : 'POST';
  const url = props.edit ? 'projects/' + props.project.id : 'projects';
  saving.value = true;
  try {
    const response = await useApi(method, url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      customerStore.updateProject(response.data);
      emit('cancel');
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
    console.log(errResponse);
  }

  saving.value = false;
};

const changeFile = async (file) => {
  const { files } = file.target;

  if (!files || !files.length) {
    return;
  }

  await uploadFile(files[0]);
};

const uploadFile = async (file) => {
  const formData = new FormData();
  formData.append('attachment', file);
  formData.append('customer_id', props.project.customer_id);
  formData.append('project_id', props.project.id);
  try {
    const response = await useApi('POST', 'media/upload', formData);
    if (response) {
      await customerStore.fetchCustomer(props.project.customer_id);
    }
  } catch (error) {
    return error;
  }
  mediaInput.value.value = '';
};

const deleteFile = async (itemId) => {
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'media/' + itemId);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      await customerStore.fetchCustomer(props.project.customer_id);
    }
  }
};
</script>
