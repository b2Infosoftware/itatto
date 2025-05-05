<template>
  <form @submit.prevent="handleSubmit">
    <div v-show="activeTab == 0">
      <div class="flex flex-col space-y-4 flex-grow mt-4">
        <div class="grid grid-cols-1 gap-4">
          <input-select
            v-model="form.category_id"
            :label="$t('consentSettings.category')"
            name="category_id"
            :options="categories"
            is-object
          ></input-select>
          <input-text
            v-model="form.name"
            :label="$t('consentSettings.name')"
            name="name"
          ></input-text>
          <input-text
            v-model="form.title"
            :label="$t('consentSettings.title')"
            name="title"
          ></input-text>
          <input-text
            v-model="form.subtitle"
            :label="$t('consentSettings.subtitle')"
            name="subtitle"
          ></input-text>
          <input-text
            v-model="form.opening_text"
            :label="$t('consentSettings.opening_text')"
            name="opening_text"
          ></input-text>

          <form-statements v-model="form.statements"></form-statements>

          <input-text
            v-model="form.closing_text"
            :label="$t('consentSettings.closing_text')"
            name="closing_text"
          ></input-text>
          <input-text
            v-model="form.sign_title"
            :label="$t('consentSettings.signature_title')"
            name="sign_title"
          ></input-text>

          <input-text-area
            optional
            v-model="form.note"
            :label="$t('consentSettings.note')"
            name="note"
          ></input-text-area>

          <ui-switch-button
            v-model="form.needs_signature"
            name="needs_signature"
            :info="$t('consentSettings.needs_signature_info')"
            >{{ $t('consentSettings.needs_signature') }}</ui-switch-button
          >

          <ui-switch-button
            v-model="form.is_active"
            name="is_active"
            :info="$t('consentSettings.is_active_info')"
            >{{ $t('consentSettings.is_active') }}</ui-switch-button
          >
        </div>
      </div>
    </div>

    <!-- PREVIEW -->
    <div v-if="showPreview" class="modalWrapper wide primary">
      <!-- Modal backdrop -->
      <div class="modal-backdrop" aria-hidden="true"></div>
      <div class="modal-box" role="dialog" aria-modal="true">
        <div class="card relative">
          <button
            @click.prevent="showPreview = false"
            class="btn btn-icon bg-gray-600 close-btn"
          >
            <nuxt-icon filled name="x-mark" class="text-lg"></nuxt-icon>
          </button>

          <ui-signature-pad
            v-if="showSignature"
            v-model="signature"
            @saved="showSignature = false"
          ></ui-signature-pad>

          <ui-consent-document
            v-else
            :form="form"
            :signature="signature"
          ></ui-consent-document>

          <div class="pt-4">
            <button
              @click.prevent="showSignature = false"
              v-if="showSignature"
              class="btn danger"
            >
              {{ $t('consentSettings.close_signature') }}
            </button>
            <button
              @click.prevent="showSignature = true"
              v-else
              class="btn success"
            >
              {{ $t('consentSettings.open_signature') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- FORM ACTION BUTTONS -->
    <div class="inline-flex mt-8 justify-between w-full">
      <div class="flex space-x-4">
        <nuxt-link
          :to="{ name: 'settings-consent-forms' }"
          class="btn secondary"
        >
          {{ $t('general.back') }}
        </nuxt-link>
        <button @click.prevent="showPreview = true" class="btn success">
          {{ $t('general.preview') }}
        </button>
      </div>
      <input-submit-button class="primary" :loading="saving">
        {{ $t('general.save') }}
      </input-submit-button>
    </div>
  </form>
</template>
<script setup>
const { $t } = useNuxtApp();
const props = defineProps({
  user: {
    type: Object,
    default: {},
  },
  edit: {
    type: Boolean,
    default: false,
  },
  id: {
    type: [Number, Boolean],
    default: false,
  },
});
const route = useRoute();
const showSignature = ref(false);
const signature = ref(null);
const showPreview = ref(false);
const activeTab = ref(0);
const { data: categories } = await useApi('GET', 'categories');
const saving = ref(false);
const customReferral = ref(null);

const form = reactive({
  category_id: '',
  name: 'Default consent form',
  logo: '',
  title: 'CONSENT TO THE TATTOO PROCEDURE',
  subtitle:
    'PLEASE READ AND BE SURE YOU UNDERSTAND THE IMPLICATIONS OF SIGNING',
  opening_text:
    'By signing this agreement I acknowledge that I have been given the full opportunity to ask any questions I may have about getting a tattoo and that all of my questions have been answered to my complete satisfaction. I specifically acknowledge that I have been advised of the facts and matters below and agree as follows:',
  statements: [
    'If I have a condition that could affect healing from this tattoo, I will notify my tattoo artist. I am not pregnant or breastfeeding. I am not under the influence of alcohol or drugs.',
    'I have no medical or skin conditions such as, but not limited to: acne, scarring (keloid) eczema, psoriasis, freckles, moles or sunburn in the area to be tattooed that could interfere with said tattoo. If I have any type of infection or rash ANYWHERE on my body, I will notify my tattoo artist.',
    'I acknowledge that it is not reasonably possible for representatives and associates of this tattoo shop to determine whether I may have an allergic reaction to the pigments or procedures used in my tattoo, and I agree to accept the risk of such a reaction occurring.',
    'I recognize that it is always possible to get an infection as a result of getting a tattoo, particularly in the event that I do not take care of my tattoo. I have received aftercare instructions and agree to follow them while my tattoo is healing. I agree that if any touch-up work becomes necessary due to my negligence, it will be done at my expense.',
    'I understand that there may be variations in color and design between the tattoo I choose and the one created on my body. I understand that if my skin color is dark, the colors will not appear as bright as on light skin.',
    'I understand that having skin treatments, laser hair removal, plastic surgery, or other procedures that alter the skin may cause adverse changes to my tattoo.',
    'I recognize that a tattoo is a permanent change to my appearance and have not been given any guarantees such as the ability to later change or remove my tattoo. To my knowledge, I do not have a physical, mental or health impairment or disability that would impact my well-being as a direct or indirect consequence of my decision to have a tattoo.',
    'I sincerely expressed to my tattoo artist that having a tattoo is my spontaneous choice. I consent to the tattoo application and any actions or conduct of tattoo shop representatives and employees reasonably necessary to perform the tattooing procedure.',
  ],
  closing_text:
    'If any provision, section, subsection, clause or phrase of this Agreement is found to be unenforceable or invalid, that portion will be severed from this Agreement. the remainder of this agreement will then be construed as if the unenforceable portion had never been contained herein. I certify that I am an adult (and have provided valid proof of my age) and that I have the authority to sign this agreement or, if not, my parent or legal guardian must sign on my behalf, and that my parent or guardian legal has fully understood and is in agreement with this contract.',
  sign_title:
    'I HAVE READ AND UNDERSTAND THIS AGREEMENT AND AGREE TO BE BOUND BY IT',
  needs_signature: true,
  notes: '',
  is_active: true,
  use_custom_consent: false,
  text: '',
  infant_consent: false,
});

const handleSubmit = async () => {
  useValidationStore().clearErrors();
  if (customReferral.value?.length) {
    form.referral = customReferral.value;
  }
  const method = props.edit ? 'PATCH' : 'POST';
  const url = props.edit ? 'consent-forms/' + route.params.id : 'consent-forms';
  saving.value = true;
  try {
    const response = await useApi(method, url, form);
    if (response) {
      useSnackbarStore().show(response.message, 'success');
      if (method == 'POST') {
        navigateTo({ name: 'settings-consent-forms' });
      }
    }
  } catch (errResponse) {
    useValidationStore().populateErrors(errResponse);
  }

  saving.value = false;
};

const populateForm = async () => {
  if (props.edit) {
    const { data: dbForm } = await useApi(
      'GET',
      'consent-forms/' + route.params.id
    );
    form.category_id = dbForm.category_id;
    form.name = dbForm.name;
    form.logo = dbForm.logo;
    form.title = dbForm.title;
    form.subtitle = dbForm.subtitle;
    form.opening_text = dbForm.opening_text;
    form.statements = dbForm.statements;
    form.closing_text = dbForm.closing_text;
    form.sign_title = dbForm.sign_title;
    form.needs_signature = dbForm.needs_signature;
    form.notes = dbForm.notes;
    form.is_active = dbForm.is_active;
    form.use_custom_consent = dbForm.use_custom_consent;
    form.text = dbForm.text;
    form.infant_consent = dbForm.infant_consent;
  }
};

onMounted(async () => {
  await populateForm();
});
</script>
