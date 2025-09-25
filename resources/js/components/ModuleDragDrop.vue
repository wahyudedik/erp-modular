<template>
  <div class="module-drag-drop">
    <!-- Drop Zones -->
    <div class="drop-zones">
      <!-- Active Modules Zone -->
      <div
        ref="activeZone"
        class="drop-zone drop-zone--active"
        :class="{ 'drop-zone--dragover': activeDragOver }"
        @dragover.prevent="handleDragOver('active')"
        @dragleave.prevent="handleDragLeave('active')"
        @drop.prevent="handleDrop('active', $event)"
      >
        <div class="drop-zone__header">
          <v-icon color="primary" size="24" class="mr-2">mdi-check-circle</v-icon>
          <h3 class="text-h6 font-weight-bold">Active Modules</h3>
          <v-chip size="small" color="primary" variant="flat" class="ml-2">
            {{ activeModules.length }}
          </v-chip>
        </div>
        
        <div class="drop-zone__content">
          <div v-if="activeModules.length === 0" class="drop-zone__empty">
            <v-icon size="48" color="grey-lighten-1" class="mb-2">
              mdi-drag
            </v-icon>
            <p class="text-body-2 text-medium-emphasis">
              Drag modules here to activate them
            </p>
          </div>
          
          <div v-else class="module-grid">
            <div
              v-for="module in activeModules"
              :key="module.id"
              class="module-item module-item--active"
              draggable="true"
              @dragstart="handleDragStart($event, module, 'active')"
              @dragend="handleDragEnd"
            >
              <v-card elevation="1" class="module-card">
                <v-card-text class="pa-3">
                  <div class="d-flex align-center">
                    <v-avatar
                      color="primary"
                      size="32"
                      class="mr-3"
                    >
                      <v-icon color="white" size="16">
                        {{ getModuleIcon(module.name) }}
                      </v-icon>
                    </v-avatar>
                    
                    <div class="flex-grow-1">
                      <div class="text-body-2 font-weight-bold">
                        {{ module.name }}
                      </div>
                      <div class="text-caption text-medium-emphasis">
                        {{ module.category }}
                      </div>
                    </div>
                    
                    <v-btn
                      icon="mdi-close"
                      size="small"
                      variant="text"
                      color="error"
                      @click="deactivateModule(module)"
                    />
                  </div>
                </v-card-text>
              </v-card>
            </div>
          </div>
        </div>
      </div>

      <!-- Available Modules Zone -->
      <div
        ref="availableZone"
        class="drop-zone drop-zone--available"
        :class="{ 'drop-zone--dragover': availableDragOver }"
        @dragover.prevent="handleDragOver('available')"
        @dragleave.prevent="handleDragLeave('available')"
        @drop.prevent="handleDrop('available', $event)"
      >
        <div class="drop-zone__header">
          <v-icon color="grey-darken-1" size="24" class="mr-2">mdi-puzzle</v-icon>
          <h3 class="text-h6 font-weight-bold">Available Modules</h3>
          <v-chip size="small" color="grey" variant="outlined" class="ml-2">
            {{ availableModules.length }}
          </v-chip>
        </div>
        
        <div class="drop-zone__content">
          <div v-if="availableModules.length === 0" class="drop-zone__empty">
            <v-icon size="48" color="grey-lighten-1" class="mb-2">
              mdi-check-all
            </v-icon>
            <p class="text-body-2 text-medium-emphasis">
              All modules are active
            </p>
          </div>
          
          <div v-else class="module-grid">
            <div
              v-for="module in availableModules"
              :key="module.id"
              class="module-item module-item--available"
              draggable="true"
              @dragstart="handleDragStart($event, module, 'available')"
              @dragend="handleDragEnd"
            >
              <v-card
                elevation="1"
                class="module-card"
                :class="{ 'module-card--recommended': isModuleRecommended(module.id) }"
              >
                <v-card-text class="pa-3">
                  <div class="d-flex align-center">
                    <v-avatar
                      :color="isModuleRecommended(module.id) ? 'success' : 'grey-lighten-1'"
                      size="32"
                      class="mr-3"
                    >
                      <v-icon :color="isModuleRecommended(module.id) ? 'white' : 'grey-darken-1'" size="16">
                        {{ getModuleIcon(module.name) }}
                      </v-icon>
                    </v-avatar>
                    
                    <div class="flex-grow-1">
                      <div class="text-body-2 font-weight-bold">
                        {{ module.name }}
                      </div>
                      <div class="text-caption text-medium-emphasis">
                        {{ module.category }}
                      </div>
                      <div v-if="isModuleRecommended(module.id)" class="text-caption text-success">
                        <v-icon size="12" class="mr-1">mdi-star</v-icon>
                        Recommended
                      </div>
                    </div>
                    
                    <v-icon size="16" color="grey">
                      mdi-drag
                    </v-icon>
                  </div>
                </v-card-text>
              </v-card>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Drag Preview -->
    <div
      v-if="isDragging"
      ref="dragPreview"
      class="drag-preview"
      :style="dragPreviewStyle"
    >
      <v-card elevation="8" class="drag-preview__card">
        <v-card-text class="pa-2">
          <div class="d-flex align-center">
            <v-avatar
              :color="dragData.zone === 'active' ? 'primary' : 'grey-lighten-1'"
              size="24"
              class="mr-2"
            >
              <v-icon :color="dragData.zone === 'active' ? 'white' : 'grey-darken-1'" size="12">
                {{ getModuleIcon(dragData.module.name) }}
              </v-icon>
            </v-avatar>
            <span class="text-body-2 font-weight-bold">
              {{ dragData.module.name }}
            </span>
          </div>
        </v-card-text>
      </v-card>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useModuleStore } from '../stores/modules'

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
  }
})

