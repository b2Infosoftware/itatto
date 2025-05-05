<template>
  <div class="relative inline-flex">
    <button
      ref="trigger"
      class="inline-flex justify-center items-center group"
      aria-haspopup="true"
      :aria-expanded="showLangMenu"
      @click.prevent="toggleLanguageMenu"
    >
      <span class="flex truncate ml-2">
        <nuxt-icon filled name="language"></nuxt-icon>
      </span>
    </button>
    <transition
      enter-active-class="transition ease-out duration-200 transform"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-out duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showLangMenu" class="dropdown-items-wrapper">
        <div class="pt-0.5 pb-2 px-3 mb-1 border-b dark:border-input-dark">
          <div class="font-medium">
            {{ activeLanguage.name }}
          </div>
        </div>
        <ul>
          <li v-for="lang in langs">
            <span
              class="dropdown-item w-full"
              @click.prevent.native="changeLanguage(lang.locale)"
            >
              {{ lang.name }}
            </span>
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { onClickOutside } from '@vueuse/core';
const langs = useSettingsStore().languages;
const showLangMenu = ref(false);
const trigger = ref(null);
let activeLanguage = langs.find(
  (item) => item.locale == useCookie('usrLang').value
);
if (!activeLanguage) {
  activeLanguage = langs[0];
}
const toggleLanguageMenu = () => {
  showLangMenu.value = !showLangMenu.value;
};
onClickOutside(trigger, (event) => {
  closeDropdown();
});
const closeDropdown = () => {
  showLangMenu.value = false;
};

const changeLanguage = (newLang) => {
  if (newLang == useCookie('usrLang').value) {
    return closeDropdown();
  }
  useCookie('usrLang').value = newLang.toLowerCase();
  reloadNuxtApp();
};
</script>
