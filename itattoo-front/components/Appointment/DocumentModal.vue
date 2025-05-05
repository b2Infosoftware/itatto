<template>
  <div class="modalWrapper wide primary">
    <!-- Modal backdrop -->
    <div class="modal-backdrop" aria-hidden="true"></div>
    <div class="modal-box" role="dialog" aria-modal="true">
      <div class="card flat relative">
        <ui-signature-pad
          v-if="showSignature"
          v-model="signature"
          @saved="showSignature = false"
        ></ui-signature-pad>

        <ui-consent-document
          ref="consentDocument"
          :form="wizard.consentForm"
          :signature="signature"
          :client="wizard.customer"
          with-checkboxes
          v-show="!showSignature"
        ></ui-consent-document>

        <div class="flex justify-between pt-4">
          <div>
            <button
              @click.prevent="showSignature = false"
              v-if="showSignature"
              class="btn danger"
            >
              {{ $t('consentSettings.close_signature') }}
            </button>
            <button
              @click.prevent="showSignature = true"
              v-else
              class="btn success"
            >
              {{ $t('consentSettings.open_signature') }}
            </button>
          </div>
          <div v-if="!showSignature" class="flex space-x-4">
            <button @click.prevent="closeModal" class="btn secondary">
              {{ $t('general.cancel') }}
            </button>
            <button @click.prevent="saveDocument" class="btn primary">
              {{ $t('general.save') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['close']);
const { $t } = useNuxtApp();
const wizard = useWizardStore();
const showSignature = ref(false);
const consentDocument = ref(null);
const signature = ref(null);

const saveDocument = async () => {
  if (!signature.value) {
    return useSnackbarStore().show($t('wizard.signature_needed'), 'error');
  }
  if (!consentDocument.value.approvedAll()) {
    return useSnackbarStore().show(
      $t('wizard.approve_document_terms'),
      'error'
    );
  }
  await wizard.signDocument({
    signature: signature.value,
    customer_id: wizard.form.customer_id,
    project_id: wizard.form.project_id,
    consent_form_id: wizard.consentForm.id,
  });
  emit('close');
};

const closeModal = () => {
  emit('close');
};
</script>
