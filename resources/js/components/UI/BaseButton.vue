<template>
  <v-btn
    :color="color"
    :variant="variant"
    :size="size"
    :loading="loading"
    :disabled="disabled"
    :prepend-icon="prependIcon"
    :append-icon="appendIcon"
    :class="buttonClass"
    v-bind="$attrs"
    @click="handleClick"
  >
    <slot />
  </v-btn>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  color: {
    type: String,
    default: 'primary'
  },
  variant: {
    type: String,
    default: 'elevated',
    validator: (value) => ['elevated', 'flat', 'tonal', 'outlined', 'text', 'plain'].includes(value)
  },
  size: {
    type: String,
    default: 'default',
    validator: (value) => ['x-small', 'small', 'default', 'large', 'x-large'].includes(value)
  },
  loading: Boolean,
  disabled: Boolean,
  prependIcon: String,
  appendIcon: String,
  rounded: {
    type: [Boolean, String],
    default: false
  },
  block: Boolean
})

const emit = defineEmits(['click'])

const buttonClass = computed(() => {
  const classes = []
  
  if (props.rounded) {
    classes.push('rounded-pill')
  }
  
  if (props.block) {
    classes.push('w-100')
  }
  
  return classes
})

const handleClick = (event) => {
  if (!props.loading && !props.disabled) {
    emit('click', event)
  }
}
</script>

<style scoped>
.v-btn {
  transition: all 0.2s ease;
  text-transform: none;
  font-weight: 500;
}

.v-btn:hover {
  transform: translateY(-1px);
}

.v-btn:active {
  transform: translateY(0);
}
</style>
