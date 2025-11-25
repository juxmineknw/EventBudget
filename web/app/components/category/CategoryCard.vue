<template>
  <div
    class="
      rounded-3xl 
      p-3 sm:p-4
      hover:shadow-lg 
      transition 
      text-[10px] sm:text-xs md:text-sm
    "
    :style="{
      background: `linear-gradient(to bottom, ${cat.color}, ${cat.color}55, #FBF7F4)`,
    }"
  >
    <!-- TOP AREA -->
    <div class="flex items-center gap-2 sm:gap-3">
      <div
        class="
          rounded-full bg-white shadow flex items-center justify-center
          w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14
        "
      >
        <Icon
          :name="cat.icon"
          class="text-xl sm:text-2xl md:text-3xl"
        />
      </div>

      <div>
        <p class="text-[8px] sm:text-[10px] text-gray-500 leading-none">
          Category
        </p>
        <p class="text-[12px] sm:text-sm md:text-base font-semibold text-gray-700 leading-none mt-1">
          {{ cat.name }}
        </p>
      </div>
    </div>

    <!-- BUTTONS -->
    <div class="flex gap-2 mt-3 sm:mt-4">
      <button
        @click.stop="$emit('edit', cat)"
        class="
          shadow flex items-center justify-center transition
          rounded-full border
          w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8
          bg-[#ffebdd] border-[#ffaf53]
          hover:bg-[#ffe2b2] active:scale-95
          text-[10px] sm:text-xs
        "
      >
        ✏️
      </button>

      <button
        @click.stop="$emit('delete', cat)"
        class="
          shadow flex items-center justify-center transition
          rounded-full border
          w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8
          bg-[#FFE5E5] border-[#FF9A9A]
          hover:bg-[#FFCACA] active:scale-95
          text-[10px] sm:text-xs
        "
      >
        🗑️
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  cat: Object,
});
</script>
