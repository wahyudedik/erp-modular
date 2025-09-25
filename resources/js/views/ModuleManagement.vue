<template>
  <div>
    <!-- Header -->
    <v-row>
      <v-col cols="12">
        <v-card class="mb-6">
          <v-card-title class="text-h4">
            <v-icon left size="large">mdi-puzzle</v-icon>
            Module Management
          </v-card-title>
          <v-card-subtitle class="text-h6">
            Activate and manage modules for your business
          </v-card-subtitle>
          <v-card-text>
            <p class="text-body-1">
              {{ userStore.businessType?.name ? `Recommended modules for ${userStore.businessType.name}` : 'Select modules that fit your business needs.' }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filters -->
    <v-row>
      <v-col cols="12" md="4">
        <v-select
          v-model="selectedCategory"
          :items="categoryOptions"
          label="Filter by Category"
          prepend-icon="mdi-filter"
          clearable
          @update:model-value="filterModules"
        ></v-select>
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
          v-model="searchQuery"
          label="Search modules"
          prepend-icon="mdi-magnify"
          clearable
          @input="filterModules"
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="4">
        <v-switch
          v-model="showOnlyRecommended"
          label="Show only recommended"
          prepend-icon="mdi-lightbulb-on"
          @change="filterModules"
        ></v-switch>
      </v-col>
    </v-row>

    <!-- Module Categories -->
    <v-row v-for="category in filteredCategories" :key="category.title">
      <v-col cols="12">
        <v-card class="mb-4">
          <v-card-title class="d-flex align-center">
            <v-icon class="mr-3" :color="getCategoryColor(category.title)">
              {{ getCategoryIcon(category.title) }}
            </v-icon>
            {{ category.title }} Modules
            <v-spacer></v-spacer>
            <v-chip
              :color="getCategoryColor(category.title)"
              variant="outlined"
            >
              {{ category.modules.length }} modules
            </v-chip>
          </v-card-title>
          
          <v-card-text>
            <v-row>
              <v-col
                v-for="module in category.modules"
                :key="module.id"
                cols="12"
                md="6"
                lg="4"
              >
                <v-card
                  class="module-card"
                  variant="outlined"
                  :class="{
                    'active-module': isModuleActive(module.id),
                    'recommended-module': isModuleRecommended(module.id)
                  }"
                >
                  <v-card-title class="d-flex align-center">
                    <v-icon
                      :color="getModuleColor(module.category)"
                      class="mr-3"
                    >
                      {{ moduleStore.getModuleIcon(module.name) }}
                    </v-icon>
                    {{ module.name }}
                    <v-spacer></v-spacer>
                    <v-chip
                      v-if="module.is_core"
                      size="small"
                      color="primary"
                      variant="outlined"
                    >
                      Core
                    </v-chip>
                    <v-chip
                      v-if="isModuleRecommended(module.id)"
                      size="small"
                      color="success"
                      variant="outlined"
                      class="ml-1"
                    >
                      Recommended
                    </v-chip>
                  </v-card-title>
                  
                  <v-card-subtitle>
                    {{ module.category }}
                  </v-card-subtitle>
                  
                  <v-card-text>
                    <p class="text-body-2">{{ module.description }}</p>
                  </v-card-text>
                  
                  <v-card-actions>
                    <v-btn
                      v-if="!isModuleActive(module.id)"
                      color="primary"
                      variant="outlined"
                      size="small"
                      prepend-icon="mdi-plus"
                      @click="activateModule(module.id)"
                      :loading="userStore.loading"
                    >
                      Activate
                    </v-btn>
                    <v-btn
                      v-else
                      color="success"
                      variant="outlined"
                      size="small"
                      prepend-icon="mdi-check"
                      disabled
                    >
                      Active
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                      icon="mdi-information"
                      size="small"
                      variant="text"
                    ></v-btn>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Loading State -->
    <v-row v-if="moduleStore.loading">
      <v-col cols="12" class="text-center py-8">
        <v-progress-circular
          indeterminate
          color="primary"
          size="64"
        ></v-progress-circular>
        <div class="text-h6 mt-4">Loading modules...</div>
      </v-col>
    </v-row>

    <!-- Empty State -->
    <v-row v-if="!moduleStore.loading && filteredCategories.length === 0">
      <v-col cols="12" class="text-center py-8">
        <v-icon size="64" color="grey">mdi-puzzle-outline</v-icon>
        <div class="text-h6 mt-4">No modules found</div>
        <div class="text-body-2 mb-4">Try adjusting your filters</div>
        <v-btn
          color="primary"
          @click="clearFilters"
        >
          Clear Filters
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useUserStore } from '../stores/user'
import { useModuleStore } from '../stores/modules'

const userStore = useUserStore()
const moduleStore = useModuleStore()

const selectedCategory = ref('')
const searchQuery = ref('')
const showOnlyRecommended = ref(false)

const categoryOptions = computed(() => 
  moduleStore.moduleCategories.map(cat => ({
    title: cat.title,
    value: cat.title
  }))
)

const filteredCategories = computed(() => {
  let categories = moduleStore.moduleCategories
  
  // Filter by category
  if (selectedCategory.value) {
    categories = categories.filter(cat => cat.title === selectedCategory.value)
  }
  
  // Filter by search query
  if (searchQuery.value) {
    categories = categories.map(cat => ({
      ...cat,
      modules: cat.modules.filter(module =>
        module.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        module.description.toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    })).filter(cat => cat.modules.length > 0)
  }
  
  // Filter by recommended only
  if (showOnlyRecommended.value && userStore.businessType) {
    categories = categories.map(cat => ({
      ...cat,
      modules: cat.modules.filter(module => isModuleRecommended(module.id))
    })).filter(cat => cat.modules.length > 0)
  }
  
  return categories
})

const isModuleActive = (moduleId) => {
  return userStore.activeModules.some(userModule => userModule.module.id === moduleId)
}

const isModuleRecommended = (moduleId) => {
  if (!userStore.businessType) return false
  return moduleStore.recommendedModules.some(module => module.id === moduleId)
}

const activateModule = async (moduleId) => {
  try {
    await userStore.activateModule(moduleId)
    // You can add success notification here
  } catch (error) {
    console.error('Error activating module:', error)
    // You can add error notification here
  }
}

const filterModules = () => {
  // The computed property will automatically update
}

const clearFilters = () => {
  selectedCategory.value = ''
  searchQuery.value = ''
  showOnlyRecommended.value = false
}

const getCategoryColor = (category) => {
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

const getCategoryIcon = (category) => {
  const iconMap = {
    'Core': 'mdi-star',
    'Manufacturing': 'mdi-factory',
    'Sales': 'mdi-cart',
    'Finance': 'mdi-calculator',
    'HR': 'mdi-account-group',
    'Inventory': 'mdi-package-variant',
    'Production': 'mdi-cog'
  }
  return iconMap[category] || 'mdi-puzzle'
}

const getModuleColor = (category) => {
  return getCategoryColor(category)
}

onMounted(async () => {
  await Promise.all([
    moduleStore.loadModules(),
    moduleStore.loadModuleCategories(),
    userStore.loadActiveModules()
  ])
  
  // Load recommendations if business type is set
  if (userStore.businessType) {
    await moduleStore.loadRecommendations(userStore.businessType.id)
  }
})
</script>

<style scoped>
.module-card {
  transition: all 0.2s ease-in-out;
}

.module-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.active-module {
  border: 2px solid rgb(var(--v-theme-success));
  background-color: rgba(var(--v-theme-success), 0.05);
}

.recommended-module {
  border-left: 4px solid rgb(var(--v-theme-success));
}
</style>
