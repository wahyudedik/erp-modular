<template>
  <v-card
    class="module-card"
    :class="{ 
      'module-card--active': isActive,
      'module-card--recommended': isRecommended,
      'module-card--disabled': isDisabled
    }"
    elevation="2"
    hover
    @click="handleCardClick"
  >
    <!-- Module Header -->
    <v-card-title class="module-card__header">
      <div class="d-flex align-center">
        <!-- Module Icon -->
        <v-avatar
          :color="iconColor"
          size="48"
          class="module-card__icon"
        >
          <v-icon :color="iconTextColor" size="24">
            {{ moduleIcon }}
          </v-icon>
        </v-avatar>

        <!-- Module Info -->
        <div class="ml-3 flex-grow-1">
          <h3 class="text-h6 font-weight-bold mb-1">
            {{ module.name }}
          </h3>
          <p class="text-caption text-medium-emphasis mb-0">
            {{ module.category }}
          </p>
        </div>

        <!-- Status Indicators -->
        <div class="d-flex align-center">
          <!-- Recommended Badge -->
          <v-chip
            v-if="isRecommended"
            size="small"
            color="success"
            variant="outlined"
            class="mr-2"
          >
            <v-icon start size="12">mdi-star</v-icon>
            Recommended
          </v-chip>

          <!-- Active Badge -->
          <v-chip
            v-if="isActive"
            size="small"
            color="primary"
            variant="flat"
          >
            <v-icon start size="12">mdi-check</v-icon>
            Active
          </v-chip>

          <!-- Action Menu -->
          <v-menu>
            <template v-slot:activator="{ props }">
              <v-btn
                icon="mdi-dots-vertical"
                size="small"
                variant="text"
                v-bind="props"
                @click.stop
              />
            </template>
            <v-list density="compact">
              <v-list-item
                v-if="!isActive"
                prepend-icon="mdi-play"
                title="Activate"
                @click="activateModule"
              />
              <v-list-item
                v-if="isActive"
                prepend-icon="mdi-pause"
                title="Deactivate"
                @click="deactivateModule"
              />
              <v-list-item
                v-if="isActive"
                prepend-icon="mdi-cog"
                title="Configure"
                @click="configureModule"
              />
              <v-list-item
                prepend-icon="mdi-information"
                title="Details"
                @click="showDetails"
              />
            </v-list>
          </v-menu>
        </div>
      </div>
    </v-card-title>

    <!-- Module Description -->
    <v-card-text class="module-card__content">
      <p class="text-body-2 text-medium-emphasis mb-3">
        {{ module.description }}
      </p>

      <!-- Module Features -->
      <div v-if="module.features && module.features.length" class="mb-3">
        <div class="text-caption text-medium-emphasis mb-1">Key Features:</div>
        <v-chip
          v-for="feature in module.features.slice(0, 3)"
          :key="feature"
          size="x-small"
          variant="outlined"
          class="mr-1 mb-1"
        >
          {{ feature }}
        </v-chip>
        <v-chip
          v-if="module.features.length > 3"
          size="x-small"
          variant="outlined"
          color="primary"
        >
          +{{ module.features.length - 3 }} more
        </v-chip>
      </div>

      <!-- Module Stats -->
      <div class="d-flex justify-space-between text-caption">
        <div class="d-flex align-center">
          <v-icon size="12" class="mr-1">mdi-clock-outline</v-icon>
          <span>Setup: {{ module.setup_time || '5 min' }}</span>
        </div>
        <div class="d-flex align-center">
          <v-icon size="12" class="mr-1">mdi-account-group</v-icon>
          <span>{{ module.users_count || 0 }} users</span>
        </div>
      </div>
    </v-card-text>

    <!-- Module Actions -->
    <v-card-actions class="module-card__actions">
      <v-spacer />
      
      <!-- Primary Action Button -->
      <v-btn
        v-if="!isActive"
        :color="primaryButtonColor"
        :variant="primaryButtonVariant"
        :loading="loading"
        @click.stop="activateModule"
      >
        <v-icon start>{{ primaryButtonIcon }}</v-icon>
        {{ primaryButtonText }}
      </v-btn>

      <v-btn
        v-else
        color="success"
        variant="outlined"
        size="small"
        @click.stop="configureModule"
      >
        <v-icon start size="16">mdi-cog</v-icon>
        Configure
      </v-btn>
    </v-card-actions>

    <!-- Loading Overlay -->
    <v-overlay
      v-model="loading"
      contained
      class="module-card__overlay"
    >
      <v-progress-circular
        indeterminate
        color="primary"
        size="48"
      />
    </v-overlay>
  </v-card>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useModuleStore } from '../stores/modules'

