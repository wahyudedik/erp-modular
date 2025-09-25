<template>
  <div>
    <!-- Welcome Section -->
    <v-row>
      <v-col cols="12">
        <v-card class="mb-6" color="primary" dark>
          <v-card-title class="text-h4">
            <v-icon left size="large">mdi-view-dashboard</v-icon>
            Welcome to ERP Modular
          </v-card-title>
          <v-card-subtitle class="text-h6">
            Multi-Industri Solution Dashboard
          </v-card-subtitle>
          <v-card-text>
            <p class="text-body-1">
              Manage your business efficiently with our modular ERP system.
              {{ userStore.hasBusinessType ? 'You have selected ' + userStore.businessType?.name : 'Please select your business type to get started.' }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Quick Stats -->
    <v-row>
      <v-col cols="12" md="3">
        <v-card class="text-center">
          <v-card-text>
            <v-icon size="48" color="primary">mdi-puzzle</v-icon>
            <div class="text-h4 mt-2">{{ userStore.moduleCount }}</div>
            <div class="text-subtitle-1">Active Modules</div>
          </v-card-text>
        </v-card>
      </v-col>
      
      <v-col cols="12" md="3">
        <v-card class="text-center">
          <v-card-text>
            <v-icon size="48" color="success">mdi-domain</v-icon>
            <div class="text-h4 mt-2">{{ userStore.hasBusinessType ? '1' : '0' }}</div>
            <div class="text-subtitle-1">Business Type</div>
          </v-card-text>
        </v-card>
      </v-col>
      
      <v-col cols="12" md="3">
        <v-card class="text-center">
          <v-card-text>
            <v-icon size="48" color="info">mdi-account-group</v-icon>
            <div class="text-h4 mt-2">1</div>
            <div class="text-subtitle-1">Users</div>
          </v-card-text>
        </v-card>
      </v-col>
      
      <v-col cols="12" md="3">
        <v-card class="text-center">
          <v-card-text>
            <v-icon size="48" color="warning">mdi-chart-line</v-icon>
            <div class="text-h4 mt-2">100%</div>
            <div class="text-subtitle-1">System Health</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Business Type Selection -->
    <v-row v-if="!userStore.hasBusinessType">
      <v-col cols="12">
        <v-card>
          <v-card-title>
            <v-icon left>mdi-domain</v-icon>
            Select Your Business Type
          </v-card-title>
          <v-card-text>
            <p class="text-body-1 mb-4">
              To get started, please select your business type. We'll recommend the most suitable modules for your industry.
            </p>
            <v-btn
              color="primary"
              size="large"
              :to="{ name: 'BusinessTypeSelection' }"
            >
              Choose Business Type
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Active Modules -->
    <v-row v-if="userStore.hasBusinessType">
      <v-col cols="12">
        <v-card>
          <v-card-title>
            <v-icon left>mdi-puzzle</v-icon>
            Your Active Modules
          </v-card-title>
          <v-card-text>
            <v-row v-if="userStore.activeModules.length === 0">
              <v-col cols="12" class="text-center py-8">
                <v-icon size="64" color="grey">mdi-puzzle-outline</v-icon>
                <div class="text-h6 mt-4">No modules activated yet</div>
                <div class="text-body-2 mb-4">Activate modules to start using ERP features</div>
                <v-btn
                  color="primary"
                  :to="{ name: 'ModuleManagement' }"
                >
                  Manage Modules
                </v-btn>
              </v-col>
            </v-row>
            
            <v-row v-else>
              <v-col
                v-for="userModule in userStore.activeModules"
                :key="userModule.id"
                cols="12"
                md="6"
                lg="4"
              >
                <v-card
                  class="module-card"
                  hover
                  :to="{ name: 'ModuleDetail', params: { id: userModule.module.id } }"
                >
                  <v-card-title class="d-flex align-center">
                    <v-icon
                      :color="getModuleColor(userModule.module.category)"
                      class="mr-3"
                    >
                      {{ moduleStore.getModuleIcon(userModule.module.name) }}
                    </v-icon>
                    {{ userModule.module.name }}
                  </v-card-title>
                  <v-card-subtitle>
                    {{ userModule.module.category }}
                  </v-card-subtitle>
                  <v-card-text>
                    <p class="text-body-2">{{ userModule.module.description }}</p>
                    <v-chip
                      v-if="userModule.module.is_core"
                      size="small"
                      color="primary"
                      variant="outlined"
                    >
                      Core Module
                    </v-chip>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Quick Actions -->
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            <v-icon left>mdi-lightning-bolt</v-icon>
            Quick Actions
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="4">
                <v-btn
                  block
                  color="primary"
                  variant="outlined"
                  prepend-icon="mdi-puzzle"
                  :to="{ name: 'ModuleManagement' }"
                >
                  Manage Modules
                </v-btn>
              </v-col>
              <v-col cols="12" md="4">
                <v-btn
                  block
                  color="secondary"
                  variant="outlined"
                  prepend-icon="mdi-cog"
                  :to="{ name: 'Settings' }"
                >
                  Settings
                </v-btn>
              </v-col>
              <v-col cols="12" md="4">
                <v-btn
                  block
                  color="info"
                  variant="outlined"
                  prepend-icon="mdi-help-circle"
                  href="#"
                >
                  Help & Support
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useUserStore } from '../stores/user'
import { useModuleStore } from '../stores/modules'

const userStore = useUserStore()
const moduleStore = useModuleStore()

const getModuleColor = (category) => {
  const colorMap = {
    'Core': 'primary',
    'Manufacturing': 'success',
    'Sales': 'info',
    'Finance': 'warning',
    'HR': 'secondary',
    'Inventory': 'purple',
    'Production': 'orange'
  }
  return colorMap[category] || 'grey'
}

onMounted(async () => {
  if (userStore.isAuthenticated) {
    await userStore.loadActiveModules()
  }
})
</script>

<style scoped>
.module-card {
  transition: transform 0.2s ease-in-out;
}

.module-card:hover {
  transform: translateY(-2px);
}
</style>
