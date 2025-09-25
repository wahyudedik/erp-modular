<template>
  <div class="module-grid">
    <!-- Grid Header -->
    <div class="module-grid__header mb-6">
      <div class="d-flex align-center justify-space-between">
        <div>
          <h2 class="text-h4 font-weight-bold mb-2">
            {{ title }}
          </h2>
          <p class="text-body-1 text-medium-emphasis">
            {{ subtitle }}
          </p>
        </div>
        
        <!-- Grid Controls -->
        <div class="d-flex align-center gap-3">
          <!-- View Toggle -->
          <v-btn-toggle
            v-model="viewMode"
            color="primary"
            variant="outlined"
            mandatory
            density="compact"
          >
            <v-btn value="grid" icon="mdi-view-grid" />
            <v-btn value="list" icon="mdi-view-list" />
          </v-btn-toggle>

          <!-- Sort Dropdown -->
          <v-select
            v-model="sortBy"
            :items="sortOptions"
            density="compact"
            variant="outlined"
            hide-details
            style="min-width: 140px;"
            @update:model-value="handleSortChange"
          />
        </div>
      </div>

      <!-- Search and Filter Bar -->
      <div class="module-grid__filters mt-4">
        <v-row>
          <v-col cols="12" md="6">
            <!-- Search Input -->
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              placeholder="Search modules..."
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @input="handleSearch"
            />
          </v-col>
          
          <v-col cols="12" md="6">
            <!-- Category Filter -->
            <v-select
              v-model="selectedCategory"
              :items="categoryOptions"
              prepend-inner-icon="mdi-filter"
              placeholder="Filter by category"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @update:model-value="handleCategoryFilter"
            />
          </v-col>
        </v-row>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="module-grid__loading">
      <v-row>
        <v-col
          v-for="i in 6"
          :key="i"
          cols="12"
          sm="6"
          md="4"
          lg="3"
        >
          <v-skeleton-loader
            type="card"
            height="280"
            class="module-grid__skeleton"
          />
        </v-col>
      </v-row>
    </div>

    <!-- Grid Content -->
    <div v-else-if="filteredModules.length > 0" class="module-grid__content">
      <!-- Grid View -->
      <v-row v-if="viewMode === 'grid'">
        <v-col
          v-for="module in paginatedModules"
          :key="module.id"
          cols="12"
          sm="6"
          md="4"
          lg="3"
          xl="2"
        >
          <ModuleCard
            :module="module"
            :is-active="isModuleActive(module.id)"
            :is-recommended="isModuleRecommended(module.id)"
            :is-disabled="isModuleDisabled(module.id)"
            @activate="handleModuleActivate"
            @deactivate="handleModuleDeactivate"
            @configure="handleModuleConfigure"
            @details="handleModuleDetails"
          />
        </v-col>
      </v-row>

      <!-- List View -->
      <div v-else class="module-grid__list">
        <v-card
          v-for="module in paginatedModules"
          :key="module.id"
          class="module-list-item mb-3"
          elevation="1"
          @click="handleModuleDetails(module)"
        >
          <v-card-text class="pa-4">
            <v-row align="center">
              <v-col cols="auto">
                <v-avatar
                  :color="isModuleActive(module.id) ? 'primary' : 'grey-lighten-1'"
                  size="48"
                >
                  <v-icon :color="isModuleActive(module.id) ? 'white' : 'grey-darken-1'">
                    {{ getModuleIcon(module.name) }}
                  </v-icon>
                </v-avatar>
              </v-col>
              
              <v-col>
                <div class="d-flex align-center mb-1">
                  <h3 class="text-h6 font-weight-bold mr-2">
                    {{ module.name }}
                  </h3>
                  
                  <v-chip
                    v-if="isModuleRecommended(module.id)"
                    size="small"
                    color="success"
                    variant="outlined"
                    class="mr-2"
                  >
                    <v-icon start size="12">mdi-star</v-icon>
                    Recommended
                  </v-chip>
                  
                  <v-chip
                    v-if="isModuleActive(module.id)"
                    size="small"
                    color="primary"
                    variant="flat"
                  >
                    <v-icon start size="12">mdi-check</v-icon>
                    Active
                  </v-chip>
                </div>
                
                <p class="text-body-2 text-medium-emphasis mb-2">
                  {{ module.description }}
                </p>
                
                <div class="d-flex align-center text-caption text-medium-emphasis">
                  <v-icon size="12" class="mr-1">mdi-tag</v-icon>
                  <span class="mr-4">{{ module.category }}</span>
                  <v-icon size="12" class="mr-1">mdi-clock-outline</v-icon>
                  <span>{{ module.setup_time || '5 min' }}</span>
                </div>
              </v-col>
              
              <v-col cols="auto">
                <v-btn
                  v-if="!isModuleActive(module.id)"
                  :color="isModuleRecommended(module.id) ? 'success' : 'primary'"
                  :variant="isModuleRecommended(module.id) ? 'flat' : 'outlined'"
                  @click.stop="handleModuleActivate(module)"
                >
                  <v-icon start>{{ isModuleRecommended(module.id) ? 'mdi-star' : 'mdi-plus' }}</v-icon>
                  {{ isModuleRecommended(module.id) ? 'Activate' : 'Add' }}
                </v-btn>
                
                <v-btn
                  v-else
                  color="success"
                  variant="outlined"
                  @click.stop="handleModuleConfigure(module)"
                >
                  <v-icon start size="16">mdi-cog</v-icon>
                  Configure
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="module-grid__pagination mt-6">
        <v-pagination
          v-model="currentPage"
          :length="totalPages"
          :total-visible="7"
          color="primary"
          @update:model-value="handlePageChange"
        />
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="module-grid__empty">
      <v-card class="text-center pa-8" elevation="0" variant="outlined">
        <v-icon size="64" color="grey-lighten-1" class="mb-4">
          mdi-puzzle-outline
        </v-icon>
        <h3 class="text-h5 font-weight-bold mb-2">
          No Modules Found
        </h3>
        <p class="text-body-1 text-medium-emphasis mb-4">
          {{ searchQuery ? 'Try adjusting your search terms' : 'No modules available at the moment' }}
        </p>
        <v-btn
          v-if="searchQuery"
          color="primary"
          variant="outlined"
          @click="clearFilters"
        >
          Clear Filters
        </v-btn>
      </v-card>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useModuleStore } from '../stores/modules'
