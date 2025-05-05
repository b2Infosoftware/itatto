<template>
  <div class="flex justify-center flex-col items-center py-5">
    <div class="d-flex justify-center align-center">
      <img
        src="/images/no-data.svg"
        alt="nothing here yet"
        class="w-1/2 max-w-64 mx-auto"
      />
    </div>
    <div>
      <div class="text-lg my-8">
        <slot name="text">{{ $t('general.no_data') }}</slot>
      </div>
      <div class="my-10 text-center">
        <slot name="cta"></slot>
      </div>
    </div>
  </div>
</template>

<script setup></script>
