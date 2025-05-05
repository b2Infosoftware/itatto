<template>
  <div class="photoForm">
    <div class="thumbnail">
      <img :src="imageSrc" alt="" />
      <div class="actions">
        <button
          v-if="isPlaceholder()"
          class="btn btn-icon success"
          :disabled="disabled"
          @click.prevent="photoInput?.click()"
        >
          <nuxt-icon filled name="upload"></nuxt-icon>
        </button>

        <button
          v-else
          class="btn btn-icon danger"
          :disabled="disabled"
          @click.prevent="resetPhoto()"
        >
          <nuxt-icon filled name="trash"></nuxt-icon>
        </button>
      </div>
    </div>
    <input
      ref="photoInput"
      type="file"
      name="file"
      accept=".jpeg,.png,.jpg,GIF"
      hidden
      :disabled="disabled"
      @input="changeImage($event)"
    />
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits(['update:modelValue', 'changed']);
const props = defineProps({
  modelValue: {
    required: true,
    default: null,
  },
  placeholder: {
    type: String,
    default: '/images/placeholder.jpg',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});
const photoInput = ref<HTMLElement>();

const imageSrc = computed(() => {
  if (props.modelValue) {
    return props.modelValue;
  }
  return props.placeholder;
});

const resetPhoto = () => {
  if (props.disabled) return;
  emit('update:modelValue', null);
  emit('changed');
};

const isPlaceholder = () => {
  return imageSrc.value == props.placeholder;
};

const changeImage = async (file: Event) => {
  if (props.disabled) return;
  const { files } = file.target as HTMLInputElement;

  if (!files || !files.length) {
    return;
  }

  const media = await uploadImage(files[0]);
  emit('update:modelValue', media.full_path);
  emit('changed');
};

const uploadImage = async (file: File) => {
  if (file.size > 2100000) {
    return useSnackbarStore().show(
      'The file is too big. 2MB maximum!',
      'error'
    );
  }
  const formData = new FormData();
  formData.append('attachment', file);
  try {
    const response: any = await useApi('POST', 'media/upload', formData);
    if (response) {
      return response.data;
    }
  } catch (error) {
    return error;
  }
};
</script>