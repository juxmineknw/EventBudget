<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useSettingsApi } from "~/composables/useSettingsApi";

// Import Components ที่เราแยกไว้
import MemberIdCard from "~/components/team/MemberIdCard.vue";
import PassportModal from "~/components/team/PassportModal.vue";

definePageMeta({
  layout: "header",
  title: "Team Management",
});

const route = useRoute();
const { getTeamMember } = useSettingsApi();

const loading = ref(true);
const member = ref(null);
const showModal = ref(false);
const selectedImage = ref("");

// ฟังก์ชันเปิดรูป (รับ URL มาจาก Component Card)
const handleViewPassport = (url) => {
  selectedImage.value = url;
  showModal.value = true;
};

onMounted(async () => {
  try {
    const id = route.params.id;
    member.value = await getTeamMember(id);
  } catch (error) {
    console.error("Failed to fetch member", error);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-6 px-4">
    
    <div class="relative">
       <NuxtLink 
          to="/team-manage/teamdetails" 
          class="absolute top-2 left-2 z-10 bg-black/40 hover:bg-black/60 text-white rounded-full p-1.5 transition-colors backdrop-blur-md"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
        </NuxtLink>

        <MemberIdCard 
          :member="member" 
          :loading="loading" 
          @view-passport="handleViewPassport"
        />
    </div>

    <PassportModal 
      :is-open="showModal" 
      :image-url="selectedImage" 
      @close="showModal = false"
    />

  </div>
</template>