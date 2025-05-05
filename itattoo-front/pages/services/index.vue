<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between space-y-4">
      <div>
        <h1 class="text-2xl">Services</h1>
        <p class="opacity-60 leading-tight">
          Manage categories and services which your company provides
        </p>
      </div>
      <nuxt-link
        v-if="useCan('create', 'services')"
        :to="{ name: 'services-create' }"
        class="btn primary md:h-10 justify-center sm:max-w-60"
        >Create Service</nuxt-link
      >
    </div>

    <!-- CATEGORY FILTER -->
    <div class="form-group">
      <label class="mt-8 pb-2 block">Categories</label>
    </div>
    <div class="serviceCategories">
      <button
        @click.prevent="setCategory(0)"
        class="btn btn-sm"
        :class="activeCategory == 0 ? 'primary' : 'tonal slate'"
      >
        {{ $t('general.all') }}
      </button>
      <button
        @click.prevent="setCategory(item.id)"
        v-for="item in categories"
        :key="item.id"
        class="btn btn-sm"
        :class="item.id == activeCategory ? 'primary' : 'tonal primary'"
      >
        {{ item.name }}
      </button>
      <button
        v-if="useCan('create', 'services')"
        class="btn success btn-icon tonal btn-sm"
        @click.prevent="showCategoryForm = true"
      >
        <nuxt-icon filled name="plus" class="text-xl"></nuxt-icon>
      </button>
    </div>
    <!-- <div id="servicesSection" class="servicesList">
      <ui-service-card
        v-for="item in filteredServices"
        :key="item.id"
        :service="item"
        @delete="deleteService(item)"
      ></ui-service-card>
    </div> -->

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>{{ $t('services.name') }}</th>
            <th>{{ $t('services.category') }}</th>
            <th>{{ $t('services.price') }}</th>
            <th>{{ $t('services.duration') }}</th>
            <th>{{ $t('services.buffer_time') }}</th>
            <th>{{ $t('general.actions') }}</th>
          </tr>
        </thead>
        <tbody id="servicesSection">
          <tr v-for="item in filteredServices" :key="item.id">
            <td class="capitalize">
              <span class="th">{{ $t('services.name') }}</span>
              <span class="inline-flex items-center">
                <i
                  class="w-4 h-4 block rounded-md mr-2"
                  :style="{
                    backgroundColor: item.color,
                  }"
                ></i
                >{{ item.name }}
              </span>
            </td>
            <td>
              <span class="th">{{ $t('services.category') }}</span>
              <span>
                {{ categoryName(item) }}
              </span>
            </td>
            <td>
              <span class="th">{{ $t('services.price') }}</span>
              <span>
                {{ item.price }}
              </span>
            </td>
            <td>
              <span class="th">{{ $t('services.duration') }}</span>
              <span>
                {{ useHelpers().niceTime(item.duration) }}
              </span>
            </td>
            <td>
              <span class="th">{{ $t('services.buffer_time') }}</span>
              <span>
                {{ useHelpers().niceTime(item.buffer_time) }}
              </span>
            </td>

            <td class="actions">
              <span>
                <nuxt-link
                  v-if="useCan('edit', 'services')"
                  :to="{ name: 'services-id', params: { id: item.id } }"
                  class="btn btn-sm btn-icon"
                >
                  <nuxt-icon filled name="pencil"></nuxt-icon>
                </nuxt-link>
                <button
                  v-if="useCan('delete', 'services')"
                  @click.prevent="deleteService(item)"
                  class="btn btn-sm btn-icon mx-4"
                >
                  <nuxt-icon filled name="trash"></nuxt-icon>
                </button>
                <button class="move btn bnt-icon btn-sm">
                  <nuxt-icon name="move-vertically" filled></nuxt-icon>
                </button>
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="fetchedData && !filteredServices.length" class="card">
      <ui-no-data>
        <template #text>{{ $t('services.no_services') }}</template>
        <template #cta>
          <div class="flex justify-center gap-x-4">
            <button class="btn danger" @click.prevent="deleteCategory">
              {{ $t('services.delete_category') }}
            </button>
          </div>
        </template>
      </ui-no-data>
    </div>

    <!-- CONFIRM DELETION -->
    <ui-confirm-modal type="danger" ref="confirmModal">
      <template #confirm>{{ $t('general.yes_delete') }}</template>
    </ui-confirm-modal>

    <!-- ADD CATEGORY FORM -->
    <ui-modal @close="showCategoryForm = false" :visible="showCategoryForm">
      <template #title>{{ $t('services.add_category') }}</template>
      <template #description>{{
        $t('services.category_description')
      }}</template>
      <template #content>
        <form
          class="grow w-full h-full flex flex-col gap-4 text-left"
          @submit.prevent="saveCategory"
        >
          <input-text
            v-model="categoryForm.name"
            :label="$t('services.category_name')"
            name="name"
            required
          ></input-text>

          <div class="inline-flex mt-5 justify-between">
            <button @click.prevent="hideCategoryForm" class="btn secondary">
              {{ $t('general.cancel') }}
            </button>
            <input-submit-button class="primary" :loading="savingCategory">
              {{ $t('general.save') }}
            </input-submit-button>
          </div>
        </form>
      </template>
    </ui-modal>
  </div>
