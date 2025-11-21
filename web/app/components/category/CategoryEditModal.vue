<template>
  <div
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 px-4"
    @click="$emit('close')"
  >
    <div
      class="bg-white w-full max-w-[420px] rounded-[32px] p-6 shadow-xl relative"
      @click.stop
    >
      <!-- Close -->
      <button
        @click="$emit('close')"
        class="absolute top-4 right-4 text-gray-400 text-2xl"
      >
        ✕
      </button>

      <h2 class="text-xl font-bold mb-4">Edit Category</h2>

      <!-- Preview -->
      <div
        class="rounded-2xl p-6 mb-4 shadow-inner"
        :style="{ backgroundColor: localColor }"
      >
        <div class="flex justify-center mb-3">
          <div
            class="w-16 h-16 bg-white rounded-full shadow flex items-center justify-center"
          >
            <Icon :name="localIcon" class="text-4xl" />
          </div>
        </div>
        <p class="text-center font-semibold">{{ localName }}</p>
      </div>

      <!-- Color picker -->
      <div class="flex gap-3 justify-center mb-4">
        <div
          v-for="c in colorOptions"
          :key="c"
          class="w-7 h-7 rounded-full border cursor-pointer"
          :style="{ backgroundColor: c }"
          @click="localColor = c"
        >
          <div v-if="localColor === c" class="text-white text-center">✓</div>
        </div>
      </div>

      <!-- Name -->
      <input
        v-model="localName"
        class="w-full p-3 mb-3 bg-gray-100 rounded-xl"
        placeholder="Name"
      />

      <!-- Category dropdown -->
      <select
        v-model="selectedIconCategory"
        class="w-full p-3 mb-3 bg-gray-100 rounded-xl border"
      >
        <option
          v-for="cat in iconCategoryList"
          :key="cat.key"
          :value="cat.key"
        >
          {{ cat.label }}
        </option>
      </select>

      <!-- Icon grid -->
      <div class="grid grid-cols-5 gap-3">
        <div
          v-for="ic in iconCategories[selectedIconCategory]"
          :key="ic"
          @click="localIcon = ic"
          class="p-3 rounded-xl border cursor-pointer bg-gray-50 hover:bg-gray-100 flex justify-center"
          :class="localIcon === ic ? 'ring-2 ring-[#FF7A1A]' : ''"
        >
          <Icon :name="ic" class="text-3xl" />
        </div>
      </div>

      <button
        @click="save"
        class="w-full py-3 mt-4 bg-[#FF7A1A] text-white rounded-xl font-semibold"
      >
        Save
      </button>
    </div>
  </div>
</template>

<script setup>
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
const selectedIconCategory = ref(findCategoryByIcon(props.category.icon));

const save = () => {
  emit("save", {
    id: props.category.id,
    name: localName.value,
    icon: localIcon.value,
    color: localColor.value,
  });

  emit("close");
};
</script>
