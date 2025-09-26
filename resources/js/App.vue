<template>
  <v-app>
    <!-- Authenticated Layout -->
    <template v-if="userStore.isAuthenticated">
      <!-- Left Sidebar - Main Navigation -->
      <v-navigation-drawer
        v-model="leftDrawer"
        :rail="leftRail"
        permanent
        class="left-sidebar"
        width="280"
        @click="leftRail = false"
      >
      <!-- User Profile Section -->
      <v-list-item class="user-profile">
        <template v-slot:prepend>
          <v-avatar color="primary" size="40">
            <v-img
              src="https://randomuser.me/api/portraits/men/85.jpg"
              alt="User Avatar"
            />
          </v-avatar>
        </template>
        
        <v-list-item-title class="text-h6 font-weight-bold">
          {{ user?.name || 'ERP User' }}
        </v-list-item-title>
        
        <v-list-item-subtitle class="text-caption">
          Multi-Industri ERP
        </v-list-item-subtitle>

        <template v-slot:append>
          <v-btn
            variant="text"
            icon="mdi-chevron-left"
            size="small"
            @click.stop="leftRail = !leftRail"
          />
        </template>
      </v-list-item>

      <v-divider class="my-2" />

      <!-- Main Navigation Menu -->
      <v-list density="compact" nav class="main-nav">
        <v-list-item
          prepend-icon="mdi-view-dashboard"
          title="Dashboard"
          value="dashboard"
          :to="{ name: 'Dashboard' }"
          :active="isActiveRoute('Dashboard')"
        />

        <v-list-item
          prepend-icon="mdi-domain"
          title="Business Type"
          value="business-type"
          :to="{ name: 'BusinessTypeSelection' }"
          :active="isActiveRoute('BusinessTypeSelection')"
        />

        <v-list-item
          prepend-icon="mdi-puzzle"
          title="Module Manager"
          value="modules"
          :to="{ name: 'ModuleManagement' }"
          :active="isActiveRoute('ModuleManagement')"
        />

        <v-divider class="my-3" />

        <!-- Active Module Menu Items (Dynamic) -->
        <v-list-subheader class="text-uppercase text-caption font-weight-bold">
          Active Modules
        </v-list-subheader>
        
        <v-list-item
          v-for="userModule in activeModuleMenus"
          :key="userModule.id"
          :prepend-icon="getModuleIcon(userModule.module.name)"
          :title="userModule.module.name"
          :value="`module-${userModule.module.id}`"
          @click="setActiveModule(userModule)"
          :active="activeModule?.id === userModule.module.id"
          class="module-menu-item"
        />

        <v-divider class="my-3" />

        <v-list-item
          prepend-icon="mdi-account-group"
          title="Users"
          value="users"
          :to="{ name: 'UserManagement' }"
          :active="isActiveRoute('UserManagement')"
        />

        <v-list-item
          prepend-icon="mdi-cog"
          title="Settings"
          value="settings"
          :to="{ name: 'Settings' }"
          :active="isActiveRoute('Settings')"
        />
      </v-list>

      <!-- Footer Actions -->
      <template v-slot:append>
        <div class="pa-3">
          <v-btn
            block
            color="primary"
            variant="outlined"
            prepend-icon="mdi-logout"
            size="small"
            @click="logout"
          >
            Logout
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>

    <!-- Right Sidebar - Module Icons -->
    <v-navigation-drawer
      v-model="rightDrawer"
      location="right"
      permanent
      class="right-sidebar"
      width="80"
    >
      <div class="module-icons-container">
        <div class="text-center pa-2">
          <v-icon color="primary" size="24">mdi-puzzle</v-icon>
          <div class="text-caption text-center mt-1">Modules</div>
        </div>
        
        <v-divider class="my-2" />
        
        <!-- Module Icons Grid -->
        <div class="module-icons-grid">
          <v-btn
            v-for="userModule in userStore.activeModules"
            :key="userModule.id"
            :icon="getModuleIcon(userModule.module.name)"
            :color="activeModule?.id === userModule.module.id ? 'primary' : 'grey'"
            :variant="activeModule?.id === userModule.module.id ? 'flat' : 'text'"
            size="large"
            class="module-icon-btn"
            @click="setActiveModule(userModule)"
          >
            <v-icon size="24">{{ getModuleIcon(userModule.module.name) }}</v-icon>
            
            <!-- Tooltip -->
            <v-tooltip activator="parent" location="left">
              {{ userModule.module.name }}
            </v-tooltip>
          </v-btn>
        </div>
        
        <!-- Add Module Button -->
        <v-divider class="my-3" />
        <div class="text-center">
          <v-btn
            icon="mdi-plus"
            color="success"
            variant="outlined"
            size="small"
            :to="{ name: 'ModuleManagement' }"
          >
            <v-icon size="20">mdi-plus</v-icon>
            <v-tooltip activator="parent" location="left">
              Add More Modules
            </v-tooltip>
          </v-btn>
        </div>
      </div>
    </v-navigation-drawer>

    <!-- Top App Bar -->
    <v-app-bar
      :elevation="1"
      color="white"
      class="app-bar"
    >
      <v-app-bar-nav-icon @click="toggleMobileNav" class="d-md-none" />
      <v-app-bar-nav-icon @click="leftDrawer = !leftDrawer" class="d-none d-md-block" />
      
      <v-toolbar-title class="d-flex align-center">
        <v-icon color="primary" class="mr-2">mdi-factory</v-icon>
        <span class="font-weight-bold text-primary">ERP</span>
        <span class="text-grey ml-1">Modular</span>
        
        <!-- Active Module Indicator -->
        <v-chip
          v-if="activeModule"
          size="small"
          color="primary"
          variant="outlined"
          class="ml-3"
        >
          <v-icon start size="16">{{ getModuleIcon(activeModule.name) }}</v-icon>
          {{ activeModule.name }}
        </v-chip>
      </v-toolbar-title>

      <v-spacer />

             <!-- Quick Actions -->
             <v-btn icon variant="text" class="mr-2">
               <v-icon>mdi-bell-outline</v-icon>
               <v-badge color="error" content="3" floating />
             </v-btn>

             <!-- Theme Toggle -->
             <ThemeToggle class="mr-2" />

             <v-btn icon variant="text">
               <v-icon>mdi-account-circle-outline</v-icon>
             </v-btn>
    </v-app-bar>

    <!-- Main Content Area -->
    <v-main class="main-content">
      <v-container fluid class="pa-0">
        <router-view />
      </v-container>
    </v-main>

      <!-- Footer -->
      <v-footer app class="app-footer">
        <span class="text-caption">
          &copy; {{ new Date().getFullYear() }} ERP Modular - Multi-Industri Solution
        </span>
        <v-spacer />
        <span class="text-caption">
          Active Modules: {{ userStore.activeModules.length }}
        </span>
      </v-footer>
    </template>

    <!-- Non-authenticated Layout -->
    <template v-else>
      <router-view />
    </template>
  </v-app>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from './stores/user'
