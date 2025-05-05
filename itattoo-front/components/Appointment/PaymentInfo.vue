<template>
  <div class="w-full flex flex-col space-y-4 max-w-screen-sm mx-auto">
    <input-text
      v-model="wizard.form.paid_by"
      label="Paid by"
      name="paid_by"
    ></input-text>
    <input-text-area
      v-model="wizard.form.note"
      optional
      name="notes"
      label="Note"
    ></input-text-area>
    <input-text
      v-model="wizard.form.deposit"
      optional
      type="number"
      name="deposit"
      label="Deposit"
    ></input-text>
  </div>
</template>

<script setup>
const wizard = useWizardStore();

onMounted(() => {
  if (!wizard.form.paid_by) {
    wizard.form.paid_by = wizard.customer.full_name;
  }
});
</script>
