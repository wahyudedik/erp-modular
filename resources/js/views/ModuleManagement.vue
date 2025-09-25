<template>
  <div class="module-management">
    <v-container fluid>
      <ModuleManager />
    </v-container>
  </div>
</template>

<script setup>
import ModuleManager from '../components/ModuleManager.vue'
</script>

<style scoped>
.module-management {
  padding: 24px 0;
}
</style>