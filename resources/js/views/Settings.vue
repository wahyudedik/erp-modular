<template>
  <div class="settings">
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-card-title class="d-flex align-center">
              <v-icon class="mr-2" color="primary">mdi-cog</v-icon>
              Settings
            </v-card-title>
            <v-card-text>
              <p class="text-body-1">
                Configure your ERP system settings and preferences.
              </p>
              <v-btn
                color="primary"
                prepend-icon="mdi-palette"
                @click="showThemeSettings = true"
              >
                Theme Settings
              </v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const showThemeSettings = ref(false)
</script>

<style scoped>
.settings {
  padding: 24px;
}
</style>
