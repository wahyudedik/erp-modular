<template>
  <div class="module-search-filter">
    <v-card class="search-filter-card" elevation="2">
      <v-card-text class="pa-4">
        <v-row>
          <!-- Search Input -->
          <v-col cols="12" md="4">
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              placeholder="Search modules..."
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @input="handleSearch"
              @clear="clearSearch"
            >
              <template v-slot:append-inner>
                <v-progress-circular
                  v-if="searchLoading"
                  indeterminate
                  size="20"
                  color="primary"
                />
              </template>
            </v-text-field>
          </v-col>

          <!-- Category Filter -->
          <v-col cols="12" md="3">
            <v-select
              v-model="selectedCategory"
              :items="categoryOptions"
              prepend-inner-icon="mdi-tag"
              placeholder="All Categories"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @update:model-value="handleCategoryFilter"
            />
          </v-col>

          <!-- Status Filter -->
          <v-col cols="12" md="3">
            <v-select
              v-model="selectedStatus"
              :items="statusOptions"
              prepend-inner-icon="mdi-check-circle"
              placeholder="All Status"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @update:model-value="handleStatusFilter"
            />
          </v-col>

          <!-- Sort By -->
          <v-col cols="12" md="2">
            <v-select
              v-model="sortBy"
              :items="sortOptions"
              prepend-inner-icon="mdi-sort"
              placeholder="Sort by"
              variant="outlined"
              density="compact"
              hide-details
              @update:model-value="handleSortChange"
            />
          </v-col>
        </v-row>

        <!-- Advanced Filters (Collapsible) -->
        <v-expand-transition>
          <div v-show="showAdvancedFilters">
            <v-divider class="my-4" />
            
            <v-row>
              <!-- Business Type Filter -->
              <v-col cols="12" md="3">
                <v-select
                  v-model="selectedBusinessType"
                  :items="businessTypeOptions"
                  prepend-inner-icon="mdi-domain"
                  placeholder="Business Type"
                  variant="outlined"
                  density="compact"
                  hide-details
                  clearable
                  @update:model-value="handleBusinessTypeFilter"
                />
              </v-col>

              <!-- Feature Filter -->
              <v-col cols="12" md="3">
                <v-select
                  v-model="selectedFeature"
                  :items="featureOptions"
                  prepend-inner-icon="mdi-star"
                  placeholder="Key Features"
                  variant="outlined"
                  density="compact"
                  hide-details
                  clearable
                  @update:model-value="handleFeatureFilter"
                />
              </v-col>

              <!-- Setup Time Filter -->
              <v-col cols="12" md="3">
                <v-select
                  v-model="selectedSetupTime"
                  :items="setupTimeOptions"
                  prepend-inner-icon="mdi-clock-outline"
                  placeholder="Setup Time"
                  variant="outlined"
                  density="compact"
                  hide-details
                  clearable
                  @update:model-value="handleSetupTimeFilter"
                />
              </v-col>

              <!-- User Count Filter -->
              <v-col cols="12" md="3">
                <v-select
                  v-model="selectedUserCount"
                  :items="userCountOptions"
                  prepend-inner-icon="mdi-account-group"
                  placeholder="User Count"
                  variant="outlined"
                  density="compact"
                  hide-details
                  clearable
                  @update:model-value="handleUserCountFilter"
                />
              </v-col>
            </v-row>
          </div>
        </v-expand-transition>

        <!-- Filter Actions -->
        <v-row class="mt-4">
          <v-col cols="12" class="d-flex align-center justify-space-between">
            <div class="d-flex align-center gap-2">
              <!-- Active Filters Display -->
              <div v-if="activeFiltersCount > 0" class="d-flex align-center gap-1">
                <span class="text-caption text-medium-emphasis">Active filters:</span>
                <v-chip
                  v-for="filter in activeFilters"
                  :key="filter.key"
                  size="small"
                  color="primary"
                  variant="outlined"
                  closable
                  @click:close="clearFilter(filter.key)"
                >
                  {{ filter.label }}
                </v-chip>
              </div>
            </div>

            <div class="d-flex align-center gap-2">
              <!-- Toggle Advanced Filters -->
              <v-btn
                :icon="showAdvancedFilters ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                variant="text"
                size="small"
                @click="toggleAdvancedFilters"
              >
                <v-icon size="16">{{ showAdvancedFilters ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                <v-tooltip activator="parent" location="top">
                  {{ showAdvancedFilters ? 'Hide' : 'Show' }} Advanced Filters
                </v-tooltip>
              </v-btn>

              <!-- Clear All Filters -->
              <v-btn
                v-if="activeFiltersCount > 0"
                color="grey"
                variant="outlined"
                size="small"
                prepend-icon="mdi-filter-remove"
                @click="clearAllFilters"
              >
                Clear All
              </v-btn>

              <!-- Export Results -->
              <v-btn
                color="primary"
                variant="outlined"
                size="small"
                prepend-icon="mdi-download"
                @click="exportResults"
              >
                Export
              </v-btn>
            </div>
          </v-col>
        </v-row>

        <!-- Search Results Summary -->
        <v-row v-if="showResultsSummary" class="mt-2">
          <v-col cols="12">
            <v-alert
              color="info"
              variant="tonal"
              density="compact"
              class="text-body-2"
            >
              <template v-slot:prepend>
                <v-icon size="16">mdi-information</v-icon>
              </template>
              Showing {{ filteredCount }} of {{ totalCount }} modules
              <template v-if="searchQuery">
                for "{{ searchQuery }}"
              </template>
            </v-alert>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useModuleStore } from '../stores/modules'

const props = defineProps({
  modules: {
    type: Array,
    default: () => []
  },
  totalCount: {
    type: Number,
    default: 0
  },
  showResultsSummary: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['filter', 'search', 'sort', 'export'])

const moduleStore = useModuleStore()

// State
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedStatus = ref('')
const sortBy = ref('name')
const showAdvancedFilters = ref(false)
const searchLoading = ref(false)

// Advanced filters
const selectedBusinessType = ref('')
const selectedFeature = ref('')
const selectedSetupTime = ref('')
const selectedUserCount = ref('')

// Computed properties
const categoryOptions = computed(() => {
  const categories = [...new Set(props.modules.map(module => module.category))]
  return categories.map(category => ({
    title: category,
    value: category
  }))
})

const statusOptions = [
  { title: 'Active', value: 'active' },
  { title: 'Inactive', value: 'inactive' },
  { title: 'Recommended', value: 'recommended' }
]

const sortOptions = [
  { title: 'Name (A-Z)', value: 'name' },
  { title: 'Name (Z-A)', value: 'name-desc' },
  { title: 'Category', value: 'category' },
  { title: 'Recently Added', value: 'created_at' },
  { title: 'Most Popular', value: 'users_count' },
  { title: 'Setup Time', value: 'setup_time' }
]

const businessTypeOptions = computed(() => {
  return moduleStore.businessTypes.map(type => ({
    title: type.name,
    value: type.id
  }))
})

const featureOptions = computed(() => {
  const features = new Set()
  props.modules.forEach(module => {
    if (module.features) {
      module.features.forEach(feature => features.add(feature))
    }
  })
  return Array.from(features).map(feature => ({
    title: feature,
    value: feature
  }))
})

const setupTimeOptions = [
  { title: 'Quick (1-5 min)', value: 'quick' },
  { title: 'Standard (5-15 min)', value: 'standard' },
  { title: 'Advanced (15+ min)', value: 'advanced' }
]

const userCountOptions = [
  { title: 'Small (1-10 users)', value: 'small' },
  { title: 'Medium (10-50 users)', value: 'medium' },
  { title: 'Large (50+ users)', value: 'large' }
]

const activeFilters = computed(() => {
  const filters = []
  
  if (selectedCategory.value) {
    filters.push({ key: 'category', label: `Category: ${selectedCategory.value}` })
  }
  if (selectedStatus.value) {
    filters.push({ key: 'status', label: `Status: ${selectedStatus.value}` })
  }
  if (selectedBusinessType.value) {
    const businessType = businessTypeOptions.value.find(bt => bt.value === selectedBusinessType.value)
    if (businessType) {
      filters.push({ key: 'businessType', label: `Business: ${businessType.title}` })
    }
  }
  if (selectedFeature.value) {
    filters.push({ key: 'feature', label: `Feature: ${selectedFeature.value}` })
  }
  if (selectedSetupTime.value) {
    filters.push({ key: 'setupTime', label: `Setup: ${selectedSetupTime.value}` })
  }
  if (selectedUserCount.value) {
    filters.push({ key: 'userCount', label: `Users: ${selectedUserCount.value}` })
  }
  
  return filters
})

const activeFiltersCount = computed(() => activeFilters.value.length)

const filteredCount = computed(() => {
  // This would be calculated based on the actual filtered results
  // For now, return the total count
  return props.totalCount
})

// Methods
const handleSearch = () => {
  searchLoading.value = true
  
  // Debounce search
  setTimeout(() => {
    emit('search', searchQuery.value)
    searchLoading.value = false
  }, 300)
}

const handleCategoryFilter = () => {
  emitFilter()
}

const handleStatusFilter = () => {
  emitFilter()
}

const handleSortChange = () => {
  emit('sort', sortBy.value)
}

const handleBusinessTypeFilter = () => {
  emitFilter()
}

const handleFeatureFilter = () => {
  emitFilter()
}

const handleSetupTimeFilter = () => {
  emitFilter()
}

const handleUserCountFilter = () => {
  emitFilter()
}

const emitFilter = () => {
  const filters = {
    category: selectedCategory.value,
    status: selectedStatus.value,
    businessType: selectedBusinessType.value,
    feature: selectedFeature.value,
    setupTime: selectedSetupTime.value,
    userCount: selectedUserCount.value
  }
  
  emit('filter', filters)
}

const clearSearch = () => {
  searchQuery.value = ''
  emit('search', '')
}

const clearFilter = (filterKey) => {
  switch (filterKey) {
    case 'category':
      selectedCategory.value = ''
      break
    case 'status':
      selectedStatus.value = ''
      break
    case 'businessType':
      selectedBusinessType.value = ''
      break
    case 'feature':
      selectedFeature.value = ''
      break
    case 'setupTime':
      selectedSetupTime.value = ''
      break
    case 'userCount':
      selectedUserCount.value = ''
      break
  }
  
  emitFilter()
}

const clearAllFilters = () => {
  selectedCategory.value = ''
  selectedStatus.value = ''
  selectedBusinessType.value = ''
  selectedFeature.value = ''
  selectedSetupTime.value = ''
  selectedUserCount.value = ''
  
  emitFilter()
}

const toggleAdvancedFilters = () => {
  showAdvancedFilters.value = !showAdvancedFilters.value
}

const exportResults = () => {
  const exportData = {
    searchQuery: searchQuery.value,
    filters: {
      category: selectedCategory.value,
      status: selectedStatus.value,
      businessType: selectedBusinessType.value,
      feature: selectedFeature.value,
      setupTime: selectedSetupTime.value,
      userCount: selectedUserCount.value
    },
    sortBy: sortBy.value,
    results: props.modules
  }
  
  emit('export', exportData)
}

// Watch for changes
watch([selectedCategory, selectedStatus, selectedBusinessType, selectedFeature, selectedSetupTime, selectedUserCount], () => {
  emitFilter()
})

// Load business types on mount
onMounted(async () => {
  try {
    await moduleStore.loadBusinessTypes()
  } catch (error) {
    console.error('Error loading business types:', error)
  }
})
</script>

<style scoped>
.module-search-filter {
  width: 100%;
}

.search-filter-card {
  border-radius: 12px;
  border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

/* Responsive adjustments */
@media (max-width: 960px) {
  .search-filter-card .v-row {
    gap: 12px;
  }
}

@media (max-width: 600px) {
  .search-filter-card .v-row .d-flex {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
}
</style>
