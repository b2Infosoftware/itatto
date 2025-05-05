<template>
  <div class="card p-4">
    <div class="card-title text-lg font-semibold">{{ title }}</div>
    
    <div class="space-y-4">
      <form @submit.prevent="handlerSubmit">
        <input-checkbox
              v-model="isStudioEnabled"
              id="studio-enable"
              >Enable Public Booking
          </input-checkbox>

        <div class="mb-8 form-group">
          <label for="description" class="block font-medium">Description</label>
          <textarea id="description" maxlength="140" v-model="description" rows="4" class="mt-2 border p-2 w-full rounded dark:bg-slate-950" placeholder="write your description max. 70 characters"></textarea>
        </div>
        
        <div class="mb-8 form-group">
          <label class="block font-medium">Upload Logo</label>
          <p class="text-gray-500 text-sm">Recommended size: 512x512 px for best quality.</p>
          <input type="file" @change="previewImage($event, 'photo')" class="mt-2 border p-2 w-full rounded" />
          <img v-if="photoPreview" :src="photoPreview" alt="Preview Foto" class="mt-2 h-48 w-48 object-cover rounded" />
        </div>
        
        <div class="mb-8 form-group">
          <label class="block font-medium">Upload Banner</label>
          <p class="text-gray-500 text-sm">Recommended size: 1200x400 px for best quality.</p>
          <input type="file" @change="previewImage($event, 'banner')" class="mt-2 border p-2 w-full rounded" />
          <img v-if="bannerPreview" :src="bannerPreview" alt="Preview Banner" class="mt-2 w-1/3 object-cover rounded" style="aspect-ratio: 3 / 1" />
        </div>

        <div class="form-group relative">
          <label class="block font-medium">Shareable URL</label>
          <div class="flex items-center mt-2 border p-2 w-full rounded bg-gray-100 dark:bg-slate-900 dark:border-gray-500">
            <button 
              type="button"
              @click="copyLink"
              @mouseleave="copied = false"
              class="p-2 text-gray-600 hover:text-blue-500 relative"
            >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 dark:text-gray-100">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h9a2.25 2.25 0 012.25 2.25v12a2.25 2.25 0 01-2.25 2.25h-9A2.25 2.25 0 015.25 18V6a2.25 2.25 0 012.25-2.25z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 7.5h6M9 12h6m-6 4.5h3" />
            </svg>
              <transition name="slide-up">
                <span 
                  v-if="copied"
                  class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded-md"
                >
                  Copied!
                </span>
              </transition>
            </button>
            <span class="truncate px-2">{{ studioLink }}</span>
          </div>
        </div>

        <div class="inline-flex justify-between mt-5">
          <input-submit-button class="primary" :loading="isLoding">
            {{ $t("general.save") }}
          </input-submit-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
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

const requestURL = useRequestURL();
const protocol = requestURL.protocol; 
const hostname = requestURL.hostname;
const port = requestURL.port; 

const title = "Public Booking Settings";
useHead({ title });

const description = ref("");
const isStudioEnabled = ref(false);
const studioLink = ref(null);
const photoPreview = ref(null);
const photoFile = ref(null);
const bannerFile = ref(null);
const bannerPreview = ref(null);
const isLoding = ref(false);
const publicid = ref(null);

const copied = ref(false);

const copyLink = () => {
  navigator.clipboard.writeText(studioLink.value);
  copied.value = true;
  setTimeout(() => (copied.value = false), 2000);
};

const previewImage = (event, type) => {
  const file = event.target.files[0];
  if (file) {
    if (type === 'photo') {
        photoFile.value = event.target.files[0];
      } else {
        bannerFile.value = event.target.files[0];
      }
    const reader = new FileReader();
    reader.onload = (e) => {
      if (type === 'photo') {
        photoPreview.value = e.target.result;
      } else {
        bannerPreview.value = e.target.result;
      }
    };
    reader.readAsDataURL(file);
  }
};

const fetchData = async () => {
  try {
    const res = await useApi("GET", 'show-setting-public-booking')
    if(res) {
      isStudioEnabled.value = res.is_open ? true : false;
      studioLink.value = `${protocol}//${hostname}${port ? `:${port}` : ''}/book/${res.search_url}`;
      photoPreview.value = res.media.find(item => item.media_type == 1)?.full_path
      bannerPreview.value = res.media.find(item => item.media_type == 2)?.full_path
      description.value = res.organisation.description || '';
    }
    
  } catch (error) {
    handlerSubmit(false);
    console.error("Failed to fetch public booking setting", error);
  }
}

const handlerSubmit = async (showSnackbar = true) => {
  
  const formData = {
    is_open: isStudioEnabled.value ? 1 : 0,
    description: description.value
  };

  try {
    isLoding.value = true;
    const res = await useApi("POST", "setting-public-booking", formData);

    if (res) {
      publicid.value = res.id;
      isStudioEnabled.value = res.is_open ? true : false;
    }

    const deletePromises = [];
    if (photoFile.value) {
      const photo = res.media?.find((item) => item.media_type == 1);
      if (photo) deletePromises.push(useApi("DELETE", `media/${photo.id}`));
    }
    if (bannerFile.value) {
      const banner = res.media?.find((item) => item.media_type == 2);
      if (banner) deletePromises.push(useApi("DELETE", `media/${banner.id}`));
    }
    
    await Promise.all(deletePromises);
    const uploadPromises = [];
    if (photoFile.value) {
      const uploadPhotoForm = new FormData();
      uploadPhotoForm.append("setting_public_booking_id", res.id);
      uploadPhotoForm.append("attachment", photoFile.value);
      uploadPhotoForm.append("media_type", 1);
      uploadPromises.push(useApi("POST", "media/upload", uploadPhotoForm));
    }

    if (bannerFile.value) {
      const uploadBannerForm = new FormData();
      uploadBannerForm.append("setting_public_booking_id", res.id);
      uploadBannerForm.append("attachment", bannerFile.value);
      uploadBannerForm.append("media_type", 2);
      uploadPromises.push(useApi("POST", "media/upload", uploadBannerForm));
    }

    await Promise.all(uploadPromises);
    await fetchData();
    
    if (showSnackbar) {
      useSnackbarStore().show("Public booking setting successfully updated!", 'success');
    }
  } catch (error) {
    if (showSnackbar) {
      useSnackbarStore().show("Failed to update public booking setting", 'error');
    }
  } finally {
    isLoding.value = false;
  }
};


onMounted( async () => {
  await fetchData()
})
</script>