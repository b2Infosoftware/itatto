<template>
  <div class="appointmentSection">
    <div class="overlay"></div>
    <div class="appointment-box">
      <!-- Wizard Navigation -->
      <appointment-steps @next="advance" @prev="goBack"></appointment-steps>

      <!-- The content wrapper for all steps -->
      <div class="step-wrapper">
        <appointment-form v-show="stepIs('appointment')"></appointment-form>

        <appointment-customer-search
          v-show="stepIs('customer')"
          ref="customerSection"
          v-model="wizard.form.customer_id"
        ></appointment-customer-search>

        <appointment-project v-if="stepIs('project')"></appointment-project>

        <appointment-payment-info
          v-if="stepIs('payment')"
        ></appointment-payment-info>
      </div>

      <!-- Action Buttons -->
      <div class="actions">
        <button @click.prevent="hide()" class="btn secondary">
          {{ $t('general.cancel') }}
        </button>

        <input-submit-button
          v-if="stepIs('payment')"
          @click="advance()"
          :loading="saving"
          class="btn primary"
        >
          {{ $t('general.save') }}
        </input-submit-button>
        <button v-else @click="advance()" :loading="saving" class="btn primary">
          {{ $t('general.next') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
const emit = defineEmits(['updated', 'hide']);
const wizard = useWizardStore();
const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
});

const customerSection = ref(null);
const saving = ref(false);

/**
 * Quite self explainatory
 */
const stepIs = (stepKey) => {
  return wizard.activeStep == stepKey;
};

const hide = () => {
  emit('hide');
  wizard.hide();
};

/**
 * Validates the main form
 */
const validateInitialForm = () => {
  let isValid = true;
  const required = [
    'staff_id',
    'location_id',
    'service_id',
    'date',
    'duration',
    'start_time',
  ];
  required.forEach((item) => {
    if (!Boolean(wizard.form[item])) {
      isValid = false;
    }
  });

  return isValid;
};

/**
 * Validates a step before proceeding further
 */
const validate = () => {
  if (wizard.activeStep == 'appointment') {
    const isValid = validateInitialForm();

    if (!isValid) {
      useSnackbarStore().show($t('wizard.appointment_data_needed'), 'error');
      return false;
    }
  }
  if (wizard.activeStep == 'customer') {
    if (!wizard.form.customer_id > 0 && !wizard.newCustomer) {
      useSnackbarStore().show($t('wizard.customer_needed'), 'error');
      return false;
    }
  }
  if (wizard.activeStep == 'project') {
    if (!wizard.form.project_id > 0) {
      useSnackbarStore().show($t('wizard.project_needed'), 'error');
      return false;
    }
  }

  return true;
};

/**
 * Saves the appointment in the database
 */
const handleSubmit = async () => {
  useValidationStore().clearErrors();

  const method = wizard.editMode ? 'PATCH' : 'POST';
  const url = wizard.editMode
    ? 'appointments/' + wizard.editingId
    : 'appointments';
  saving.value = true;
  try {
    const response = await useApi(method, url, wizard.form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      emit('updated', response.data);
    }
  } catch (errResponse) {
    console.log(errResponse);
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};

/**
 * Advances to the next step
 */
const advance = () => {
  if (!validate()) {
    return;
  }
  // last step is active so we just need to submit
  if (wizard.activeStep == 'payment') {
    handleSubmit();

    return;
  }

  // Creates a customer
  if (wizard.activeStep == 'customer' && wizard.newCustomer) {
    customerSection.value.createCustomer();
    return;
  }

  // Proceeds further
  const filteredSteps = wizard.hasProject
    ? wizard.steps
    : wizard.steps.filter((item) => item.key != 'project');
  const keys = filteredSteps.map((item) => item.key);
  const currentIndex = keys.indexOf(wizard.activeStep);
  wizard.activeStep = keys[currentIndex + 1];
};

const goBack = () => {
  if (wizard.activeStep == 'appointment') {
    return false;
  }
  const filteredSteps = wizard.hasProject
    ? wizard.steps
    : wizard.steps.filter((item) => item.key != 'project');
  const keys = filteredSteps.map((item) => item.key);
  const currentIndex = keys.indexOf(wizard.activeStep);
  wizard.activeStep = keys[currentIndex - 1];
};
</script>
