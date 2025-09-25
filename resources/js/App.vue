<template>
  <v-app>
    <v-navigation-drawer
      v-model="drawer"
      :rail="rail"
      permanent
      @click="rail = false"
    >
      <v-list-item
        prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
        :title="user?.name || 'ERP User'"
        subtitle="Multi-Industri ERP"
        nav
      >
        <template v-slot:append>
          <v-btn
            variant="text"
            icon="mdi-chevron-left"
            @click.stop="rail = !rail"
          ></v-btn>
        </template>
      </v-list-item>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-item
          prepend-icon="mdi-view-dashboard"
          title="Dashboard"
          value="dashboard"
          :to="{ name: 'Dashboard' }"
        ></v-list-item>

        <v-list-item
          prepend-icon="mdi-domain"
          title="Business Type"
          value="business-type"
          :to="{ name: 'BusinessTypeSelection' }"
        ></v-list-item>

        <v-list-item
          prepend-icon="mdi-puzzle"
          title="Modules"
          value="modules"
          :to="{ name: 'ModuleManagement' }"
        ></v-list-item>

        <v-list-item
          prepend-icon="mdi-account-group"
          title="Users"
          value="users"
          :to="{ name: 'UserManagement' }"
        ></v-list-item>

        <v-list-item
          prepend-icon="mdi-cog"
          title="Settings"
          value="settings"
          :to="{ name: 'Settings' }"
        ></v-list-item>
      </v-list>

      <template v-slot:append>
        <div class="pa-2">
          <v-btn
            block
            color="primary"
            variant="outlined"
            prepend-icon="mdi-logout"
          >
            Logout
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>

    <v-app-bar
      :elevation="2"
    >
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      
      <v-toolbar-title>
        <span class="font-weight-bold text-primary">ERP</span>
        <span class="text-grey">Modular</span>
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon>mdi-bell</v-icon>
      </v-btn>

      <v-btn icon>
        <v-icon>mdi-account</v-icon>
      </v-btn>
    </v-app-bar>

    <v-main>
      <v-container fluid>
        <router-view />
      </v-container>
    </v-main>

    <v-footer app>
      <span>&copy; {{ new Date().getFullYear() }} ERP Modular - Multi-Industri Solution</span>
    </v-footer>
  </v-app>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useUserStore } from './stores/user'

const drawer = ref(true)
const rail = ref(true)

const userStore = useUserStore()
const user = computed(() => userStore.user)
</script>

<style scoped>
.v-navigation-drawer {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.v-list-item--active {
  background-color: rgba(255, 255, 255, 0.1) !important;
}

.v-toolbar-title {
  font-size: 1.5rem;
}
</style>
