<template>
  <div class="module-manager">
    <!-- Module Manager Header -->
    <div class="module-manager__header mb-6">
      <v-row align="center">
        <v-col cols="12" md="8">
          <h1 class="text-h3 font-weight-bold mb-2">
            Module Manager
          </h1>
          <p class="text-h6 text-medium-emphasis">
            Manage and configure your ERP modules
          </p>
        </v-col>
        
        <v-col cols="12" md="4" class="text-md-right">
          <!-- Quick Stats -->
          <div class="d-flex align-center justify-md-end gap-4">
            <div class="text-center">
              <div class="text-h4 font-weight-bold text-primary">
                {{ activeModulesCount }}
              </div>
              <div class="text-caption text-medium-emphasis">
                Active Modules
              </div>
            </div>
            
            <div class="text-center">
              <div class="text-h4 font-weight-bold text-success">
                {{ recommendedModulesCount }}
              </div>
              <div class="text-caption text-medium-emphasis">
                Recommended
              </div>
            </div>
            
            <div class="text-center">
              <div class="text-h4 font-weight-bold text-info">
                {{ totalModulesCount }}
              </div>
              <div class="text-caption text-medium-emphasis">
                Total Available
              </div>
            </div>
          </div>
        </v-col>
      </v-row>
    </div>

    <!-- Module Tabs -->
    <v-tabs
      v-model="activeTab"
      color="primary"
      class="module-manager__tabs mb-6"
    >
      <v-tab value="all">
        <v-icon start>mdi-puzzle</v-icon>
        All Modules
        <v-chip
          v-if="allModulesCount > 0"
          size="small"
          color="primary"
          variant="outlined"
          class="ml-2"
        >
          {{ allModulesCount }}
        </v-chip>
      </v-tab>
      
      <v-tab value="recommended">
        <v-icon start>mdi-star</v-icon>
        Recommended
        <v-chip
          v-if="recommendedModulesCount > 0"
          size="small"
          color="success"
          variant="outlined"
          class="ml-2"
        >
          {{ recommendedModulesCount }}
        </v-chip>
      </v-tab>
      
      <v-tab value="active">
        <v-icon start>mdi-check-circle</v-icon>
        Active
        <v-chip
          v-if="activeModulesCount > 0"
          size="small"
          color="primary"
          variant="flat"
          class="ml-2"
        >
          {{ activeModulesCount }}
        </v-chip>
      </v-tab>
      
      <v-tab value="categories">
        <v-icon start>mdi-tag</v-icon>
        Categories
      </v-tab>
    </v-tabs>

    <!-- Tab Content -->
    <v-window v-model="activeTab" class="module-manager__content">
      <!-- All Modules Tab -->
      <v-window-item value="all">
        <ModuleGrid
          :modules="allModules"
          :active-modules="activeModules"
          :recommended-modules="recommendedModules"
          title="All Available Modules"
          subtitle="Browse all modules available in the system"
          :loading="loading"
          @activate="handleModuleActivate"
          @deactivate="handleModuleDeactivate"
          @configure="handleModuleConfigure"
          @details="handleModuleDetails"
        />
      </v-window-item>

      <!-- Recommended Modules Tab -->
      <v-window-item value="recommended">
        <ModuleGrid
          :modules="recommendedModules"
          :active-modules="activeModules"
          :recommended-modules="recommendedModules"
          title="Recommended Modules"
          subtitle="Modules recommended for your business type"
          :loading="loading"
          @activate="handleModuleActivate"
          @deactivate="handleModuleDeactivate"
          @configure="handleModuleConfigure"
          @details="handleModuleDetails"
        />
      </v-window-item>

      <!-- Active Modules Tab -->
      <v-window-item value="active">
        <ModuleGrid
          :modules="activeModuleObjects"
          :active-modules="activeModules"
          :recommended-modules="recommendedModules"
          title="Active Modules"
          subtitle="Modules currently active in your system"
          :loading="loading"
          @activate="handleModuleActivate"
          @deactivate="handleModuleDeactivate"
          @configure="handleModuleConfigure"
          @details="handleModuleDetails"
        />
      </v-window-item>

      <!-- Categories Tab -->
      <v-window-item value="categories">
        <div class="module-categories">
          <v-row>
            <v-col
              v-for="category in moduleCategories"
              :key="category.name"
              cols="12"
              md="6"
              lg="4"
            >
              <v-card
                class="category-card"
                elevation="2"
                hover
                @click="viewCategory(category.name)"
              >
                <v-card-title class="d-flex align-center">
                  <v-avatar
                    :color="getCategoryColor(category.name)"
                    size="48"
                    class="mr-3"
                  >
                    <v-icon color="white">
                      {{ getCategoryIcon(category.name) }}
                    </v-icon>
                  </v-avatar>
                  
                  <div>
                    <h3 class="text-h6 font-weight-bold">
                      {{ category.name }}
                    </h3>
                    <p class="text-caption text-medium-emphasis mb-0">
                      {{ category.modules.length }} modules
                    </p>
                  </div>
                </v-card-title>

                <v-card-text>
                  <div class="d-flex flex-wrap gap-1">
                    <v-chip
                      v-for="module in category.modules.slice(0, 3)"
                      :key="module.id"
                      size="small"
                      variant="outlined"
                      :color="isModuleActive(module.id) ? 'primary' : 'grey'"
                    >
                      {{ module.name }}
                    </v-chip>
                    <v-chip
                      v-if="category.modules.length > 3"
                      size="small"
                      variant="outlined"
                      color="primary"
                    >
                      +{{ category.modules.length - 3 }} more
                    </v-chip>
                  </div>
                </v-card-text>

                <v-card-actions>
                  <v-btn
                    color="primary"
                    variant="outlined"
                    block
                    @click.stop="viewCategory(category.name)"
                  >
                    View Category
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </div>
      </v-window-item>
    </v-window>

    <!-- Module Details Dialog -->
    <v-dialog v-model="detailsDialog" max-width="800">
      <v-card v-if="selectedModule">
        <v-card-title class="d-flex align-center">
          <v-avatar
            :color="isModuleActive(selectedModule.id) ? 'primary' : 'grey-lighten-1'"
            size="48"
            class="mr-3"
          >
            <v-icon :color="isModuleActive(selectedModule.id) ? 'white' : 'grey-darken-1'">
              {{ getModuleIcon(selectedModule.name) }}
            </v-icon>
          </v-avatar>
          
          <div>
            <h2 class="text-h5 font-weight-bold">
              {{ selectedModule.name }}
            </h2>
            <p class="text-body-2 text-medium-emphasis mb-0">
              {{ selectedModule.category }}
            </p>
          </div>
          
          <v-spacer />
          
          <v-btn
            icon="mdi-close"
            variant="text"
            @click="detailsDialog = false"
          />
        </v-card-title>

        <v-divider />

        <v-card-text class="pa-6">
          <v-row>
            <v-col cols="12" md="8">
              <h3 class="text-h6 font-weight-bold mb-3">
                Description
              </h3>
              <p class="text-body-1 mb-4">
                {{ selectedModule.description }}
              </p>

              <h3 class="text-h6 font-weight-bold mb-3">
                Features
              </h3>
              <div v-if="selectedModule.features" class="mb-4">
                <v-chip
                  v-for="feature in selectedModule.features"
                  :key="feature"
                  size="small"
                  variant="outlined"
                  class="mr-2 mb-2"
                >
                  {{ feature }}
                </v-chip>
              </div>

              <h3 class="text-h6 font-weight-bold mb-3">
                Requirements
              </h3>
              <div v-if="selectedModule.requirements" class="mb-4">
                <v-chip
                  v-for="requirement in selectedModule.requirements"
                  :key="requirement"
                  size="small"
                  color="warning"
                  variant="outlined"
                  class="mr-2 mb-2"
                >
                  {{ requirement }}
                </v-chip>
              </div>
            </v-col>
            
            <v-col cols="12" md="4">
              <v-card variant="outlined" class="pa-4">
                <h3 class="text-h6 font-weight-bold mb-3">
                  Module Info
                </h3>
                
                <div class="mb-3">
                  <div class="text-caption text-medium-emphasis">
                    Setup Time
                  </div>
                  <div class="text-body-1">
                    {{ selectedModule.setup_time || '5 minutes' }}
                  </div>
                </div>
                
                <div class="mb-3">
                  <div class="text-caption text-medium-emphasis">
                    Active Users
                  </div>
                  <div class="text-body-1">
                    {{ selectedModule.users_count || 0 }}
                  </div>
                </div>
                
                <div class="mb-3">
                  <div class="text-caption text-medium-emphasis">
                    Status
                  </div>
                  <v-chip
                    :color="isModuleActive(selectedModule.id) ? 'success' : 'grey'"
                    size="small"
                    :variant="isModuleActive(selectedModule.id) ? 'flat' : 'outlined'"
                  >
                    {{ isModuleActive(selectedModule.id) ? 'Active' : 'Inactive' }}
                  </v-chip>
                </div>
                
                <div class="mb-3">
                  <div class="text-caption text-medium-emphasis">
                    Recommendation
                  </div>
                  <v-chip
                    :color="isModuleRecommended(selectedModule.id) ? 'success' : 'grey'"
                    size="small"
                    :variant="isModuleRecommended(selectedModule.id) ? 'outlined' : 'text'"
                  >
                    {{ isModuleRecommended(selectedModule.id) ? 'Recommended' : 'Optional' }}
                  </v-chip>
                </div>
              </v-card>
            </v-col>
          </v-row>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4">
          <v-spacer />
          
          <v-btn
            variant="outlined"
            @click="detailsDialog = false"
          >
            Close
          </v-btn>
          
          <v-btn
            v-if="!isModuleActive(selectedModule.id)"
            :color="isModuleRecommended(selectedModule.id) ? 'success' : 'primary'"
            :variant="isModuleRecommended(selectedModule.id) ? 'flat' : 'outlined'"
            @click="handleModuleActivate(selectedModule)"
          >
            <v-icon start>{{ isModuleRecommended(selectedModule.id) ? 'mdi-star' : 'mdi-plus' }}</v-icon>
            {{ isModuleRecommended(selectedModule.id) ? 'Activate' : 'Add Module' }}
          </v-btn>
          
          <v-btn
            v-else
            color="success"
            variant="outlined"
            @click="handleModuleConfigure(selectedModule)"
          >
            <v-icon start size="16">mdi-cog</v-icon>
            Configure
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Module Configuration Dialog -->
    <v-dialog v-model="configDialog" max-width="600">
      <v-card v-if="selectedModule">
        <v-card-title class="d-flex align-center">
          <v-icon class="mr-2">mdi-cog</v-icon>
          Configure {{ selectedModule.name }}
          
          <v-spacer />
          
          <v-btn
            icon="mdi-close"
            variant="text"
            @click="configDialog = false"
          />
        </v-card-title>

        <v-divider />

        <v-card-text class="pa-6">
          <!-- Configuration form will go here -->
          <p class="text-body-1 text-medium-emphasis">
            Module configuration options will be displayed here based on the module's specific requirements.
          </p>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4">
          <v-spacer />
          
          <v-btn
            variant="outlined"
            @click="configDialog = false"
          >
            Cancel
          </v-btn>
          
          <v-btn
            color="primary"
            @click="saveConfiguration"
          >
            Save Configuration
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useModuleStore } from '../stores/modules'
import ModuleGrid from './ModuleGrid.vue'

