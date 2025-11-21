<script setup>
import { ref } from "vue";
import {
  colorOptions,
  iconCategoryList,
  iconCategories,
} from "@/utils/categoryIcons";

const emit = defineEmits(["close", "save"]);

const name = ref("");
const icon = ref("");
const color = ref("#FFE5B4");
const selectedIconCategory = ref("money");

const save = () => {
  if (!name.value || !icon.value) return alert("กรุณากรอกข้อมูลให้ครบ");
  emit("save", { name: name.value, icon: icon.value, color: color.value });
  emit("close");
};
</script>

<template>
  <div
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center px-4 z-50"
    @click="emit('close')"
  >
    <div class="bg-white rounded-[32px] p-6 max-w-[420px] w-full" @click.stop>
      <button
        class="absolute top-4 right-4 text-gray-400 text-2xl"
        @click="emit('close')"
      >
        ✕
      </button>

      <h2 class="text-xl font-bold mb-4">Add Category</h2>

      <div class="rounded-2xl p-6 mb-4" :style="{ backgroundColor: color }">
        <div class="flex justify-center mb-3">
          <div
            class="w-16 h-16 bg-white rounded-full shadow flex items-center justify-center"
          >
            <Icon v-if="icon" :name="icon" class="text-4xl" />
          </div>
        </div>
        <p class="text-center font-semibold">{{ name || "Add Category" }}</p>
      </div>

      <div class="flex gap-3 justify-center mb-4">
        <div
          v-for="c in colorOptions"
          :key="c"
          class="w-7 h-7 rounded-full border cursor-pointer"
          :style="{ backgroundColor: c }"
          @click="color = c"
        ></div>
      </div>

      <input
        v-model="name"
        placeholder="Name"
        class="w-full p-3 mb-3 bg-gray-100 rounded-xl"
      />

      <select
        v-model="selectedIconCategory"
        class="w-full p-3 mb-3 bg-gray-100 rounded-xl"
      >
        <option v-for="cat in iconCategoryList" :key="cat.key" :value="cat.key">
          {{ cat.label }}
        </option>
      </select>

      <div class="grid grid-cols-5 gap-3">
        <div
          v-for="ic in iconCategories[selectedIconCategory]"
          :key="ic"
          class="p-3 rounded-xl border cursor-pointer bg-gray-50 hover:bg-gray-100 flex justify-center"
          @click="icon = ic"
        >
          <Icon :name="ic" class="text-3xl" />
        </div>
      </div>

      <button
        class="w-full py-3 mt-4 bg-[#FF7A1A] text-white rounded-xl"
        @click="save"
      >
        Confirm
      </button>
    </div>
  </div>
</template>
