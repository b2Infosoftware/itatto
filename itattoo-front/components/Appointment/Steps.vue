<template>
  <div class="steps-overview">
    <div
      v-for="(step, index) in wizard.steps"
      :key="step.key"
      :class="{
        active: wizard.activeStep == step.key,
        hide: step.key == 'project' && !wizard.hasProject,
      }"
    >
      <nuxt-icon v-if="index > 0" filled name="chevron-right"></nuxt-icon>
      <button @click.prevent="goTo(step)" class="cursor-pointer">
        <i>
          <nuxt-icon :name="step.icon" filled></nuxt-icon>
        </i>
        <div>
          <span>{{ getName(step) }}</span>
          <span class="desc">
            {{ getDescription(step) }}
          </span>
        </div>
      </button>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['next', 'prev']);
const { $t } = useNuxtApp();
const wizard = useWizardStore();

/**
 * Gets the name for a step
 */
const getName = (step) => {
  return $t('steps.' + step.key);
};

/**
 * Gets the description for a step
 */
const getDescription = (step) => {
  return $t('steps.' + step.key + '_description');
};

const goTo = (step) => {
  if (wizard.editMode) {
    const desiredStep = wizard.steps.find((item) => item.key == step.key);
    wizard.activeStep = desiredStep.key;
    return;
  }
  const activeOrder = wizard.steps.find(
    (item) => item.key == wizard.activeStep
  ).order;
  if (activeOrder < step.order) {
    emit('next');
  }
  if (activeOrder > step.order) {
    emit('prev');
  }
};
</script>
