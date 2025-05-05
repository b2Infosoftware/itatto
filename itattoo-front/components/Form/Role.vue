<template>
  <form @submit.prevent="handleSubmit">
    <div class="flex flex-col space-y-4 flex-grow mt-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input-text
          v-model="form.name"
          :label="$t('roles.name')"
          name="name"
          required
        ></input-text>
        <span></span>
      </div>

      <span
        v-for="(item, key) in sections"
        :key="key"
        class="bg-slate-50 dark:bg-slate-950 rounded-xl px-4 py-2"
      >
        <span class="font-semibold text-base mt-2 mb-4 block">{{ item }}</span>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
          <input-checkbox
            v-model="form.permissions"
            :value="p.id"
            v-for="p in permissionsFor(key)"
            :key="p.id"
            class="sm"
          >
            <span class="capitalize">{{ p.action }}</span>
          </input-checkbox>
        </div>
      </span>
    </div>
    <div class="flex mt-8 justify-between">
      <nuxt-link :to="{ name: 'settings-roles' }" class="btn secondary">
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
  role: {
    type: Object,
    default: {},
  },
  edit: {
    type: Boolean,
    default: false,
  },
});

const route = useRoute();
const saving = ref(false);
const sections = {
  dashboard: $t('roles.dashboard'),
  appointments: $t('roles.appointments'),
  customers: $t('roles.customers'),
  staff: $t('roles.staff'),
  services: $t('roles.services'),
  campaigns: $t('roles.marketing'),
  locations: $t('roles.locations'),
  roles: $t('roles.calendar_settings'),
  'calendar-settings': $t('roles.roles'),
  notifications: $t('roles.notifications'),
  'consent-forms': $t('roles.consent_forms'),
  logs: $t('roles.logs'),
};
const { data: permissions } = await useApi('GET', 'roles/permissions');

const form = reactive({
  name: '',
  permissions: [],
});

const permissionsFor = (entity) => {
  return permissions.filter((item) => item.entity == entity);
};

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  const method = props.edit ? 'PATCH' : 'POST';
  const url = props.edit ? 'roles/' + route.params.id : 'roles';
  saving.value = true;
  try {
    const response = await useApi(method, url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      if (method == 'POST') {
        navigateTo({
          name: 'settings-roles-id',
          params: { id: response.data.id },
        });
      } else {
        navigateTo({ name: 'settings-roles' });
      }
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};

const populateForm = async () => {
  if (!props.edit) {
    return;
  }
  form.name = props.role.name;
  form.permissions = props.role.permissions.map((item) => item.id);
};

onMounted(() => {
  populateForm();
});
</script>
