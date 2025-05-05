<template>
  <div class="card serviceCard">
    <span class="color-code" :style="{ backgroundColor: service.color }"></span>
    <span class="flex relative">
      <button class="move btn bnt-icon secondary btn-sm">
        <nuxt-icon name="hand" filled></nuxt-icon>
      </button>
      <img :src="getImage(service)" :alt="service.name" />
      <span class="flex flex-col justify-between">
        <h6>{{ service.name }}</h6>
        <p class="pr-4 w-full">{{ service.description }}</p>
      </span>
    </span>
    <div class="info">
      <div>
        <nuxt-icon filled name="clock"></nuxt-icon>
        <span>
          <i>{{ useHelpers().niceTime(service.duration) }}</i>
          <i>Duration </i>
        </span>
      </div>
      <div>
        <nuxt-icon filled name="bars2"></nuxt-icon>
        <span>
          <i>{{ useHelpers().niceTime(service.buffer_time) }}</i>
          <i>Buffer </i>
        </span>
      </div>
      <div>
        <nuxt-icon filled name="banknotes"></nuxt-icon>
        <span>
          <i class="flex items-end">
            <em>{{ service.price }}</em>
          </i>
          <i>Price </i>
        </span>
      </div>
    </div>
    <div class="actions">
      <button @click.prevent="emit('delete')" class="btn btn-sm secondary">
        Delete
      </button>
      <nuxt-link
        :to="{ name: 'services-id', params: { id: service.id } }"
        class="btn btn-sm primary"
      >
        Edit
      </nuxt-link>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['delete']);
const props = defineProps({
  service: {
    type: Object,
    required: true,
  },
});

const getImage = (service) => {
  return service.image || '/images/placeholder.jpg';
};
</script>
