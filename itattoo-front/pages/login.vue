<template>
  <div class="w-full md:mt-0 sm:max-w-md xl:p-0">
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
      <div class="w-full flex justify-center md:hidden">
        <img
          class="w-56 mb-14 h-auto block dark:hidden"
          src="/images/logo/black.svg"
          alt="iTattoo logo"
        />
        <img
          class="w-56 mb-14 h-auto hidden dark:block"
          src="/images/logo/big-white.svg"
          alt="iTattoo logo"
        />
      </div>
      <ui-alert v-if="auth.errorMessage" type="warning" class="my-5">
        <span class="py-2 block text-sm">
          {{ auth.errorMessage }}
        </span>
      </ui-alert>
      <h1 class="text-xl font-bold leading-tight tracking-tight md:text-2xl">
        {{ $t('auth.title') }}
      </h1>
      <form
        @keydown.enter="handleSubmit"
        class="space-y-4 md:space-y-6"
        @submit.prevent="handleSubmit"
      >
        <ui-radio-group
          v-model="form.login_as"
          name="login_as"
          :label="$t('auth.i_am_a')"
          :options="[$t('auth.staff'), $t('auth.customer')]"
        ></ui-radio-group>
        <span class="py-5 flex">
          <i class="border-t-2 border-gray-400 w-full"></i>
        </span>
        <input-text
          v-model="form.email"
          :label="$t('auth.your_email')"
          name="email"
          placeholder="johndoe@example.com"
          required
        ></input-text>
        <input-text
          v-model="form.password"
          :label="$t('auth.password')"
          name="password"
          type="password"
          placeholder="••••••••"
          required
          is-password
        ></input-text>
        <div class="flex items-center justify-between">
          <div></div>
          <nuxt-link
            :to="{ name: 'forgot-password' }"
            class="text-sm font-medium link"
            >{{ $t('auth.forgot_password') }}?</nuxt-link
          >
        </div>
        <input-submit-button
          class="primary w-full justify-center"
          :loading="loading"
          >{{ $t('auth.sign_in') }}</input-submit-button
        >
        <ui-button @click="handleClickLoginGoogle" v-if="form.login_as !== 'customer'">
          <nuxt-icon name="google" class="text-2xl" filled></nuxt-icon>
          Sign in with Google
        </ui-button>
              
        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
          {{ $t('auth.no_account') }}?
          <nuxt-link :to="{ name: 'register' }" class="font-semibold link">{{
            $t('auth.sign_up')
          }}</nuxt-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  layout: 'authentication',
  middleware: 'guest',
});
useHead({
  title: $t('auth.login'),
});
const auth = useAuthStore();
let loading = ref(false);
let form = reactive({
  email: '',
  password: '',
  login_as: 'staff',
});

const handleSubmit = async () => {
  loading.value = true;
  try {
    const response = await useApi('POST', 'login', form);
    if (response) {
      useCookie('auth_token').value = response.accessToken;
      await new Promise((r) => setTimeout(r, 1000));
      await auth.fetchUser();
      await Promise.all([
        useSettingsStore().getSettings(),
        useOrganisationStore().fetchData(),
      ]);
      if (useOrganisationStore().organisations.length > 1) {
        navigateTo({ name: 'pick-organisation' });
      } else if (useCan('view', 'dashboard')) {
        navigateTo({ name: 'index' });
      } else {
        navigateTo({ name: 'calendar' });
      }
    }
  } catch (error) {
    console.log(error);
  }
  loading.value = false;
};

const handleClickLoginGoogle = async () => {
  try {
    if (!form.login_as) {
      console.error("Error: login_as is required");
      return;
    }

    const redirectUrl = encodeURIComponent(`${window.location.origin}/auth/callback`);
    const apiUrl = `google/redirect?status=login&login_as=${form.login_as}`;
    
    const res = await useApi("GET", apiUrl);

    if (res) {
      window.location.replace(res.redirect_url);
    } else {
      console.error("Invalid response from API:", res);
    }
  } catch (error) {
    console.error("Error during Google login:", error);
  }
};


</script>
