<script setup>
import { ref, watch, computed, h } from "vue";
import { useRouter } from "vue-router";

definePageMeta({
  layout: "header",
  title: "Event Management",
});

const router = useRouter();
const config = useRuntimeConfig();

// --- SETUP API ---
const BASE_URL = process.server ? config.API_URL_INTERNAL : config.public.API_URL;
const API_URL = BASE_URL + "/api/events";

// Fetch Data
const { data, refresh } = await useFetch(API_URL);

const events = computed(() => {
  const raw = data.value;
  return (raw && (raw.data ?? raw)) || [];
});

const goToDetails = (id) => {
  router.push(`/event-manage/${id}`);
};

// --- FORMATTERS ---
const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  const d = new Date(dateStr);
  return d.toLocaleDateString("en-GB", { day: "numeric", month: "short", year: "2-digit" });
};

const formatMoney = (num) => {
  if (!num) return "0";
  return Number(num).toLocaleString("th-TH", { style: "currency", currency: "THB", minimumFractionDigits: 0 });
};

// Helper ดึงค่า (ใช้ชื่อ field ตาม DB)
const getEventName = (e) => e.name || "Untitled Event";
const getClientName = (e) => e.client_name || "-";
const getStartDate = (e) => e.start_date || "";
const getEndDate = (e) => e.end_date || "";

// --- STATUS ---
const getStatusInfo = (e) => {
  const startRaw = getStartDate(e);
  const endRaw = getEndDate(e);

  if (!startRaw || !endRaw) {
    return { label: "Unknown", cardBorder: "border-gray-200 hover:border-gray-300", badge: "bg-gray-100 text-gray-600", accent: "text-gray-400" };
  }
  const now = new Date();
  const start = new Date(startRaw);
  const end = new Date(endRaw);
  now.setHours(0,0,0,0); start.setHours(0,0,0,0); end.setHours(0,0,0,0);

  if (now > end) return { label: "Done", cardBorder: "border-purple-100 hover:border-purple-300", badge: "bg-purple-100 text-purple-600", accent: "text-purple-400" };
  if (now >= start && now <= end) return { label: "Active", cardBorder: "border-sky-100 hover:border-sky-300", badge: "bg-sky-100 text-sky-600", accent: "text-sky-400" };
  return { label: "Soon", cardBorder: "border-teal-100 hover:border-teal-300", badge: "bg-teal-100 text-teal-700", accent: "text-teal-500" };
};

// --- STATE ---
const isOpen = ref(false);
const isEditing = ref(false);

// ✅ แก้ไข 1: ตั้งชื่อตัวแปรให้ตรงกับ Database เป๊ะๆ
const createEmptyEvent = () => ({
  id: null,
  name: "",
  description: "",
  start_date: "",
  end_date: "",
  client_name: "",
  location: "",         // เพิ่ม location
  total_budget: null,   // แก้จาก total เป็น total_budget
  venue_name: "",
  venue_url: "",        // แก้จาก client_website เป็น venue_url
  accommodation_name: "", // แก้จาก commended_name เป็น accommodation_name
  accommodation_url: "",  // แก้จาก commended_website เป็น accommodation_url
  drive_link: "",       // แก้จาก online_drive เป็น drive_link
  team: [],
});

const newEvent = ref(createEmptyEvent());

watch(isOpen, (val) => {
  if (process.client) document.body.style.overflow = val ? "hidden" : "";
});

// --- ACTIONS ---
const openCreate = () => {
  isEditing.value = false;
  newEvent.value = createEmptyEvent();
  isOpen.value = true;
};

const editEvent = (event) => {
  isEditing.value = true;
  // Clone object เพื่อป้องกันการแก้ไขข้อมูลใน List โดยตรงก่อนกด Save
  newEvent.value = JSON.parse(JSON.stringify(event));
  
  // ตรวจสอบว่า field ไหนเป็น null ให้ใส่ค่า default กัน error ใน form
  if (!newEvent.value.team) newEvent.value.team = [];
  
  isOpen.value = true;
};

const closePopup = () => { isOpen.value = false; };