</template>

<script setup>
import { useSortable } from '@vueuse/integrations/useSortable';
import { moveArrayElement } from '@vueuse/integrations/useSortable';
const { $t } = useNuxtApp();
const categories = ref([]);
const confirmModal = ref(null);
const showCategoryForm = ref(false);
const savingCategory = ref(false);
const activeCategory = ref(0);
const categoryForm = reactive({ name: '' });
const fetchedData = ref(false);
const filteredServices = ref([]);

const filterServices = () => {
  if (activeCategory.value == 0) {
    filteredServices.value = categories.value
      .map((item) => item.services)
      .flat()
      .sort((a, b) => parseInt(a.position) - parseInt(b.position));
  } else {
    filteredServices.value = categories.value.find(
      (item) => item.id == activeCategory.value
    ).services;
  }
  useSortable('#servicesSection', filteredServices, {
    onUpdate: (e) => {
      if (!useCan('edit', 'services')) {
        return useSnackbarStore().show($t('general.no_permissions'), 'warning');
      }
      moveArrayElement(filteredServices.value, e.oldIndex, e.newIndex);
      nextTick(() => {
        setNewOrder();
      });
    },
    handle: '.move',
  });

  return;
};

const setNewOrder = async () => {
  const form = {
    ids: filteredServices.value.map((i) => i.id),
    order: filteredServices.value.map((i, index) => index),
  };

  const response = await useApi('POST', 'services/reorder', form);
  if (response) {
    useSnackbarStore().show(response.message, 'success');
  }
};

const setCategory = (catId) => {
  activeCategory.value = catId;
  filterServices();
};

const categoryName = (service) => {
  const cat = categories.value.find((item) => item.id == service.category_id);
  return cat?.name || 'N/A';
};

const fetchCategories = async () => {
  const response = await useApi('GET', 'categories');
  categories.value = response.data;
  filterServices();
};

const deleteService = async (item) => {
  const confirmed = await confirmModal.value.open();
  if (Boolean(confirmed)) {
    const response = await useApi('DELETE', 'services/' + item.id);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      fetchCategories();
    }
  }
};

const deleteCategory = async (item) => {
  if (!useCan('delete', 'services')) {
    return useSnackbarStore().show($t('general.no_permissions'), 'warning');
  }
  const response = await useApi('DELETE', 'categories/' + activeCategory.value);
  if (response) {
    useSnackbarStore().show(response.message, 'success');
    activeCategory.value = 0;
    fetchCategories();
  }
};

const hideCategoryForm = () => {
  showCategoryForm.value = false;
  useValidationStore().clearErrors();
};

const saveCategory = async () => {
  useValidationStore().clearErrors();

  savingCategory.value = true;
  try {
    const response = await useApi('POST', 'categories', categoryForm);
    if (response) {
      await fetchCategories();
      showCategoryForm.value = false;
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  savingCategory.value = false;
};

onMounted(async () => {
  await fetchCategories();
  fetchedData.value = true;
});
</script>
