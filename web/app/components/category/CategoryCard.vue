<template>
  <div
    class="
      group relative
      flex flex-col justify-between
      bg-white
      p-4 sm:p-5      
      rounded-[1.5rem]
      border-2
      transition-all duration-300 cubic-bezier(0.4, 0, 0.2, 1)
      cursor-default
      hover:-translate-y-1
      overflow-hidden  /* 
      will-change-transform transform-gpu
    "
    :style="{
      borderColor: isHovered ? cat.color : `${cat.color}30`, 
      boxShadow: isHovered ? `0 10px 25px -5px ${cat.color}60` : '0 2px 8px -2px rgba(0, 0, 0, 0.05)'
    }"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <div 
      class="absolute -top-10 -right-10 w-32 h-32 rounded-full opacity-10 blur-2xl transition-transform duration-700 group-hover:scale-125"
      :style="{ backgroundColor: cat.color }"
    ></div>

    <div class="relative z-10 flex items-start gap-3 sm:gap-4">
      <div
        class="
          w-12 h-12 sm:w-14 sm:h-14  /* ✅ เล็กลง */
          rounded-2xl
          flex items-center justify-center
          text-2xl sm:text-3xl       /* ✅ ไอคอนเล็กลง */
          shadow-sm transition-all duration-300 shrink-0
          group-hover:scale-105 group-hover:rotate-3
        "
        :style="{ 
          backgroundColor: `${cat.color}50`, 
          color: cat.color,
          boxShadow: `inset 0 0 0 1px ${cat.color}40` 
        }"
      >
        <Icon :name="cat.icon" class="filter drop-shadow-sm" />
      </div>

      <div class="flex-1 min-w-0 pt-1">
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
          Category
        </p>
        <h3 class="text-base sm:text-lg font-bold text-slate-800 leading-tight truncate">
          {{ cat.name }}
        </h3>
      </div>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-100 flex gap-2 relative z-10">
      
      <button
        @click.stop="$emit('edit', cat)"
        class="
          flex-1 py-1.5 rounded-xl
          bg-blue-50 text-blue-500 font-bold text-xs
          border border-blue-100
          hover:bg-blue-500 hover:text-white hover:border-blue-500
          transition-all duration-200 active:scale-95
          flex items-center justify-center gap-1.5
        "
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
        Edit
      </button>

      <button
        @click.stop="$emit('delete', cat)"
        class="
          flex-1 py-1.5 rounded-xl
          bg-pink-50 text-pink-500 font-bold text-xs
          border border-pink-100
          hover:bg-pink-500 hover:text-white hover:border-pink-500
          transition-all duration-200 active:scale-95
          flex items-center justify-center gap-1.5
        "
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Del
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  cat: Object,
});

const isHovered = ref(false);
</script>