const deleteEvent = async (event) => {
  if (!confirm(`Delete "${event.name}"?`)) return;
  try {
    await $fetch(`${API_URL}/${event.id}`, { method: "DELETE" });
    await refresh();
  } catch (error) {
    console.error(error);
    alert("Error deleting event");
  }
};

const addTeamMember = () => newEvent.value.team.push({ name: "" });
const removeTeamMember = (index) => newEvent.value.team.splice(index, 1);

const saveEvent = async () => {
  try {
    const method = isEditing.value ? "PUT" : "POST";
    // ถ้า Edit ต้องต่อท้าย URL ด้วย ID
    const url = isEditing.value ? `${API_URL}/${newEvent.value.id}` : API_URL;
    
    await $fetch(url, { method, body: newEvent.value });
    
    await refresh(); // โหลดข้อมูลใหม่จาก Server
    closePopup();
  } catch (err) {
    console.error(err);
    // แสดง Error ที่ชัดเจนขึ้น (ถ้า Backend ส่งมา)
    alert("Error saving event: " + (err.data?.message || err.message));
  }
};

const FormField = (props, { slots }) => {
  return h('div', { class: 'flex flex-col gap-1' }, [
    h('label', { class: 'text-xs font-bold text-slate-500 ml-1 uppercase tracking-wider' }, props.label),
    slots.default?.()
  ])
}
</script>

