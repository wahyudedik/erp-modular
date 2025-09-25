<template>
  <div>
    <!-- Header -->
    <v-row>
      <v-col cols="12">
        <v-card class="mb-6">
          <v-card-title class="text-h4">
            <v-icon left size="large">mdi-domain</v-icon>
            Select Your Business Type
          </v-card-title>
          <v-card-subtitle class="text-h6">
            Choose your business type to get personalized module recommendations
          </v-card-subtitle>
          <v-card-text>
            <p class="text-body-1">
              Our system will recommend the most suitable modules for your industry.
              You can always add or remove modules later.
            </p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Business Types Grid -->
    <v-row v-if="!moduleStore.loading">
      <v-col
        v-for="businessType in moduleStore.businessTypeOptions"
        :key="businessType.value"
        cols="12"
        md="6"
        lg="4"
      >
        <v-card
          class="business-type-card"
          hover
          @click="selectBusinessType(businessType.value)"
          :class="{ 'selected': selectedBusinessType === businessType.value }"
        >
          <v-card-title class="d-flex align-center">
            <v-icon
              :color="selectedBusinessType === businessType.value ? 'primary' : 'grey'"
              size="large"
              class="mr-3"
            >
              {{ businessType.icon }}
            </v-icon>
            {{ businessType.title }}
          </v-card-title>
          <v-card-text>
            <p class="text-body-2">{{ businessType.subtitle }}</p>
          </v-card-text>
          <v-card-actions v-if="selectedBusinessType === businessType.value">
            <v-btn
              color="primary"
              variant="text"
              prepend-icon="mdi-check"
            >
              Selected
            </v-btn>
          </v-card-actions>
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
        <div class="text-h6 mt-4">Loading business types...</div>
      </v-col>
    </v-row>

    <!-- Recommendations Preview -->
    <v-row v-if="selectedBusinessType && recommendations.length > 0">
      <v-col cols="12">
        <v-card>
          <v-card-title>
            <v-icon left>mdi-lightbulb-on</v-icon>
            Recommended Modules for {{ getSelectedBusinessTypeName() }}
          </v-card-title>
          <v-card-text>
            <p class="text-body-1 mb-4">
              Based on your business type, we recommend these modules:
            </p>
            <v-row>
              <v-col
                v-for="recommendation in recommendations"
                :key="recommendation.id"
                cols="12"
                md="6"
                lg="4"
              >
                <v-card
                  class="recommendation-card"
                  variant="outlined"
                >
                  <v-card-title class="d-flex align-center">
                    <v-icon
                      color="primary"
                      class="mr-3"
                    >
                      {{ moduleStore.getModuleIcon(recommendation.name) }}
                    </v-icon>
                    {{ recommendation.name }}
                  </v-card-title>
                  <v-card-subtitle>
                    {{ recommendation.category }}
                    <v-chip
                      size="small"
                      color="primary"
                      variant="outlined"
                      class="ml-2"
                    >
                      Priority {{ recommendation.priority }}
                    </v-chip>
                  </v-card-subtitle>
                  <v-card-text>
                    <p class="text-body-2">{{ recommendation.description }}</p>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Action Buttons -->
    <v-row v-if="selectedBusinessType">
      <v-col cols="12" class="text-center">
        <v-btn
          color="primary"
          size="large"
          prepend-icon="mdi-arrow-right"
          @click="confirmSelection"
          :loading="userStore.loading"
        >
          Continue with {{ getSelectedBusinessTypeName() }}
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'
import { useModuleStore } from '../stores/modules'

const router = useRouter()
const userStore = useUserStore()
const moduleStore = useModuleStore()

const selectedBusinessType = ref(null)
const recommendations = ref([])

const getSelectedBusinessTypeName = () => {
  if (!selectedBusinessType.value) return ''
  const businessType = moduleStore.getBusinessTypeById(selectedBusinessType.value)
  return businessType?.name || ''
}

const selectBusinessType = async (businessTypeId) => {
  selectedBusinessType.value = businessTypeId
  
  // Load recommendations for selected business type
  try {
    const response = await moduleStore.loadRecommendations(businessTypeId)
    recommendations.value = response.data
  } catch (error) {
    console.error('Error loading recommendations:', error)
  }
}

const confirmSelection = async () => {
  try {
    await userStore.setBusinessType(selectedBusinessType.value)
    
    // Show success message
    // You can add a snackbar here
    
    // Redirect to module management
    router.push({ name: 'ModuleManagement' })
  } catch (error) {
    console.error('Error setting business type:', error)
    // You can add error handling here
  }
}

onMounted(async () => {
  await moduleStore.loadBusinessTypes()
})
</script>

<style scoped>
.business-type-card {
  transition: all 0.2s ease-in-out;
  cursor: pointer;
}

.business-type-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.business-type-card.selected {
  border: 2px solid rgb(var(--v-theme-primary));
  background-color: rgba(var(--v-theme-primary), 0.05);
}

.recommendation-card {
  border: 1px solid rgba(var(--v-theme-primary), 0.2);
}
</style>
