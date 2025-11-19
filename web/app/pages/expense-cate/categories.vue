<script setup>
import { ref, onMounted, watch } from "vue";

definePageMeta({
  layout: "header",
  title: "Expense Categories",
});

const API = "http://localhost:8000/api/categories";

const categories = ref([]);

// --- Data Form ---
const showAddModal = ref(false);
const name = ref("");
const icon = ref("");
const color = ref("#FFE5B4");

const showEditModal = ref(false);
const editId = ref(null);
const editName = ref("");
const editIcon = ref("");
const editColor = ref("");

// ======================== COLOR OPTIONS ========================
const colorOptions = [
  "#FFE5B4",
  "#FFD7D2",
  "#FFCACA",
  "#FFECB3",
  "#FFF5CC",
  "#D4F6C8",
  "#C8E5FF",
  "#D8D1FF",
  "#F3C9FF",
  "#E2E2E2",
];

// ======================== ICON CATEGORY DROPDOWN ========================
const iconCategoryList = [
  { key: "money", label: "เงิน" },
  { key: "food", label: "อาหาร" },
  { key: "animals", label: "สัตว์" },
  { key: "gifts", label: "ของขวัญ" },
  { key: "travel", label: "ท่องเที่ยว" },
];

const selectedIconCategory = ref("money");

// ======================== ICON LIST (แน่นอนว่าแสดงได้ 100%) ========================
const iconCategories = {
  money: [
    "twemoji:money-bag",
    "twemoji:coin",
    "twemoji:credit-card",
    "twemoji:chart-increasing",
    "twemoji:shopping-bags",
  ],
  food: [
    "twemoji:hamburger",
    "twemoji:pizza",
    "twemoji:green-salad",
    "twemoji:strawberry",
    "twemoji:fork-and-knife",
  ],
  animals: [
    "twemoji:cat",
    "twemoji:dog",
    "twemoji:rabbit",
    "twemoji:bear",
    "twemoji:monkey",
  ],
  gifts: [
    "twemoji:gift",
    "twemoji:balloon",
    "twemoji:party-popper",
    "twemoji:sparkles",
  ],
  travel: [
    "twemoji:airplane",
    "twemoji:automobile",
    "twemoji:ship",
    "twemoji:rocket",
  ],
};

// ======================== LOAD ========================
const load = async () => {
  categories.value = await $fetch(API);
};

// ======================== ADD ========================
const openAdd = () => {
  name.value = "";
  icon.value = "";
  color.value = "#FFE5B4";
  selectedIconCategory.value = "money";
  showAddModal.value = true;
};

const addCategory = async () => {
  if (!name.value) return alert("กรุณากรอกชื่อหมวดหมู่");
  if (!icon.value) return alert("กรุณาเลือกไอคอน");

  await $fetch(API, {
    method: "POST",
    body: { name: name.value, icon: icon.value, color: color.value },
  });

  showAddModal.value = false;
  load();
};

// ======================== DELETE ========================
const deleteCategory = async (id) => {
  if (confirm("ต้องการลบหมวดหมู่นี้ไหม?")) {
    await $fetch(`${API}/${id}`, { method: "DELETE" });
    load();
  }
};

// ======================== EDIT ========================
const openEdit = (cat) => {
  editId.value = cat.id;
  editName.value = cat.name;
  editIcon.value = cat.icon;
  editColor.value = cat.color || "#FFE5B4";
  selectedIconCategory.value = "money";
  showEditModal.value = true;
};

const saveEdit = async () => {
  await $fetch(`${API}/${editId.value}`, {
    method: "PUT",
    body: {
      name: editName.value,
      icon: editIcon.value,
      color: editColor.value,
    },
  });

  showEditModal.value = false;
  load();
};

// ======================== DISABLE SCROLL WHEN POPUP OPEN ========================
watch([showAddModal, showEditModal], ([add, edit]) => {
  document.body.style.overflow = add || edit ? "hidden" : "auto";
});

onMounted(load);
</script>

<!-- =================================================================== -->
<!-- ===========================  TEMPLATE ============================== -->
<!-- =================================================================== -->

