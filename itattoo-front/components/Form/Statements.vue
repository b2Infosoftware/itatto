<template>
  <div class="form-group">
    <label> {{ $t('consentSettings.statements') }}</label>
    <div class="flex flex-col space-y-4">
      <div v-for="(statement, index) in model" :key="index" class="flex w-full">
        <input-text-area
          v-model="model[index]"
          class="flex-grow"
          :name="'statements[' + index + ']'"
        ></input-text-area>
        <button
          @click.prevent="removeStatement(index)"
          class="btn btn-sm danger ml-4"
        >
          <nuxt-icon name="trash" filled></nuxt-icon>
        </button>
      </div>
    </div>
    <div class="flex justify-center py-4">
      <button @click.prevent="addStatement" class="btn btn-sm primary">
        Add statement
      </button>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
  modelValue: {
    required: true,
    default: [],
  },
});

const model = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  },
});

const addStatement = () => {
  model.value.push('');
};

const removeStatement = (index) => {
  model.value.splice(index, 1);
};
</script>
