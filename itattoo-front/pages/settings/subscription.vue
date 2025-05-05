<template>
  <div class="card">
    <div class="card-title">
      <div class="flex justify-between w-full">
        <span class="capitalize">{{ title }}</span>
        <div class="switch-container">
          <span :class="{ 'active-label': selectedType === subscribeSelectedType[1].id }" class="left">
            {{ subscribeSelectedType[1].name }}
          </span>

          <label class="switch">
            <input type="checkbox" :checked="selectedType === subscribeSelectedType[1].id" @change="toggleType" />
            <span class="slider"></span>
          </label>

          <span :class="{ 'active-label': selectedType === subscribeSelectedType[0].id }" class="right">
            {{ subscribeSelectedType[0].name }}
          </span>
        </div>

        <div>
        </div>
      </div>
    </div>
    <div class="card-body gap-y-4 flex flex-col">
      <div v-if="!loadingPlan">
      <ui-alert
        v-if="
          useOrganisationStore().defaultOrganisation.owner_id !=
          useAuthStore().user.id
        "
        type="warning"
      >
        <span class="text-lg my-10 flex items-center">
          {{ $t('subscription.ask_owner_to_buy') }}
        </span></ui-alert
      >
        <div v-else class="plansWrapper grid grid-cols-[repeat(auto-fit,minmax(300px,1fr))] gap-4">
          <div
            v-for="(plan, key) in PlanFilters"
            :class="{ hide: isActive(plan), popular: key == 2, first: key == 0 }"
            :key="plan.id"
            class="planCard"
            v-show="showPlan(plan)"
          >
            <div v-if="key == 2" class="popular">Popular</div>
            <span class="mt-8">
              <!-- <img :src="images[key]" :alt="plan.name" /> -->
            </span>
            <div class="font-semibold text-xl my-4">{{ getName(plan) }}</div>
            <div class="description mb-4 text-center">{{ plan.description }}</div>
            <div class="flex items-baseline">
              <div class="price">{{ plan.price === 0 ? 'Free ' : `&euro; ${plan.price}` }}</div>
              <span v-if="plan.price != 1" class="text-xs ml-1">/{{ getRecurrence(plan) }}</span>
              <span v-else class="text-xs ml-1">/7 day</span>
            </div>

            <ul class="features">
              <li>Secure Dashboard</li>
              <li>Instant Messaging System</li>
              <li>Advanced Calendar Module</li>
              <li>Customer Module</li>
              <li>Services Module</li>
              <li>Payment Module</li>
              <li :class="{ 'line-through opacity-70' : !plan.is_marketing_modul}" >Marketing Module</li>
              <li :class="{ 'line-through opacity-70' : !plan.is_sms}">150 Free SMS</li>
              <li :class="{ 'line-through opacity-70': plan.is_artist }">
                Staff Module
              </li>
              <li :class="{ 'line-through opacity-70': plan.is_artist }">
                Studios Module
              </li>
            </ul>

            <ui-alert v-if="showTextForCancelledPlan(plan)" type="warning">
              {{ getButtonText(plan) }}
              {{}}
            </ui-alert>
            <button
              v-if="showCancelButton(plan)"
              :disabled="loading"
              class="btn danger outlined mb-4"
              @click.prevent.stop="cancel(plan)"
            >
              <div v-show="loading" class="loading-ring"></div>
              {{ getButtonText(plan) }}
            </button>
            <button
              v-if="showSubscribeButton(plan)"
              :disabled="disableButton(plan)"
              class="btn success mb-4"
              :class="isActive(plan) ? 'success ' : 'primary'"
              @click.prevent.stop="triggerSubscription(plan)"
            >
              <div v-show="loading" class="loading-ring"></div>
              {{ getButtonText(plan) }}
            </button>
          </div>
        </div>
      </div>
      <ui-confirm-modal ref="confirmModal">
        <template #title>{{ $t('subscription.proceed_title') }}?</template>
        <template #description>
          <div class="flex flex-col items-center px-5">
            <img
              src="/images/secure.png"
              alt="secure payment"
              class="w-24 my-5"
            />
            <div class="py-4 text-sm font-semibold text-center" type="success">
              <div>
                <p>
                  {{ $t('subscription.we_know_appoitments') }}
                  {{ $t('subscription.send_to_stripe') }}
                </p>
                <ui-alert type="warning" class="mt-4">
                  <p>
                    {{ $t('subscription.if_upgrade') }}
                  </p>
                </ui-alert>
              </div>
            </div>
          </div>
        </template>
        <template #confirm>{{ $t('subscription.proceed') }}</template>
      </ui-confirm-modal>
    </div>
  </div>
</template>
<script setup>
import { onMounted } from 'vue';
const { $t } = useNuxtApp();
definePageMeta({
  layout: 'settings',
  middleware: 'auth',
});
const title = $t('subscription.title');
useHead({
  title: title,
});

const subscribeSelectedType = [
  {name: 'Annual', id: 1},
  {name: 'Monthly', id: 2},
]

