<template>
  <div v-if="show" class="loading-overlay" :class="overlayClass">
    <div class="loading-content">
      <!-- Spinner -->
      <div class="loading-spinner">
        <v-progress-circular
          :size="spinnerSize"
          :width="spinnerWidth"
          :color="spinnerColor"
          indeterminate
        />
      </div>
      
      <!-- Text -->
      <div v-if="text" class="loading-text">
        {{ text }}
      </div>
      
      <!-- Progress Bar (if percentage is provided) -->
      <div v-if="showProgress && percentage !== null" class="loading-progress">
        <v-progress-linear
          :model-value="percentage"
          :color="progressColor"
          height="4"
          rounded
        />
        <div class="loading-percentage">
          {{ Math.round(percentage) }}%
        </div>
      </div>
      
      <!-- Custom Content -->
      <div v-if="$slots.default" class="loading-custom">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  text: String,
  percentage: {
    type: Number,
    default: null
  },
  showProgress: {
    type: Boolean,
    default: false
  },
  spinnerSize: {
    type: [Number, String],
    default: 50
  },
  spinnerWidth: {
    type: [Number, String],
    default: 4
  },
  spinnerColor: {
    type: String,
    default: 'primary'
  },
  progressColor: {
    type: String,
    default: 'primary'
  },
  overlayColor: {
    type: String,
    default: 'rgba(255, 255, 255, 0.9)'
  },
  blur: {
    type: Boolean,
    default: true
  },
  fullscreen: {
    type: Boolean,
    default: false
  }
})

const overlayClass = computed(() => {
  const classes = []
  
  if (props.fullscreen) {
    classes.push('loading-overlay--fullscreen')
  }
  
  if (props.blur) {
    classes.push('loading-overlay--blur')
  }
  
  return classes
})
</script>

<style scoped>
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: v-bind(overlayColor);
  z-index: 1000;
  transition: all 0.3s ease;
}

.loading-overlay--fullscreen {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
}

.loading-overlay--blur {
  backdrop-filter: blur(4px);
}

.loading-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  max-width: 300px;
  padding: 24px;
}

.loading-spinner {
  margin-bottom: 16px;
  animation: pulse 2s ease-in-out infinite;
}

.loading-text {
  font-size: 1rem;
  font-weight: 500;
  color: #424242;
  margin-bottom: 16px;
  animation: fadeInOut 2s ease-in-out infinite;
}

.loading-progress {
  width: 100%;
  margin-top: 16px;
}

.loading-percentage {
  font-size: 0.875rem;
  color: #757575;
  margin-top: 8px;
  text-align: center;
}

.loading-custom {
  margin-top: 16px;
}

/* Animations */
@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.05);
    opacity: 0.8;
  }
}

@keyframes fadeInOut {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

/* Responsive */
@media (max-width: 600px) {
  .loading-content {
    padding: 16px;
  }
  
  .loading-text {
    font-size: 0.875rem;
  }
}
</style>
