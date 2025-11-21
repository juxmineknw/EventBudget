<template>
  <div
    class="rounded-3xl p-4 hover:shadow-lg transition"
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

    <div class="flex gap-2 mt-4">
      <button
        @click.stop="$emit('edit', cat)"
        class="w-8 h-8 rounded-full bg-[#ffebdd] border border-[#ffaf53] shadow flex items-center justify-center hover:bg-[#ffe2b2] active:scale-95 transition"
      >
        ✏️
      </button>

      <button
        @click.stop="$emit('delete', cat)"
        class="w-8 h-8 rounded-full bg-[#FFE5E5] border border-[#FF9A9A] shadow flex items-center justify-center hover:bg-[#FFCACA] active:scale-95 transition"
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
