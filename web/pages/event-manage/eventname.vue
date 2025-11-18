<script setup>
definePageMeta({
  layout: "header",
  title: "Event Management",
});

import { ref, watch } from "vue";
import { navigateTo } from "#app";

// โหลดรายการ events
const { data: events } = await useFetch("/api/events");

// State Popup
const isOpen = ref(false);

// Lock background scroll
watch(isOpen, (value) => {
  document.body.style.overflow = value ? "hidden" : "";
});

// Event Form
const newEvent = ref({
  name: "",
  description: "",
  start_date: "",
  end_date: "",
  client_name: "",
  country: "",
  venue: "",
  accommodation: "",
  drive_link: "",
  team: [],
});

// กดปุ่มแก้ไข
const editEvent = (event) => {
  newEvent.value = { ...event }; // ดึงข้อมูลเก่าใส่ฟอร์ม
  isOpen.value = true; // เปิด popup
  isEditing.value = true; // โหมดแก้ไข
};

const isEditing = ref(false);

// ลบข้อมูลจริง
const deleteEvent = async (event) => {
  if (!confirm(`ต้องการลบอีเว้นต์ "${event.name}" จริงหรือไม่?`)) return;

  try {
    await $fetch(`/api/events/${event.id}`, {
      method: "DELETE",
    });

    // อัปเดตรายการ event หลังลบ
    const updated = await $fetch("/api/events");
    events.value = updated;

    alert("ลบข้อมูลสำเร็จ!");
  } catch (error) {
    console.error(error);
    alert("เกิดข้อผิดพลาด ไม่สามารถลบข้อมูลได้");
  }
};

const addTeamMember = () => newEvent.value.team.push({ name: "" });
const removeTeamMember = (index) => newEvent.value.team.splice(index, 1);

// Close Popup
const closePopup = () => (isOpen.value = false);

const saveEvent = async () => {
  try {
    if (isEditing.value) {
      // ➤ UPDATE
      await $fetch(`/api/events/${newEvent.value.id}`, {
        method: "PUT",
        body: newEvent.value,
      });

      alert("อัปเดตข้อมูลสำเร็จ!");
    } else {
      // ➤ CREATE
      await $fetch(`/api/events`, {
        method: "POST",
        body: newEvent.value,
      });

      alert("เพิ่มอีเว้นต์สำเร็จ!");
    }

    // โหลดข้อมูลใหม่
    events.value = await $fetch("/api/events");

    // ปิด popup + รีเซ็ต form
    closePopup();
  } catch (error) {
    console.error(error);
    alert("เกิดข้อผิดพลาด ไม่สามารถบันทึกได้");
  }
};
</script>

