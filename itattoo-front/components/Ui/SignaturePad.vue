<template>
  <div class="signaturePad">
    <span>
      <vue3-signature :defaultUrl="model" ref="signaturePad"></vue3-signature>
    </span>
    <div class="flex space-x-4">
      <button class="btn btn-sm secondary" @click.prevent="eraseSignature()">
        {{ $t('general.clear') }}
      </button>
      <button class="btn btn-sm primary" @click.prevent="saveSignature()">
        {{ $t('general.save') }}
      </button>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
import Vue3Signature from 'vue3-signature';
const emit = defineEmits(['update:modelValue', 'saved']);
const props = defineProps({
  modelValue: {
    type: [String, null],
    default: null,
  },
});
const signaturePad = ref(null);

const model = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  },
});

const saveSignature = () => {
  if (signaturePad.value.isEmpty()) {
    return useSnackbarStore().show(
      $t('consentSettings.signature_needed'),
      'error'
    );
  }
  model.value = signaturePad.value.save('image/png');
  emit('saved');
};

const eraseSignature = () => {
  signaturePad.value.clear();
  model.value = null;
};
</script>
