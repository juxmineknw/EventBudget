<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import {
  colorOptions,
  iconCategoryList,
  iconCategories,
  findCategoryByIcon,
} from "@/utils/categoryIcons";

const props = defineProps({
  category: Object,
});

const emit = defineEmits(["close", "save"]);

const localName = ref(props.category.name);
const localIcon = ref(props.category.icon);
const localColor = ref(props.category.color);
const selectedIconCategory = ref(findCategoryByIcon(props.category.icon) || "money");

// Lock Scroll
onMounted(() => { document.body.style.overflow = "hidden"; });
onUnmounted(() => { document.body.style.overflow = ""; });

const save = () => {
  if (!localName.value || !localIcon.value) return alert("Please fill all info ✨");
  emit("save", {
    id: props.category.id,
    name: localName.value,
    icon: localIcon.value,
    color: localColor.value,
  });
  emit("close");
};
</script>

<template>
  <div
    class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm flex justify-center items-center px-4 z-[9999]"
    @click="$emit('close')"
  >
    <div
      class="bg-white rounded-[2rem] p-5 w-full max-w-[360px] shadow-2xl relative overflow-hidden animate-pop-in"
      @click.stop
    >
      <div class="absolute -top-6 -left-6 w-32 h-32 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 blur-2xl -z-10 opacity-60"></div>

      <button
        @click="$emit('close')"
        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-400 transition-colors text-sm"
      >
        ✕
      </button>

      <h2 class="text-lg font-black text-slate-800 mb-4 text-center tracking-tight">
        Edit Category
      </h2>

      <div 
        class="mb-5 p-3 rounded-2xl border flex items-center gap-4 transition-all duration-300"
        :style="{
          backgroundColor: localColor + '10',
          borderColor: localColor,
          boxShadow: `0 4px 12px -4px ${localColor}40`
        }"
      >
        <div 
          class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm transition-transform shrink-0"
          :style="{ backgroundColor: localColor + '30', color: localColor }"
        >
          <Icon :name="localIcon" class="text-2xl drop-shadow-sm" />
        </div>
        <div class="flex-1 min-w-0">
           <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Preview</p>
           <p class="text-base font-bold truncate transition-colors" :style="{ color: localColor }">
             {{ localName || "Category Name" }}
           </p>
        </div>
      </div>

      <div class="space-y-4">
        
        <div>
          <input 
            v-model="localName" 
            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-transparent rounded-xl text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:bg-white focus:border-blue-300 outline-none transition-all" 
            placeholder="Category Name" 
          />
        </div>

        <div>
          <label class="text-[10px] font-bold text-slate-400 uppercase ml-1 mb-1 block">Theme Color</label>
          <div class="flex flex-wrap gap-2 justify-center bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <div 
              v-for="c in colorOptions" 
              :key="c" 
              class="w-7 h-7 rounded-full cursor-pointer transition-transform hover:scale-110 shadow-sm relative"
              :style="{ backgroundColor: c }"
              @click="localColor = c"
            >
              <span v-if="localColor === c" class="absolute inset-0 ring-2 ring-offset-1 ring-gray-300 rounded-full animate-pulse"></span>
            </div>
          </div>
        </div>

        <div>
          <div class="flex justify-between items-center px-1 mb-1">
             <label class="text-[10px] font-bold text-slate-400 uppercase">Icon Set</label>
             <select 
                v-model="selectedIconCategory" 
                class="text-[10px] font-bold text-slate-600 bg-transparent outline-none cursor-pointer hover:text-blue-500 text-right"
              >
                <option v-for="cat in iconCategoryList" :key="cat.key" :value="cat.key">{{ cat.label }} ▼</option>
              </select>
          </div>

          <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 max-h-[120px] overflow-y-auto custom-scrollbar">
            <div class="grid grid-cols-5 gap-1.5">
              <div 
                v-for="ic in iconCategories[selectedIconCategory]" 
                :key="ic" 
                @click="localIcon = ic"
                class="aspect-square rounded-lg flex items-center justify-center cursor-pointer transition-all duration-200 hover:bg-white hover:shadow-sm active:scale-90"
                :class="localIcon === ic ? 'bg-white shadow-sm ring-2 ring-blue-200 text-blue-500' : 'text-slate-400 hover:text-slate-600'"
              >
                <Icon :name="ic" class="text-xl" />
              </div>
            </div>
          </div>
        </div>

        <button 
          @click="save" 
          class="w-full py-3 mt-2 bg-gradient-to-r from-blue-400 to-indigo-400 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-200 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2"
        >
          <span>💾 Save Changes</span>
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

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
</style>