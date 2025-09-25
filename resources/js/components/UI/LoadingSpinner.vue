<template>
  <div class="loading-container" :class="containerClass">
    <v-progress-circular
      :size="size"
      :width="width"
      :color="color"
      :indeterminate="indeterminate"
      :value="value"
      :class="spinnerClass"
    >
      <slot name="content">
        <span v-if="showText" class="text-caption">{{ text }}</span>
      </slot>
    </v-progress-circular>
    
    <div v-if="showText && textPosition === 'below'" class="text-center mt-2">
      <span class="text-caption">{{ text }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: {
    type: [Number, String],
    default: 40
  },
  width: {
    type: [Number, String],
    default: 4
  },
  color: {
    type: String,
    default: 'primary'
  },
  indeterminate: {
    type: Boolean,
    default: true
  },
  value: {
    type: [Number, String],
    default: 0
  },
  text: String,
  showText: Boolean,
  textPosition: {
    type: String,
    default: 'center',
    validator: (value) => ['center', 'below'].includes(value)
  },
  overlay: Boolean,
  fullscreen: Boolean
})

const containerClass = computed(() => {
  const classes = []
  
  if (props.overlay) {
    classes.push('loading-overlay')
  }
  
  if (props.fullscreen) {
    classes.push('loading-fullscreen')
  }
  
  return classes
})

const spinnerClass = computed(() => {
  const classes = []
  
  if (props.showText && props.textPosition === 'center') {
    classes.push('loading-with-text')
  }
  
  return classes
})
</script>

<style scoped>
.loading-container {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(2px);
  z-index: 1000;
}

.loading-fullscreen {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(4px);
  z-index: 9999;
}

.loading-with-text {
  margin-bottom: 8px;
}

/* Animation for smooth transitions */
.loading-container {
  transition: all 0.3s ease;
}

/* Custom spinner animation */
.v-progress-circular {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
