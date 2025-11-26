<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import {
  colorOptions,
  iconCategoryList,
  iconCategories,
} from "@/utils/categoryIcons";

const emit = defineEmits(["close", "save"]);

const name = ref("");
const icon = ref("");
const color = ref("#ff9e70"); // สีเริ่มต้น (ส้มพาสเทล)
const selectedIconCategory = ref("money");

// --- Lock Background Scroll ---
onMounted(() => { document.body.style.overflow = "hidden"; });
onUnmounted(() => { document.body.style.overflow = ""; });

// --- Validation Check ---
const isValid = computed(() => {
  return name.value.trim() !== "" && icon.value !== "";
});

const save = () => {
  if (!isValid.value) return;
  emit("save", { name: name.value, icon: icon.value, color: color.value });
  emit("close");
};
</script>

<template>
  <div
    class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm flex justify-center items-center px-4 z-[9999]"
    @click="emit('close')"
  >
    <!-- Compact Container -->
    <div
      class="bg-white rounded-[2rem] p-5 w-full max-w-[360px] shadow-2xl relative overflow-hidden animate-pop-in"
      @click.stop
    >
      <!-- Decorative Background -->
      <div class="absolute -top-6 -left-6 w-32 h-32 rounded-full bg-gradient-to-br from-orange-100 to-pink-100 blur-2xl -z-10 opacity-60"></div>

      <!-- Close Button -->
      <button
        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-400 transition-colors text-sm z-20"
        @click="emit('close')"
      >
        ✕
      </button>

      <!-- Header -->
      <h2 class="text-lg font-black text-slate-800 mb-4 text-center tracking-tight relative z-10">
        New Category
      </h2>

      <!-- 1. COMPACT PREVIEW (Horizontal) -->
      <div 
        class="mb-5 p-3 rounded-2xl border flex items-center gap-4 transition-all duration-300 relative z-10"
        :style="{
          backgroundColor: color + '10',
          borderColor: color,
          boxShadow: `0 4px 12px -4px ${color}40`
        }"
      >
        <!-- Icon Preview -->
        <div 
          class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm transition-transform shrink-0"
          :style="{ backgroundColor: color + '30', color: color }"
        >
          <Icon v-if="icon" :name="icon" class="text-2xl drop-shadow-sm" />
          <span v-else class="text-lg opacity-40 font-bold">?</span>
        </div>
        
        <!-- Name Preview -->
        <div class="flex-1 min-w-0">
           <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Preview</p>
           <p 
             class="text-base font-bold truncate transition-colors"
             :style="{ color: color }"
           >
             {{ name || "Category Name" }}
           </p>
        </div>
      </div>

      <!-- 2. FORM INPUTS -->
      <div class="space-y-4 relative z-10">
        
        <!-- Name Input -->
        <div>
          <input 
            v-model="name" 
            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-transparent rounded-xl text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:bg-white focus:border-pink-300 focus:ring-4 focus:ring-pink-100 outline-none transition-all" 
            placeholder="e.g. Shopping" 
          />
        </div>

        <!-- Color Picker (Compact) -->
        <div>
          <label class="text-[10px] font-bold text-slate-400 uppercase ml-1 mb-1 block">Theme Color</label>
          <div class="flex flex-wrap gap-2 justify-center bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <div 
              v-for="c in colorOptions" 
              :key="c" 
              class="w-7 h-7 rounded-full cursor-pointer transition-transform hover:scale-110 shadow-sm relative"
              :style="{ backgroundColor: c }"
              @click="color = c"
            >
              <span v-if="color === c" class="absolute inset-0 ring-2 ring-offset-1 ring-gray-300 rounded-full animate-pulse"></span>
            </div>
          </div>
        </div>

        <!-- Icon Selector -->
        <div>
          <div class="flex justify-between items-center px-1 mb-1">
             <label class="text-[10px] font-bold text-slate-400 uppercase">Icon Set</label>
             <select 
                v-model="selectedIconCategory" 
                class="text-[10px] font-bold text-slate-600 bg-transparent outline-none cursor-pointer hover:text-pink-500 text-right appearance-none pr-3"
              >
                <option v-for="cat in iconCategoryList" :key="cat.key" :value="cat.key">
                  {{ cat.label }} ▼
                </option>
              </select>
          </div>

          <!-- Icon Grid (Reduced Height) -->
          <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 max-h-[120px] overflow-y-auto custom-scrollbar">
            <div class="grid grid-cols-5 gap-1.5">
              <div 
                v-for="ic in iconCategories[selectedIconCategory]" 
                :key="ic" 
                class="aspect-square rounded-lg flex items-center justify-center cursor-pointer transition-all duration-200 hover:bg-white hover:shadow-sm active:scale-90"
                :class="icon === ic ? 'bg-white shadow-sm ring-2 ring-pink-200 text-pink-500 scale-105' : 'text-slate-400 hover:text-slate-600'"
                @click="icon = ic"
              >
                <Icon :name="ic" class="text-xl" />
              </div>
            </div>
          </div>
        </div>

        <!-- Create Button -->
        <button 
          class="w-full py-3 mt-2 rounded-xl font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2 relative z-20"
          :class="isValid 
            ? 'bg-gradient-to-r from-pink-400 to-orange-400 text-white shadow-orange-200 hover:scale-[1.02] active:scale-95 cursor-pointer' 
            : 'bg-gray-200 text-gray-400 cursor-not-allowed shadow-none'"
          :disabled="!isValid"
          @click="save"
        >
          <span v-if="isValid">✨ Create Now</span>
          <span v-else>Fill info to create</span>
        </button>

      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes pop-in {
  0% { opacity: 0; transform: scale(0.9); }
  100% { opacity: 1; transform: scale(1); }
}
.animate-pop-in {
  animation: pop-in 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 10px;
}
</style>