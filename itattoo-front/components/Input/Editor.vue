<template>
  <input-base
    v-bind="{ name: name, label: label, optional: optional, error: error }"
  >
    <div class="editor-buttons" v-if="editor">
      <button
        @click="editor.chain().focus().toggleBold().run()"
        :disabled="!editor.can().chain().focus().toggleBold().run()"
        :class="{ 'is-active': editor.isActive('bold') }"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="svg-icon"
          style="
            width: 1em;
            height: 1em;
            vertical-align: middle;
            fill: currentColor;
            overflow: hidden;
          "
          viewBox="0 0 1024 1024"
          version="1.1"
        >
          <path
            d="M576 661.333H426.667v-128H576c35.413 0 64 28.587 64 64 0 35.414-28.587 64-64 64m-149.333-384h128c35.413 0 64 28.587 64 64 0 35.414-28.587 64-64 64h-128m238.933 55.04C706.987 431.36 736 384 736 341.333c0-96.426-74.667-170.666-170.667-170.666H298.667V768H599.04c89.6 0 158.293-72.533 158.293-161.707 0-64.853-36.693-120.32-91.733-145.92z"
          />
        </svg>
      </button>
      <button
        @click="editor.chain().focus().toggleItalic().run()"
        :disabled="!editor.can().chain().focus().toggleItalic().run()"
        :class="{ 'is-active': editor.isActive('italic') }"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="svg-icon"
          style="
            width: 1em;
            height: 1em;
            vertical-align: middle;
            fill: currentColor;
            overflow: hidden;
          "
          viewBox="0 0 1024 1024"
          version="1.1"
        >
          <path
            d="M426.667 170.667v128h94.293L375.04 640H256v128h341.333V640H503.04l145.92-341.333H768v-128H426.667z"
          />
        </svg>
      </button>

      <button
        @click="editor.chain().focus().toggleBulletList().run()"
        :class="{ 'is-active': editor.isActive('bulletList') }"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="svg-icon"
          style="
            width: 1em;
            height: 1em;
            vertical-align: middle;
            fill: currentColor;
            overflow: hidden;
          "
          viewBox="0 0 1024 1024"
          version="1.1"
        >
          <path
            d="M298.667 213.333v85.334H896v-85.334M298.667 554.667H896v-85.334H298.667m0 341.334H896v-85.334H298.667m-128-14.08c-31.574 0-56.747 25.6-56.747 56.747s25.6 56.747 56.747 56.747c31.146 0 56.746-25.6 56.746-56.747s-25.173-56.747-56.746-56.747m0-519.253c-35.414 0-64 28.587-64 64s28.586 64 64 64c35.413 0 64-28.587 64-64s-28.587-64-64-64m0 256c-35.414 0-64 28.587-64 64s28.586 64 64 64c35.413 0 64-28.587 64-64s-28.587-64-64-64z"
          />
        </svg>
      </button>

      <button @click="editor.chain().focus().setHorizontalRule().run()">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="svg-icon"
          style="
            width: 1em;
            height: 1em;
            vertical-align: middle;
            fill: currentColor;
            overflow: hidden;
          "
          viewBox="0 0 1024 1024"
          version="1.1"
        >
          <path
            d="M128 640v-85.333h85.333V640H128m0-170.667V384h85.333v85.333H128M298.667 640v-85.333H384V640h-85.333m0-170.667V384H384v85.333h-85.333M469.333 640v-85.333h85.334V640h-85.334m0-170.667V384h85.334v85.333h-85.334M640 640v-85.333h85.333V640H640m0-170.667V384h85.333v85.333H640M810.667 640v-85.333H896V640h-85.333m0-170.667V384H896v85.333h-85.333z"
          />
        </svg>
      </button>
    </div>
    <editor-content :editor="editor"></editor-content>
  </input-base>
</template>

<script setup>
import { Editor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
const apiErrors = useValidationStore();
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {
    required: true,
    default: '',
  },
  rows: {
    type: Number,
    default: 4,
  },
  optional: {
    type: Boolean,
    default: false,
  },
  name: {
    type: String,
    required: true,
  },
  placeholder: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  autocomplete: {
    type: String,
    default: 'off',
  },
});

const editor = ref(null);

watch(
  () => props.modelValue,
  (val) => {
    // HTML
    const isSame = editor.value.getHTML() === val;

    if (isSame) {
      return;
    }

    editor.value.commands.setContent(val, false);
  }
);

const errorMessage = computed(() => {
  if (props.error.length) {
    return props.error;
  }
  return apiErrors.errorsList[props.name]
    ? apiErrors.errorsList[props.name][0]
    : null;
});

onMounted(() => {
  editor.value = new Editor({
    extensions: [StarterKit],
    content: props.modelValue,
    onUpdate: () => {
      // HTML
      emit('update:modelValue', editor.value.getHTML());
    },
  });
});

onBeforeUnmount(() => {
  editor.destroy;
});
</script>
