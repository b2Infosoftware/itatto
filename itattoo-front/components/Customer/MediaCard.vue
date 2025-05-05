<template>
  <div>
    <div v-if="media.type == 'image'" class="mediaCard" :style="bgImage"></div>
    <div v-else-if="media.type == 'video'" class="videoCard">
      <video :src="media.full_path" controls></video>
    </div>
    <div v-else class="h-28 flex flex-col items-center justify-center">
      <nuxt-icon class="text-4xl" filled name="document"></nuxt-icon>
      <span>{{ media.type }}</span>
    </div>
    <div class="mediaActions">
      <a :href="media.full_path" target="_blank" download>
        <nuxt-icon name="download" filled></nuxt-icon>
      </a>
      <button @click.prevent="emit('delete')">
        <nuxt-icon name="trash" filled></nuxt-icon>
      </button>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['delete']);
const props = defineProps({
  media: {
    default: Object,
    required: true,
  },
});

const bgImage = {
  backgroundImage: 'url(' + props.media.full_path + ')',
};
</script>