const emit = defineEmits(['activate', 'deactivate', 'reorder'])

const moduleStore = useModuleStore()

// State
const isDragging = ref(false)
const activeDragOver = ref(false)
const availableDragOver = ref(false)
const dragData = ref({})
const dragPreviewStyle = ref({})

// Refs
const activeZone = ref(null)
const availableZone = ref(null)
const dragPreview = ref(null)

// Computed properties
const availableModules = computed(() => {
  const activeModuleIds = props.activeModules.map(module => module.id)
  return props.modules.filter(module => !activeModuleIds.includes(module.id))
})

// Methods
const getModuleIcon = (moduleName) => {
  return moduleStore.getModuleIcon(moduleName)
}

const isModuleRecommended = (moduleId) => {
  return props.recommendedModules.some(rec => rec.id === moduleId)
}

const handleDragStart = (event, module, zone) => {
  isDragging.value = true
  dragData.value = { module, zone }
  
  // Set drag effect
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/plain', JSON.stringify({ module, zone }))
  
  // Create custom drag image
  if (dragPreview.value) {
    event.dataTransfer.setDragImage(dragPreview.value, 50, 25)
  }
  
  // Update drag preview position
  updateDragPreview(event)
}

const handleDragOver = (zone) => {
  if (zone === 'active') {
    activeDragOver.value = true
    availableDragOver.value = false
  } else {
    availableDragOver.value = true
    activeDragOver.value = false
  }
}

const handleDragLeave = (zone) => {
  if (zone === 'active') {
    activeDragOver.value = false
  } else {
    availableDragOver.value = false
  }
}

const handleDrop = (zone, event) => {
  const data = JSON.parse(event.dataTransfer.getData('text/plain'))
  const { module, zone: sourceZone } = data
  
  if (sourceZone !== zone) {
    if (zone === 'active') {
      // Activate module
      emit('activate', module)
    } else {
      // Deactivate module
      emit('deactivate', module)
    }
  }
  
  // Reset drag state
  resetDragState()
}

const handleDragEnd = () => {
  resetDragState()
}

const resetDragState = () => {
  isDragging.value = false
  activeDragOver.value = false
  availableDragOver.value = false
  dragData.value = {}
}

const updateDragPreview = (event) => {
  if (dragPreview.value) {
    dragPreviewStyle.value = {
      position: 'fixed',
      left: event.clientX - 50 + 'px',
      top: event.clientY - 25 + 'px',
      zIndex: 9999,
      pointerEvents: 'none',
      opacity: 0.8
    }
  }
}

const activateModule = (module) => {
  emit('activate', module)
}

const deactivateModule = (module) => {
  emit('deactivate', module)
}

const handleMouseMove = (event) => {
  if (isDragging.value) {
    updateDragPreview(event)
  }
}

// Lifecycle
onMounted(() => {
  document.addEventListener('mousemove', handleMouseMove)
})

onUnmounted(() => {
  document.removeEventListener('mousemove', handleMouseMove)
})
</script>

<style scoped>
.module-drag-drop {
  width: 100%;
  min-height: 600px;
}

.drop-zones {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  height: 100%;
}

.drop-zone {
  border: 2px dashed rgba(var(--v-border-color), var(--v-border-opacity));
  border-radius: 12px;
  padding: 24px;
  transition: all 0.3s ease;
  min-height: 500px;
}

.drop-zone--active {
  background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.05) 0%, rgba(var(--v-theme-primary), 0.02) 100%);
  border-color: rgba(var(--v-theme-primary), 0.3);
}

.drop-zone--available {
  background: rgba(var(--v-theme-surface), 0.5);
  border-color: rgba(var(--v-border-color), var(--v-border-opacity));
}

.drop-zone--dragover {
  border-color: rgb(var(--v-theme-primary));
  background: rgba(var(--v-theme-primary), 0.1);
  transform: scale(1.02);
}

.drop-zone__header {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.drop-zone__content {
  min-height: 400px;
}

.drop-zone__empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 300px;
  text-align: center;
  color: rgba(var(--v-theme-on-surface), 0.6);
}

.module-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 12px;
}

.module-item {
  cursor: grab;
  transition: all 0.2s ease;
}

.module-item:active {
  cursor: grabbing;
}

.module-item:hover .module-card {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.module-card {
  transition: all 0.2s ease;
  border: 2px solid transparent;
}

.module-card--recommended {
  border-color: rgba(var(--v-theme-success), 0.3);
  background: linear-gradient(135deg, rgba(var(--v-theme-success), 0.05) 0%, rgba(var(--v-theme-success), 0.02) 100%);
}

.drag-preview {
  position: fixed;
  z-index: 9999;
  pointer-events: none;
  opacity: 0.8;
}

.drag-preview__card {
  min-width: 120px;
  background: white;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

/* Animation for drag states */
.drop-zone--dragover .drop-zone__header {
  animation: pulse 1s infinite;
}

@keyframes pulse {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
  100% {
    opacity: 1;
  }
}

/* Responsive design */
@media (max-width: 960px) {
  .drop-zones {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .drop-zone {
    min-height: 400px;
    padding: 16px;
  }
  
  .module-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 8px;
  }
}

@media (max-width: 600px) {
  .drop-zone {
    padding: 12px;
  }
  
  .drop-zone__header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  
  .module-grid {
    grid-template-columns: 1fr;
  }
}
</style>
