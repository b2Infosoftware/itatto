<template>
  <button @click.prevent="useLayoutStore().toggleTheme()" class="btn btn-icon">
    <nuxt-icon
      v-if="useLayoutStore().theme == 'light'"
      name="moon"
      filled
    ></nuxt-icon>
    <nuxt-icon v-else name="sun" filled></nuxt-icon>
  </button>
</template>

<script setup></script>
