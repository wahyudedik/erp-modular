<template>
  <div class="empty-state" :class="containerClass">
    <div class="empty-state-content">
      <!-- Icon -->
      <div class="empty-state-icon">
        <v-icon
          :size="iconSize"
          :color="iconColor"
          :class="iconClass"
        >
          {{ icon }}
        </v-icon>
      </div>
      
      <!-- Title -->
      <h3 v-if="title" class="empty-state-title" :class="titleClass">
        {{ title }}
      </h3>
      
      <!-- Subtitle -->
      <p v-if="subtitle" class="empty-state-subtitle" :class="subtitleClass">
        {{ subtitle }}
      </p>
      
      <!-- Action Button -->
      <div v-if="showAction" class="empty-state-action">
        <v-btn
          :color="actionColor"
          :variant="actionVariant"
          :size="actionSize"
          :loading="actionLoading"
          :disabled="actionDisabled"
          @click="handleAction"
        >
          <v-icon v-if="actionIcon" :left="!actionIconRight" :right="actionIconRight">
            {{ actionIcon }}
          </v-icon>
          {{ actionText }}
        </v-btn>
      </div>
      
      <!-- Custom Content -->
      <div v-if="$slots.default" class="empty-state-custom">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Content
  title: String,
  subtitle: String,
  icon: {
    type: String,
    default: 'mdi-inbox-outline'
  },
  
  // Styling
  iconSize: {
    type: [Number, String],
    default: 64
  },
  iconColor: {
    type: String,
    default: 'grey'
  },
  iconClass: String,
  titleClass: String,
  subtitleClass: String,
  
  // Layout
  centered: {
    type: Boolean,
    default: true
  },
  fullHeight: {
    type: Boolean,
    default: false
  },
  padding: {
    type: String,
    default: 'large',
    validator: (value) => ['none', 'small', 'default', 'large'].includes(value)
  },
  
  // Action
  showAction: {
    type: Boolean,
    default: false
  },
  actionText: {
    type: String,
    default: 'Get Started'
  },
  actionIcon: String,
  actionIconRight: {
    type: Boolean,
    default: false
  },
  actionColor: {
    type: String,
    default: 'primary'
  },
  actionVariant: {
    type: String,
    default: 'elevated'
  },
  actionSize: {
    type: String,
    default: 'default'
  },
  actionLoading: Boolean,
  actionDisabled: Boolean
})

const emit = defineEmits(['action'])

const containerClass = computed(() => {
  const classes = []
  
  if (props.centered) {
    classes.push('empty-state--centered')
  }
  
  if (props.fullHeight) {
    classes.push('empty-state--full-height')
  }
  
  if (props.padding === 'none') {
    classes.push('empty-state--no-padding')
  } else if (props.padding === 'small') {
    classes.push('empty-state--small-padding')
  } else if (props.padding === 'large') {
    classes.push('empty-state--large-padding')
  }
  
  return classes
})

const handleAction = () => {
  emit('action')
}
</script>

<style scoped>
.empty-state {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  padding: 24px;
}

.empty-state--centered {
  text-align: center;
}

.empty-state--full-height {
  min-height: 100vh;
}

.empty-state--no-padding {
  padding: 0;
}

.empty-state--small-padding {
  padding: 12px;
}

.empty-state--large-padding {
  padding: 48px;
}

.empty-state-content {
  max-width: 400px;
  width: 100%;
}

.empty-state-icon {
  margin-bottom: 16px;
  opacity: 0.6;
}

.empty-state-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 8px;
  color: #424242;
}

.empty-state-subtitle {
  font-size: 1rem;
  color: #757575;
  margin-bottom: 24px;
  line-height: 1.5;
}

.empty-state-action {
  margin-top: 24px;
}

.empty-state-custom {
  margin-top: 24px;
}

/* Animation */
.empty-state {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive */
@media (max-width: 600px) {
  .empty-state {
    padding: 16px;
  }
  
  .empty-state-title {
    font-size: 1.25rem;
  }
  
  .empty-state-subtitle {
    font-size: 0.875rem;
  }
}
</style>
