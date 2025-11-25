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

// Convert file to Base64
const toBase64 = (file) =>
  new Promise((resolve) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.readAsDataURL(file);
  });

const scanPassport = async (file) => {
  const config = useRuntimeConfig();

  const formData = new FormData();
  formData.append("file", file);

  const res = await fetch(`${config.public.API_URL}/api/mrz-scan`, {
    method: "POST",
    body: formData,
  });

  return await res.json();
};


const onPassportSelected = async (e) => {
  const file = e.target.files[0];
  if (!file) return;

  passportPreview.value = URL.createObjectURL(file);
  scanning.value = true;

  try {
    const result = await scanPassport(file);
    console.log("MRZ:", result);

    if (result && result.fields) {
      const f = result.fields;

      form.value.passport_no = f.documentNumber || "";
      form.value.given_name = f.firstName || "";
      form.value.surname = f.lastName || "";
      form.value.nationality = f.nationality || "";
      form.value.dob = f.birthDate || "";
      form.value.sex = f.sex || "";
      form.value.date_issue = f.issueDate || "";
      form.value.date_expiry = f.expiryDate || "";
    }
  } catch (err) {
    console.error("MRZ Scan failed", err);
  } finally {
    scanning.value = false;
  }
};



// PROFILE UPLOAD
const onProfileSelected = async (e) => {
  const file = e.target.files[0];
  if (!file) return;
  profilePreview.value = URL.createObjectURL(file);
  form.value.profile_image = await toBase64(file);
};

// SAVE
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

// DELETE
const handleDelete = (item) => {
  if (confirm(`Delete "${item.personalname || item.given_name}" ?`)) {
    deleteTeamMember(item.id);
  }
};

// OPEN EDIT
const openEdit = (item) => {
  isEditing.value = true;
  form.value = { ...item };

  passportPreview.value = item.passport_image
    ? `http://localhost:8000/storage/${item.passport_image}`
    : null;

  profilePreview.value = item.profile_image
    ? `http://localhost:8000/storage/${item.profile_image}`
    : null;

  isModalOpen.value = true;
};

// OPEN CREATE
const openCreate = () => {
  isEditing.value = false;
  resetForm();
  isModalOpen.value = true;
};
const handleFile = (e) => {
  const file = e.target.files[0];
  const reader = new FileReader();

  reader.onload = () => {
    form.imageBase64 = reader.result.split(",")[1]; // เอาเฉพาะ base64
  };

  reader.readAsDataURL(file);
};

</script>

