<template>
  <div class="user-management">
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-card-title class="d-flex align-center">
              <v-icon class="mr-2" color="primary">mdi-account-group</v-icon>
              User Management
            </v-card-title>
            <v-card-text>
              <p class="text-body-1">
                Manage your team members, roles, and permissions.
              </p>
              <v-btn
                color="primary"
                prepend-icon="mdi-account-plus"
                @click="showInviteDialog = true"
              >
                Invite User
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

const showInviteDialog = ref(false)
</script>

<style scoped>
.user-management {
  padding: 24px;
}
</style>
