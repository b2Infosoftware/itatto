<template>
  <div class="paginationWrapper">
    <ul v-if="meta.last_page">
      <li>
        <nuxt-link
          :class="{ disabled: meta.current_page == 1 }"
          :to="prevPageRoute()"
        >
          <nuxt-icon filled name="chevron-left"></nuxt-icon>
        </nuxt-link>
      </li>
      <li
        v-for="page in meta.last_page"
        :key="page"
        :class="{ hide: shouldHide(page) }"
      >
        <i v-if="shouldHide(page)"></i>
        <nuxt-link
          v-else
          :class="{ active: isActive(page) }"
          :to="{
            name: route.name,
            query: { ...route.query, page: page },
          }"
        >
          {{ page }}
        </nuxt-link>
      </li>
      <li>
        <nuxt-link
          :class="{ disabled: meta.current_page == meta.last_page }"
          :to="nextPageRoute()"
        >
          <nuxt-icon filled name="chevron-right"></nuxt-icon>
        </nuxt-link>
      </li>
    </ul>
  </div>
</template>

<script setup>
const props = defineProps({
  meta: {
    required: true,
  },
});
const route = useRoute();
const shouldHide = (page) => {
  if (props.meta.last_page < 9) {
    return false;
  }
  if (page <= 3 || props.meta.last_page - page < 3) {
    return false;
  }

  return Math.abs(page - props.meta.current_page) >= 2;
};

const isActive = (page) => {
  return page == props.meta.current_page;
};

const prevPageRoute = () => {
  if (props.meta.current_page == 1) {
    return { name: route.name, page: props.meta.current_page };
  }
  return {
    name: route.name,
    query: { page: props.meta.current_page - 1 },
  };
};

const nextPageRoute = () => {
  if (props.meta.current_page == props.meta.last_page) {
    return { name: route.name, page: props.meta.current_page };
  }
  return {
    name: route.name,
    query: { page: props.meta.current_page + 1 },
  };
};
</script>
