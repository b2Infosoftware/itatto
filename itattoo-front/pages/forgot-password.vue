<template>
  <div class="w-full md:mt-0 sm:max-w-md xl:p-0">
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
      <ui-alert v-if="passwordSent" type="success" class="text-xs">
        <nuxt-icon
          name="check-circle"
          class="text-5xl flex justify-center my-5"
        ></nuxt-icon>
        <p class="mb-3">
          A password-reset link has been sent to your email address.
        </p>
        <p class="mb-5">
          If you are not receiving the email within the next 3-5 minutes, please
          check the "Spam" folder in your email as well
        </p>
      </ui-alert>
      <h1 class="text-xl font-bold leading-tight tracking-tight md:text-2xl">
        Forgot your password?
      </h1>

      <form 
      @keydown.enter="handleSubmit" class="space-y-4 md:space-y-6" @submit.prevent="handleSubmit">
        <ui-radio-group
          v-model="form.for"
          name="for"
          label="I am a..."
          :options="['staff', 'customer']"
        ></ui-radio-group>
        <span class="py-5 flex">
          <i class="border-t-2 border-gray-400 w-full"></i>
        </span>
        <input-text
          v-model="form.email"
          label="Your email address"
          name="email"
          type="email"
          placeholder="johndoe@example.com"
          required
        ></input-text>

        <input-submit-button
          class="primary w-full justify-center"
          :loading="loading"
          >Send me a reset link</input-submit-button
        >
        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
          Do you remember your password?
          <nuxt-link :to="{ name: 'login' }" class="font-medium link"
            >Sign in</nuxt-link
          >
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'authentication',
  middleware: 'guest',
});

let loading = ref(false);
let passwordSent = ref(false);
let form = reactive({
  email: '',
  for: 'staff',
});

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  loading.value = true;
  try {
    const response = await useApi('POST', 'forgot-password', form);
    if (response) {
      passwordSent = true;
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  loading.value = false;
};
</script>