<template>
  <div class="w-full flex flex-col items-center px-3 sm:px-6 lg:px-12 py-6">

    <!-- LIST -->
    <div 
      v-if="!loading"
      class="grid
        grid-cols-1
        sm:grid-cols-2
        lg:grid-cols-3
        gap-6
        w-full
        max-w-5xl   <!-- จำกัดความกว้างไม่ให้หลุดขวา -->
        mx-auto">

      <div 
        v-for="m in teamMembers" 
        :key="m.id"
        class="w-full flex flex-col items-center">

        <NuxtLink :to="`/team-manage/${m.id}`" class="block w-full">
          <UiCard
            class="w-full p-5 bg-white rounded-2xl shadow text-center border hover:shadow-lg transition">

            <div class="flex flex-col items-center gap-3">

              <!-- IMAGE -->
              <img
                v-if="m.profile_image"
                :src="`http://localhost:8000/storage/${m.profile_image}`"
                class="w-20 h-20 rounded-full object-cover shadow"
              />

              <div
                v-else
                class="w-20 h-20 rounded-full bg-gradient-to-br from-orange-400 to-red-500 text-white
                       flex items-center justify-center font-bold text-2xl shadow">
                {{ (m.personalname || m.given_name || m.surname || "?")[0] }}
              </div>

              <!-- NAME -->
              <div class="font-semibold text-lg sm:text-xl text-gray-900">
                {{ m.personalname || "-" }}
              </div>

              <div class="text-sm text-gray-600">
                {{ m.given_name && m.surname ? m.given_name + ' ' + m.surname : '-' }}
              </div>

              <div class="text-xs text-gray-400">
                Passport: 
                {{ m.passport_no ? m.passport_no.replace(/(.)(.{6})$/, (_, f) => f + "••••••") : "-" }}
              </div>

            </div>
          </UiCard>
        </NuxtLink>

        <!-- BUTTONS -->
        <div class="flex gap-3 mt-3">
          <UiButton
            variant="secondary"
            class="px-4 py-2 text-sm"
            @click="openEdit(m)">
            Edit
          </UiButton>

          <UiButton
            variant="danger"
            class="px-4 py-2 text-sm"
            @click="handleDelete(m)">
            Delete
          </UiButton>
        </div>

      </div>

    </div>

    <div v-if="loading" class="text-center text-gray-500 py-10">
      Loading...
    </div>

    <!-- Floating Add Button -->
    <EventFabButton 
      @click="openCreate" 
      class="fixed bottom-8 right-8 z-[200]" />

    <!-- MODAL -->
    <div v-if="isModalOpen"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[200] flex items-start justify-center pt-16 p-4 overflow-y-auto">
      <div class="w-full max-w-2xl bg-[#0f172a] text-white rounded-2xl shadow-xl p-6 space-y-6 animate-fadeIn">

        <div class="flex justify-between items-center">
          <h2 class="text-xl font-bold">{{ isEditing ? "Edit Team Member" : "Add Team Member" }}</h2>
          <button @click="isModalOpen = false" class="text-gray-400 text-xl">✕</button>
        </div>

        <!-- PROFILE -->
        <div class="space-y-2">
          <p class="text-sm text-gray-300">Profile Photo</p>
          <label class="bg-green-600 px-4 py-2 rounded cursor-pointer inline-flex items-center gap-2">
            📷 Upload Profile
            <input type="file" accept="image/*" @change="onProfileSelected" class="hidden" />
          </label>
          <img v-if="profilePreview" :src="profilePreview" class="w-24 h-24 rounded-lg object-cover" />
        </div>

        <!-- PASSPORT -->
        <div class="border border-gray-700 rounded-xl p-6 text-center space-y-3 bg-[#1e293b]">
          <h3 class="text-lg font-semibold">Scan Passport with AI</h3>

          <p v-if="scanning" class="text-orange-300 text-sm">Scanning passport...</p>

          <label class="cursor-pointer bg-blue-500 px-4 py-2 rounded inline-flex items-center gap-2">
            📤 Upload Passport Photo
            <input type="file" accept="image/*" @change="onPassportSelected" class="hidden" />
          </label>

          <img v-if="passportPreview" :src="passportPreview" class="mx-auto mt-3 w-40 rounded shadow object-cover" />
        </div>

        <!-- Display Name -->
        <UiInput v-model="form.personalname" label="Display Name" />
        <!-- PASSPORT DETAILS CARD (UI เหมือนภาพตัวอย่างของคุณ) -->
        <div class="border border-gray-700 rounded-2xl p-6 bg-[#1e293b] text-white space-y-6 shadow-lg">

          <!-- HEADER -->
          <div class="flex flex-col gap-2">
            <h3 class="text-xl font-semibold">Passport Details</h3>
            <p class="text-sm text-gray-400">Scan from passport or fill information manually</p>
          </div>

          <!-- PASSPORT IMAGE -->
          <div class="flex justify-center">
            <img v-if="passportPreview" :src="passportPreview"
              class="w-64 rounded-xl shadow-lg border border-gray-600 object-cover" />
            <div v-else
              class="w-64 h-40 rounded-xl bg-gray-800 border border-gray-700 flex items-center justify-center text-gray-500">
              No Image
            </div>
          </div>

          <!-- GRID FORM (2 columns) -->
          <div class="grid grid-cols-2 gap-4">

            <UiInput v-model="form.given_name" label="Given Name" />
            <UiInput v-model="form.surname" label="Surname" />

            <UiInput v-model="form.given_name_th" label="Given Name (Thai)" />
            <UiInput v-model="form.surname_th" label="Surname (Thai)" />

            <UiInput v-model="form.passport_no" label="Passport No." placeholder="AC1062346" maxlength="9"
              pattern="^[A-Z]{2}[0-9]{7}$" />

            <UiInput v-model="form.nationality" label="Nationality" />

            <UiInput type="date" v-model="form.dob" label="Date of Birth" />

            <!-- SEX SELECT -->
            <div>
              <label class="text-sm text-gray-300">Sex</label>
              <select v-model="form.sex" class="w-full mt-1 p-2 rounded bg-[#334155] border border-gray-600 text-white">
                <option value="">Select</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
                <option value="X">Other</option>
              </select>
            </div>

            <UiInput type="date" v-model="form.date_issue" label="Date of Issue" />
            <UiInput type="date" v-model="form.date_expiry" label="Date of Expiry" />

            <UiInput v-model="form.personal_no" label="Personal No." />
            <UiInput v-model="form.issuing_authority" label="Issuing Authority" />

          </div>
        </div>


        <div class="flex justify-end gap-3 pt-2">
          <UiButton variant="secondary" @click="isModalOpen = false">Cancel</UiButton>
          <UiButton variant="primary" @click="handleSave">Save</UiButton>
        </div>

      </div>
    </div>
  </div>
</template>

<style>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.25s ease-out;
}
</style>
