<script setup>
import { ref, onMounted, watch } from "vue";

definePageMeta({
  layout: "header",
  title: "Expense Categories",
});

const API = "http://localhost:8000/api/categories";

const categories = ref([]);

// ADD modal form
const showAddModal = ref(false);
const name = ref("");
const icon = ref("");
const color = ref("#FFE5B4");

// EDIT modal form
const showEditModal = ref(false);
const editId = ref(null);
const editName = ref("");
const editIcon = ref("");
const editColor = ref("");

// ---------------------- COLOR OPTIONS ----------------------
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

// ---------------------- ICON CATEGORY ----------------------
const iconCategoryList = [
  { key: "money", label: "money" },
  { key: "food", label: "food" },
  { key: "animals", label: "animals" },
  { key: "gifts", label: "gifts" },
  { key: "travel", label: "travel" },
  { key: "drinks", label: "drinks" }
];

const selectedIconCategory = ref("money");

// ---------------------- ICON LIST ----------------------
const iconCategories = {
  money: [
    "twemoji:money-bag",
    "twemoji:coin",
    "twemoji:chart-increasing",
    "twemoji:credit-card",
    "twemoji:shopping-bags",
  ],
  food: [
    "twemoji:hamburger",
    "twemoji:pizza",
    "twemoji:green-salad",
    "twemoji:french-fries",
    "twemoji:strawberry",
  ],
  animals: [
    "twemoji:cat",
    "twemoji:dog",
    "twemoji:rabbit",
    "twemoji:bear",
    "twemoji:elephant",
  ],
  gifts: [
    "twemoji:wrapped-gift",
    "twemoji:party-popper",
    "twemoji:balloon",
    "twemoji:ribbon",
    "twemoji:sparkles",
  ],
  travel: [
    "twemoji:airplane",
    "twemoji:automobile",
    "twemoji:ship",
    "twemoji:train",
    "twemoji:rocket",
  ],
  drinks: [
    "twemoji:hot-beverage",   
    "twemoji:teacup-without-handle", 
    "twemoji:glass-of-milk",   
    "twemoji:clinking-beer-mugs", 
    "twemoji:bubble-tea",       
  ],
};
const search = ref(""); 

// ---------------------- LOAD DATA ----------------------
const load = async () => {
  categories.value = await $fetch(API);
};

// ---------------------- ADD ----------------------
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

// ---------------------- DELETE ----------------------
const deleteCategory = async (id) => {
  if (confirm("ต้องการลบหมวดหมู่นี้ไหม?")) {
    await $fetch(`${API}/${id}`, { method: "DELETE" });
    load();
  }
};

