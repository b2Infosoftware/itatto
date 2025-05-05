<template>
  <div class="staffCard">
    <div class="color" :style="{ backgroundColor: staff.color }">
      <span
        class="avatar"
        :style="{ backgroundImage: 'url(' + getImage(staff) + ')' }"
      >
      </span>
      <ui-dropdown :style="{ color: useColor().getContrastColor(staff.color) }">
        <ul>
          <li class="dropdown-item">
            <nuxt-link :to="{ name: 'staff-id', params: { id: staff.id } }">
              Edit
            </nuxt-link>
          </li>
          <li class="dropdown-item">
            <button @click.prevent="emit('delete')">Delete</button>
          </li>
        </ul>
      </ui-dropdown>
    </div>
    <div class="info">
      <span class="font-semibold mb-2">{{ staff.full_name }}</span>
      <span
        ><nuxt-icon name="envelope" filled></nuxt-icon>{{ staff.email }}</span
      >
      <span
        ><nuxt-icon name="briefcase" filled></nuxt-icon
        >{{ staff.services.length }} Services</span
      >
      <span
        ><nuxt-icon name="shop" filled></nuxt-icon
        >{{ staff.is_guest ? $t('staff.guest') : $t('staff.resident') }}</span
      >
      <span class="tags">
        <i v-for="tag in staff.tags" :key="tag.id">{{ tag.name }}</i>
      </span>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['delete']);
const props = defineProps({
  staff: {
    type: Object,
    required: true,
  },
});
const getImage = (staff) => {
  return staff.avatar || '/images/placeholder.jpg';
};
</script>
