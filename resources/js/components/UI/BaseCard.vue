<template>
  <v-card
    :elevation="elevation"
    :color="color"
    :class="cardClass"
    v-bind="$attrs"
  >
    <v-card-title v-if="title || $slots.title" class="d-flex align-center">
      <slot name="title">
        <v-icon v-if="icon" :color="iconColor" class="mr-2">{{ icon }}</v-icon>
        {{ title }}
      </slot>
    </v-card-title>

    <v-card-subtitle v-if="subtitle || $slots.subtitle">
      <slot name="subtitle">{{ subtitle }}</slot>
    </v-card-subtitle>

    <v-card-text v-if="$slots.default" :class="textClass">
      <slot />
    </v-card-text>

    <v-card-actions v-if="$slots.actions" :class="actionsClass">
      <slot name="actions" />
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  subtitle: String,
  icon: String,
  iconColor: {
    type: String,
    default: 'primary'
  },
  elevation: {
    type: [Number, String],
    default: 2
  },
  color: String,
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'outlined', 'flat', 'elevated'].includes(value)
  },
  padding: {
    type: String,
    default: 'default'
  }
})

const cardClass = computed(() => {
  const classes = []
  
  if (props.variant === 'outlined') {
    classes.push('border')
  } else if (props.variant === 'flat') {
    classes.push('v-card--flat')
  } else if (props.variant === 'elevated') {
    classes.push('v-card--elevated')
  }
  
  return classes
})

const textClass = computed(() => {
  const classes = []
  
  if (props.padding === 'none') {
    classes.push('pa-0')
  } else if (props.padding === 'small') {
    classes.push('pa-2')
  } else if (props.padding === 'large') {
    classes.push('pa-6')
  }
  
  return classes
})

const actionsClass = computed(() => {
  const classes = []
  
  if (props.padding === 'none') {
    classes.push('pa-0')
  } else if (props.padding === 'small') {
    classes.push('pa-2')
  } else if (props.padding === 'large') {
    classes.push('pa-6')
  }
  
  return classes
})
</script>

<style scoped>
.v-card {
  transition: all 0.3s ease;
  border-radius: 12px;
}

.v-card:hover {
  transform: translateY(-2px);
}
</style>