// ---------------------- EDIT ----------------------
const openEdit = (cat) => {
  editId.value = cat.id;
  editName.value = cat.name;
  editIcon.value = cat.icon;
  editColor.value = cat.color;
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

// ---------------------- Disable scroll when popup open ----------------------
watch([showAddModal, showEditModal], ([add, edit]) => {
  document.body.style.overflow = add || edit ? "hidden" : "auto";
});

onMounted(load);
</script>
<template>
  <div>
    <!-- ======================= FILTER + SEARCH + ADD ======================= -->
    <div class="flex items-center justify-between gap-4 mb-6">
      <!-- LEFT: Filter + Search -->
      <div class="flex items-center gap-4 flex-grow">
        <!-- FILTER BUTTON -->
        <button
          class="px-6 py-2 rounded-full bg-[#FFD578] text-white text-lg font-semibold shadow"
        >
          All
        </button>

        <!-- SEARCH BOX -->
        <div class="flex-grow">
          <input
            v-model="search"
            type="text"
            placeholder="Search categories..."
            class="w-full px-5 py-3 bg-[#ededed] text-gray-600 rounded-full outline-none"
          />
        </div>
      </div>

        <!-- RIGHT: ADD BUTTON -->
      <button
  @click="openAdd"
  class="w-14 h-14 bg-[#F6A441] rounded-full shadow-lg flex items-center justify-center"
>
  <span class="text-white text-4xl leading-none pb-[5px]">+</span>
</button>
    </div>

    <!-- Category Box -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
      <div
        v-for="cat in categories"
        :key="cat.id"
        class="rounded-3xl p-4 relative hover:shadow-lg transition"
        :style="{
          background: `linear-gradient(to bottom, ${cat.color}, ${cat.color}55, #FBF7F4)`,
        }"
      >
        <div class="flex items-center gap-3">
          <div
            class="w-14 h-14 rounded-full bg-white shadow flex items-center justify-center"
          >
            <Icon :name="cat.icon" class="text-3xl" />
          </div>

          <div>
            <p class="text-[10px] text-gray-500 leading-none">Category</p>
            <p class="text-base font-semibold text-gray-700 leading-none mt-1">
              {{ cat.name }}
            </p>
          </div>
        </div>

        <!-- EDIT + DELETE BUTTONS -->
        <div class="flex gap-2 mt-4">
          <button
            @click.stop="openEdit(cat)"
            class="w-8 h-8 rounded-full bg-[#ffe3ca] shadow flex items-center justify-center"
          >
            ✏️
          </button>

          <button
            @click.stop="deleteCategory(cat.id)"
            class="w-8 h-8 rounded-full bg-[#ffe4e3] shadow flex items-center justify-center"
          >
            🗑️
          </button>
        </div>
      </div>
    </div>

    <!-- ================ EDIT MODAL ================= -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 px-4"
      @click="showEditModal = false"
    >
      <div
        class="bg-white w-full max-w-[420px] rounded-[32px] p-6 shadow-xl relative animate-[fadeIn_0.25s]"
        @click.stop
      >
        <button
          @click="showEditModal = false"
          class="absolute top-4 right-4 text-gray-400 text-2xl"
        >
          ✕
        </button>

        <h2 class="text-xl font-bold mb-4">Edit Category</h2>

        <div
          class="rounded-2xl p-6 mb-4 shadow-inner"
          :style="{ backgroundColor: editColor }"
        >
          <div class="flex justify-center mb-3">
            <div
              class="w-16 h-16 bg-white rounded-full shadow flex items-center justify-center"
            >
              <Icon :name="editIcon" class="text-4xl" />
            </div>
          </div>
          <p class="text-center font-semibold">{{ editName }}</p>
        </div>

        <!-- COLOR PICKER -->
        <div class="flex gap-3 justify-center mb-4">
          <div
            v-for="c in colorOptions"
            :key="c"
            class="w-7 h-7 rounded-full border cursor-pointer"
            :style="{ backgroundColor: c }"
            @click="editColor = c"
          >
            <div v-if="editColor === c" class="text-white text-center">✓</div>
          </div>
        </div>

        <!-- NAME -->
        <input
          v-model="editName"
          class="w-full p-3 mb-3 bg-gray-100 rounded-xl"
          placeholder="Name"
        />

        <!-- ICON CATEGORY SELECT -->
        <select
          v-model="selectedIconCategory"
          class="w-full p-3 mb-3 bg-gray-100 rounded-xl border"
        >
          <option
            v-for="cat in iconCategoryList"
            :key="cat.key"
            :value="cat.key"
          >
            {{ cat.label }}
          </option>
        </select>

        <div class="grid grid-cols-5 gap-3">
          <div
            v-for="ic in iconCategories[selectedIconCategory]"
            :key="ic"
            @click="editIcon = ic"
            class="p-3 rounded-xl border cursor-pointer bg-gray-50 hover:bg-gray-100 flex justify-center"
            :class="editIcon === ic ? 'ring-2 ring-[#FF7A1A]' : ''"
          >
            <Icon :name="ic" class="text-3xl" />
          </div>
        </div>

        <button
          @click="saveEdit"
          class="w-full py-3 mt-4 bg-[#FF7A1A] text-white rounded-xl font-semibold"
        >
          Save
        </button>
      </div>
    </div>

    <!-- ================ ADD MODAL ================= -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 px-4"
      @click="showAddModal = false"
    >
      <div
        class="bg-white w-full max-w-[420px] rounded-[32px] p-6 shadow-xl relative animate-[fadeIn_0.25s]"
        @click.stop
      >
        <button
          @click="showAddModal = false"
          class="absolute top-4 right-4 text-gray-400 text-2xl"
        >
          ✕
        </button>

        <h2 class="text-xl font-bold mb-4">Add Category</h2>

        <div
          class="rounded-2xl p-6 mb-4 shadow-inner"
          :style="{ backgroundColor: color }"
        >
          <div class="flex justify-center mb-3">
            <div
              class="w-16 h-16 bg-white rounded-full shadow flex items-center justify-center"
            >
              <Icon :name="icon" class="text-4xl" v-if="icon" />
            </div>
          </div>
          <p class="text-center font-semibold">{{ name || "Add Category" }}</p>
        </div>

        <!-- COLORS -->
        <div class="flex gap-3 justify-center mb-4">
          <div
            v-for="c in colorOptions"
            :key="c"
            class="w-7 h-7 rounded-full border cursor-pointer"
            :style="{ backgroundColor: c }"
            @click="color = c"
          >
            <div v-if="color === c" class="text-white text-center">✓</div>
          </div>
        </div>

        <!-- NAME -->
        <input
          v-model="name"
          class="w-full p-3 mb-3 bg-gray-100 rounded-xl"
          placeholder="Name"
        />

        <!-- ICON CATEGORY DROPDOWN -->
        <select
          v-model="selectedIconCategory"
          class="w-full p-3 mb-3 bg-gray-100 rounded-xl"
        >
          <option
            v-for="cat in iconCategoryList"
            :key="cat.key"
            :value="cat.key"
          >
            {{ cat.label }}
          </option>
        </select>

        <!-- ICON GRID -->
        <div class="grid grid-cols-5 gap-3">
          <div
            v-for="ic in iconCategories[selectedIconCategory]"
            :key="ic"
            @click="icon = ic"
            class="p-3 rounded-xl border cursor-pointer bg-gray-50 hover:bg-gray-100 flex justify-center"
            :class="icon === ic ? 'ring-2 ring-[#FF7A1A]' : ''"
          >
            <Icon :name="ic" class="text-3xl" />
          </div>
        </div>

        <button
          @click="addCategory"
          class="w-full py-3 mt-4 bg-[#FF7A1A] text-white rounded-xl font-semibold"
        >
          Confirm
        </button>
      </div>
    </div>
  </div>
</template>
<style>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
