<script setup>
import { ref, computed } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const eventId = route.params.id;

// runtime config จาก nuxt.config
const config = useRuntimeConfig();

// ที่เก็บรูปจาก Laravel storage
const STORAGE_URL = "http://localhost:8000/storage/";

// base URL ของ API (ไม่ต้องใส่ /events ตรงนี้)
const API_BASE = config.public.API_URL || "http://localhost:8000/api";

// ดึงข้อมูล event เดียว
const { data, pending, error } = await useFetch(
  () => `${API_BASE}/events/${eventId}`,
  {
    method: "GET",
    credentials: "include", // ถ้าใช้ Sanctum / cookie
    key: `event-${eventId}`,
    server: false, // ให้ fetch ฝั่ง client เท่านั้น
  }
);

// แปลง data ให้ใช้ง่ายใน template
const event = computed(() => {
  const rawData = data.value?.data || data.value || {};
  return rawData;
});

// รูปโปรไฟล์ team member
const getProfileImage = (member) => {
  if (member?.profile_image) {
    return `${STORAGE_URL}${member.profile_image}`;
  }
  const name = member?.name || "User";
  return `https://ui-avatars.com/api/?name=${name}&background=ffedd5&color=c2410c&size=128&rounded=true&bold=true`;
};

// ฟอร์แมตช่วงวันที่
const formatDateRange = (start, end) => {
  if (!start) return "-";
  const formatDate = (d) =>
    new Date(d).toLocaleDateString("en-GB", {
      day: "numeric",
      month: "short",
      year: "2-digit",
    });
  return end ? `${formatDate(start)} - ${formatDate(end)}` : formatDate(start);
};

// bottom menu
const activeTab = ref("Details");
const bottomMenu = [
  { name: "Details", icon: "calendar" },
  { name: "Expenses", icon: "wallet" },
  { name: "Budget", icon: "chart" },
  { name: "Schedule", icon: "clock" },
  { name: "Tasks", icon: "clipboard" },
];
</script>


