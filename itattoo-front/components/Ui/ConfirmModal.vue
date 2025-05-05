<template>
  <div class="modalWrapper" :class="type">
    <!-- Modal backdrop -->
    <div v-show="visible" class="modal-backdrop" aria-hidden="true"></div>

    <div v-if="visible" class="modal-box" role="dialog" aria-modal="true">
      <div class="card">
        <!-- <div v-click-outside="closeModal" class="modal-card"> -->
        <div class="modal-container">
          <!-- Content -->
          <div class="w-full">
            <!-- Modal header -->
            <div class="mb-2">
              <div class="title">
                <slot name="title">{{ $t('general.confirm') }}?</slot>
              </div>
            </div>
            <!-- Modal content -->
            <div class="text-sm mb-10">
              <div class="space-y-2">
                <slot name="description">
                  Are you sure you want to proceed with this action?
                </slot>
              </div>
            </div>
            <!-- Modal footer -->
            <div class="buttons">
              <button
                class="btn inline-flex justify-center"
                :class="type == 'danger' ? 'danger' : 'primary'"
                @click.stop="agree"
              >
                <slot name="confirm">{{ $t('general.yes') }}</slot>
              </button>
              <button class="btn secondary" @click.stop="cancel">
                <slot name="cancel">{{ $t('general.cancel') }}</slot>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['close']);
const props = defineProps({
  type: {
    type: String,
    default: 'primary',
  },
});
const visible = ref(false);
let resolvePromise = () => {
  return undefined;
};

const closeModal = () => {
  visible.value = false;
  emit('close');
};
const open = () => {
  visible.value = true;
  return new Promise((resolve, reject) => {
    resolvePromise = resolve;
  });
};

const agree = () => {
  resolvePromise(true);
  visible.value = false;
};

const cancel = () => {
  resolvePromise(false);
  closeModal();
};
defineExpose({ open });
</script>