const props = defineProps({
  module: {
    type: Object,
    required: true
  },
  isActive: {
    type: Boolean,
    default: false
  },
  isRecommended: {
    type: Boolean,
    default: false
  },
  isDisabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['activate', 'deactivate', 'configure', 'details'])

const moduleStore = useModuleStore()
const loading = ref(false)

// Computed properties
const moduleIcon = computed(() => {
  return moduleStore.getModuleIcon(props.module.name)
})

const iconColor = computed(() => {
  if (props.isActive) return 'primary'
  if (props.isRecommended) return 'success'
  if (props.isDisabled) return 'grey-lighten-2'
  return 'grey-lighten-1'
})

const iconTextColor = computed(() => {
  if (props.isActive) return 'white'
  if (props.isRecommended) return 'white'
  if (props.isDisabled) return 'grey'
  return 'grey-darken-1'
})

const primaryButtonColor = computed(() => {
  if (props.isRecommended) return 'success'
  return 'primary'
})

const primaryButtonVariant = computed(() => {
  if (props.isRecommended) return 'flat'
  return 'outlined'
})

const primaryButtonIcon = computed(() => {
  if (props.isRecommended) return 'mdi-star'
  return 'mdi-plus'
})

const primaryButtonText = computed(() => {
  if (props.isRecommended) return 'Activate'
  return 'Add Module'
})

// Methods
const handleCardClick = () => {
  if (props.isDisabled) return
  emit('details', props.module)
}

const activateModule = async () => {
  if (props.isDisabled || loading.value) return
  
  loading.value = true
  try {
    await emit('activate', props.module)
  } catch (error) {
    console.error('Error activating module:', error)
  } finally {
    loading.value = false
  }
}

const deactivateModule = async () => {
  if (loading.value) return
  
  loading.value = true
  try {
    await emit('deactivate', props.module)
  } catch (error) {
    console.error('Error deactivating module:', error)
  } finally {
    loading.value = false
  }
}

const configureModule = () => {
  emit('configure', props.module)
}

const showDetails = () => {
  emit('details', props.module)
}
</script>

<style scoped>
.module-card {
  transition: all 0.3s ease;
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.module-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.module-card--active {
  border-color: rgb(var(--v-theme-primary));
  background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.05) 0%, rgba(var(--v-theme-primary), 0.02) 100%);
}

.module-card--recommended {
  border-color: rgb(var(--v-theme-success));
  background: linear-gradient(135deg, rgba(var(--v-theme-success), 0.05) 0%, rgba(var(--v-theme-success), 0.02) 100%);
}

.module-card--disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.module-card__header {
  padding: 16px 20px 12px;
  border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.module-card__content {
  padding: 16px 20px;
}

.module-card__actions {
  padding: 12px 20px 16px;
  background: rgba(var(--v-theme-surface), 0.5);
}

.module-card__icon {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.module-card__overlay {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(2px);
}

/* Animation for status changes */
.module-card--active .module-card__icon {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }
  50% {
    box-shadow: 0 2px 8px rgba(var(--v-theme-primary), 0.3);
  }
  100% {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .module-card__header {
    padding: 12px 16px 8px;
  }
  
  .module-card__content {
    padding: 12px 16px;
  }
  
  .module-card__actions {
    padding: 8px 16px 12px;
  }
}
</style>
