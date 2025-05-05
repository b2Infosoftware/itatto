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

      <h1 class="text-xl font-bold leading-tight tracking-tight md:text-2xl">
        {{ $t('auth.create_account') }}
      </h1>

      <form class="space-y-4 md:space-y-6" @submit.prevent="handleSubmit">
        <input-text
          v-model="form.first_name"
          :label="$t('auth.first_name')"
          name="first_name"
          placeholder="john"
        ></input-text>

        <input-text
          v-model="form.last_name"
          :label="$t('auth.last_name')"
          name="last_name"
          placeholder="Doe"
        ></input-text>

        <input-text
          v-model="form.email"
          :label="$t('auth.email')"
          name="email"
          type="email"
          placeholder="johndoe@example.com"
        ></input-text>

        <input-text
          v-model="form.password"
          :label="$t('auth.password')"
          name="password"
          type="password"
          placeholder="••••••••"
          is-password
        ></input-text>
        <input-text
          v-model="form.password_confirmation"
          :label="$t('auth.repeat_password')"
          name="password_confirmation"
          type="password"
          placeholder="••••••••"
          is-password
        ></input-text>

        <input-text
          v-model="form.organisation_name"
          :label="$t('auth.business_name')"
          name="organisation_name"
          placeholder="ABC Tattos Inc."
        ></input-text>

        <input-select
          v-model="form.country_id"
          label="Country"
          :options="useSettingsStore().countries"
          name="country_id"
          is-object
          required
        ></input-select>

        <div class="flex flex-col mt-2 space-y-4">
          <div class="flex items-center space-x-2">
            <input
              type="checkbox"
              id="accept_privacy_terms"
              v-model="form.accept_privacy_terms"
              required
              class="w-4 h-4 cursor-pointer"
            />
            <label for="accept_privacy_terms" class="text-xs">
              {{ $t('auth.accept_privacy_terms') }} 
              <nuxt-link to="https://www.iubenda.com/privacy-policy/74158345" target='_blank' class="text-xs text-blue-500">{{ $t('auth.privacy_link') }}</nuxt-link>
            </label>
          </div>

          <div class="flex items-center space-x-2">
            <input
              type="checkbox"
              id="accept_terms_conditions"
              v-model="form.accept_terms_conditions"
              required
              class="w-4 h-4 cursor-pointer"
            />
            <label for="accept_terms_conditions" class="text-xs">
              {{ $t('auth.accept_terms_conditions') }} 
              <nuxt-link to="https://www.iubenda.com/terms-and-conditions/74158345" target='_blank' class="text-xs text-blue-500">{{ $t('auth.terms_link') }}</nuxt-link>
            </label>
          </div>
        </div>

        <input-submit-button
          class="justify-center w-full pb-4 primary"
          :loading="loading"
          >{{ $t('auth.create_account') }}</input-submit-button
        >

        <ui-button @click="handleClickRegisterGoogle">
          <nuxt-icon name="google" class="text-2xl" filled></nuxt-icon>
          Sign up with Google
        </ui-button>

        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
          {{ $t('auth.already_member') }}?
          <nuxt-link :to="{ name: 'login' }" class="font-medium link">{{
            $t('auth.login_here')
          }}</nuxt-link>
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
useHead({
  title: 'Register',
});

const auth = useAuthStore();
let loading = ref(false);
let invalidCredentials = ref(false);
let form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  country_id: '',
  password_confirmation: '',
  organisation_name: '',
  tz: 'GMT+00',
  accept_privacy_terms: false,
  accept_terms_conditions: false,
});

const getTimeZone = () => {
  var offset = new Date().getTimezoneOffset(),
    o = Math.abs(offset);
  return (
    (offset < 0 ? '+' : '-') +
    ('00' + Math.floor(o / 60)).slice(-2) +
    ':' +
    ('00' + (o % 60)).slice(-2)
  );
};

const tzDiff = getTimeZone();

form.tz = 'GMT' + tzDiff;

const handleSubmit = async (e) => {
  useValidationStore().clearErrors();
  loading.value = true;
  invalidCredentials.value = false;
  try {
    const response = await useApi('POST', 'register', form);
    auth.loginWithToken(response.accessToken);
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  loading.value = false;
};

onMounted(() => {
  fetch('https://ip2c.org/s')
    .then((response) => response.text())
    .then((response) => {
      const result = (response || '').toString();

      if (!result || result[0] !== '1') {
        return null;
      }
      const cNames = result.split(';');
      if (!cNames.length) {
        return;
      }
      const name = cNames[cNames.length - 1];
      const c = useSettingsStore().countries.find(
        (item) => item.name.toLowerCase() == name.toLowerCase()
      );
      if (c) {
        form.country_id = c.id;
      }
    });
});

const handleClickRegisterGoogle = async () => {
  try {
    const apiUrl = `google/redirect?status=register`;
    const res = await useApi("GET", apiUrl);

    console.log("API Response:", res);

    if (res?.redirect_url) {
      window.location.replace(res.redirect_url);
    } else {
      console.error("Invalid response from API:", res);
    }
  } catch (error) {
    console.error("Error during Google registration:", error);
  }
};
</script>