<template>
  <div class="p-6 min-h-screen bg-base-bg text-text-primary">
    <!-- PAGE HEADER -->
    <div class="flex justify-between mb-4">
      <h1 class="text-2xl font-bold">หมวดหมู่</h1>
      <button
        @click="openAdd"
        class="px-4 py-2 rounded bg-accent hover:bg-accent-hover text-white"
      >
        + เพิ่มหมวดหมู่
      </button>
    </div>

    <!-- CATEGORY LIST -->
    <div
      v-for="cat in categories"
      :key="cat.id"
      class="flex justify-between items-center bg-white border p-4 mb-3 rounded-xl shadow-sm"
    >
      <div class="flex items-center gap-4">
        <div
          class="w-14 h-14 rounded-full flex items-center justify-center"
          :style="{ backgroundColor: cat.color || '#FFE5B4' }"
        >
          <Icon :name="cat.icon" class="text-3xl" />
        </div>

        <span class="text-lg font-semibold">{{ cat.name }}</span>
      </div>

      <div class="flex gap-2">
        <button
          @click="openEdit(cat)"
          class="px-4 py-2 rounded bg-accent hover:bg-accent-hover text-white"
        >
          แก้ไข
        </button>
        <button
          @click="deleteCategory(cat.id)"
          class="px-4 py-2 rounded bg-danger hover:bg-danger-hover text-white"
        >
          ลบ
        </button>
      </div>
    </div>

    <!-- ================================================================ -->
    <!-- ======================== ADD MODAL ============================= -->
    <!-- ================================================================ -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 px-4 md:px-0"
    >
      <div
        class="bg-white w-full max-w-[430px] md:max-w-[450px] lg:max-w-[480px] max-h-[90vh] rounded-3xl p-5 md:p-6 shadow-xl flex flex-col overflow-hidden"
      >
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">เพิ่มหมวดหมู่</h2>
          <button @click="showAddModal = false" class="text-2xl text-gray-400">
            ✕
          </button>
        </div>

        <!-- CONTENT SCROLL -->
        <div class="flex-1 overflow-y-auto pr-1">
          <!-- PREVIEW -->
          <div
            class="rounded-2xl p-6 text-center mb-4 shadow"
            :style="{ background: color }"
          >
            <div class="flex justify-center mb-2">
              <div
                class="w-16 h-16 rounded-full bg-white flex items-center justify-center shadow"
              >
                <Icon v-if="icon" :name="icon" class="text-4xl" />
              </div>
            </div>
            <p class="font-bold text-lg">{{ name || "ชื่อหมวดหมู่" }}</p>
          </div>

          <!-- COLOR PICKER -->
          <div class="flex gap-2 overflow-x-auto py-2 mb-3">
            <div
              v-for="c in colorOptions"
              :key="c"
              class="w-8 h-8 rounded-full cursor-pointer border"
              :style="{ backgroundColor: c }"
              @click="color = c"
            >
              <div
                v-if="color === c"
                class="w-full h-full flex items-center justify-center text-xl"
              >
                ✓
              </div>
            </div>
          </div>

          <!-- NAME -->
          <input
            v-model="name"
            class="w-full p-3 border rounded-xl mb-4 bg-base-bg"
            placeholder="ชื่อหมวดหมู่"
          />

          <!-- CATEGORY DROPDOWN -->
          <select
            v-model="selectedIconCategory"
            class="w-full p-3 border rounded-xl bg-base-bg mb-3"
          >
            <option
              v-for="cat in iconCategoryList"
              :value="cat.key"
              :key="cat.key"
            >
              {{ cat.label }}
            </option>
          </select>

          <!-- ICONS -->
          <div class="grid grid-cols-4 md:grid-cols-5 gap-3">
            <div
              v-for="ic in iconCategories[selectedIconCategory]"
              :key="ic"
              @click="icon = ic"
              class="p-3 rounded-xl border cursor-pointer flex items-center justify-center bg-base-card hover:bg-base-bg"
              :class="icon === ic ? 'ring-2 ring-accent' : ''"
            >
              <Icon :name="ic" class="text-3xl" />
            </div>
          </div>
        </div>

        <!-- FOOTER -->
        <button
          @click="addCategory"
          class="w-full p-3 mt-4 rounded-xl bg-accent hover:bg-accent-hover text-white font-semibold"
        >
          บันทึก
        </button>
      </div>
    </div>

    <!-- ================================================================ -->
    <!-- ======================== EDIT MODAL ============================ -->
    <!-- ================================================================ -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 px-4"
    >
      <div
        class="bg-white w-full max-w-[430px] max-h-[90vh] rounded-3xl p-6 shadow-xl flex flex-col overflow-hidden"
      >
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">แก้ไขหมวดหมู่</h2>
          <button @click="showEditModal = false" class="text-2xl text-gray-400">
            ✕
          </button>
        </div>

        <!-- CONTENT -->
        <div class="flex-1 overflow-y-auto pr-1">
          <!-- PREVIEW -->
          <div
            class="rounded-2xl p-6 text-center mb-4 shadow"
            :style="{ background: editColor }"
          >
            <div class="flex justify-center mb-2">
              <div
                class="w-16 h-16 rounded-full bg-white flex items-center justify-center shadow"
              >
                <Icon v-if="editIcon" :name="editIcon" class="text-4xl" />
              </div>
            </div>
            <p class="font-bold text-lg">{{ editName }}</p>
          </div>

          <!-- COLOR PICKER -->
          <div class="flex gap-2 overflow-x-auto py-2 mb-3">
            <div
              v-for="c in colorOptions"
              :key="c"
              class="w-8 h-8 rounded-full cursor-pointer border"
              :style="{ backgroundColor: c }"
              @click="editColor = c"
            >
              <div
                v-if="editColor === c"
                class="w-full h-full flex items-center justify-center text-xl"
              >
                ✓
              </div>
            </div>
          </div>

          <!-- NAME -->
          <input
            v-model="editName"
            class="w-full p-3 border rounded-xl mb-4 bg-base-bg"
            placeholder="ชื่อหมวดหมู่"
          />

          <!-- CATEGORY DROPDOWN -->
          <select
            v-model="selectedIconCategory"
            class="w-full p-3 border rounded-xl bg-base-bg mb-3"
          >
            <option
              v-for="cat in iconCategoryList"
              :value="cat.key"
              :key="cat.key"
            >
              {{ cat.label }}
            </option>
          </select>

          <!-- ICONS -->
          <div class="grid grid-cols-5 gap-3">
            <div
              v-for="ic in iconCategories[selectedIconCategory]"
              :key="ic"
              @click="editIcon = ic"
              class="p-3 rounded-xl border cursor-pointer flex items-center justify-center bg-base-card hover:bg-base-bg"
              :class="editIcon === ic ? 'ring-2 ring-accent' : ''"
            >
              <Icon :name="ic" class="text-3xl" />
            </div>
          </div>
        </div>

        <!-- FOOTER -->
        <button
          @click="saveEdit"
          class="w-full p-3 mt-4 rounded-xl bg-accent hover:bg-accent-hover text-white font-semibold"
        >
          บันทึก
        </button>
      </div>
    </div>
  </div>
</template>
