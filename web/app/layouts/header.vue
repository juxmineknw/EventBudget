<script setup>
import { ref } from "vue";
import { useRoute } from "vue-router";

const isOpen = ref(false);
const toggleMenu = () => (isOpen.value = !isOpen.value);

const route = useRoute();

// ใช้เช็คว่าเมนูไหนกำลัง active อยู่
const isActive = (path) => route.path.startsWith(path);

// Mock User Data (ข้อมูลจำลองสำหรับแสดงผล)
const currentUser = {
  name: "Admin User",
  role: "Event Manager",
  // ใช้ UI Avatars สร้างรูปจากชื่อ
  avatar: "https://ui-avatars.com/api/?name=Admin+User&background=ff7a3c&color=fff&rounded=true" 
};

const handleLogout = () => {
  // ใส่ Logic Logout ตรงนี้
  alert("Logging out...");
};
</script>

<template>
  <div class="min-h-screen bg-[#FBF7F4] font-sans">
    
    <header
      class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm flex items-center justify-between px-6 py-4 transition-all"
    >
      <div class="flex items-center gap-4">
        <button
          @click="toggleMenu"
          class="w-10 h-10 rounded-xl border border-orange-100 bg-orange-50/50 flex flex-col items-center justify-center gap-[5px] shrink-0 hover:bg-orange-100 hover:scale-105 active:scale-95 transition-all duration-300 group"
        >
          <span class="w-5 h-[2px] rounded-full bg-[#ff7a3c] group-hover:w-6 transition-all"></span>
          <span class="w-5 h-[2px] rounded-full bg-[#ff7a3c]"></span>
          <span class="w-5 h-[2px] rounded-full bg-[#ff7a3c] group-hover:w-4 transition-all"></span>
        </button>

        <div>
           <h1 class="text-xl font-bold text-gray-700 tracking-tight">
            {{ route.meta.title || 'Dashboard' }}
          </h1>
        </div>
      </div>

      <div class="hidden sm:flex flex-col items-end border-r-4 border-[#ff7a3c] pr-3 space-y-0.5">
        <span class="text-[11px] tracking-[0.2em] text-gray-600 font-bold uppercase">
          DINSOR ADVERTISING
        </span>
        <span class="text-[11px] text-gray-400 font-medium italic">
          our idea start here
        </span>
      </div>
    </header>

    <div 
      v-if="isOpen" 
      @click="toggleMenu"
      class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[998] transition-opacity"
    ></div>

    <aside
      :class="[
        'fixed top-0 left-0 h-full w-72 bg-white shadow-2xl border-r border-gray-100 transition-transform duration-300 ease-in-out z-[999] flex flex-col',
        isOpen ? 'translate-x-0' : '-translate-x-full',
      ]"
    >
      <div class="p-6 flex justify-between items-center border-b border-dashed border-gray-100">
        <div class="flex items-center gap-2">
           <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#ff7a3c] to-[#ff9e70] flex items-center justify-center text-white font-bold text-lg shadow-orange-200 shadow-lg">
             E
           </div>
           <h2 class="text-xl font-bold text-gray-800 tracking-tight">EventBudget</h2>
        </div>
        <button 
          @click="toggleMenu" 
          class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors"
        >
          ✕
        </button>
      </div>

      <nav class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-2">
        <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Main Menu</p>

        <NuxtLink
          to="/event-manage/eventname"
          :class="['nav-item', isActive('/event-manage') ? 'active' : 'inactive']"
        >
          <span class="icon">📅</span>
          Event Management
        </NuxtLink>

        <NuxtLink
          to="/expense-cate/categories"
          :class="['nav-item', isActive('/expense-cate') ? 'active' : 'inactive']"
        >
          <span class="icon">🏷️</span>
          Expense Categories
        </NuxtLink>

        <NuxtLink
          to="/currency-manage"
          :class="['nav-item', isActive('/currency-manage') ? 'active' : 'inactive']"
        >
          <span class="icon">💰</span>
          Currency Management
        </NuxtLink>

        <NuxtLink
          to="/team-manage/teamdetails"
          :class="['nav-item', isActive('/team-manage') ? 'active' : 'inactive']"
        >
          <span class="icon">👥</span>
          Team Management
        </NuxtLink>

        <div class="my-4 border-t border-gray-100"></div>
        <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">System</p>

        <NuxtLink
          to="/general-setting"
          :class="['nav-item', isActive('/general-setting') ? 'active' : 'inactive']"
        >
          <span class="icon">⚙️</span>
          General Settings
        </NuxtLink>
      </nav>

      <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow cursor-default group">
          <img 
            :src="currentUser.avatar" 
            alt="Profile" 
            class="w-10 h-10 rounded-full border-2 border-white shadow-sm"
          />
          
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-700 truncate group-hover:text-[#ff7a3c] transition-colors">
              {{ currentUser.name }}
            </p>
            <p class="text-xs text-gray-400 truncate">
              {{ currentUser.role }}
            </p>
          </div>

          <button 
            @click="handleLogout"
            class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors"
            title="Logout"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <main class="px-6 py-8 transition-all duration-300">
      <slot />
    </main>
  </div>
</template>

<style scoped>
/* Utility Class สำหรับ Nav Item */
.nav-item {
  @apply flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200;
}

.nav-item .icon {
  @apply text-lg opacity-70 transition-transform;
}

/* State: Active */
.active {
  @apply bg-[#fff3eb] text-[#ff7a3c] shadow-sm;
}
.active .icon {
  @apply opacity-100 scale-110;
}

/* State: Inactive */
.inactive {
  @apply text-gray-500 hover:bg-white hover:text-[#ff7a3c] hover:shadow-sm;
}
</style>