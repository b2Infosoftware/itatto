<template>
  <div class="staffCrudWrapper">
    <!-- Panel body -->

    <div class="flex w-full xl:space-x-4">
      <div class="user-preview hidden w-1/3 xl:flex">
        <div class="card userCard">
          <div class="avatar">
            <img :src="user.avatar" alt="" />
          </div>
          <div class="block text-lg mb-4 mx-auto text-center">
            <!-- {{ user.full_name }} -->
          </div>
          <div class="flex mb-4 justify-center">
            <!-- <div v-for="tag in user.tags" :key="tag.id" class="badge">
              {{ tag.name }}
            </div> -->
          </div>
          <div class="flex justify-around my-8">
            <div class="flex gap-x-4">
              <span
                class="bg-primary/20 w-10 h-10 flex items-center justify-center rounded-md"
              >
                <nuxt-icon
                  class="text-lg text-primary"
                  name="check-badge"
                  filled
                ></nuxt-icon>
              </span>
              <div class="flex flex-col">
                <span class="font-semibold">{{
                  user.completed_appointments_count
                }}</span>
                <span class="text-sm"
                  >{{ $t('staff.completed_appointments') }}
                </span>
              </div>
            </div>
            <div class="flex gap-x-4">
              <span
                class="bg-primary/20 w-10 h-10 flex items-center justify-center rounded-md"
              >
                <nuxt-icon
                  class="text-lg text-primary"
                  name="briefcase"
                  filled
                ></nuxt-icon>
              </span>
              <div class="flex flex-col">
                <span class="font-semibold">{{ user.services.length }}</span>
                <span class="text-sm">{{ $t('staff.services_provided') }}</span>
              </div>
            </div>
          </div>
          <div class="details">
            <span
              class="text-lg pb-2 border-b mb-5 block border-gray-200 dark:border-input-dark"
              >Details</span
            >
            <ul>
              <li>
                <span>{{ $t('staff.email') }}:</span>
                <span>{{ user.email }}</span>
              </li>

              <li>
                <span>{{ $t('staff.is_guest') }}:</span>
                <span>{{
                  user.is_guest ? $t('general.yes') : $t('general.no')
                }}</span>
              </li>
              <li>
                <span>{{ $t('staff.phone_number') }}:</span>
                <span>{{ user.phone_number }}</span>
              </li>
              <li>
                <span>{{ $t('wizard.location') }}:</span>
                <span>{{ getLocationName(0) }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="flex flex-col w-full xl:w-2/3">
        <div class="text-xl w-full text-slate-800 font-bold">
          <ul class="tabsHeader">
            <li v-for="(tab, index) in tabs" :key="index">
              <button
                :class="{ active: tabIsActive(index) }"
                @click.prevent.native="changeTab(index)"
              >
                <nuxt-icon class="text-lg" filled :name="tab.icon"></nuxt-icon>
                <span>{{ tab.name }}</span>
              </button>
            </li>
          </ul>
        </div>

        <div class="tabsContent w-full">
          <form-staff
            edit
            :user="user"
            v-if="activeTab == 0 && user.id"
            @updated="staffUpdated"
          ></form-staff>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const { $t } = useNuxtApp();
definePageMeta({
  middleware: 'subscribed',
});
useHead({
  title: $t('staff.edit_staff'),
});

const authStore = useAuthStore();

const {user} = storeToRefs(authStore)

const getLocationName = () => {
  const location = user.value.locations.find(
    (item) => item.id == user.value.default_location_id
  );
  return location?.name || 'N/A';
};

const staffUpdated = async () => {
  await useAuthStore().fetchUser();
  navigateTo('/profile');
};

const activeTab = ref(0);
const tabs = [
  { name: $t('staff.personal_info'), icon: 'user' },
];

/**
 * Quite self explainatory
 */
const changeTab = (index) => {
  if (index === activeTab.value) {
    return false;
  }
  activeTab.value = index;
};

/**
 * Quite self explainatory
 */
const tabIsActive = (index) => {
  return index === activeTab.value;
};
</script>
