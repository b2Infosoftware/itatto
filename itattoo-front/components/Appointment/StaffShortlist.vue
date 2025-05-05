<template>
  <div class="staffShortlist">
    <button
      @click.prevent="showModal = !showModal"
      class="ml-4 btn md:ml-0 btn-add max-md:ml-2"
    >
      <span v-if="staff.length">
        <nuxt-icon name="plus" filled></nuxt-icon>
      </span>
      <span v-else>{{ $t('staff.create_shortlist') }}</span>
    </button>

    <div class="flex flex-wrap whitespace-nowrap gap-x-3 max-md:gap-x-1">
      <button
        class="btn-profile"
        :class="isActive(item) ? 'primary' : 'secondary'"
        v-for="(item, index) in visibleStaff"
        :key="item.id"
        @click.prevent="setPreferred(item)"
      >
        <img class="avatar-left" :src="item.avatar" alt="Profile Picture" />
        <span class="mr-4 name">{{ item.name }}</span>
      </button>

      <div v-if="staff.length > maxVisible" class="relative">
        <button
          @click="toggleMoreVisibility"
          class="btn-more-profile"
          :class="showMore ? 'bg-[#628E34]' : 'bg-[#85bd4a]'"
        >
          + {{ staff.length - maxVisible }} more
        </button>

        <transition name="fade" appear>
          <div v-if="showMore" class="more-profile-box">
            <button
              :class="isActive(item) ? 'primary' : 'secondary'"
              v-for="(item, index) in hiddenStaff"
              :key="item.id"
              @click.prevent="setPreferred(item)"
            >
              <img class="avatar-left" :src="item.avatar" alt="Profile Picture" />
              <span class="mr-4 name" style="display:block !important;">{{ item.name }}</span>
            </button>
          </div>
        </transition>
      </div>
    </div>

    <div v-if="showModal" class="modalWrapper">
      <div class="modal-backdrop"></div>
      <div class="modal-box">
        <div class="card">
          <div class="flex justify-end mb-4 -mt-3 -mr-3">
            <button @click.prevent="showModal = false" class="secondary btn">
              <nuxt-icon
                name="x-mark"
                class="mt-1 text-base"
                filled
              ></nuxt-icon>
            </button>
          </div>
          <input-multiselect
            v-model="staffIds"
            name="staff_id"
            is-object
            :label="$t('staff.pick_shortlist')"
            text-key="full_name"
            :placeholder="$t('wizard.pick_staff')"
            :options="allMembers"
            :max="12"
          >
          </input-multiselect>
          <div class="flex justify-center mt-4">
            <button @click.prevent="saveShortlist" class="btn primary">
              {{ $t('general.save') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const auth = useAuthStore();
const showMore = ref(false); 
const showModal = ref(false);
const staffIds = ref([]);
const staff = computed(() => {
  if (!auth.user.staff_shortlist_ids?.length) {
    return [];
  }
  return useOrganisationStore()
    .staff.filter((item) => auth.user.staff_shortlist_ids.includes(item.id))
    .map((item) => {
      return {
        name: item.full_name || 'Unknown Staff',
        id: item.id,
        avatar: item.avatar || 'https://i.imgur.com/SMBGn8O.png',
      };
    });
});
const maxVisible = ref(4);
const updateMaxVisible = () => {
  if (window.innerWidth <= 768) {
    maxVisible.value = 1; 
  } else if (window.innerWidth <= 1280) {
    maxVisible.value = 2; 
  } else {
    maxVisible.value = 4;
  }
};
onMounted(() => {
  updateMaxVisible();
  window.addEventListener('resize', updateMaxVisible);
});
onUnmounted(() => {
  window.removeEventListener('resize', updateMaxVisible);
});
const visibleStaff = computed(() => staff.value.slice(0, maxVisible.value));
const hiddenStaff = computed(() => staff.value.slice(maxVisible.value));
const toggleMoreVisibility = () => {
  showMore.value = !showMore.value;
};

/**
 *  Sets the shortlist of staff members to display in main header
 */
const saveShortlist = async () => {
  await useApi('POST', 'me/shortlist', { ids: staffIds.value });
  useAuthStore().fetchUser();
  showModal.value = false;
};

/**
 * Options list for staff dropdown
 */
const allMembers = computed(() => {
  return useOrganisationStore().staff;
});

/**
 * Decides whether a staff is preselected or not
 */
const isActive = (item) => {
  return item.id == auth.user.preselected_staff_id;
};

/**
 *  Sets the preferred staff to be pre-defined on future visits
 */
const setPreferred = async (item) => {
  await useApi('POST', 'me/preferred-staff/' + item.id);
  useAuthStore().fetchUser();
  useAppointmentStore().filters.staff_ids = [item.id];
  useAppointmentStore().staff = useOrganisationStore().staff.filter((item) =>
    useAppointmentStore().filters.staff_ids.includes(item.id)
  );
  useAppointmentStore().fetchItems();
};

/**
 * Show staff initials
 */
const getInitials = (item) => {
  let text = '';
  text += item.first_name.substring(0, 1).toUpperCase();
  text += item.last_name.substring(0, 1).toUpperCase();
  return text;
};

onBeforeMount(() => {
  const allStaff = useOrganisationStore().staff.map((item) => item.id);
  if (auth.user.staff_shortlist_ids?.length) {
    staffIds.value = auth.user.staff_shortlist_ids.filter((item) =>
      allStaff.includes(item)
    );
  }
  const cookieStaff = useCookie('selectedStaff').value;

  if (cookieStaff) {
    const preselectedStaff = useOrganisationStore().staff.filter((item) =>
      cookieStaff.includes(item.id)
    );
    if (preselectedStaff) {
      useAppointmentStore().staff = preselectedStaff;
      useAppointmentStore().filters.staff_ids = cookieStaff;
    }
  } else {
    const preselectedStaff = useOrganisationStore().staff.find(
      (item) => item.id == auth.user.preselected_staff_id
    );
    if (preselectedStaff) {
      useAppointmentStore().staff.push(preselectedStaff);
    }
  }
});
</script>