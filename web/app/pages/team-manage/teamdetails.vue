<script setup>
import { ref, onMounted } from "vue";
import UiCard from "~/components/ui/UiCard.vue";
import UiButton from "~/components/ui/UiButton.vue";
import UiInput from "~/components/ui/UiInput.vue";
import EventFabButton from "~/components/event/EventFabButton.vue";
import { useSettingsApi } from "~/composables/useSettingsApi";

definePageMeta({
  layout: "header",
  title: "Team Management",
});

// API
const {
  teamMembers,
  loadTeamMembers,
  createTeamMember,
  updateTeamMember,
  deleteTeamMember,
} = useSettingsApi();

const isModalOpen = ref(false);
const isEditing = ref(false);
const loading = ref(true);

const form = ref({
  id: null,
  personalname: "",
  given_name: "",
  surname: "",
  given_name_th: "",
  surname_th: "",
  passport_no: "",
  nationality: "",
  dob: "",
  sex: "",
  date_issue: "",
  date_expiry: "",
  personal_no: "",
  issuing_authority: "",
  passport_image: "",
  profile_image: "",
});

const passportPreview = ref(null);
const profilePreview = ref(null);
const scanning = ref(false);

// LOAD MEMBER LIST
onMounted(async () => {
  await loadTeamMembers();
  loading.value = false;
});

// RESET FORM
const resetForm = () => {
  Object.assign(form.value, {
    id: null,
    personalname: "",
    given_name: "",
    surname: "",
    given_name_th: "",
    surname_th: "",
    passport_no: "",
    nationality: "",
    dob: "",
    sex: "",
    date_issue: "",
    date_expiry: "",
    personal_no: "",
    issuing_authority: "",
    passport_image: "",
    profile_image: "",
  });

  passportPreview.value = null;
  profilePreview.value = null;
};

const toBase64 = (file) =>
  new Promise((resolve) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.readAsDataURL(file);
  });

// Mock Scan Function (แทนที่ API จริงของคุณ)
const scanPassport = async (file) => {
    // ใส่ Logic เดิมของคุณตรงนี้
    return { fields: {} }; 
};

const onPassportSelected = async (e) => {
  const file = e.target.files[0];
  if (!file) return;

  passportPreview.value = URL.createObjectURL(file);
  // เก็บรูปเข้า Form
  form.value.passport_image = await toBase64(file);
  
  scanning.value = true;

  try {
    const result = await scanPassport(file);
    // ... (Logic เดิมของคุณ) ...
  } catch (err) {
    console.error("MRZ Scan failed", err);
  } finally {
    scanning.value = false;
  }
};

const onProfileSelected = async (e) => {
  const file = e.target.files[0];
  if (!file) return;
  profilePreview.value = URL.createObjectURL(file);
  form.value.profile_image = await toBase64(file);
};

const handleSave = async () => {
  const payload = { ...form.value };
  if (isEditing.value) {
    await updateTeamMember(form.value.id, payload);
  } else {
    await createTeamMember(payload);
  }
  isModalOpen.value = false;
  await loadTeamMembers();
};

const handleDelete = (item) => {
  if (confirm(`Delete "${item.personalname || item.given_name}" ?`)) {
    deleteTeamMember(item.id);
  }
};

const openEdit = (item) => {
  isEditing.value = true;
  form.value = { ...item };
  passportPreview.value = item.passport_image ? `http://localhost:8000/storage/${item.passport_image}` : null;
  profilePreview.value = item.profile_image ? `http://localhost:8000/storage/${item.profile_image}` : null;
  isModalOpen.value = true;
};

const openCreate = () => {
  isEditing.value = false;
  resetForm();
  isModalOpen.value = true;
};
</script>

