<script setup>
import { computed } from "vue"

const { data } = await useFetch("http://localhost:8000/api/events")

// Laravel API → { data: [...] }
const events = computed(() => data.value?.data ?? [])
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Event List</h1>

    <div
      v-for="ev in events"
      :key="ev.id"
      class="bg-white shadow p-4 rounded mb-4 border"
    >
      <h2 class="text-xl font-semibold">{{ ev.name }}</h2>

      <p class="text-gray-600">{{ ev.description }}</p>

      <div class="mt-2 text-sm text-gray-700">
        📅 {{ ev.start_date }} → {{ ev.end_date }} <br />
        👤 Client: {{ ev.client_name }} <br />
        🌍 Location: {{ ev.location }} <br />
        💰 Budget: {{ ev.total_budget }} บาท <br />
        🏢 Venue: {{ ev.venue_name }}  
        <br />
        🔗 Venue URL: 
        <a :href="ev.venue_url" target="_blank" class="text-blue-500 underline">{{ ev.venue_url }}</a>
        <br />

        🏨 Accommodation: {{ ev.accommodation_name }} <br />
        🔗 Accommodation URL:
        <a :href="ev.accommodation_url" target="_blank" class="text-blue-500 underline">{{ ev.accommodation_url }}</a>
        <br />

        📁 Drive Link:
        <a :href="ev.drive_link" target="_blank" class="text-blue-500 underline">{{ ev.drive_link }}</a>
        <br />

        <div class="text-gray-400 text-xs mt-2">
          Created: {{ ev.created_at }} <br />
          Updated: {{ ev.updated_at }}
        </div>
      </div>
    </div>
  </div>
</template>