import ModuleCard from './ModuleCard.vue'

const props = defineProps({
  modules: {
    type: Array,
    default: () => []
  },
  activeModules: {
    type: Array,
    default: () => []
  },
  recommendedModules: {
    type: Array,
    default: () => []
  },
  title: {
    type: String,
    default: 'Available Modules'
  },
  subtitle: {
    type: String,
    default: 'Browse and activate modules for your business'
  },
  loading: {
    type: Boolean,
    default: false
  },
  itemsPerPage: {
    type: Number,
    default: 12
  }
})

const emit = defineEmits(['activate', 'deactivate', 'configure', 'details'])

const moduleStore = useModuleStore()

// State
const viewMode = ref('grid')
const sortBy = ref('name')
const searchQuery = ref('')
const selectedCategory = ref('')
const currentPage = ref(1)

// Computed properties
const sortOptions = [
  { title: 'Name (A-Z)', value: 'name' },
  { title: 'Name (Z-A)', value: 'name-desc' },
  { title: 'Category', value: 'category' },
  { title: 'Recently Added', value: 'created_at' },
  { title: 'Most Popular', value: 'users_count' }
]

const categoryOptions = computed(() => {
  const categories = [...new Set(props.modules.map(module => module.category))]
  return categories.map(category => ({
    title: category,
    value: category
  }))
})

const filteredModules = computed(() => {
  let filtered = [...props.modules]

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(module =>
      module.name.toLowerCase().includes(query) ||
      module.description.toLowerCase().includes(query) ||
      module.category.toLowerCase().includes(query)
    )
  }

  // Category filter
  if (selectedCategory.value) {
    filtered = filtered.filter(module => module.category === selectedCategory.value)
  }

  // Sort
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'name':
        return a.name.localeCompare(b.name)
      case 'name-desc':
        return b.name.localeCompare(a.name)
      case 'category':
        return a.category.localeCompare(b.category)
      case 'created_at':
        return new Date(b.created_at) - new Date(a.created_at)
      case 'users_count':
        return (b.users_count || 0) - (a.users_count || 0)
      default:
        return 0
    }
  })

  return filtered
})

const totalPages = computed(() => {
  return Math.ceil(filteredModules.value.length / props.itemsPerPage)
})

const paginatedModules = computed(() => {
  const start = (currentPage.value - 1) * props.itemsPerPage
  const end = start + props.itemsPerPage
  return filteredModules.value.slice(start, end)
})

// Methods
const isModuleActive = (moduleId) => {
  return props.activeModules.some(active => active.module_id === moduleId)
}

const isModuleRecommended = (moduleId) => {
  return props.recommendedModules.some(rec => rec.id === moduleId)
}

const isModuleDisabled = (moduleId) => {
  // Add logic for disabled modules if needed
  return false
}

const getModuleIcon = (moduleName) => {
  return moduleStore.getModuleIcon(moduleName)
}

const handleModuleActivate = (module) => {
  emit('activate', module)
}

const handleModuleDeactivate = (module) => {
  emit('deactivate', module)
}

const handleModuleConfigure = (module) => {
  emit('configure', module)
}

const handleModuleDetails = (module) => {
  emit('details', module)
}

const handleSearch = () => {
  currentPage.value = 1
}

const handleCategoryFilter = () => {
  currentPage.value = 1
}

const handleSortChange = () => {
  currentPage.value = 1
}

const handlePageChange = (page) => {
  currentPage.value = page
  // Scroll to top when page changes
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
}

// Watch for changes in modules to reset pagination
watch(() => props.modules, () => {
  currentPage.value = 1
})
</script>

<style scoped>
.module-grid {
  width: 100%;
}

.module-grid__header {
  background: rgba(var(--v-theme-surface), 0.5);
  border-radius: 12px;
  padding: 24px;
  border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.module-grid__filters {
  background: rgba(var(--v-theme-surface), 0.3);
  border-radius: 8px;
  padding: 16px;
}

.module-grid__loading {
  min-height: 400px;
}

.module-grid__skeleton {
  border-radius: 12px;
}

.module-grid__content {
  min-height: 400px;
}

.module-grid__list {
  min-height: 400px;
}

.module-list-item {
  transition: all 0.2s ease;
  cursor: pointer;
}

.module-list-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.module-grid__pagination {
  display: flex;
  justify-content: center;
}

.module-grid__empty {
  min-height: 400px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Responsive adjustments */
@media (max-width: 960px) {
  .module-grid__header {
    padding: 16px;
  }
  
  .module-grid__filters {
    padding: 12px;
  }
}

@media (max-width: 600px) {
  .module-grid__header .d-flex {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  
  .module-grid__filters .v-row {
    gap: 12px;
  }
}
</style>