import { useModuleStore } from './stores/modules'
import { ThemeToggle } from './components/UI'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const moduleStore = useModuleStore()

// Sidebar states
const leftDrawer = ref(true)
const leftRail = ref(false)
const rightDrawer = ref(true)

// Active module state
const activeModule = ref(null)

// Computed properties
const user = computed(() => userStore.user)
const activeModuleMenus = computed(() => userStore.activeModules)

// Methods
const isActiveRoute = (routeName) => {
  return route.name === routeName
}

const setActiveModule = (userModule) => {
  activeModule.value = userModule.module
  // You can add logic here to navigate to module-specific routes
  // For now, we'll just set the active module
}

const getModuleIcon = (moduleName) => {
  return moduleStore.getModuleIcon(moduleName)
}

const logout = () => {
  userStore.logout()
  router.push({ name: 'Login' })
}

// Mobile navigation
const mobileNavOpen = ref(false)

const toggleMobileNav = () => {
  mobileNavOpen.value = !mobileNavOpen.value
}

// Close mobile nav when route changes
watch(() => route.path, () => {
  if (mobileNavOpen.value) {
    mobileNavOpen.value = false
  }
})

// Authentication check and redirect
const checkAuth = () => {
  const token = localStorage.getItem('auth_token')
  if (token && !userStore.isAuthenticated) {
    // Try to load user data from token
    // This would typically involve an API call to get user info
  }
  
  // Redirect logic
  const publicRoutes = ['LandingPage', 'Login', 'Register']
  if (!userStore.isAuthenticated && !publicRoutes.includes(route.name)) {
    router.push({ name: 'LandingPage' })
  } else if (userStore.isAuthenticated && publicRoutes.includes(route.name)) {
    router.push({ name: 'Dashboard' })
  }
}

