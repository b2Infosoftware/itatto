<template>
    <div class="tabs flex mb-4">
        <button v-for="tab in tabs" :key="tab.value" @click="activeTab = tab.value"
            :class="['py-2 px-4 text-sm font-semibold hover:text-[#28C76F] mr-5', activeTab === tab.value ? 'bg-[#28C76F] text-sm rounded-md text-white font-semibold hover:text-white' : '']">
            {{ tab.label }}
        </button>
    </div>

    <div class="card p-4">
        <div v-if="activeTab === 'vipSettings'">
            <form-vipSettings />
        </div>
        <div v-else>
            <form-exclusiveOffers />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const tabs = [
    { label: 'VIP Settings', value: 'vipSettings' },
    { label: 'Exclusive Offers', value: 'exclusiveOffers' }
];
const activeTab = ref('vipSettings');

const { $t } = useNuxtApp();
definePageMeta({
    layout: "settings",
    middleware: [
        "subscribed",
        function () {
            if (!useCan("edit", "calendar-settings")) {
                return navigateTo({ name: "index" });
            }
        },
    ],
});

const title = "VIP Settings";
useHead({ title });
</script>