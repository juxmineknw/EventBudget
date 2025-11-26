<script setup>
import { ref, computed, onMounted } from "vue";
import { useCategories } from "@/composables/useCategories";

// Components
import CategoryCard from "@/components/category/CategoryCard.vue";
import CategoryAddModal from "@/components/category/CategoryAddModal.vue";
import CategoryEditModal from "@/components/category/CategoryEditModal.vue";

definePageMeta({
  layout: "header",
  title: "Expense Categories",
});

// Composables
const {
  categories,
  loadCategories,
  createCategory,
  updateCategory,
  deleteCategory,
} = useCategories();

// State
const search = ref("");
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingCategory = ref(null);

// Lifecycle
onMounted(loadCategories);

// Computed
const filteredCategories = computed(() =>
  categories.value.filter((cat) =>
    cat.name.toLowerCase().includes(search.value.toLowerCase())
  )
);

// Methods
const openEdit = (cat) => {
  editingCategory.value = { ...cat };
  showEditModal.value = true;
};

const confirmDelete = (cat) => {
  if (!confirm(`Delete category "${cat.name}"?`)) {
    return;
  }
  deleteCategory(cat.id);
};
</script>

<template>
  <div>
    <div class="flex items-center gap-4 mb-6 text-base md:text-lg">
      
      <button
      class="
        px-6 py-3 rounded-2xl 
        bg-[#FFD578] hover:bg-[#ffca58] text-white font-bold 
        transition-all hover:scale-105 active:scale-95
        text-sm md:text-base
      "
    >
      All
    </button>

    <div class="flex-grow relative group">
      <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-focus-within:text-orange-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      <input
        v-model="search"
        placeholder="Search categories..."
        class="
          w-full pl-12 pr-5 py-3 
          bg-white border-2 border-transparent rounded-2xl
          text-slate-600 font-medium placeholder:text-slate-300
          shadow-sm
          focus:border-orange-200 focus:ring-4 focus:ring-orange-100 focus:outline-none
          transition-all duration-200
        "
      />
    </div>

    <button
      @click="showAddModal = true"
      class="
        w-12 h-12 md:w-14 md:h-14 
        rounded-2xl 
        bg-gradient-to-br from-orange-400 to-pink-500 
        text-white text-2xl md:text-3xl font-light
        shadow-lg shadow-orange-300/50 
        flex items-center justify-center 
        transition-all duration-300 
        hover:scale-110 hover:rotate-90 active:scale-95 cursor-pointer
      "
    >
      +
    </button>
    </div>

    <div class="grid 
        grid-cols-2       
        gap-2             
        sm:gap-4          
        sm:grid-cols-2    
        md:grid-cols-3    lg:grid-cols-5"   >
      <CategoryCard
        v-for="cat in filteredCategories"
        :key="cat.id"
        :cat="cat"
        class="text-sm md:text-base" 
        @edit="openEdit"
        @delete="confirmDelete"
      />
    </div>

    <CategoryAddModal
      v-if="showAddModal"
      @close="showAddModal = false"
      @save="createCategory"
    />

    <CategoryEditModal
      v-if="showEditModal"
      :category="editingCategory"
      @close="showEditModal = false"
      @save="updateCategory"
    />
  </div>
</template>