const toggleType = () => {
  selectedType.value = selectedType.value === subscribeSelectedType[0].id 
    ? subscribeSelectedType[1].id 
    : subscribeSelectedType[0].id;
};

const images = [
  '/images/plans/solo.png',
  '/images/plans/pro.png',
  '/images/plans/enterprise.png',
];
const confirmModal = ref(null);
const selectedType = ref(2);
const loading = ref(false);
const loadingPlan = ref(false);
const activeSubscription = useOrganisationStore().defaultOrganisation.activeSubscription || null;
const plans = ref([]);
const response = ref({ status: 0 });

onMounted(async () => {
  loadingPlan.value = true;

  try {
    const plansRes = await useApi('GET', 'plans');
    plans.value = plansRes?.data || [];

    const checkRes = await useApi('GET', '/plans/check');
    response.value.status = checkRes?.status ?? 0;

    console.log('Data fetched:', { plans: plans.value, response: response.value });
  } catch (error) {
    console.error('Error fetching data:', error);
  }

  loadingPlan.value = false;
});


const isActive = (plan) => {
  const subscriptionPlanId = activeSubscription?.plan_id || null;
  return plan.id == subscriptionPlanId;
};

const PlanFilters = computed(() => {
  const hideTrial = response.value?.status === 1;

  return plans.value
    .filter((item) => {
      const isTrial = item.months === 0;

      if (hideTrial && isTrial) return false;

      const hasPaidSubscription = activeSubscription?.plan_id && activeSubscription.months > 0;
      if (hasPaidSubscription && isTrial) return false;

      if (selectedType.value == 1) return item.months == 12 || isTrial;
      if (selectedType.value == 2) return item.months == 1 || isTrial;

      return false;
    })
    .sort((a, b) => (a.months === 0 ? -1 : b.months === 0 ? 1 : 0));
});

const showPlan = (plan) => {
   const value = plan.name != 'Education' || useRoute().query.special == 'education' || isActive(plan);
   return value;
};

const getRecurrence = (plan) => {
  if (plan.months == 1) {
    return $t('general.month');
  }
  if (plan.months == 12) {
    return $t('general.year');
  }
  return plan.months + ' ' + $t('general.months');
};

const getName = (plan) => {
  return plan.name;
};

const disableButton = (plan) => {
  return isActive(plan) || loading.value;
};

/**
 * Decides whether to show a button for a plan or not
 */
 const showSubscribeButton = (plan) => {
  if (plan.name === 'Education') {
    return true; 
  }
  if (isActive(plan)) return false;
  const isTrial = activeSubscription?.plan?.months === 0;
  if (isTrial) return plan?.price > 0;
  if (!activeSubscription) return true;
  return plan.price > activeSubscription.plan.price || plan.months > activeSubscription.plan.months;
};



const showTextForCancelledPlan = (plan) => {
  if (!isActive(plan)) {
    return false;
  }

  return Boolean(activeSubscription?.canceled_at);
};

/**
 * Decides whether to show the cancel button for a plan or not
 */
const showCancelButton = (plan) => {
  if (!isActive(plan)) {
    return false;
  }

  return !Boolean(activeSubscription?.canceled_at);
};

/**
 * Decides what text to display on a plan card based on the current subscription
 */
 const getButtonText = (plan) => {
  const hideTrial = response.value?.status === 1;
  const isTrial = plan.months === 0;

  if (hideTrial && isTrial) {
    return '';
  }

  if (isActive(plan)) {
    if (activeSubscription?.canceled_at) {
      return $t('subscription.canceled');
    }
    return $t('subscription.cancel');
  }
  if (activeSubscription) {
    if (activeSubscription.plan.price < plan.price) {
      return $t('subscription.upgrade');
    }
  }

  return $t('subscription.subscribe');
};

/**
 * Subscribes to a plan and redirects to a stripe payment page
 */
 const subscribe = async (plan) => {
  loading.value = true;
  const isTrial = plan.months === 0;
  let url = isTrial 
    ? `/plans/${plan.id}/subscribe/trial` 
    : `/plans/${plan.id}/subscribe`;

  try {
    const response = await useApi('POST', url);
    window.location.replace(response.url);
  } catch (error) {
    console.log(error);
  }

  loading.value = false;
};



/**
 * Subscribes to a plan and redirects to a stripe payment page
 */
const cancel = async (plan) => {
  loading.value = true;
  let url = `/plans/${plan.id}/cancel`;
  try {
    const response = await useApi('POST', url);
    if (response.url) {
      window.location.replace(response.url);
    } else {
      useSnackbarStore().show(response.message, 'success');
    }
  } catch (error) {
    console.log(error);
  }

  loading.value = false;
};

/**
 * Triggers when a user clicks on a subscribe button.
 * Opens a confirmation modal to decide whether we proceed with the action.
 */
const triggerSubscription = async (plan) => {
  if (loading.value) {
    return;
  }
  let confirmed = await confirmModal.value.open();

  if (!confirmed) {
    return;
  }

  subscribe(plan);
};
</script>