<template>
  <div class="w-full flex flex-col items-center px-4 py-6 font-sans">

    <div v-if="!loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-5xl mx-auto">
      <div v-for="m in teamMembers" :key="m.id" class="w-full flex flex-col items-center">
         <NuxtLink :to="`/team-manage/${m.id}`" class="block w-full">
            <UiCard class="w-full p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
               <div class="flex items-center gap-4">
                  <img v-if="m.profile_image" :src="`http://localhost:8000/storage/${m.profile_image}`" class="w-14 h-14 rounded-full object-cover border border-gray-200" />
                  <div v-else class="w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg">
                     {{ (m.personalname || "?")[0] }}
                  </div>
                  <div class="flex-1 min-w-0">
                     <div class="font-bold text-gray-900 truncate">{{ m.personalname || "No Name" }}</div>
                     <div class="text-xs text-gray-500 truncate">{{ m.given_name }} {{ m.surname }}</div>
                  </div>
               </div>
            </UiCard>
         </NuxtLink>
         <div class="flex gap-2 mt-2 w-full">
            <button @click="openEdit(m)" class="flex-1 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition">Edit</button>
            <button @click="handleDelete(m)" class="flex-1 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100 transition">Delete</button>
         </div>
      </div>
    </div>

    <EventFabButton @click="openCreate" class="fixed bottom-8 right-8 z-[50]" />

    <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity">
      
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden animate-scaleIn">
        
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-white">
          <div>
             <h2 class="text-lg font-bold text-gray-900">{{ isEditing ? "Edit Information" : "Add New Member" }}</h2>
             <p class="text-xs text-gray-500">กรอกข้อมูลพนักงานหรือสแกนจาก Passport</p>
          </div>
          <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
          
          <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            
            <div class="md:col-span-4 space-y-6">
              
              <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col items-center text-center">
                 <label class="relative cursor-pointer group">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-100">
                       <img v-if="profilePreview" :src="profilePreview" class="w-full h-full object-cover" />
                       <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                       </div>
                    </div>
                    <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                       <span class="text-white text-xs font-medium">เปลี่ยนรูป</span>
                    </div>
                    <input type="file" accept="image/*" @change="onProfileSelected" class="hidden" />
                 </label>
                 <span class="mt-3 text-sm font-medium text-gray-700">รูปโปรไฟล์</span>
                 <span class="text-xs text-gray-400">คลิกที่รูปเพื่ออัปโหลด</span>
              </div>

              <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                 <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-bold text-gray-700">Scan Passport</h3>
                    <span v-if="scanning" class="text-xs text-orange-500 animate-pulse flex items-center gap-1">
                       <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                       Scanning...
                    </span>
                 </div>
                 
                 <label class="block w-full cursor-pointer">
                    <div v-if="!passportPreview" class="border-2 border-dashed border-gray-300 rounded-lg h-32 flex flex-col items-center justify-center text-gray-400 hover:border-indigo-500 hover:text-indigo-500 hover:bg-indigo-50 transition-colors">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                       <span class="text-xs">อัปโหลดรูป Passport</span>
                    </div>
                    <div v-else class="relative group">
                       <img :src="passportPreview" class="w-full h-auto rounded-lg shadow-sm border border-gray-200" />
                       <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition rounded-lg">
                          <span class="text-white text-xs">คลิกเพื่อเปลี่ยน</span>
                       </div>
                    </div>
                    <input type="file" accept="image/*" @change="onPassportSelected" class="hidden" />
                 </label>
                 <p class="mt-2 text-[10px] text-gray-400 text-center">ระบบจะดึงข้อมูลจากรูปให้อัตโนมัติ (AI)</p>
              </div>

            </div>

            <div class="md:col-span-8 space-y-6">
              
              <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                 <h3 class="text-sm font-bold text-gray-800 mb-4 border-b pb-2">ข้อมูลทั่วไป</h3>
                 <div class="grid grid-cols-1 gap-4">
                    <UiInput v-model="form.personalname" label="Display Name (ชื่อที่แสดงในระบบ)" placeholder="เช่น John Doe" class="bg-white" />
                 </div>
              </div>

              <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                 <h3 class="text-sm font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    ข้อมูลตามบัตร/พาสปอร์ต
                 </h3>
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <UiInput v-model="form.given_name" label="Given Name (Eng)" />
                    <UiInput v-model="form.surname" label="Surname (Eng)" />
                    
                    <UiInput v-model="form.given_name_th" label="ชื่อจริง (ไทย)" />
                    <UiInput v-model="form.surname_th" label="นามสกุล (ไทย)" />

                    <UiInput type="date" v-model="form.dob" label="Date of Birth" />
                    
                    <div class="flex flex-col">
                       <label class="text-sm text-gray-700 font-medium mb-1">Gender</label>
                       <select v-model="form.sex" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-shadow">
                          <option value="">Select Gender</option>
                          <option value="Male">Male</option>
                          <option value="Female">Female</option>
                       </select>
                    </div>

                    <UiInput v-model="form.nationality" label="Nationality" />
                 </div>
              </div>

              <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                 <h3 class="text-sm font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    รายละเอียดเอกสาร
                 </h3>
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <UiInput v-model="form.passport_no" label="Passport No." placeholder="Ex. AA1234567" class="font-mono" />
                    <UiInput v-model="form.personal_no" label="Personal / ID No." class="font-mono" />
                    
                    <UiInput type="date" v-model="form.date_issue" label="Date of Issue" />
                    <UiInput type="date" v-model="form.date_expiry" label="Date of Expiry" class="text-red-600" />
                    
                    <div class="sm:col-span-2">
                       <UiInput v-model="form.issuing_authority" label="Issuing Authority" />
                    </div>
                 </div>
              </div>

            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-white">
           <button @click="isModalOpen = false" class="px-5 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
              Cancel
           </button>
           <button @click="handleSave" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 transition flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
              Save Information
           </button>
        </div>

      </div>
    </div>

  </div>
</template>

<style scoped>
@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-scaleIn {
  animation: scaleIn 0.2s ease-out;
}
</style>