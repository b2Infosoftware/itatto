<template>
    <div class="card-title text-lg font-semibold">{{ $t('vipSetting.title') }}</div>

    <div class="table-wrapper">
        <div class="w-full block text-right mb-5">
            <button @click="openCreateModal" class="btn primary">
                {{ $t('vipSetting.create_vip') }}
            </button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>{{ $t('vipSetting.label_vip') }}</th>
                    <th>{{ $t('vipSetting.action_vip') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in vips" :key="item.id">
                    <td>
                        <strong class="inline-block w-3 h-3 rounded-full mr-3"
                            :style="{ backgroundColor: item.color || '#facc15' }"></strong>
                        {{ item.label }}
                    </td>
                    <td class="actions">
                        <button @click="openEditModal(item)" class="btn btn-sm btn-icon">
                            <nuxt-icon filled name="pencil" />
                        </button>
                        <button @click="deleteCustomer(item)" class="btn btn-sm btn-icon ml-4">
                            <nuxt-icon filled name="trash" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <ui-confirm-modal type="danger" ref="confirmModal">
        <template #confirm>{{ $t('vipSetting.yes_delete') }}</template>
    </ui-confirm-modal>

    <ui-modal @close="showModal = false" :visible="showModal">
        <template #title>{{ isEditing ? $t('vipSetting.edit_vip') : $t('vipSetting.create_vip') }}</template>
        <template #description>ㅤ</template>
        <template #content>
            <form @submit.prevent="isEditing ? handleEdit() : handleCreate()"
                class="flex flex-col gap-5 text-left -mt-5">
                <input-text v-model="form.label" :placeholder="$t('vipSetting.label_vip')" :label="$t('vipSetting.input_label')" />

                <label class="font-bold -mb-4">{{ $t('vipSetting.input_color') }}</label>
                <input v-model="form.color" type="color" class="w-[100px] h-[80px] border p-1 rounded" />

                <div class="w-full block text-right border-t border-t-gray-300 pt-5 dark:border-t-gray-500">
                    <input-submit-button :loading="isLodding" type="submit"
                        class="btn primary text-right w-18">{{ $t('vipSetting.save') }}</input-submit-button>
                </div>
            </form>
        </template>
    </ui-modal>
</template>

<script setup>
const { $t } = useNuxtApp();
import { ref, onMounted } from 'vue';
import { useCustomerStore } from '~/stores/customerStore';

const customerStore = useCustomerStore();
const confirmModal = ref(null);
const showModal = ref(false);
const isEditing = ref(false);
const vips = ref([]);
const isLodding = ref(false);

const form = ref({
    label: '',
    color: '#000000'
});

const openEditModal = (item) => {
    isEditing.value = true;
    form.value = {
        id: item.id,
        label: item.label,
        color: item.color || '#000000'
    };
    showModal.value = true;
};

const openCreateModal = () => {
    isEditing.value = false;
    form.value = {
        id: null,
        label: '',
        color: '#000000'
    };
    showModal.value = true;
};

const handleCreate = async () => {
    try {
        isLodding.value = true;
        const payload = {
            label: form.value.label,
            color: form.value.color
        };

        const response = await useApi('POST', 'vip', payload);
        if (response) {
            useSnackbarStore().show(response.message, 'success');
            customerStore.fetchItems();
            showModal.value = false;
            fetchVip();
        }
    } catch (error) {
        useSnackbarStore().show($t('vipSetting.fail_create'), 'error');
    } finally {
        isLodding.value = false;
    }
};

const handleEdit = async () => {
    try {
        isLodding.value = true;
        const payload = {
            label: form.value.label,
            color: form.value.color
        };

        const response = await useApi('PATCH', `vip/${form.value.id}`, payload);
        if (response) {
            useSnackbarStore().show(response.message, 'success');
            customerStore.fetchItems();
            showModal.value = false;
            fetchVip();
        }
    } catch (error) {
        useSnackbarStore().show($t('vipSetting.fail_update'), 'error');
    } finally {
        isLodding.value = false;
    }
};

const fetchVip = async () => {
    const { data } = await useApi("GET", "vip");
    if (data) {
        vips.value = data.data || data;
    }
};

const deleteCustomer = async (item) => {
    const confirmed = await confirmModal.value.open();
    if (Boolean(confirmed)) {
        const response = await useApi('DELETE', 'vip/' + item.id);
        if (response) {
            useSnackbarStore().show(response.message, 'success');
            customerStore.fetchItems();
            fetchVip();
        }
    }
};

onMounted(() => {
    fetchVip();
});
</script>