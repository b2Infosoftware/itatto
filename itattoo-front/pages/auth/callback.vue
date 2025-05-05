<template>
  <div class="auth-callback-container">
    <div v-if="isLoading" class="loading-screen">
      <svg
        class="loading-spinner"
        viewBox="0 0 50 50"
      >
        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
      </svg>
      <p class="loading-text">Processing authentication...</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "authentication",
  middleware: "guest",
});

const auth = useAuthStore();

useHead({
  title: "Auth Callback",
});

const route = useRoute();
const router = useRouter();

const token = route.query.token;
const error = route.query.error;
const isLoading = ref(true);

onMounted(async () => {
  if (token) {
    try {
      const authToken = useCookie("auth_token");
      authToken.value = token;

      await new Promise((resolve) => setTimeout(resolve, 1000));

      await auth.fetchUser();

      const settingsStore = useSettingsStore();
      const organisationStore = useOrganisationStore();
      await Promise.all([
        settingsStore.getSettings(),
        organisationStore.fetchData(),
      ]);

      // Arahkan pengguna berdasarkan jumlah organisasi & izin akses
      if (organisationStore.organisations.length > 1) {
        router.push({ name: "pick-organisation" });
      } else if (useCan("view", "dashboard")) {
        router.push({ name: "index" });
      } else {
        router.push({ name: "calendar" });
      }
    } catch (err) {
      console.error("Authentication failed:", err);
      // Handle error, e.g., redirect to login page
      router.push({ name: "login" });
    } finally {
      // Matikan loading setelah proses selesai
      isLoading.value = false;
    }
  } else if (error) {
    console.error("Authentication error:", error);
    // Handle error, e.g., redirect to login page
    router.push({ name: "login" });
    isLoading.value = false;
  }
});
</script>

<style scoped>
.auth-callback-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  background: var(--color-background);
}

.loading-screen {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}

.loading-text {
  font-size: 1rem;
  color: var(--color-text);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.path {
  stroke: var(--color-primary);
  stroke-linecap: round;
  animation: dash 1.5s ease-in-out infinite;
}

@keyframes dash {
  0% { stroke-dasharray: 1, 150; stroke-dashoffset: 0; }
  50% { stroke-dasharray: 90, 150; stroke-dashoffset: -35; }
  100% { stroke-dasharray: 90, 150; stroke-dashoffset: -125; }
}
</style>