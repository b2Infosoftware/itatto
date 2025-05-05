<template>
  <div class="w-full md:mt-0 sm:max-w-md xl:p-0">
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
      <h1 class="text-xl font-bold leading-tight tracking-tight md:text-2xl">
        {{ $t('auth.account_verification') }}
      </h1>
      <!-- If link is broken -->
      <transition name="fade">
        <ui-alert v-if="verificationError" type="danger" class="text-xs">
          <p>{{ verificationError }}</p>
        </ui-alert>
      </transition>

      <!-- If requested a new one -->
      <transition name="fade">
        <ui-alert v-if="resentLink" type="success" class="my-5">
          <p class="mb-3">
            {{ $t('auth.fresh_link_in_email') }}
          </p>
          <p>
            {{ $t('auth.wait_5_mins_for_email') }}
          </p>
        </ui-alert>
      </transition>
      <!-- If just landed here -->
      <transition name="fade">
        <ui-alert v-if="justRegistered" type="warning" size="sm" class="my-5">
          <p>
            {{ $t('auth.account_created') }}
          </p>
          <p>
            {{ $t('auth.check_inbox') }}
          </p>
        </ui-alert>
      </transition>

      <form class="space-y-4 md:space-y-6" @submit.prevent="resendEmail">
        <input-submit-button
          class="primary w-full justify-center"
          :loading="resendingEmail"
        >
          {{ $t('auth.send_me_new_email') }}</input-submit-button
        >
      </form>
    </div>
  </div>
</template>

<script setup>
const auth = useAuthStore();
definePageMeta({
  layout: 'authentication',
  middleware: () => {
    const auth = useAuthStore();
    if (!auth.loggedIn) {
      return navigateTo({ name: 'login' });
    }
    if (auth.emailVerified) {
      return navigateTo({ name: 'settings' });
    }
  },
});

let resendingEmail = ref(false);
let resentLink = ref(false);
let verificationError = ref(null);
let justRegistered = ref(false);
const route = useRoute();

onMounted(async () => {
  if (useAuthStore().emailVerified) {
    return navigateTo({ name: 'index' });
  }

  await new Promise((r) => setTimeout(r, 1000));
  await submitForVerification();
});

const submitForVerification = async () => {
  if (!Object.keys(route.query).length) {
    justRegistered.value = true;
    return;
  }
  try {
    const response = await useApi('GET', 'verify-email', route.query);
    useSnackbarStore().show(response.message, 'success');
    await useAuthStore().fetchUser();
    navigateTo({ name: 'index' });
  } catch (error) {
    verificationError.value = error.message;
  }
};

const resendEmail = async () => {
  resendingEmail.value = true;
  try {
    const response = await useApi('POST', 'resend-verification');
    resentLink.value = true;
    justRegistered.value = false;
    useSnackbarStore().show(response.message, 'success');
  } catch (errResponse) {
    useSnackbarStore().show(errResponse.message, 'error');
  }

  resendingEmail.value = false;
};
</script>
