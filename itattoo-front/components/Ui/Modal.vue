<template>
  <div class="modalWrapper primary">
    <!-- Modal backdrop -->
    <div v-show="visible" class="modal-backdrop" aria-hidden="true"></div>
    <div v-if="visible" class="modal-box" role="dialog" aria-modal="true">
      <div class="card relative">
        <button
          @click.prevent="emit('close')"
          class="btn btn-icon bg-gray-200 dark:bg-gray-600 close-btn"
        >
          <nuxt-icon filled name="x-mark" class="text-lg"></nuxt-icon>
        </button>
        <div class="modal-container">
          <!-- Content -->
          <div class="w-full">
            <!-- Modal header -->
            <div class="mb-2">
              <div class="title">
                <span>
                  <slot name="title">
                    {{ $t('general.confirm') }}
                  </slot>
                </span>
                <div class="text-sm font-normal mt-2">
                  <slot name="description">
                    Are you sure you want to proceed with this action?
                  </slot>
                </div>
              </div>
            </div>
            <!-- Modal content -->
            <div class="text-sm">
              <div class="space-y-2">
                <slot name="content">
                  Are you sure you want to proceed with this action?
                </slot>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['close']);
const props = defineProps({
  visible: {
    type: Boolean,
    default: 'primary',
  },
});
</script>
