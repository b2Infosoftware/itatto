<template>
  <div class="w-full md:mt-0 sm:max-w-md xl:p-0">
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
      <h1
        class="text-xl font-bold leading-tight tracking-tight text-slate-900 md:text-2xl"
      >
        Reset Password
      </h1>

      <form
        @keydown.enter="handleSubmit"
        class="space-y-4 md:space-y-6"
        @submit.prevent="handleSubmit"
      >
        <ui-radio-group
          v-model="form.for"
          name="login_as"
          :label="$t('auth.i_am_a')"
          :options="[$t('auth.staff'), $t('auth.customer')]"
        ></ui-radio-group>
        <span class="py-5 flex">
          <i class="border-t-2 border-gray-400 w-full"></i>
        </span>
        <input-text
          v-model="form.password"
          label="Password"
          name="password"
          type="password"
          placeholder="••••••••"
          is-password
        ></input-text>
        <input-text
          v-model="form.password_confirmation"
          label="Repeat password"
          name="password_confirmation"
          type="password"
          placeholder="••••••••"
          is-password
        ></input-text>

        <input-submit-button
          class="primary w-full justify-center pb-4"
          :loading="loading"
          >Reset my password</input-submit-button
        >
      </form>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'authentication',
  middleware: 'guest',
});

const route = useRoute();

let loading = ref(false);
let form = reactive({
  password: '',
  password_confirmation: '',
  token: '',
  email: '',
  for: 'staff',
});

onMounted(() => {
  form.email = route.query.email;
  form.token = route.query.token;
});

const handleSubmit = async (e) => {
  useValidationStore().clearErrors();
  loading.value = true;
  try {
    const response = await useApi('POST', 'reset-password', form);
    if (response) {
      useSnackbarStore().show('Your password has been updated.', 'success');
      navigateTo({ name: 'login' });
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
    useSnackbarStore().show(errResponse.message, 'error');    
  }

  loading.value = false;
};
</script>