<template>
  <div class="p-4 min-h-screen bg-[#F2F6FA] text-[#2B3856]">
    <!-- EVENT LIST -->
    <div
      v-for="event in events"
      :key="event.id"
      @click="navigateTo(`/event-manage/${event.id}`)"
      class="cursor-pointer bg-white border border-[#E3EAF3] p-6 rounded-xl shadow-sm mt-4 flex justify-between hover:shadow-md transition"
    >
      <div>
        <h2 class="text-xl font-semibold text-[#2B3856]">{{ event.name }}</h2>
        <p class="text-[#3A5BA0] font-medium uppercase text-sm mt-1">
          {{ event.client_name }}
        </p>

        <div class="flex items-center gap-2 mt-3 text-[#4A5D7A]">
          <span class="w-2 h-2 rounded-full bg-[#F47A27]"></span>
          {{ event.start_date }} → {{ event.end_date }}
        </div>
      </div>

      <div class="flex flex-col items-end gap-3">
        <!-- CHECK BUTTON -->
        <button
          @click.stop
          class="w-10 h-10 flex items-center justify-center rounded-full border-2 border-[#6558F5] text-[#6558F5] bg-[#E7E5FF] hover:bg-[#6558F5] hover:text-white transition"
        >
          ✓
        </button>

        <div class="flex gap-3">
          <!-- EDIT BUTTON -->
          <button
            @click.stop="editEvent(event)"
            class="w-9 h-9 flex items-center justify-center rounded-full border border-[#3A5BA0] text-[#3A5BA0] bg-white hover:bg-[#3A5BA0] hover:text-white transition"
          >
            ✎
          </button>

          <!-- DELETE BUTTON -->
          <button
            @click.stop="deleteEvent(event)"
            class="w-9 h-9 flex items-center justify-center rounded-full border border-red-300 text-red-500 bg-white hover:bg-red-100 transition"
          >
            🗑
          </button>
        </div>
      </div>
    </div>

    <!-- ADD BUTTON -->
    <button
      @click="isOpen = true"
      class="fixed bottom-6 right-6 bg-[#F47A27] text-white rounded-full px-6 py-3 text-lg shadow-lg flex items-center gap-2 hover:bg-[#d96b22] transition"
    >
      <span class="text-2xl">+</span>
    </button>

    <!-- ADD BUTTON -->
    <button
      @click="
        isEditing = false;
        newEvent = {
          name: '',
          description: '',
          start_date: '',
          end_date: '',
          client_name: '',
          country: '',
          venue: '',
          accommodation: '',
          drive_link: '',
          team: [],
        };
        isOpen = true;
      "
      class="fixed bottom-6 right-6 bg-[#F47A27] text-white rounded-full px-6 py-3 ..."
    >
      <span class="text-2xl">+</span>
    </button>

    <!-- POPUP -->
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
      <div
        class="bg-[#1C2536] w-full max-w-2xl max-h-[90vh] rounded-xl shadow-xl flex flex-col"
      >
        <!-- HEADER -->
        <div
          class="flex justify-between items-center px-6 py-4 border-b border-gray-700"
        >
          <h2 class="text-xl font-semibold text-white">
            {{ isEditing ? "Edit Event" : "Add New Event" }}
          </h2>
          <button
            @click="closePopup"
            class="text-gray-300 hover:text-white text-2xl"
          >
            ✕
          </button>
        </div>

        <!-- FORM -->
        <form
          @submit.prevent="saveEvent"
          class="px-6 py-4 overflow-y-auto space-y-4"
        >
          <FormField label="Event Name">
            <input
              v-model="newEvent.name"
              class="form-input-dark"
              placeholder="Enter event name"
              required
            />
          </FormField>

          <div class="grid grid-cols-2 gap-4">
            <FormField label="Start Date">
              <input
                v-model="newEvent.start_date"
                type="date"
                class="form-input-dark"
                required
              />
            </FormField>
            <FormField label="End Date">
              <input
                v-model="newEvent.end_date"
                type="date"
                class="form-input-dark"
                required
              />
            </FormField>
          </div>

          <FormField label="Client">
            <input
              v-model="newEvent.client_name"
              class="form-input-dark"
              required
            />
          </FormField>

          <FormField label="Location">
            <input
              v-model="newEvent.country"
              class="form-input-dark"
              required
            />
          </FormField>

          <FormField label="Total Budget (THB)">
            <input
              v-model="newEvent.total"
              type="number"
              class="form-input-dark"
              required
            />
          </FormField>

          <FormField label="Description">
            <textarea
              v-model="newEvent.description"
              rows="4"
              class="form-input-dark"
              required
            ></textarea>
          </FormField>

          <hr class="border-gray-700 my-4" />

          <h3 class="text-lg font-semibold text-white">Event Details</h3>

          <FormField label="Venue Name">
            <input
              v-model="newEvent.venue_name"
              class="form-input-dark"
              required
            />
          </FormField>

          <FormField label="Venue Website/Map URL">
            <input
              v-model="newEvent.client_website"
              class="form-input-dark"
              required
              placeholder="https://..."
            />
          </FormField>

          <FormField label="Commended Name">
            <input
              v-model="newEvent.commended_name"
              class="form-input-dark"
              required
            />
          </FormField>

          <FormField label="Commended Website/Map URL">
            <input
              v-model="newEvent.commended_website"
              class="form-input-dark"
              required
              placeholder="https://..."
            />
          </FormField>

          <FormField label="Online Drive URL">
            <input
              v-model="newEvent.online_drive"
              class="form-input-dark"
              required
              placeholder="https://..."
            />
          </FormField>

          <hr class="border-gray-700 my-4" />

          <h3 class="text-lg font-semibold text-white">Team Management</h3>

          <div
            v-for="(member, index) in newEvent.team"
            :key="index"
            class="flex items-center gap-3 mt-3"
          >
            <input
              v-model="member.name"
              class="form-input-dark"
              placeholder="Team Member"
              required
            />
            <button
              type="button"
              @click="removeTeamMember(index)"
              class="text-gray-400 hover:text-red-400 text-xl"
            >
              🗑
            </button>
          </div>

          <button
            type="button"
            @click="addTeamMember"
            class="mt-3 flex items-center gap-2 border border-[#3A5BA0] text-[#3A5BA0] px-4 py-2 rounded-lg hover:bg-[#1E293B] transition"
          >
            <span class="text-xl">+</span> Add Team Member
          </button>

          <div class="flex justify-end gap-2 pt-6 border-t border-gray-700">
            <button
              type="button"
              @click="closePopup"
              class="px-4 py-2 bg-gray-600 text-white rounded-lg"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-[#F47A27] text-white rounded-lg hover:bg-[#d96b22]"
            >
              {{ isEditing ? "Update" : "Save" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style>
.form-input-dark {
  @apply w-full bg-[#101827] text-white p-3 rounded-lg 
         border border-gray-600 focus:border-[#F47A27] focus:ring-2 focus:ring-[#F47A27]
         outline-none;
}
</style>