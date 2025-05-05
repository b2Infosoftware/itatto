<template>
  <div class="w-full flex justify-center items-center pb-4 max-w-96 mx-auto">
    <!-- ADD PROJECT -->
    <form class="w-full" v-if="newProject" @submit.prevent="saveProject">
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
            :options="categories"
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
      <!-- FORM ACTION BUTTONS -->
      <div class="inline-flex mt-8 justify-between w-full">
        <button @click.prevent="toggleForm(false)" class="btn secondary">
          {{ $t('general.cancel') }}
        </button>
        <input-submit-button class="primary" :loading="savingProject">
          {{ $t('general.save') }}
        </input-submit-button>
      </div>
    </form>

    <!-- NO PROJECT UI -->
    <ui-no-data v-else-if="!wizard.projects.length">
      <template #text>
        <div class="text-sm">
          {{ $t('projects.no_projects_for_customer') }}
        </div>
      </template>
      <template #cta>
        <button @click.prevent="toggleForm(true)" class="btn primary">
          <span> {{ $t('projects.add_new') }} </span>
        </button>
      </template>
    </ui-no-data>

    <!-- PICK PROJECT -->
    <div v-else class="w-full">
      <div class="form-group">
        <input-select
          label="Pick a project"
          name="category_id"
          v-model="wizard.form.project_id"
          :options="wizard.projects"
          @change="setWizardProject"
          is-object
        ></input-select>

        <appointment-project-document></appointment-project-document>

        <div class="divider border-slate-200/20">or</div>
        <div class="text-center">
          <button @click.prevent="toggleForm(true)" class="btn primary">
            <span> {{ $t('projects.add_new') }} </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
const emit = defineEmits(['update:modelValue']);
const wizard = useWizardStore();
const newProject = ref(false);
const savingProject = ref(null);
const { data: categories } = await useApi('GET', 'categories');

const form = reactive({
  name: '',
  description: '',
  category_id: '',
  customer_id: wizard.form.customer_id,
  staff_id: wizard.form.staff_id,
});

const prepopulateForm = () => {
  form.category_id = wizard.form.category_id;
  form.customer_id = wizard.form.customer_id;
  form.staff_id = wizard.form.staff_id;
};

const setWizardProject = async (project) => {
  if (!project) {
    wizard.project = null;
    return;
  }
  wizard.project = project;
  await wizard.fetchConsentForm();
};

const toggleForm = (visible) => {
  if (visible == true) {
    prepopulateForm();
  }
  newProject.value = visible;
};

const saveProject = async () => {
  useValidationStore().clearErrors();

  // const method = wizard.editMode ? 'PATCH' : 'POST';
  // const url = wizard.editMode ? 'projects/' + wizard.project.id : 'projects';
  savingProject.value = true;
  try {
    const response = await useApi('POST', 'projects', form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      wizard.form.project_id = response.data.id;
      await Promise.all([
        setWizardProject(response.data),
        wizard.fetchProjects(),
      ]);
      toggleForm(false);
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  savingProject.value = false;
};

onMounted(() => {
  wizard.fetchProjects();
});
</script>
