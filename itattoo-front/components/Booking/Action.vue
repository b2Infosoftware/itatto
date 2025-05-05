<template>
  <div class="relative inline-block">
    <div ref="dropdownMenu">
      <!-- Tombol untuk membuka dropdown -->
      <button @click="toggleDropdown">
        <nuxt-icon filled name="pencil"></nuxt-icon>
      </button>

      <!-- Dropdown -->
      <div
        v-if="visible"
        class="absolute right-0 mt-2 w-40 bg-white shadow-md rounded-md z-[1000]"
      >
        <ul class="text-sm text-gray-700">
          <li
            class="px-4 m-2 rounded-md text-gray-100 font-semibold py-2 bg-warning-500 hover:bg-warning-700 hover:text-gray-400 transition-colors cursor-pointer flex items-center space-x-2"
          >
            <button
              @click="handelClicReschedule"
              :disabled="props.disable"
              class="flex items-center space-x-2 w-full text-white bg-transparent hover:bg-warning-700 rounded-md py-1 px-4 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span>Reschedule</span>
            </button>
          </li>

          <li
            class="px-4 m-2 rounded-md text-gray-100 font-semibold py-2 bg-danger-500 hover:bg-danger-700 hover:text-gray-400 transition-colors cursor-pointer flex items-center space-x-2"
          >
            <button
              @click="handleAction(2)"
              :disabled="props.disable"
              class="flex items-center space-x-2 w-full text-white bg-transparent hover:bg-danger-700 rounded-md py-1 px-4 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span>Rejected</span>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onClickOutside } from "@vueuse/core";

const props = defineProps({
  visible: Boolean,
  disable: Boolean
});

const emit = defineEmits(["action", "toggle", "hadlerReschedule"]);
const dropdownMenu = ref(null);

const toggleDropdown = () => {
  emit("toggle");
};

const handelClicReschedule = () => {
  emit("hadlerReschedule");
}

onClickOutside(dropdownMenu, () => {
  if (props.visible) {
    emit("toggle");
  }
});

const handleAction = (id) => {
  emit("action", id);
};
</script>