const moduleStore = useModuleStore()

// State
const activeTab = ref('all')
const detailsDialog = ref(false)
const configDialog = ref(false)
const selectedModule = ref(null)
const loading = ref(false)

// Computed properties
const allModules = computed(() => moduleStore.modules)
const activeModules = computed(() => moduleStore.activeModules || [])
const recommendedModules = computed(() => moduleStore.recommendedModules || [])
const moduleCategories = computed(() => moduleStore.moduleCategories || [])

const allModulesCount = computed(() => allModules.value.length)
const activeModulesCount = computed(() => activeModules.value.length)
const recommendedModulesCount = computed(() => recommendedModules.value.length)
const totalModulesCount = computed(() => allModulesCount.value)

const activeModuleObjects = computed(() => {
  return activeModules.value.map(userModule => userModule.module).filter(Boolean)
})

// Methods
const isModuleActive = (moduleId) => {
  return activeModules.value.some(active => active.module_id === moduleId)
}

const isModuleRecommended = (moduleId) => {
  return recommendedModules.value.some(rec => rec.id === moduleId)
}

const getModuleIcon = (moduleName) => {
  return moduleStore.getModuleIcon(moduleName)
}

const getCategoryIcon = (categoryName) => {
  const iconMap = {
    'Accounting': 'mdi-calculator',
    'Inventory': 'mdi-package-variant',
    'Sales': 'mdi-cart',
    'Purchase': 'mdi-shopping',
    'HR': 'mdi-account-group',
    'Production': 'mdi-cog',
    'Quality Control': 'mdi-check-circle',
    'Maintenance': 'mdi-wrench',
    'Mix Design': 'mdi-flask',
    'Recipe Management': 'mdi-book-open-page-variant'
  }
  return iconMap[categoryName] || 'mdi-tag'
}

