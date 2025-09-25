import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../api/axios'

export const useModuleStore = defineStore('modules', () => {
  // State
  const businessTypes = ref([])
  const modules = ref([])
  const categories = ref([])
  const recommendations = ref([])
  const loading = ref(false)

  // Getters
  const businessTypeOptions = computed(() => 
    businessTypes.value.map(type => ({
      title: type.name,
      value: type.id,
      subtitle: type.description,
      icon: getBusinessTypeIcon(type.name)
    }))
  )

  const moduleCategories = computed(() => 
    categories.value.map(category => ({
      title: category,
      value: category,
      modules: modules.value.filter(module => module.category === category)
    }))
  )

  const recommendedModules = computed(() => 
    recommendations.value.map(rec => ({
      ...rec.module,
      priority: rec.priority,
      isRecommended: true
    }))
  )

  // Actions
  const loadBusinessTypes = async () => {
    loading.value = true
    try {
      const response = await api.get('/business-types')
      businessTypes.value = response.data.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const loadModules = async (filters = {}) => {
    loading.value = true
    try {
      const params = new URLSearchParams(filters).toString()
      const response = await api.get(`/modules?${params}`)
      modules.value = response.data.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const loadModuleCategories = async () => {
    loading.value = true
    try {
      const response = await api.get('/modules/categories/list')
      categories.value = response.data.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const loadRecommendations = async (businessTypeId) => {
    loading.value = true
    try {
      const response = await api.get(`/business-types/${businessTypeId}/module-recommendations`)
      recommendations.value = response.data.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const getModuleById = (moduleId) => {
    return modules.value.find(module => module.id === moduleId)
  }

  const getBusinessTypeById = (businessTypeId) => {
    return businessTypes.value.find(type => type.id === businessTypeId)
  }

  // Helper functions
  const getBusinessTypeIcon = (businessTypeName) => {
    const iconMap = {
      'Pabrik Beton': 'mdi-concrete-mixer',
      'Pabrik Roti': 'mdi-bread-slice',
      'Retail': 'mdi-storefront',
      'Konstruksi': 'mdi-hard-hat',
      'Logistik': 'mdi-truck-delivery',
      'Kesehatan': 'mdi-medical-bag',
      'Pendidikan': 'mdi-school',
      'Pertanian': 'mdi-sprout',
      'Teknologi': 'mdi-laptop',
      'Manufaktur Umum': 'mdi-factory'
    }
    return iconMap[businessTypeName] || 'mdi-domain'
  }

  const getModuleIcon = (moduleName) => {
    const iconMap = {
      'Accounting': 'mdi-calculator',
      'Inventory': 'mdi-package-variant',
      'Sales': 'mdi-cart',
      'Purchase': 'mdi-shopping',
      'HR': 'mdi-account-group',
      'Production': 'mdi-cog',
      'Quality Control': 'mdi-check-circle',
      'Maintenance': 'mdi-wrench',
      'Mix Design': 'mdi-flask',
      'Recipe Management': 'mdi-book-open-page-variant'
    }
    return iconMap[moduleName] || 'mdi-puzzle'
  }

  return {
    // State
    businessTypes,
    modules,
    categories,
    recommendations,
    loading,
    
    // Getters
    businessTypeOptions,
    moduleCategories,
    recommendedModules,
    
    // Actions
    loadBusinessTypes,
    loadModules,
    loadModuleCategories,
    loadRecommendations,
    getModuleById,
    getBusinessTypeById,
    
    // Helper functions
    getBusinessTypeIcon,
    getModuleIcon
  }
})
