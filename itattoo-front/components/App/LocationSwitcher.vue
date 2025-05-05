<template>
  <div class="relative inline-flex">
    <button
      ref="trigger"
      class="inline-flex justify-center items-center group"
      aria-haspopup="true"
      :aria-expanded="showLocationMenu"
      @click.prevent="toggleLocationMenu"
    >
      <div class="hidden sm:flex items-center truncate">
        <span
          class="truncate ml-2 text-sm font-medium group-hover:text-slate-800"
        >
          {{ defaultLocation.name }}
        </span>
        <svg
          v-if="organisation.locations.length > 1"
          class="w-3 h-3 shrink-0 ml-1 fill-current text-slate-400"
          viewBox="0 0 12 12"
        >
          <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
        </svg>

        <div class="ml-5">
          <img
            :src="avatarImage"
            alt="Profile Picture"
            class="object-cover w-10 h-10 rounded-full"
          />
        </div>
      </div>

      <span class="flex sm:hidden truncate ml-2">
        <nuxt-icon filled name="shop"></nuxt-icon>
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
      <div v-if="showLocationMenu" class="dropdown-items-wrapper">
        <div class="pt-0.5 pb-2 px-3 mb-1 border-b dark:border-input-dark">
          <div class="font-medium">
            {{ defaultLocation.name }}
          </div>
          <div class="text-xs text-slate-500 italic">
            {{ organisation.defaultOrganisation.name }}
          </div>
        </div>
        <!-- <nuxt-link :to="{ name: 'profile' }">
          <div
            class="pt-2 pb-2 px-3 mb-1 border-b dark:border-input-dark cursor-pointer hover:bg-slate-50/50 dark:hover:bg-slate-800/50"
          >
            Show Profil
          </div>
        </nuxt-link> -->
        <ul>
          <li v-for="location in organisation.locations">
            <span
              class="dropdown-item w-full"
              :to="{ name: 'settings' }"
              @click.prevent.native="changeDefaultLocation(location)"
            >
              {{ location.name }}
            </span>
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { onClickOutside } from "@vueuse/core";

const organisation = useOrganisationStore();
const defaultLocation = organisation.defaultLocation;
const {defaultLocation: location} = storeToRefs(organisation);

const trigger = ref(null);
const showLocationMenu = ref(false);
const toggleLocationMenu = () => {
  showLocationMenu.value = !showLocationMenu.value;
};

const avatarImage = computed(() => {
  return location.value.avatar
    ? location.value.avatar
    : "https://i.imgur.com/SMBGn8O.png";
});

const closeDropdown = () => {
  showLocationMenu.value = false;
};

const changeDefaultLocation = async (location) => {
  if (defaultLocation.id == location.id) {
    return closeDropdown();
  }
  const response = await useApi("POST", "change-location/" + location.id);
  useSnackbarStore().show(response.message, "success");

  reloadNuxtApp({ ttl: 1000 });
};

onClickOutside(trigger, (event) => {
  closeDropdown();
});
</script>