<template>
  <div class="min-h-screen bg-[#FDFCFB] font-sans text-slate-700 pb-28 relative">
    
    <div v-if="pending" class="flex flex-col items-center justify-center h-screen">
       <div class="animate-pulse w-16 h-16 bg-orange-100 rounded-full mb-4"></div>
       <p class="text-slate-400 text-sm font-medium animate-pulse">Loading event details...</p>
    </div>

    <div v-else-if="error" class="flex flex-col items-center justify-center h-screen text-center px-4">
       <div class="text-5xl mb-4 opacity-50">⚠️</div>
       <h3 class="text-lg font-bold text-slate-600">Oops! Something went wrong.</h3>
       <p class="text-sm text-slate-400 mt-1 max-w-xs mx-auto">{{ error.message }}</p>
       <NuxtLink to="/event-manage/eventname" class="mt-6 px-6 py-2 bg-orange-100 text-orange-600 rounded-full font-bold hover:bg-orange-200 transition-colors">
         Go Back
       </NuxtLink>
    </div>

    <div v-else>
      
      <div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-b from-orange-100 via-pink-50 to-transparent -z-10 opacity-60"></div>

      <nav class="sticky top-0 z-40 px-4 py-3 bg-white/80 backdrop-blur-md border-b border-orange-100/50 flex items-center justify-between shadow-sm transition-all">
        <div class="flex items-center gap-3">
          <NuxtLink to="/event-manage/eventname" class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-400 hover:text-orange-500 hover:border-orange-200 transition-all shadow-sm active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
          </NuxtLink>
          <span class="text-lg font-bold text-slate-800 tracking-tight">Event Details</span>
        </div>
        <div class="text-[10px] font-bold text-orange-500 uppercase tracking-wider bg-orange-50 border border-orange-100 px-3 py-1.5 rounded-lg shadow-sm max-w-[120px] truncate">
          {{ event.client_name || event.client || 'No Client' }}
        </div>
      </nav>

      <header class="px-6 py-8">
        <h1 class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-pink-500 leading-tight mb-4 break-words">
          {{ event.name || event.event_name || 'Untitled Event' }}
        </h1>
        <div class="bg-white/60 p-5 rounded-2xl border border-slate-100 shadow-sm backdrop-blur-sm">
           <p class="text-slate-500 text-sm leading-relaxed">
             {{ event.description || "No description provided for this event." }}
           </p>
        </div>
      </header>

      <div class="px-4 space-y-3">
        
        <div class="info-card">
          <div class="icon-box text-orange-500 bg-orange-50 ring-1 ring-orange-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
          </div>
          <div>
            <p class="label">Date Range</p>
            <p class="value font-bold text-slate-800">
              {{ formatDateRange(event.start_date || event.dateRange, event.end_date) }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="info-card">
            <div class="icon-box text-pink-500 bg-pink-50 ring-1 ring-pink-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            </div>
            <div class="min-w-0">
              <p class="label">Client</p>
              <p class="value truncate">{{ event.client_name || event.client || '-' }}</p>
            </div>
          </div>
          <div class="info-card">
            <div class="icon-box text-blue-500 bg-blue-50 ring-1 ring-blue-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="min-w-0">
              <p class="label">Location</p>
              <p class="value truncate">{{ event.location || event.country || '-' }}</p>
            </div>
          </div>
        </div>

        <a 
          :href="event.venue_url || event.venueUrl || '#'" 
          :target="event.venue_url || event.venueUrl ? '_blank' : ''"
          class="info-card group cursor-pointer hover:border-orange-200 hover:shadow-md hover:-translate-y-0.5 transition-all"
        >
          <div class="icon-box text-purple-500 bg-purple-50 ring-1 ring-purple-100 group-hover:bg-purple-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="label">Venue</p>
            <div class="flex items-center gap-1">
               <p class="value group-hover:text-orange-500 transition-colors truncate">
                 {{ event.venue_name || event.venue || 'No Venue Specified' }}
               </p>
               <svg v-if="event.venue_url || event.venueUrl" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-300 group-hover:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </div>
          </div>
        </a>

        <a 
          :href="event.accommodation_url || event.accommodationUrl || '#'" 
          :target="event.accommodation_url || event.accommodationUrl ? '_blank' : ''"
          class="info-card group cursor-pointer hover:border-orange-200 hover:shadow-md hover:-translate-y-0.5 transition-all"
        >
          <div class="icon-box text-teal-500 bg-teal-50 ring-1 ring-teal-100 group-hover:bg-teal-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="label">Accommodation</p>
            <div class="flex items-center gap-1">
               <p class="value group-hover:text-orange-500 transition-colors truncate">
                 {{ event.accommodation_name || event.accommodation || 'Not Booked' }}
               </p>
               <svg v-if="event.accommodation_url || event.accommodationUrl" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-300 group-hover:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </div>
          </div>
        </a>

        <a 
          :href="event.drive_link || event.driveUrl || '#'" 
          :target="event.drive_link || event.driveUrl ? '_blank' : ''"
          class="info-card group cursor-pointer hover:border-orange-200 hover:shadow-md hover:-translate-y-0.5 transition-all"
        >
          <div class="icon-box text-indigo-500 bg-indigo-50 ring-1 ring-indigo-100 group-hover:bg-indigo-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" /></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="label">Online Drive</p>
            <div class="flex items-center gap-1">
               <p class="value group-hover:text-orange-500 transition-colors truncate">
                 {{ (event.drive_link || event.driveUrl) ? 'Access Files' : 'No Link' }}
               </p>
               <svg v-if="event.drive_link || event.driveUrl" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-300 group-hover:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </div>
          </div>
        </a>

        <div class="mt-6 bg-orange-50/60 border border-orange-100 rounded-3xl p-5 relative overflow-hidden">
           <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-100 rounded-full opacity-50 blur-xl"></div>

           <div class="flex items-center gap-3 mb-4 relative z-10">
              <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-500 text-white shadow-md shadow-orange-200">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
              </div>
              <h2 class="text-lg font-black text-orange-800 tracking-wide">Team</h2>
              <span class="ml-auto bg-white text-orange-500 text-[10px] font-bold px-2.5 py-1 rounded-lg border border-orange-100 shadow-sm">
                {{ event.team?.length || 0 }} Members
              </span>
           </div>

           <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 relative z-10">
              <template v-if="event.team && event.team.length > 0">
                <div 
                  v-for="member in event.team" 
                  :key="member.id || member.name"
                  class="flex items-center gap-3 bg-white p-3 rounded-2xl border border-orange-100/50 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5"
                >
                   <img 
                     :src="getProfileImage(member)" 
                     class="w-10 h-10 rounded-full object-cover border-2 border-orange-50 shadow-sm"
                     alt="Profile"
                   />
                   <div class="flex-1 min-w-0">
                      <p class="text-sm font-bold text-slate-700 truncate">{{ member.name }}</p>
                      <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Staff</p>
                   </div>
                </div>
              </template>

              <div v-else class="col-span-full text-center py-6 bg-white/50 rounded-xl border border-dashed border-orange-200 text-slate-400 text-xs font-medium">
                 No team members assigned yet.
              </div>
           </div>
        </div>

      </div>
    </div>

    <div class="fixed bottom-0 left-0 w-full z-50 bg-white border-t border-slate-100 shadow-[0_-5px_20px_rgba(0,0,0,0.03)]">
      <div class="grid grid-cols-5 h-[76px] pb-2"> 
        <button 
          v-for="menu in bottomMenu" 
          :key="menu.name"
          @click="activeTab = menu.name"
          class="relative flex flex-col items-center justify-center gap-1 transition-all duration-300 group pt-2"
        >
          <span 
            class="absolute top-0 w-full h-[3px] bg-orange-400 transition-all duration-300 origin-center"
            :class="activeTab === menu.name ? 'scale-x-50 opacity-100' : 'scale-x-0 opacity-0'"
          ></span>

          <div 
            class="transition-transform duration-300"
            :class="activeTab === menu.name ? '-translate-y-1 text-orange-500 drop-shadow-sm' : 'text-slate-400 group-hover:text-slate-600'"
          >
            <svg v-if="menu.icon === 'calendar'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            <svg v-else-if="menu.icon === 'wallet'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
            <svg v-else-if="menu.icon === 'chart'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            <svg v-else-if="menu.icon === 'clock'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <svg v-else-if="menu.icon === 'clipboard'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
          </div>
          
          <span 
            class="text-[10px] font-bold tracking-wide transition-colors duration-300"
            :class="activeTab === menu.name ? 'text-orange-500' : 'text-slate-300 group-hover:text-slate-400'"
          >
            {{ menu.name }}
          </span>
        </button>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* Reuse styles for consistency */
.info-card {
  @apply flex items-center gap-4 px-5 py-4 bg-white rounded-2xl border border-slate-100 shadow-sm transition-all;
}
.icon-box {
  @apply w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-sm;
}
.label {
  @apply text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5;
}
.value {
  @apply text-sm font-bold text-slate-700;
}
</style>