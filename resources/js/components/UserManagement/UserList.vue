<template>
  <div class="user-management">
    <v-card>
      <v-card-title class="d-flex justify-space-between align-center">
        <span>User Management</span>
        <v-btn
          color="primary"
          @click="openInviteDialog"
          prepend-icon="mdi-account-plus"
        >
          Invite User
        </v-btn>
      </v-card-title>

      <v-card-text>
        <!-- Search and Filters -->
        <v-row class="mb-4">
          <v-col cols="12" md="6">
            <v-text-field
              v-model="search"
              label="Search users..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              clearable
              @input="debouncedSearch"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="selectedBusinessType"
              :items="businessTypes"
              item-title="name"
              item-value="id"
              label="Business Type"
              variant="outlined"
              clearable
              @update:model-value="filterUsers"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="statusFilter"
              :items="statusOptions"
              label="Status"
              variant="outlined"
              clearable
              @update:model-value="filterUsers"
            />
          </v-col>
        </v-row>

        <!-- Users Table -->
        <v-data-table
          :headers="headers"
          :items="users"
          :loading="loading"
          :items-per-page="15"
          class="elevation-1"
        >
          <template v-slot:item.name="{ item }">
            <div class="d-flex align-center">
              <v-avatar size="32" class="mr-3">
                <v-img
                  v-if="item.avatar"
                  :src="item.avatar"
                  :alt="item.name"
                />
                <v-icon v-else>mdi-account</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-medium">{{ item.name }}</div>
                <div class="text-caption text-grey">{{ item.email }}</div>
              </div>
            </div>
          </template>

          <template v-slot:item.business_type="{ item }">
            <v-chip
              :color="item.business_type?.color || 'primary'"
              size="small"
            >
              {{ item.business_type?.name }}
            </v-chip>
          </template>

          <template v-slot:item.is_active="{ item }">
            <v-chip
              :color="item.is_active ? 'success' : 'error'"
              size="small"
            >
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </v-chip>
          </template>

          <template v-slot:item.roles="{ item }">
            <div class="d-flex flex-wrap gap-1">
              <v-chip
                v-for="role in item.roles"
                :key="role.id"
                size="x-small"
                color="primary"
                variant="outlined"
              >
                {{ role.name }}
              </v-chip>
            </div>
          </template>

          <template v-slot:item.actions="{ item }">
            <div class="d-flex gap-1">
              <v-btn
                icon="mdi-eye"
                size="small"
                variant="text"
                @click="viewUser(item)"
              />
              <v-btn
                icon="mdi-pencil"
                size="small"
                variant="text"
                @click="editUser(item)"
              />
              <v-btn
                :icon="item.is_active ? 'mdi-account-off' : 'mdi-account-check'"
                size="small"
                variant="text"
                :color="item.is_active ? 'error' : 'success'"
                @click="toggleUserStatus(item)"
              />
            </div>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>

    <!-- User Details Dialog -->
    <UserDetailsDialog
      v-model="showUserDialog"
      :user="selectedUser"
      @update="handleUserUpdate"
    />

    <!-- Invite User Dialog -->
    <InviteUserDialog
      v-model="showInviteDialog"
      :business-types="businessTypes"
      @invited="handleUserInvited"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useUserStore } from '@/stores/user'
import { useBusinessTypeStore } from '@/stores/businessType'
import UserDetailsDialog from './UserDetailsDialog.vue'
import InviteUserDialog from './InviteUserDialog.vue'
import { debounce } from 'lodash-es'

// Stores
const userStore = useUserStore()
const businessTypeStore = useBusinessTypeStore()

// Reactive data
const users = ref([])
const businessTypes = ref([])
const loading = ref(false)
const search = ref('')
const selectedBusinessType = ref(null)
const statusFilter = ref(null)
const showUserDialog = ref(false)
const showInviteDialog = ref(false)
const selectedUser = ref(null)

// Table headers
const headers = [
  { title: 'User', key: 'name', sortable: true },
  { title: 'Business Type', key: 'business_type', sortable: true },
  { title: 'Status', key: 'is_active', sortable: true },
  { title: 'Roles', key: 'roles', sortable: false },
  { title: 'Actions', key: 'actions', sortable: false, align: 'center' }
]

// Status options
const statusOptions = [
  { title: 'Active', value: 'active' },
  { title: 'Inactive', value: 'inactive' }
]

// Computed
const debouncedSearch = debounce(() => {
  fetchUsers()
}, 500)

// Methods
const fetchUsers = async () => {
  loading.value = true
  try {
    const params = {
      search: search.value,
      business_type_id: selectedBusinessType.value,
      status: statusFilter.value
    }
    
    const response = await userStore.fetchUsers(params)
    users.value = response.data.data
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}

const fetchBusinessTypes = async () => {
  try {
    await businessTypeStore.fetchBusinessTypes()
    businessTypes.value = businessTypeStore.businessTypes
  } catch (error) {
    console.error('Error fetching business types:', error)
  }
}

const filterUsers = () => {
  fetchUsers()
}

const openInviteDialog = () => {
  showInviteDialog.value = true
}

const viewUser = (user) => {
  selectedUser.value = user
  showUserDialog.value = true
}

const editUser = (user) => {
  selectedUser.value = user
  showUserDialog.value = true
}

const toggleUserStatus = async (user) => {
  try {
    if (user.is_active) {
      await userStore.deactivateUser(user.id)
    } else {
      await userStore.activateUser(user.id)
    }
    await fetchUsers()
  } catch (error) {
    console.error('Error toggling user status:', error)
  }
}

const handleUserUpdate = () => {
  fetchUsers()
}

const handleUserInvited = () => {
  fetchUsers()
}

// Lifecycle
onMounted(() => {
  fetchUsers()
  fetchBusinessTypes()
})
</script>

<style scoped>
.user-management {
  padding: 16px;
}

.gap-1 {
  gap: 4px;
}
</style>
