<script setup>
import { ref, computed, onMounted } from "vue";

definePageMeta({
  layout: "header",
  title: "Expense Categories",
});

import { useCategories } from "@/composables/useCategories";
import CategoryCard from "@/components/category/CategoryCard.vue";
import CategoryAddModal from "@/components/category/CategoryAddModal.vue";
import CategoryEditModal from "@/components/category/CategoryEditModal.vue";

const {
  categories,
  loadCategories,
  createCategory,
  updateCategory,
  deleteCategory,
} = useCategories();

const search = ref("");
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingCategory = ref(null);

onMounted(loadCategories);

const filteredCategories = computed(() =>
  categories.value.filter((cat) =>
    cat.name.toLowerCase().includes(search.value.toLowerCase())
  )
);

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

    <!-- Search + Add Button -->
    <div class="flex items-center gap-4 mb-6 text-base md:text-lg">
      <button
        class="px-6 py-2 rounded-full bg-[#FFD578] text-white font-semibold shadow text-sm md:text-lg">
        All
      </button>

      <input
        v-model="search"
        placeholder="Search categories..."
        class="flex-grow px-5 py-3 bg-[#ededed] rounded-full text-sm md:text-base"
      />

      <button
        @click="showAddModal = true"
        class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-[#F6A441] text-white text-xl md:text-2xl shadow">
        +
      </button>
    </div>

    <!-- Category Grid -->
    <div class="grid 
    grid-cols-2
    gap-2               
    sm:gap-4
    sm:grid-cols-2
    md:grid-cols-3        
    lg:grid-cols-5">

      <CategoryCard
        v-for="cat in filteredCategories"
        :key="cat.id"
        :cat="cat"
        class="text-sm md:text-base"
        @edit="openEdit"
        @delete="confirmDelete"
      />

    </div>

    <!-- Modals -->
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
