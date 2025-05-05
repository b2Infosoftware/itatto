<template>
  <div
    v-if="wizard.form.project_id"
    class="py-4 flex flex-col items-center justify-center space-y-4"
  >
    <span>{{ $t('consentSettings.consent_form') }}</span>

    <ui-alert v-if="wizard.showNoConsentFormWarning" type="warning">
      <div class="text-sm">
        <p class="mb-4">
          {{ $t('wizard.no_forms') }}
        </p>
        <p>
          {{ $t('wizard.add_forms_before_projects') }}
        </p>
      </div>
    </ui-alert>
    <div v-else class="flex justify-around space-x-4">
      <button
        @click.prevent="showDocument = !showDocument"
        class="btn secondary btn-sm"
      >
        {{ $t('wizard.new_sign_form') }}
      </button>
      <a
        target="_blank"
        v-if="wizard.project.signed_document"
        class="btn secondary btn-sm"
        :href="wizard.project.signed_document.path"
      >
        <nuxt-icon name="document-check" filled class="mr-1"></nuxt-icon
        >{{ $t('wizard.view_signed_form') }}
      </a>
    </div>
    <appointment-document-modal
      v-if="showDocument"
      @close="showDocument = false"
    ></appointment-document-modal>
  </div>
</template>

<script setup>
const wizard = useWizardStore();
const showDocument = ref(false);
</script>
