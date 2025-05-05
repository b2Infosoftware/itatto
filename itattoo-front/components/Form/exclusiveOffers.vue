<template>
  <div class="card-title text-lg font-semibold">{{ $t('exclusiveOffers.title') }}</div>

  <div class="table-wrapper">
    <div class="w-full block text-right mb-5">
      <button @click="openCreateModal" class="btn primary">
        {{ $t('exclusiveOffers.create_offer') }}
      </button>
    </div>
    <table>
      <thead>
        <tr>
          <th>{{ $t('exclusiveOffers.offer_name') }}</th>
          <th>{{ $t('exclusiveOffers.offer_details') }}</th>
          <th>{{ $t('exclusiveOffers.discount') }}</th>
          <th>{{ $t('exclusiveOffers.start_date') }}</th>
          <th>{{ $t('exclusiveOffers.end_date') }}</th>
          <th>{{ $t('exclusiveOffers.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="offer in offers" :key="offer.id">
          <td>{{ offer.offer_name }}</td>
          <td>
            {{
              offer.offer_details.length > 50
                ? offer.offer_details.slice(0, 50) + "..."
                : offer.offer_details
            }}
          </td>
          <td>
            <span v-if="offer?.discount?.type == 1">
              {{ offer.discount.value }}%
            </span>
            <span v-else-if="offer?.discount?.type == 0">
              € {{ offer.discount.value }}
            </span>
          </td>
          <td>{{ formatDateForInput(offer.start_date) }}</td>
          <td>{{ formatDateForInput(offer.end_date) }}</td>
          <td class="actions">
            <button @click="openEditModal(offer)" class="btn btn-sm btn-icon">
              <nuxt-icon filled name="pencil" />
            </button>
            <button @click="deleteOffer(offer)" class="btn btn-sm btn-icon ml-4">
              <nuxt-icon filled name="trash" />
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <ui-confirm-modal type="danger" ref="confirmModal">
    <template #confirm>{{ $t('exclusiveOffers.yes_delete') }}</template>
  </ui-confirm-modal>

  <ui-modal @close="showModal = false" :visible="showModal">
    <template #title>
      {{ isEditing ? $t('exclusiveOffers.edit_offer') : $t('exclusiveOffers.create_offer') }}
    </template>
    <template #description>ㅤ</template>
    <template #content>
      <form
        @submit.prevent="isEditing ? handleEdit() : handleCreate()"
        class="flex flex-col gap-5 text-left -mt-5"
      >
        <input-text
          v-model="form.offer_name"
          type="text"
          :placeholder="$t('exclusiveOffers.offer_name')"
          class="input"
          :label="$t('exclusiveOffers.offer_name')"
        />
        <input-textArea
          v-model="form.offer_details"
          :placeholder="$t('exclusiveOffers.offer_details')"
          class="input"
          :label="$t('exclusiveOffers.offer_details')"
        />

        <input-select
          v-model="form.discount_type"
          :options="discountTypes"
          is-object
          class="input"
          :label="$t('exclusiveOffers.discount_type')"
        />
        <input-text
          v-model="form.discount_value"
          type="number"
          placeholder="Nominal"
          class="input"
          :label="$t('exclusiveOffers.discount_value')"
        />

        <input-text
          v-model="form.start_date"
          type="date"
          :placeholder="$t('exclusiveOffers.start_date')"
          class="input"
          :label="$t('exclusiveOffers.start_date')"
        />
        <input-text
          v-model="form.end_date"
          type="date"
          :placeholder="$t('exclusiveOffers.end_date')"
          class="input"
          :label="$t('exclusiveOffers.end_date')"
          :min="form.start_date"
        />

        <input-multiselect
          v-model="selectedVip"
          multiple
          name="select_vip"
          :options="vipList.map(i => i.id)" 
          :custom-label="(opt) => vipList.find(e => e.id === opt)?.name || 'Unknown VIP'"
          :close-on-select="false"
          :searchable="false"
          :label="$t('exclusiveOffers.selected_vip')"
        />

        <div
          class="w-full block text-right border-t border-t-gray-300 pt-5 dark:border-t-gray-500"
        >
          <input-submit-button
            :loading="isLoading"
            type="submit"
            class="btn primary text-right w-18"
          >
          {{ $t('exclusiveOffers.save') }}
          </input-submit-button>
        </div>
      </form>
    </template>
  </ui-modal>
</template>

<script setup>
const { $t } = useNuxtApp();
import { ref, onMounted, watch } from "vue";
import { useSnackbarStore } from "~/stores/snackbarStore";

const isLoading = ref(false);
const offers = ref([]);
const confirmModal = ref(null);
const showModal = ref(false);
const isEditing = ref(false);


const calendarSettings = ref({ date_format: "DD-MM-YYYY" });

const discountTypes = [
  { name: "Percentage", id: 1 },
  { name: "Fixed", id: 0 },
];

const form = ref({
  offer_name: "",
  offer_details: "",
  discount_type: "",
  discount_value: "",
  start_date: "",
  end_date: "",
});

const selectedVip = ref([]);
const vipList = ref([]);

const toISODate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  const tzOffset = date.getTimezoneOffset() * 60000;
  return new Date(date - tzOffset).toISOString().slice(0, 10);
};

const formatDateForInput = (dateString) => {
  if (!dateString) return "";
  
  const date = new Date(dateString);
  const day = ("0" + date.getDate()).slice(-2);
  const monthNumber = ("0" + (date.getMonth() + 1)).slice(-2);
  const monthAbbr = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"][date.getMonth()];
  const year = date.getFullYear();

  const format = calendarSettings.value?.date_format || "DD-MM-YYYY";

  return format.replace(/(YYYY|YY|MMM|MM|DD|D)/g, (token) => {
    switch (token) {
      case "YYYY":
        return year;
      case "YY":
        return String(year).slice(-2);
      case "MMM":
        return monthAbbr;
      case "MM":
        return monthNumber;
      case "DD":
        return day;
      case "D":
        return String(date.getDate());
      default:
        return token;
    }
  });
};

const fetchOffers = async () => {
  const { data } = await useApi("GET", "exclusive-offer");
  if (data) {
    offers.value = data.data || data;
  }
};

const fetchVip = async () => {
  const res = await useApi("GET", "vip");
  if (res && res.data) {
    vipList.value = res.data.map((vip) => ({
      id: vip.id,  
      name: vip.label || vip.name || "Unknown VIP",  
    }));
  }
};

watch(vipList, (newVal) => {
  console.log("vipList updated:", newVal);
}, { deep: true });

const fetchCalendarSettings = async () => {
  try {
    const response = await useApi("GET", "/calendar-settings");
    if (response?.data) {
      calendarSettings.value = response.data;
    }
  } catch (error) {
  }
};

onMounted(() => {
  fetchOffers();
  fetchVip();
  fetchCalendarSettings();
});

const openCreateModal = () => {
  isEditing.value = false;
  Object.assign(form.value, {
    offer_name: "",
    offer_details: "",
    discount_type: "",
    discount_value: "",
    start_date: "",
    end_date: "",
  });
  selectedVip.value = [];
  showModal.value = true;
};

const openEditModal = (offer) => {
  isEditing.value = true;
  
  form.value = {
    ...offer,
    start_date: toISODate(offer.start_date),
    end_date: toISODate(offer.end_date),
    discount_type: offer.discount.type,
    discount_value: offer.discount.value,
  };
  
  selectedVip.value = offer.vips.map((vip) => vip.id);

  showModal.value = true;
};

const handleCreate = async () => {
  isLoading.value = true;

  if(form.value.discount_type == 1) {
    if(form.value.discount_value > 100) {
      useSnackbarStore().show($t('exclusiveOffers.alert_max_discount'), "error");
      isLoading.value = false;
      return;
    }
  }
  
  const payload = {
    offer_name: form.value.offer_name,
    offer_details: form.value.offer_details,
    discount_type: Number(form.value.discount_type),
    discount_value: form.value.discount_value,
    start_date: form.value.start_date, 
    end_date: form.value.end_date,    
    vips: selectedVip.value.map((vip) => Number(vip.id) || vip),
  };

  const response = await useApi("POST", "exclusive-offer", payload);
  if (response) {
    useSnackbarStore().show(response.message, "success");
    showModal.value = false;
    fetchOffers();
  }
  isLoading.value = false;
};

const handleEdit = async () => {
  isLoading.value = true;

  if(form.value.discount_type == 1) {
    if(form.value.discount_value > 100) {
      useSnackbarStore().show($t('exclusiveOffers.alert_max_discount'), "error");
      isLoading.value = false;
      return;
    }
  }

  const payload = {
    offer_name: form.value.offer_name,
    offer_details: form.value.offer_details,
    discount_type: form.value.discount_type,
    discount_value: form.value.discount_value,
    start_date: form.value.start_date,
    end_date: form.value.end_date,
    vips: selectedVip.value.map((vip) => Number(vip.id) || vip),
  };

  const response = await useApi("PATCH", `exclusive-offer/${form.value.id}`, payload);
  if (response) {
    useSnackbarStore().show(response.message, "success");
    showModal.value = false;
  }
  fetchOffers();
  isLoading.value = false;
};

const deleteOffer = async (offer) => {
  const confirmed = await confirmModal.value.open();
  if (confirmed) {
    const response = await useApi("DELETE", `exclusive-offer/${offer.id}`);
    if (response) {
      useSnackbarStore().show(response.message, "success");
      fetchOffers();
    }
  }
};

watch(
  () => form.value.end_date,
  (newEndDate) => {
    if (
      newEndDate &&
      form.value.start_date &&
      new Date(newEndDate) < new Date(form.value.start_date)
    ) {
      useSnackbarStore().show($t('exclusiveOffers.alert_end_date'), "error");
      form.value.end_date = "";
    }
  }
);

watch(
  () => form.value.start_date,
  (newStartDate) => {
    if (form.value.end_date && new Date(form.value.end_date) < new Date(newStartDate)) {
      useSnackbarStore().show($t('exclusiveOffers.alert_start_date'), "error");
      form.value.start_date = "";
    }
  }
);

watch(
  () => form.value.start_date,
  (newStartDate) => {
    if (form.value.end_date && new Date(form.value.end_date) < new Date(newStartDate)) {
      form.value.end_date = "";
    }
  }
);
</script>
