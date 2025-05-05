<template>
  <div class="card">
    <!-- ADD PROJECT -->
    <div v-if="showModal" class="modalWrapper primary">
      <div class="modal-backdrop" aria-hidden="true"></div>
      <div class="modal-box">
        <div class="card">
          <button
            @click.prevent="showModal = false"
            class="btn btn-icon close-btn mt-2 mr-2"
          >
            <nuxt-icon filled name="x-mark" class="text-lg"></nuxt-icon>
          </button>
          <span class="title mb-6 text-xl font-semibold">
            {{ $t('projects.add_new') }}
          </span>
          <form-project
            :edit="editMode"
            :customer="customerStore.item"
            :project="editableProject"
            @cancel="showModal = false"
          ></form-project>
        </div>
      </div>
    </div>

    <ui-no-data v-if="!projects.length">
      <template #text>{{ $t('customers.no_projects') }}</template>
    </ui-no-data>

    <!-- Projects list -->
    <div v-else>
      <span
        v-for="item in projects"
        :key="item.id"
        @click.prevent="editProject(item)"
        class="appointmentCard"
      >
        <nuxt-icon name="brush" filled></nuxt-icon>
        <div>
          <div class="bold">
            <i>{{ item.name }}</i>
          </div>
          <div class="details">
            <i>{{ item.category_name }}</i>
            <i>{{
              item.completed_at ? $t('projects.completed') : $t('projects.open')
            }}</i>
            <i>{{
              item.signed ? $t('projects.signed') : $t('projects.not_signed')
            }}</i>
          </div>
        </div>
      </span>
    </div>

    <div class="flex justify-center mt-4 pt-4">
      <button @click.prevent="showModal = true" class="btn success">
        {{ $t('customers.add_project') }}
      </button>
    </div>
  </div>
</template>
<script setup>
const emit = defineEmits(['updated']);

const customerStore = useCustomerStore();
const projects = customerStore.projects;
const showModal = ref(false);
const editableProject = ref(null);

const editProject = async (item) => {
  editableProject.value = item;
  showModal.value = true;
};

const editMode = computed(() => {
  return Boolean(editableProject.value?.id);
});

watch(
  () => showModal.value,
  (val) => {
    if (!val) {
      editableProject.value = null;
    }
  }
);
</script>
