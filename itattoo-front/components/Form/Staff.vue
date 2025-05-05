<template>
  <form class="card" @submit.prevent="handleSubmit">
    <div class="card-title">{{ $t('staff.personal_info') }}</div>
    <div class="flex space-x-4">
      <div class="form-group w-24">
        <label for="">{{ $t('staff.color') }}</label>
        <input v-model="form.color" type="color" class="color h-24" />
      </div>

      <div class="form-group">
        <label>{{ $t('staff.photo') }}</label>
        <form-photo
          v-model="form.avatar"
          @changed="updateAvatar"
          class="w-24"
        ></form-photo>
      </div>
    </div>

    <div class="flex flex-col space-y-4 flex-grow mt-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input-text
          v-model="form.first_name"
          :label="$t('staff.first_name')"
          name="first_name"
          required
        ></input-text>
        <input-text
          v-model="form.last_name"
          :label="$t('staff.last_name')"
          name="last_name"
          required
        ></input-text>
        <input-text
          v-model="form.email"
          :label="$t('staff.email')"
          name="email"
          required
        ></input-text>
        <input-phone
          :auto-country="!props.edit"
          :studio-country="country?.[0] || {}" 
          v-model="form.phone_number"
          :label="$t('staff.phone_number')"
          name="phone_number"
          required
        ></input-phone>
      </div>
      <input-text-area
        v-model="form.description"
        :label="$t('staff.description')"
        optional
        name="description"
        required
      ></input-text-area>
      <input-select
        :disabled="
          !useCan('view', 'roles') || props.user?.id == useAuthStore().user.id
        "
        :options="roles"
        label="Role"
        name="role_id"
        v-model="form.role_id"
        is-object
      ></input-select>

      <input-multiselect
        v-model="form.service_ids"
        multiple
        name="service_ids"
        :options="services.map((i) => i.id)"
        :custom-label="(opt) => services.find((e) => e.id === opt).name"
        :close-on-select="false"
        :searchable="false"
        :label="$t('staff.services')"
      ></input-multiselect>
      <input-multiselect
        v-model="form.tag_ids"
        multiple
        name="tag_ids"
        @addTag="saveTag"
        :options="useOrganisationStore().tags.map((i) => i.id)"
        :custom-label="
          (opt) => useOrganisationStore().tags.find((e) => e.id === opt)?.name
        "
        :close-on-select="false"
        searchable
        taggable
        :label="$t('staff.tags')"
      ></input-multiselect>

      <input-multiselect
        v-model="form.location_ids"
        multiple
        name="location_ids"
        :label="$t('staff.locations')"
        :options="locations.map((i) => i.id)"
        :custom-label="(opt) => locations.find((e) => e.id === opt)?.name"
        :close-on-select="false"
        :searchable="false"
      ></input-multiselect>

      <ui-switch-button
        name="show_stats"
        v-model="form.view_statistics"
        class="pt-8"
        >{{ $t('staff.show_stats_info') }}
      </ui-switch-button>
      <ui-switch-button name="is_guest" v-model="form.is_guest"
        >{{ $t('staff.is_guest') }}
      </ui-switch-button>
    </div>
    <div class="inline-flex mt-8 justify-between">
      <nuxt-link :to="{ name: 'staff' }" class="btn secondary">
        {{ $t('general.back') }}
      </nuxt-link>
      <input-submit-button class="primary" :loading="saving">
        {{ $t('general.save') }}
      </input-submit-button>
    </div>
  </form>
</template>
<script setup>
const { $t } = useNuxtApp();
const props = defineProps({
  user: {
    type: Object,
    default: {},
  },
  edit: {
    type: Boolean,
    default: false,
  },
});
const emit = defineEmits(['updated']);
const services = useOrganisationStore().services;
const locations = useOrganisationStore().locations;
const country = locations.map(location => location.country);
const auth = useAuthStore();
const route = useRoute();
const saving = ref(false);
const { data: roles } = await useApi('GET', 'roles');
const form = reactive({
  default_organisation_id: auth.user.default_organisation_id,
  first_name: '',
  last_name: '',
  email: '',
  avatar: '',
  description: '',
  phone_number: '',
  color: '#' + Math.floor(Math.random() * 16777215).toString(16),
  view_statistics: false,
  is_guest: false,
  tag_ids: [],
  role_id: null,
  service_ids: [],
  location_ids: [],
});

const saveTag = async (tagName) => {
  await useApi('POST', '/tags', {
    name: tagName,
  });
  useOrganisationStore().fetchTags();
};

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  const method = props.edit ? 'PATCH' : 'POST';
  const url = props.edit ? 'staff/' + props.user.id : 'staff';
  saving.value = true;
  try {
    const response = await useApi(method, url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      useOrganisationStore().fetchStaff();
      if (method == 'POST') {
        navigateTo({ name: 'staff' });
      } else {
        emit('updated');
      }
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};

const updateAvatar = async () => {
  if (!props.edit) {
    return;
  }
  await handleSubmit();
};

const populateForm = async () => {
  if (!props.edit) {
    return;
  }
  form.first_name = props.user.first_name;
  form.last_name = props.user.last_name;
  form.email = props.user.email;
  form.description = props.user.description;
  form.phone_number = props.user.phone_number || '';
  form.avatar = props.user.avatar;
  form.color = props.user.color;
  form.service_ids = props.user.services.map((item) => item.id);
  form.role_id = props.user.role?.id || null;
  form.view_statistics = props.user.view_statistics;
  form.is_guest = props.user.is_guest;
  form.tag_ids = props.user.tags?.map((item) => item.id) || [];
  form.location_ids = props.user.locations?.map((item) => item.id) || [];
};

onMounted(() => {
  populateForm();
});
</script>
