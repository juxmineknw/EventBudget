<script setup>
defineProps({
  isOpen: Boolean,
  imageUrl: String
});

defineEmits(['close']);
</script>

<template>
  <Transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4" @click="$emit('close')">
      
      <button class="absolute top-4 right-4 text-white hover:text-gray-300 p-2 bg-white/10 rounded-full transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <img 
        :src="imageUrl" 
        class="max-w-full max-h-[85vh] rounded-lg shadow-2xl border border-gray-700 animate-zoom-in" 
        alt="Passport Full Size" 
        @click.stop 
      />
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

@keyframes zoomIn {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.animate-zoom-in { animation: zoomIn 0.2s ease-out; }
</style>