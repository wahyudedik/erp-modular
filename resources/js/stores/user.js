import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../api/axios'

export const useUserStore = defineStore('user', () => {
  // State
  const user = ref(null)
  const businessType = ref(null)
  const activeModules = ref([])
  const loading = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!user.value)
  const hasBusinessType = computed(() => !!businessType.value)
  const moduleCount = computed(() => activeModules.value.length)

  // Actions
  const login = async (email, password) => {
    loading.value = true
    try {
      const response = await api.post('/auth/login', { email, password })
      user.value = response.data.data.user

      // Store token
      if (response.data.data.token) {
        localStorage.setItem('auth_token', response.data.data.token)
      }

      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const register = async (userData) => {
    loading.value = true
    try {
      const response = await api.post('/auth/register', userData)
      user.value = response.data.data.user

      // Store token
      if (response.data.data.token) {
        localStorage.setItem('auth_token', response.data.data.token)
      }

      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const setBusinessType = async (businessTypeId) => {
    loading.value = true
    try {
      const response = await api.post('/user/business-type', {
        business_type_id: businessTypeId
      })
      businessType.value = response.data.businessType
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const loadActiveModules = async () => {
    loading.value = true
    try {
      const response = await api.get('/user-modules/active')
      activeModules.value = response.data.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const activateModule = async (moduleId) => {
    loading.value = true
    try {
      const response = await api.post('/user-modules/activate', {
        module_id: moduleId
      })
      await loadActiveModules() // Refresh the list
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const deactivateModule = async (moduleId) => {
    loading.value = true
    try {
      const response = await api.delete(`/user-modules/${moduleId}/deactivate`)
      await loadActiveModules() // Refresh the list
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  // User Management
  const fetchUsers = async (params = {}) => {
    loading.value = true
    try {
      const response = await api.get('/users', { params })
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchUser = async (userId) => {
    loading.value = true
    try {
      const response = await api.get(`/users/${userId}`)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const updateUser = async (userId, userData) => {
    loading.value = true
    try {
      const response = await api.put(`/users/${userId}`, userData)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const updateProfile = async (profileData) => {
    loading.value = true
    try {
      const response = await api.put('/profile', profileData)
      user.value = response.data.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const activateUser = async (userId) => {
    loading.value = true
    try {
      const response = await api.post(`/users/${userId}/activate`)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const deactivateUser = async (userId) => {
    loading.value = true
    try {
      const response = await api.post(`/users/${userId}/deactivate`)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  // User Invitations
  const fetchInvitations = async (params = {}) => {
    loading.value = true
    try {
      const response = await api.get('/invitations', { params })
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const sendInvitation = async (invitationData) => {
    loading.value = true
    try {
      const response = await api.post('/invitations', invitationData)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const resendInvitation = async (invitationId) => {
    loading.value = true
    try {
      const response = await api.post(`/invitations/${invitationId}/resend`)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const cancelInvitation = async (invitationId) => {
    loading.value = true
    try {
      const response = await api.post(`/invitations/${invitationId}/cancel`)
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const logout = () => {
    user.value = null
    businessType.value = null
    activeModules.value = []
    localStorage.removeItem('auth_token')
  }

  return {
    // State
    user,
    businessType,
    activeModules,
    loading,

    // Getters
    isAuthenticated,
    hasBusinessType,
    moduleCount,

    // Actions
    login,
    register,
    setBusinessType,
    loadActiveModules,
    activateModule,
    deactivateModule,

    // User Management
    fetchUsers,
    fetchUser,
    updateUser,
    updateProfile,
    activateUser,
    deactivateUser,

    // User Invitations
    fetchInvitations,
    sendInvitation,
    resendInvitation,
    cancelInvitation,

    logout
  }
})