<template>
  <div class="min-h-screen font-sans text-slate-800 pb-20 bg-[#FDFCFB]">
    
    <div class="absolute top-0 left-0 right-0 h-48 bg-gradient-to-b from-pink-50 via-orange-50 to-transparent -z-10 opacity-70"></div>

    <div class="p-4 max-w-4xl mx-auto">
      <div class="mb-6 flex justify-between items-end">
        <div>
          <h1 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-orange-400 tracking-tight">
            My Events
          </h1>
          <p class="text-slate-400 text-xs font-medium mt-1">Manage your projects ✨</p>
        </div>
      </div>

      <TransitionGroup name="list" tag="div" class="grid grid-cols-1 gap-3">
        <div
          v-for="event in events"
          :key="event.id"
          @click="goToDetails(event.id)"
          class="group relative bg-white rounded-2xl border p-4
                 cursor-pointer transition-all duration-200 ease-out shadow-sm
                 hover:-translate-y-1 hover:shadow-lg active:scale-[0.99]"
          :class="getStatusInfo(event).cardBorder"
        >
          <div class="flex items-center gap-4">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <span :class="`px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide ${getStatusInfo(event).badge}`">
                  {{ getStatusInfo(event).label }}
                </span>
                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider truncate">
                  {{ getClientName(event) }}
                </span>
              </div>

              <h2 class="text-lg font-bold text-slate-700 truncate group-hover:text-pink-500 transition-colors">
                {{ getEventName(event) }}
              </h2>

              <div class="flex items-center gap-4 mt-2 text-xs font-medium text-slate-400">
                <div class="flex items-center gap-1" :class="getStatusInfo(event).accent">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                  <span>{{ formatDate(getStartDate(event)) }} - {{ formatDate(getEndDate(event)) }}</span>
                </div>
                
                <div class="flex items-center gap-1 text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md">
                  <span>💰</span>
                  {{ formatMoney(event.total_budget) }}
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button @click.stop="editEvent(event)" class="action-btn bg-blue-50 text-blue-400 hover:bg-blue-100 hover:text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
              </button>
              <button @click.stop="deleteEvent(event)" class="action-btn bg-pink-50 text-pink-400 hover:bg-pink-100 hover:text-pink-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </div>
          </div>
        </div>
      </TransitionGroup>

      <div v-if="events.length === 0" class="flex flex-col items-center justify-center py-20 text-center opacity-60">
         <div class="text-4xl mb-2 animate-bounce">🎉</div>
         <p class="text-sm text-slate-400 font-medium">No events yet.</p>
      </div>
    </div>

    <button @click="openCreate" class="fixed bottom-6 right-6 bg-gradient-to-r from-pink-400 to-orange-400 text-white rounded-full p-3 shadow-lg shadow-pink-500/30 hover:scale-110 active:scale-95 transition-all duration-200 z-30">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
    </button>

    <Transition name="modal">
      <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm transition-opacity" @click="closePopup"></div>

        <div class="relative bg-white w-full max-w-lg max-h-[90vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden transform transition-all">
          <div class="flex justify-between items-center px-5 py-4 bg-gradient-to-r from-pink-50 to-orange-50 border-b border-orange-100">
            <h2 class="text-lg font-bold text-slate-700">
              {{ isEditing ? "Edit Event" : "New Event" }}
            </h2>
            <button @click="closePopup" class="w-7 h-7 flex items-center justify-center rounded-full bg-white text-slate-400 hover:text-pink-500 transition-colors text-sm">✕</button>
          </div>

          <form @submit.prevent="saveEvent" class="flex-1 overflow-y-auto px-5 py-5 space-y-4 bg-white">
            <FormField label="Event Name">
              <input v-model="newEvent.name" class="input-field font-bold" placeholder="Event Name" required />
            </FormField>

            <div class="grid grid-cols-2 gap-3">
              <FormField label="Start">
                <input v-model="newEvent.start_date" type="date" class="input-field" required />
              </FormField>
              <FormField label="End">
                <input v-model="newEvent.end_date" type="date" class="input-field" required />
              </FormField>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <FormField label="Client">
                <input v-model="newEvent.client_name" class="input-field" placeholder="Client" required />
              </FormField>
              <FormField label="Budget (THB)">
                <input v-model="newEvent.total_budget" type="number" class="input-field text-orange-500" placeholder="0" required />
              </FormField>
            </div>

            <FormField label="Location">
                <input v-model="newEvent.location" class="input-field" placeholder="City, Country" />
            </FormField>

            <FormField label="Note">
              <textarea v-model="newEvent.description" rows="2" class="input-field min-h-[60px]"></textarea>
            </FormField>

            <div class="p-3 bg-gray-50 rounded-xl border border-dashed border-gray-200 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <FormField label="Venue">
                        <input v-model="newEvent.venue_name" class="input-field bg-white" placeholder="Location Name" />
                    </FormField>
                    <FormField label="Link">
                        <input v-model="newEvent.venue_url" class="input-field bg-white" placeholder="URL" />
                    </FormField>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <FormField label="Accommodation">
                        <input v-model="newEvent.accommodation_name" class="input-field bg-white" placeholder="Hotel Name" />
                    </FormField>
                    <FormField label="Link">
                        <input v-model="newEvent.accommodation_url" class="input-field bg-white" placeholder="URL" />
                    </FormField>
                </div>

                <FormField label="Drive">
                    <input v-model="newEvent.drive_link" class="input-field bg-white" placeholder="URL" />
                </FormField>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 ml-1 uppercase tracking-wider">Team</label>
                 <div class="space-y-2 mt-1">
                    <div v-for="(member, index) in newEvent.team" :key="index" class="flex gap-2">
                       <input v-model="member.name" class="input-field py-1.5" placeholder="Name" />
                       <button type="button" @click="removeTeamMember(index)" class="text-pink-400 hover:text-pink-600 px-2">✕</button>
                    </div>
                 </div>
                <button type="button" @click="addTeamMember" class="mt-2 text-xs font-bold text-pink-500 hover:underline">+ Add Member</button>
             </div>
          </form>

          <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
            <button type="button" @click="closePopup" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-gray-200">Cancel</button>
            <button @click="saveEvent" class="px-5 py-2 rounded-xl bg-orange-400 text-white text-xs font-bold shadow-md hover:bg-orange-500">
               {{ isEditing ? 'Update' : 'Create' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.action-btn {
  @apply w-8 h-8 flex items-center justify-center rounded-xl transition-all duration-200 active:scale-95;
}

.input-field {
  @apply w-full px-3 py-2 bg-gray-50 border border-transparent rounded-xl text-sm text-slate-700
  focus:bg-white focus:border-pink-300 focus:ring-2 focus:ring-pink-100 outline-none transition-all;
}

.list-enter-active, .list-leave-active { transition: all 0.3s ease; }
.list-enter-from, .list-leave-to { opacity: 0; transform: scale(0.95); }

.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

.modal-enter-active .relative, .modal-leave-active .relative { transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-enter-from .relative, .modal-leave-to .relative { transform: scale(0.95); }
</style>