// Load active modules on mount
onMounted(async () => {
  checkAuth()
  if (userStore.isAuthenticated) {
    await userStore.loadActiveModules()
  }
})

// Watch for route changes
watch(() => route.name, () => {
  checkAuth()
})
</script>

<style scoped>
/* Left Sidebar Styling */
.left-sidebar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.left-sidebar .v-list-item--active {
  background-color: rgba(255, 255, 255, 0.15) !important;
  border-radius: 8px;
  margin: 2px 8px;
}

.left-sidebar .v-list-item {
  border-radius: 8px;
  margin: 2px 8px;
  transition: all 0.2s ease;
}

.left-sidebar .v-list-item:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.user-profile {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  margin: 8px;
  backdrop-filter: blur(10px);
}

.main-nav .v-list-subheader {
  color: rgba(255, 255, 255, 0.8);
  padding: 8px 16px;
}

.module-menu-item {
  background: rgba(255, 255, 255, 0.05);
  border-left: 3px solid transparent;
}

.module-menu-item.v-list-item--active {
  background-color: rgba(255, 255, 255, 0.15) !important;
  border-left-color: #4CAF50;
}

/* Right Sidebar Styling */
.right-sidebar {
  background: #f8f9fa;
  border-left: 1px solid #e0e0e0;
}

.module-icons-container {
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 8px 4px;
}

.module-icons-grid {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: center;
}

.module-icon-btn {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  transition: all 0.2s ease;
}

.module-icon-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* App Bar Styling */
.app-bar {
  border-bottom: 1px solid #e0e0e0;
  backdrop-filter: blur(10px);
}

.app-bar .v-toolbar-title {
  font-size: 1.25rem;
}

/* Main Content Styling */
.main-content {
  background: #fafafa;
  min-height: calc(100vh - 64px - 36px);
}

/* Footer Styling */
.app-footer {
  background: white;
  border-top: 1px solid #e0e0e0;
  color: #666;
}

       /* Responsive Design */
       @media (max-width: 1264px) {
         .left-sidebar {
           width: 260px !important;
         }
         
         .right-sidebar {
           width: 70px !important;
         }
       }

       @media (max-width: 960px) {
         .left-sidebar {
           width: 240px !important;
         }
         
         .right-sidebar {
           width: 60px !important;
         }
         
         .module-icon-btn {
           width: 44px;
           height: 44px;
         }
       }

       @media (max-width: 768px) {
         .left-sidebar {
           width: 100% !important;
           position: fixed !important;
           z-index: 1000;
           transform: translateX(-100%);
           transition: transform 0.3s ease;
         }
         
         .left-sidebar.v-navigation-drawer--active {
           transform: translateX(0);
         }
         
         .right-sidebar {
           display: none !important;
         }
         
         .module-icon-btn {
           width: 40px;
           height: 40px;
         }
         
         .app-bar .v-toolbar-title {
           font-size: 1rem;
         }
         
         .main-content {
           margin-left: 0 !important;
           margin-right: 0 !important;
         }
       }

       @media (max-width: 600px) {
         .left-sidebar {
           width: 100% !important;
         }
         
         .module-icon-btn {
           width: 36px;
           height: 36px;
         }
         
         .app-bar .v-toolbar-title {
           font-size: 0.9rem;
         }
         
         .user-profile {
           padding: 12px;
         }
         
         .main-nav .v-list-item {
           margin: 1px 4px;
         }
       }

       @media (max-width: 400px) {
         .module-icon-btn {
           width: 32px;
           height: 32px;
         }
         
         .app-bar .v-toolbar-title {
           font-size: 0.8rem;
         }
       }

/* Animation Classes */
.v-list-item {
  transition: all 0.2s ease-in-out;
}

.v-btn {
  transition: all 0.2s ease-in-out;
}

/* Custom Scrollbar */
.left-sidebar ::-webkit-scrollbar {
  width: 4px;
}

.left-sidebar ::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
}

.left-sidebar ::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
}

.left-sidebar ::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style>
