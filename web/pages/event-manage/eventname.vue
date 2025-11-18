<script setup>
import { useRoute } from 'vue-router'
const route = useRoute()
</script>

<template>
  <div class="p-6">

    <p class="text-gray-700 mt-4">
      Your route ID is: {{ route.params.id }}
    </p>

    <NuxtLink
      to="/event-manage/1"
      class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg"
    >
      ไป Event ID 1
    </NuxtLink>

  </div>
</template>
