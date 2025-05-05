<template>
  <div class="consentPreview">
    <span class="form-title">
      {{ form.title }}
    </span>
    <span class="parts">
      <div>
        <span>{{ $t('consentSettings.agency') }}</span>
        <span>{{ useOrganisationStore().defaultLocation.name }}</span>
        <span>{{ useOrganisationStore().defaultLocation.address }}</span>
        <span>{{ useOrganisationStore().defaultLocation.city }}</span>
        <span>{{ useOrganisationStore().defaultLocation.country.name }}</span>
        <span>{{ useOrganisationStore().defaultLocation.phone_number }}</span>
      </div>
      <div>
        <span>{{ $t('consentSettings.client') }}</span>
        <span>{{ client?.full_name || 'John Doe' }}</span>
        <span>{{ client?.phone_number || '+0123456789' }}</span>
        <span>{{ client?.email || 'email@example.com' }}</span>
      </div>
    </span>
    <span class="form-subtitle">
      {{ form.subtitle }}
    </span>
    <div class="opening-text" v-html="form.opening_text"></div>
    <div class="statements">
      <span
        class="text-left flex"
        v-for="(statement, index) in form.statements"
        :key="index"
      >
        <div class="flex-grow-0">
          <input-checkbox
            v-if="withCheckboxes"
            v-model="checkboxes[index]"
            :value="true"
          >
          </input-checkbox>
        </div>
        <p>
          {{ statement }}
        </p>
      </span>
    </div>

    <div class="closing-text" v-html="form.closing_text"></div>
    <div class="sign-title">
      {{ form.sign_title }}
    </div>

    <div v-if="!client.is_minor" class="signature max-w-96">
      <span>{{ client.full_name }}</span>
      <div><img :src="signature" /></div>
    </div>
    <div v-else class="signature">
      <span
        >{{ client.parent_1.full_name }}
        <template v-if="client.parent_2"
          >& {{ client.parent_2.full_name }}</template
        >
      </span>
      <div><img :src="signature" /></div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  client: {
    type: [Object, null],
    default: null,
  },
  company: {
    type: [Object, null],
    default: null,
  },
  form: {
    type: Object,
    required: true,
  },
  signature: {
    type: [String, null],
    default: null,
  },
  withCheckboxes: {
    type: Boolean,
    default: false,
  },
});

const checkboxes = ref([]);

const approvedAll = () => {
  const e =
    checkboxes.value.filter((item) => item == true).length ==
    props.form.statements.length;
  console.log(e);
  return e;
};

defineExpose({ approvedAll });
</script>
