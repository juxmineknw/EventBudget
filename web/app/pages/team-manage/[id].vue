<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import { useSettingsApi } from "~/composables/useSettingsApi";

definePageMeta({
  layout: "header",
  title: "Team Management",
});

const route = useRoute();
const { getTeamMember } = useSettingsApi();

const loading = ref(true);
const member = ref(null);

// Base URL สำหรับรูปภาพ (แยกตัวแปรไว้จะได้แก้ง่ายๆ)
const STORAGE_URL = "http://localhost:8000/storage/";

// Computed: รวมชื่อ
const fullName = computed(() => {
  if (!member.value) return "";
  return member.value.personalname || `${member.value.given_name} ${member.value.surname}`;
});

// Computed: รูปโปรไฟล์
const profileImageUrl = computed(() => {
  if (member.value?.profile_image) {
    return `${STORAGE_URL}${member.value.profile_image}`;
  }
  return "https://ui-avatars.com/api/?name=" + fullName.value + "&background=random";
});

// Computed: รูป Passport (เพิ่มใหม่)
const passportImageUrl = computed(() => {
  if (member.value?.passport_image) {
    return `${STORAGE_URL}${member.value.passport_image}`;
  }
  return null;
});

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
  <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-4xl mx-auto mb-6">
      <NuxtLink 
        to="/team-manage/teamdetails" 
        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        กลับไปรายการทีม
      </NuxtLink>
    </div>

    <div v-if="loading" class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm p-10 text-center">
      <div class="animate-pulse flex flex-col items-center">
        <div class="rounded-full bg-gray-200 h-32 w-32 mb-4"></div>
        <div class="h-6 bg-gray-200 w-1/3 rounded mb-2"></div>
        <div class="h-4 bg-gray-200 w-1/4 rounded"></div>
      </div>
    </div>

    <div v-else class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
      
      <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-700 relative"></div>

      <div class="px-8 pb-8">
        <div class="relative flex flex-col sm:flex-row items-center sm:items-end -mt-16 mb-8 gap-6">
          <div class="relative">
            <img 
              :src="profileImageUrl"
              class="w-32 h-32 sm:w-40 sm:h-40 object-cover rounded-full border-4 border-white shadow-lg bg-white"
              alt="Profile"
            />
            <span class="absolute bottom-2 right-2 w-5 h-5 bg-green-400 border-2 border-white rounded-full"></span>
          </div>

          <div class="text-center sm:text-left mb-2">
            <h1 class="text-3xl font-bold text-gray-900">{{ fullName }}</h1>
            <p class="text-gray-500 font-medium">{{ member.nationality }}</p>
          </div>
        </div>

        <div class="border-b border-gray-200 mb-8"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
          
          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              ข้อมูลส่วนตัว
            </h3>
            <dl class="space-y-4">
              <div class="grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                <dd class="text-sm text-gray-900 col-span-2 font-medium">{{ member.given_name }} {{ member.surname }}</dd>
              </div>
              <div class="grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">ชื่อ (ไทย)</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ member.given_name_th || '-' }} {{ member.surname_th || '-' }}</dd>
              </div>
              <div class="grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">วันเกิด</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ member.dob }}</dd>
              </div>
              <div class="grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">เพศ</dt>
                <dd class="text-sm text-gray-900 col-span-2">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                    :class="member.sex === 'Male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'">
                    {{ member.sex }}
                  </span>
                </dd>
              </div>
            </dl>
          </div>

          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
              ข้อมูลหนังสือเดินทาง
            </h3>
            <dl class="space-y-4">
              <div class="grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Passport No.</dt>
                <dd class="text-sm text-gray-900 col-span-2 font-mono">{{ member.passport_no }}</dd>
              </div>
              <div class="grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Personal No.</dt>
                <dd class="text-sm text-gray-900 col-span-2 font-mono">{{ member.personal_no }}</dd>
              </div>
              <div class="grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">วันที่ออก</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ member.date_issue }}</dd>
              </div>
              <div class="grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">วันหมดอายุ</dt>
                <dd class="text-sm text-red-600 col-span-2 font-medium">{{ member.date_expiry }}</dd>
              </div>
              <div class="grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">ผู้ออกเอกสาร</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ member.issuing_authority }}</dd>
              </div>
              
              <div v-if="passportImageUrl" class="pt-4 mt-4 border-t border-dashed border-gray-200">
                <dt class="text-sm font-medium text-gray-500 mb-2">รูปหน้าหนังสือเดินทาง</dt>
                <dd class="col-span-3">
                  <a 
                    :href="passportImageUrl" 
                    target="_blank" 
                    class="block relative group overflow-hidden rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all"
                  >
                    <img 
                      :src="passportImageUrl" 
                      class="w-full h-48 object-cover bg-gray-50 transition-transform duration-300 group-hover:scale-105" 
                      alt="Passport Image" 
                    />
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all flex items-center justify-center">
                        <span class="text-white opacity-0 group-hover:opacity-100 bg-black bg-opacity-50 px-2 py-1 rounded text-xs">คลิกเพื่อดูรูปใหญ่</span>
                    </div>
                  </a>
                </dd>
              </div>

            </dl>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>