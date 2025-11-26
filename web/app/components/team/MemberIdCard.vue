<script setup>
import { computed } from 'vue';
import coverImg from '~/assets/Profile.png';

const props = defineProps({
  member: { type: Object, required: true },
  loading: Boolean
});

const emit = defineEmits(['view-passport']);

// Config
const STORAGE_URL = "http://localhost:8000/storage/";
const FIXED_COVER_IMAGE = coverImg;
// Computed
const fullName = computed(() => props.member?.personalname || `${props.member?.given_name} ${props.member?.surname}`);

const profileImageUrl = computed(() => {
  if (props.member?.profile_image) return `${STORAGE_URL}${props.member.profile_image}`;
  return `https://ui-avatars.com/api/?name=${fullName.value}&background=random`;
});

const passportImageUrl = computed(() => {
  if (props.member?.passport_image) return `${STORAGE_URL}${props.member.passport_image}`;
  return null;
});
</script>

<template>
  <div class="w-full max-w-6xl bg-white rounded-2xl shadow-xl overflow-hidden relative border border-gray-200 font-sans text-sm flex flex-col md:flex-row">
    
    <div v-if="loading" class="w-full p-16 flex flex-col items-center justify-center min-h-[400px]">
      <div class="rounded-full bg-gray-200 h-24 w-24 mb-6 animate-pulse"></div>
      <div class="h-4 bg-gray-200 w-48 rounded animate-pulse"></div>
    </div>

    <div v-else class="flex flex-col md:flex-row w-full min-h-[450px]">
      
      <div class="w-full md:w-80 bg-gray-50 border-r border-gray-100 flex flex-col shrink-0">
        <div class="h-48 w-full relative bg-gray-300">
          <img :src="FIXED_COVER_IMAGE" class="w-full h-full object-cover opacity-90" alt="Cover" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
        </div>
        
        <div class="px-6 pb-6 flex flex-col items-center -mt-20 relative z-10">
          <img :src="profileImageUrl" class="w-40 h-40 object-cover rounded-full border-[6px] border-white shadow-lg bg-white" alt="Profile" />
          
          <div class="text-center mt-4 w-full">
            <h1 class="text-2xl font-bold text-gray-900 leading-tight break-words">{{ fullName }}</h1>
            <div class="inline-flex items-center gap-2 mt-2 px-3 py-1 bg-white border border-gray-200 rounded-full shadow-sm">
                <p class="text-sm text-gray-600 font-medium">{{ member.nationality }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="flex-1 p-8 md:p-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 h-full">
          
          <div class="space-y-6">
             <div class="flex items-center gap-3 border-b border-gray-100 pb-3">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <h3 class="text-base font-bold text-gray-600 uppercase tracking-wider">ข้อมูลส่วนตัว</h3>
             </div>

             <div class="grid grid-cols-[120px_1fr] gap-y-4 items-center text-base">
                <span class="text-gray-500 font-medium">ชื่อเต็ม:</span>
                <span class="font-semibold text-gray-900">{{ member.given_name }} {{ member.surname }}</span>

                <span class="text-gray-500 font-medium">ชื่อ (ไทย):</span>
                <span class="text-gray-800">{{ member.given_name_th || '-' }} {{ member.surname_th || '-' }}</span>

                <span class="text-gray-500 font-medium">วันเกิด:</span>
                <span class="text-gray-800">{{ member.dob }}</span>

                <span class="text-gray-500 font-medium">เพศ:</span>
                <span>
                   <span class="px-3 py-1 text-sm font-bold rounded-md uppercase tracking-wide" 
                      :class="member.sex === 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700'">
                      {{ member.sex }}
                   </span>
                </span>
             </div>
          </div>

          <div class="space-y-6 flex flex-col h-full bg-gray-50/50 rounded-xl p-6 border border-gray-100">
             <div class="flex items-center gap-3 border-b border-gray-200 pb-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                </div>
                <h3 class="text-base font-bold text-gray-600 uppercase tracking-wider">Passport Details</h3>
             </div>

             <div class="grid grid-cols-[120px_1fr] gap-y-4 items-center text-base">
                <span class="text-gray-500 font-medium">Passport No.:</span>
                <span class="font-mono font-bold text-gray-900 text-xl tracking-wide">{{ member.passport_no }}</span>

                <span class="text-gray-500 font-medium">Personal ID:</span>
                <span class="font-mono text-gray-800">{{ member.personal_no }}</span>

                <span class="text-gray-500 font-medium">วันที่ออก:</span>
                <span class="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded">{{ member.date_issue }}</span>
                

                <span class="text-gray-500 font-medium">วันหมดอายุ:</span>
                <span class="text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded">{{ member.date_expiry }}</span>
                
                <span class="text-gray-500 font-medium">ผู้ออกเอกสาร:</span>
                <span class="text-gray-800 italic">{{ member.issuing_authority }}</span>
             </div>

             <div v-if="passportImageUrl" class="mt-auto pt-4">
                <button 
                  @click="emit('view-passport', passportImageUrl)"
                  class="w-full py-3 bg-white text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800 text-sm font-bold rounded-lg border border-indigo-200 shadow-sm hover:shadow transition-all flex items-center justify-center gap-2"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                  ดูรูปหน้า Passport
                </button>
             </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>