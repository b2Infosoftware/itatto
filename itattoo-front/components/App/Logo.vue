<template>
  <div class="logo">
    <template v-if="layout.theme == 'light'">
      <img class="mini" src="/images/logo/icon.png" alt="iTattoo logo" />
      <img class="large" src="/images/logo/black.svg" alt="iTattoo logo" />
    </template>
    <template v-else>
      <img class="mini" src="/images/logo/icon.png" alt="iTattoo logo" />
      <img class="large" src="/images/logo/white.png" alt="iTattoo logo" />
    </template>
  </div>
</template>

<script setup>
const layout = useLayoutStore();
</script>