const getCategoryColor = (categoryName) => {
  const colorMap = {
    'Accounting': 'primary',
    'Inventory': 'success',
    'Sales': 'info',
    'Purchase': 'warning',
    'HR': 'error',
    'Production': 'secondary',
    'Quality Control': 'success',
    'Maintenance': 'warning',
    'Mix Design': 'info',
    'Recipe Management': 'primary'
  }
  return colorMap[categoryName] || 'grey'
}

const handleModuleActivate = async (module) => {
  try {
    loading.value = true
    await moduleStore.activateModule(module.id)
    // Show success message
  } catch (error) {
    console.error('Error activating module:', error)
    // Show error message
  } finally {
    loading.value = false
  }
}

const handleModuleDeactivate = async (module) => {
  try {
    loading.value = true
    await moduleStore.deactivateModule(module.id)
    // Show success message
  } catch (error) {
    console.error('Error deactivating module:', error)
    // Show error message
  } finally {
    loading.value = false
  }
}

const handleModuleConfigure = (module) => {
  selectedModule.value = module
  configDialog.value = true
}

const handleModuleDetails = (module) => {
  selectedModule.value = module
  detailsDialog.value = true
}

const viewCategory = (categoryName) => {
  // Switch to all modules tab and filter by category
  activeTab.value = 'all'
  // You can add category filtering logic here
}

const saveConfiguration = () => {
  // Save configuration logic
  configDialog.value = false
}

// Load data on mount
onMounted(async () => {
  loading.value = true
  try {
    await Promise.all([
      moduleStore.loadModules(),
      moduleStore.loadModuleCategories(),
      moduleStore.loadRecommendations(1) // Assuming business type 1
    ])
  } catch (error) {
    console.error('Error loading modules:', error)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.module-manager {
  width: 100%;
}

.module-manager__header {
  background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.1) 0%, rgba(var(--v-theme-primary), 0.05) 100%);
  border-radius: 16px;
  padding: 32px;
  border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.module-manager__tabs {
  border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.module-manager__content {
  min-height: 600px;
}

.category-card {
  transition: all 0.3s ease;
  cursor: pointer;
}

.category-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Responsive adjustments */
@media (max-width: 960px) {
  .module-manager__header {
    padding: 24px;
  }
  
  .module-manager__header .d-flex {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
}

@media (max-width: 600px) {
  .module-manager__header {
    padding: 16px;
  }
}
</style>
