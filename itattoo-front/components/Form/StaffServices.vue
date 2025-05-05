<template>
  <div class="card">
    <div class="justify-between card-title">
      <span>{{ $t('staff.services') }}</span>
      <input-select
        v-model="activeCategory"
        name="categroy_id"
        :options="categories"
        hide-empty
        is-object
        class="sm min-w-40"
      ></input-select>
    </div>

    <div>
      <div
        v-for="service in filteredServices"
        :key="service.id"
        class="serviceItem"
        @click.prevent="toggleService(service)"
      >
        <span class="color-code" :style="{ backgroundColor: service.color }"></span>
        <span class="flex">
          <img :src="getImage(service)" :alt="service.name" />
          <span class="flex flex-col justify-between overflow-hidden">
            <h6>{{ service.name }}</h6>
            <p>{{ useHelpers().niceTime(service.duration) }}</p>
          </span>
        </span>
        <div class="flex gap-3">
          <div
            v-show="isActive(service)"
            class="check-box-eye"
            :class="{ active: isOnline(service) }"
            @click.stop="toggleOnline(service)"
          >
            <span
              class="px-2 pt-0.5 mr-10 text-xs font-bold uppercase border rounded-sm"
              :class="{
                'border-success-500 text-success-500': isOnline(service),
                'border-black text-black dark:text-danger-200 dark:border-danger-200': !isOnline(service)
              }"
            >
              {{ isOnline(service) ? 'Online' : 'Offline' }}
            </span>
          </div>
          <div class="check-box" :class="{ active: isActive(service) }">
            <nuxt-icon name="bold-checked" filled></nuxt-icon>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const emit = defineEmits(["updated"]);
const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const organisationStore = useOrganisationStore();
const activeCategory = ref(0);

const filteredServices = computed(() => {
  if (activeCategory.value == 0) {
    return organisationStore.services;
  } else {
    return organisationStore.services.filter(
      (item) => item.category_id == activeCategory.value
    );
  }
});

const dbCategories = useOrganisationStore().categories;
const categories = [
  { id: 0, name: $t("general.all") },
  ...dbCategories,
];

const form = reactive({
  service_ids: props.user.services.map((item) => ({
    id: item.id,
    is_online: item.is_online || 0, 
  })),
});

const getImage = (service) => {
  return service.image || "/images/placeholder.jpg";
};

const toggleService = (service) => {
  const index = form.service_ids.findIndex((s) => s.id === service.id);
  if (index > -1) {
    form.service_ids.splice(index, 1);
  } else {
    form.service_ids.push({ id: service.id, is_online: 0 }); 
  }
  syncServices();
};

const toggleOnline = (service) => {
  const index = form.service_ids.findIndex((s) => s.id === service.id);
  if (index > -1) {
    form.service_ids[index].is_online = form.service_ids[index].is_online ? 0 : 1;
  } else {
    form.service_ids.push({ id: service.id, is_online: 1 });
  }
  syncServices();
};

const isActive = (service) => {
  return form.service_ids.some((s) => s.id === service.id);
};

const isOnline = (service) => {
  const found = form.service_ids.find((s) => s.id === service.id);
  return found ? found.is_online : false;
};

const syncServices = async () => {
  useValidationStore().clearErrors();
  const url = "sync-services/" + props.user.id;
  try {
    const response = await useApi("PATCH", url, { service_ids: form.service_ids });
    if (response) {
      useSnackbarStore().show(response.message, "success");
      emit("updated");
    }
  } catch (errResponse) {}
};

const populateForm = async () => {
  form.service_ids = props.user.services?.map((item) => ({
    id: item.id,
    is_online: item.is_online || 0,
  })) || [];
};

onMounted(() => {
  populateForm();
});
</script>
