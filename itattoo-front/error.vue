<template>
  <div class="h-screen w-screen p-4 md:p-10">
    <div
      class="flex flex-col justify-center md:justify-center items-center h-full bg-slate-200 dark:bg-slate-200/20 rounded-big relative px-8"
    >
      <img
        :src="imageScript"
        class="w-60 md:w-full md:max-w-sm absolute bottom-0 right-0 filter grayscale"
      />
      <h1>{{ error.statusCode }}</h1>
      <div v-if="statusCode == 403" class="max-w-3xl mx-auto my-10">
        <ui-alert type="danger">
          <span class="p-5 flex text-base">{{ error.message }}</span>
        </ui-alert>
      </div>
      <div v-else class="p-4 max-w-2xl text-center">
        <div v-html="error.message"></div>
      </div>
      <div v-if="statusCode == 500" class="text-center">
        If you refresh the page and this message still appears please
        <nuxt-link :to="{ name: contect }">contact us</nuxt-link>
      </div>

      <button class="btn primary mt-10" @click="handleClearError">
        Take me home
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps(['error']);
const handleClearError = () =>
  clearError({
    redirect: '/',
  });

const statusCode = computed(() => {
  return props.error?.statusCode || false;
});

const imageScript = computed(() => {
  if (statusCode.value == 404) {
    return '/images/not-found.png';
  }
  if (statusCode.value == 500) {
    return '/images/server-error.png';
  }
  if (statusCode.value == 403) {
    return '/images/no-access.webp';
  }
  if (statusCode.value == 405) {
    return '/images/not-allowed.png';
  }

  return '/images/sad-man.png';
});
</script>
