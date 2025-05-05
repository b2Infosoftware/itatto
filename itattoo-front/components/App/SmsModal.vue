<template>
  <div class="modalWrapper">
    <!-- Modal backdrop -->
    <div class="modal-backdrop" aria-hidden="true"></div>

    <div class="modal-box" role="dialog" aria-modal="true">
      <div class="card">
        <!-- <div v-click-outside="closeModal" class="modal-card"> -->
        <div class="modal-container">
          <!-- Content -->
          <div class="w-full">
            <!-- Modal header -->
            <div class="mb-2">
              <div class="title">{{ $t('subscription.sms_modal_title') }}?</div>
            </div>
            <!-- Modal content -->
            <div class="text-sm mb-10">
              <div class="space-y-2">
                <div class="text-left mb-4">
                  <input-select
                    :options="options"
                    :label="$t('subscription.sms_input_label')"
                    name="viewoptions"
                    v-model="form.amount"
                    is-object
                  ></input-select>
                </div>

                <ui-alert type="warning">
                  <p>
                    {{ $t('subscription.valid_one_year') }}
                  </p>
                  <p>{{ $t('subscription.you_save') }}</p>
                </ui-alert>
              </div>
            </div>
            <!-- Modal footer -->
            <div class="buttons">
              <button
                class="btn inline-flex justify-center"
                :class="type == 'danger' ? 'danger' : 'primary'"
                @click.stop="purchase"
              >
                <div v-show="loading" class="loading-ring"></div>
                {{ $t('subscription.purchase') }}
              </button>
              <button
                class="btn secondary"
                @click.stop="cancel"
                :disabled="loading"
              >
                {{ $t('general.cancel') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['close']);
const loading = ref(false);
const form = reactive({
  amount: 100,
});
const options = [
  {
    id: 100,
    name: '100 SMS',
  },
  {
    id: 200,
    name: '200 SMS',
  },
  {
    id: 500,
    name: '500 SMS',
  },
  {
    id: 1000,
    name: '1000 SMS',
  },
];

const closeModal = () => {
  emit('close');
};

/**
 * Attemps to purchase some SMS
 */
const purchase = async (plan) => {
  loading.value = true;

  try {
    const response = await useApi('POST', 'plans/buy-sms', form);
    if (response.url) {
      window.location.replace(response.url);
    } else {
      await new Promise((resolve) => setTimeout(resolve, 1000));
      await useAuthStore().fetchUser();
      await useOrganisationStore().fetchData();
      useSnackbarStore().show(response.message, 'success');
    }
  } catch (error) {
    console.log(error);
  }

  loading.value = false;
};
const cancel = () => {
  closeModal();
};
</script>
