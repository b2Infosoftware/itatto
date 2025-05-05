<template>
  <div class="bg-white dark:bg-slate-900">
    <ui-snackbar></ui-snackbar>
    <div class="relative flex">
      <!-- Image -->
      <div
        class="hidden relative flex-col md:flex justify-center items-center md:w-3/5 rounded-big bg-slate-950"
        aria-hidden="true"
      >
        <nuxt-link
          :to="{ name: 'index' }"
          class="w-40 mb-20"
          aria-label="Homepage"
        >
          <!-- <img class="large" src="/images/logo/white.png" alt="iTattoo logo" /> -->
        </nuxt-link>
        <transition name="fade" mode="out-in">
          <!-- <img
            v-if="route.name != 'index'"
            :key="route.name"
            class="object-cover object-center h-3/5 max-h-full opacity-75"
            :src="'/images/auth/' + route.name + '.png'"
            alt="Login"
          /> -->
          <img
            class="object-contain object-center h:2/5 md:h-1/5 max-h-96"
            src="/images/logo/big-white.svg"
            alt="Login"
          />
        </transition>
      </div>
      <!-- Content -->
      <div class="w-full md:w-2/5">
        <div
          class="min-h-screen h-full flex flex-col justify-center items-center overflow-auto"
        >
          <slot></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const route = useRoute();
// watch(
//   () => route.name,
//   (value) => {
//     useValidationStore().clearErrors();
//   }
// );
</script>
