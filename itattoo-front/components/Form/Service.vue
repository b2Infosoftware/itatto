<template>
  <form
    class="grow w-full h-full flex flex-col gap-4"
    @submit.prevent="handleSubmit"
  >
    <div class="flex w-full">
      <div class="flex flex-col space-y-4 flex-grow">
        <input-text
          v-model="form.name"
          :label="$t('services.name')"
          name="name"
          class="flex-grow"
          required
        ></input-text>
        <input-text-area
          v-model="form.description"
          :label="$t('services.description')"
          name="description"
        ></input-text-area>
      </div>

      <div class="flex flex-col ml-4 space-y-4">
        <div class="form-group">
          <label for="">Color</label>
          <input v-model="form.color" type="color" class="color h-12" />
        </div>
        <div class="form-group">
          <label>Photo</label>
          <form-photo
            v-model="form.image"
            @changed="updateImage"
            class="w-24"
          ></form-photo>
        </div>
      </div>
    </div>
    <input-select
      v-model="form.category_id"
      :label="$t('services.category')"
      name="category_id"
      :options="categories"
      is-object
      required
    >
    </input-select>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
      <input-text
        v-model.number="form.duration"
        :label="$t('services.duration')"
        name="duration"
        required
      ></input-text>
      <input-text
        v-model.number="form.price"
        :label="$t('services.price')"
        name="price"
        required
      ></input-text>
      <input-text
        v-model.number="form.buffer_time"
        :label="$t('services.buffer_time')"
        name="buffer_time"
        required
      ></input-text>
    </div>

    <div class="flex flex-col gap-4 mt-4 pt-6 border-t border-slate-200/20">
      <ui-switch-button
        name="is_private"
        v-model="form.is_private"
        :info="$t('services.is_private_info')"
        >{{ $t('services.is_private') }}
      </ui-switch-button>
      <ui-switch-button
        name="hide_from_statistics"
        v-model="form.hide_from_statistics"
        :info="$t('services.hide_from_statistics_info')"
        >{{ $t('services.hide_from_statistics') }}
      </ui-switch-button>
      <ui-switch-button
        name="is_hourly_rated"
        v-model="form.is_hourly_rated"
        :info="$t('services.is_hourly_rated_info')"
        >{{ $t('services.is_hourly_rated') }}
      </ui-switch-button>
    </div>

    <div class="inline-flex mt-5 justify-between">
      <nuxt-link :to="{ name: 'services' }" class="btn secondary">
        {{ $t('general.back') }}
      </nuxt-link>
      <input-submit-button class="primary" :loading="saving">
        {{ $t('general.save') }}
      </input-submit-button>
    </div>
  </form>
</template>
<script setup>
const props = defineProps({
  edit: {
    type: Boolean,
    default: false,
  },
});

const { data: categories } = await useApi('GET', 'categories');

const auth = useAuthStore();
const route = useRoute();
const saving = ref(false);
const form = reactive({
  organisation_id: useOrganisationStore().defaultOrganisation.id,
  category_id: null,
  color: '#' + Math.floor(Math.random() * 16777215).toString(16),
  name: '',
  description: '',
  duration: 15,
  price: 0,
  buffer_time: 15,
  is_private: false,
  hide_from_statistics: false,
  is_hourly_rated: true,
});

const handleSubmit = async (redirectBack = true) => {
  useValidationStore().clearErrors();
  const method = props.edit ? 'PATCH' : 'POST';
  const url = props.edit ? 'services/' + route.params.id : 'services';
  saving.value = true;
  try {
    const response = await useApi(method, url, form);
    if (response && redirectBack) {
      navigateTo({ name: 'services' });
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};

const updateImage = async () => {
  if (!props.edit) {
    return;
  }
  await handleSubmit(false);
};

const populateForm = async () => {
  if (props.edit) {
    const { data: service } = await useApi(
      'GET',
      'services/' + route.params.id
    );
    form.category_id = service.category_id;
    form.color = service.color;
    form.image = service.image;
    form.name = service.name;
    form.description = service.description;
    form.duration = service.duration;
    form.price = service.price;
    form.buffer_time = service.buffer_time;
    form.is_private = service.is_private;
    form.hide_from_statistics = service.hide_from_statistics;
    form.is_hourly_rated = service.is_hourly_rated;
  }
};

onMounted(async () => {
  await populateForm();
});
</script>
