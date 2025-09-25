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
  const login = async (credentials) => {
    loading.value = true
    try {
      const response = await api.post('/auth/login', credentials)
      user.value = response.data.user
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
      user.value = response.data.user
      businessType.value = response.data.businessType
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

  const logout = () => {
    user.value = null
    businessType.value = null
    activeModules.value = []
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
    logout
  }
})
