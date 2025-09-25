<template>
  <v-btn
    :icon="iconOnly"
    :variant="variant"
    :size="size"
    :color="color"
    @click="toggleTheme"
    class="theme-toggle"
  >
    <v-icon>{{ currentThemeIcon }}</v-icon>
    <span v-if="!iconOnly" class="ml-2">{{ currentThemeText }}</span>
    
    <v-tooltip v-if="showTooltip" activator="parent" location="bottom">
      {{ currentThemeText }}
    </v-tooltip>
  </v-btn>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  iconOnly: {
    type: Boolean,
    default: false
  },
  variant: {
    type: String,
    default: 'text'
  },
  size: {
    type: String,
    default: 'default'
  },
  color: {
    type: String,
    default: 'primary'
  },
  showTooltip: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['theme-change'])

// Theme state
const isDark = ref(false)

// Computed properties
const currentThemeIcon = computed(() => {
  return isDark.value ? 'mdi-weather-sunny' : 'mdi-weather-night'
})

const currentThemeText = computed(() => {
  return isDark.value ? 'Light Mode' : 'Dark Mode'
})

// Methods
const toggleTheme = () => {
  isDark.value = !isDark.value
  applyTheme()
  emit('theme-change', isDark.value ? 'dark' : 'light')
}

const applyTheme = () => {
  // Apply theme to Vuetify
  if (window.Vuetify) {
    // This would need to be implemented with Vuetify theme system
    // For now, we'll just store the preference
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
  }
  
  // Apply theme to document
  document.documentElement.setAttribute('data-theme', isDark.value ? 'dark' : 'light')
  
  // Add/remove dark class to body
  if (isDark.value) {
    document.body.classList.add('dark-theme')
  } else {
    document.body.classList.remove('dark-theme')
  }
}

const loadTheme = () => {
  const savedTheme = localStorage.getItem('theme')
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  
  if (savedTheme) {
    isDark.value = savedTheme === 'dark'
  } else {
    isDark.value = prefersDark
  }
  
  applyTheme()
}

// Lifecycle
onMounted(() => {
  loadTheme()
  
  // Listen for system theme changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('theme')) {
      isDark.value = e.matches
      applyTheme()
    }
  })
})
</script>

<style scoped>
.theme-toggle {
  transition: all 0.3s ease;
}

.theme-toggle:hover {
  transform: scale(1.05);
}

/* Dark theme styles */
:global(.dark-theme) {
  background-color: #121212;
  color: #ffffff;
}

:global(.dark-theme) .v-application {
  background-color: #121212 !important;
}

:global(.dark-theme) .v-card {
  background-color: #1e1e1e !important;
  color: #ffffff !important;
}

:global(.dark-theme) .v-navigation-drawer {
  background-color: #1e1e1e !important;
}

:global(.dark-theme) .v-app-bar {
  background-color: #1e1e1e !important;
}

/* Smooth transitions for theme changes */
:global(*) {
  transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}
</style>
