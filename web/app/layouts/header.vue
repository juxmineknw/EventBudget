<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

const isOpen = ref(false)
const toggleMenu = () => (isOpen.value = !isOpen.value)

const route = useRoute()
</script>

<template>
  <div class="min-h-screen bg-[#F2F6FA] text-[#2B3856]">

    <!-- NAVBAR -->
    <header class="bg-[#050A1A] text-white px-4 py-3 flex items-center gap-4 shadow-lg">
      <button @click="toggleMenu" class="text-2xl">☰</button>
      <h1 class="text-xl font-semibold">
        {{ route.meta.title || "EventBudget" }}
      </h1>
    </header>

    <!-- SIDEBAR -->
    <aside 
      :class="[
        'fixed top-0 left-0 h-full w-64 bg-[#0B1326] text-white p-5 z-[999] transform transition-transform duration-300',
        isOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold">EventBudget</h2>
        <button @click="toggleMenu" class="text-xl">✕</button>
      </div>

      <nav class="flex flex-col gap-4">
        <NuxtLink to="/event-manage/eventname" class="text-[#B8C4D6] hover:text-white text-lg transition">
          Event Management
        </NuxtLink>
        <NuxtLink to="/categories" class="text-[#B8C4D6] hover:text-white text-lg transition">
          Expense Categories
        </NuxtLink>
        <NuxtLink to="/currency" class="text-[#B8C4D6] hover:text-white text-lg transition">
          Currency Management
        </NuxtLink>
        <NuxtLink to="/team" class="text-[#B8C4D6] hover:text-white text-lg transition">
          Team Management
        </NuxtLink>
        <NuxtLink to="/settings" class="text-[#B8C4D6] hover:text-white text-lg transition">
          General Settings
        </NuxtLink>
      </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="p-6 pt-8">
      <slot />
    </main>

  </div>
</template>

