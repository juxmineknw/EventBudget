<script setup>
const config = useRuntimeConfig();
console.log("KEY:", config.MRZ_API_KEY);
console.log("HOST:", config.MRZ_API_HOST);
</script>

<template>
  <div>Check console</div>
</template>
