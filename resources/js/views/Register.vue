<template>
  <v-app>
    <v-container fluid class="register-container">
      <v-row align="center" justify="center" class="fill-height">
        <v-col cols="12" sm="10" md="8" lg="6" xl="5">
          <v-card elevation="12" class="pa-8 rounded-xl">
            <!-- Header -->
            <div class="text-center mb-8">
              <v-icon icon="mdi-factory" size="64" color="primary" class="mb-4" />
              <h1 class="text-h4 font-weight-bold text-primary mb-2">
                Create Account
              </h1>
              <p class="text-body-1 text-grey-darken-1">
                Join ERP Modular and start managing your business
              </p>
            </div>

            <!-- Registration Form -->
            <v-form @submit.prevent="handleRegister" ref="registerForm">
              <!-- Personal Information -->
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.name"
                    label="Full Name"
                    prepend-inner-icon="mdi-account"
                    variant="outlined"
                    :rules="nameRules"
                    :error-messages="errors.name"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.email"
                    label="Email Address"
                    type="email"
                    prepend-inner-icon="mdi-email"
                    variant="outlined"
                    :rules="emailRules"
                    :error-messages="errors.email"
                    required
                  />
                </v-col>
              </v-row>

              <!-- Company Information -->
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.company_name"
                    label="Company Name"
                    prepend-inner-icon="mdi-domain"
                    variant="outlined"
                    :rules="companyRules"
                    :error-messages="errors.company_name"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.phone"
                    label="Phone Number"
                    prepend-inner-icon="mdi-phone"
                    variant="outlined"
                    :rules="phoneRules"
                    :error-messages="errors.phone"
                    required
                  />
                </v-col>
              </v-row>

              <!-- Business Type Selection -->
              <v-row>
                <v-col cols="12">
                  <v-select
                    v-model="form.business_type_id"
                    label="Business Type"
                    prepend-inner-icon="mdi-briefcase"
                    variant="outlined"
                    :items="businessTypes"
                    item-title="name"
                    item-value="id"
                    :rules="businessTypeRules"
                    :error-messages="errors.business_type_id"
                    required
                  />
                </v-col>
              </v-row>

              <!-- Address -->
              <v-row>
                <v-col cols="12">
                  <v-textarea
                    v-model="form.address"
                    label="Company Address"
                    prepend-inner-icon="mdi-map-marker"
                    variant="outlined"
                    rows="3"
                    :rules="addressRules"
                    :error-messages="errors.address"
                    required
                  />
                </v-col>
              </v-row>

              <!-- Password Fields -->
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.password"
                    label="Password"
                    :type="showPassword ? 'text' : 'password'"
                    prepend-inner-icon="mdi-lock"
                    :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append-inner="showPassword = !showPassword"
                    variant="outlined"
                    :rules="passwordRules"
                    :error-messages="errors.password"
                    required
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.password_confirmation"
                    label="Confirm Password"
                    :type="showPasswordConfirm ? 'text' : 'password'"
                    prepend-inner-icon="mdi-lock-check"
                    :append-inner-icon="showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append-inner="showPasswordConfirm = !showPasswordConfirm"
                    variant="outlined"
                    :rules="passwordConfirmRules"
                    :error-messages="errors.password_confirmation"
                    required
                  />
                </v-col>
              </v-row>

              <!-- Terms and Conditions -->
              <v-row>
                <v-col cols="12">
                  <v-checkbox
                    v-model="form.accept_terms"
                    color="primary"
                    :rules="termsRules"
                    :error-messages="errors.accept_terms"
                  >
                    <template v-slot:label>
                      <span class="text-body-2">
                        I agree to the 
                        <a href="#" class="text-primary">Terms of Service</a> 
                        and 
                        <a href="#" class="text-primary">Privacy Policy</a>
                      </span>
                    </template>
                  </v-checkbox>
                </v-col>
              </v-row>

              <!-- Register Button -->
              <v-btn
                type="submit"
                color="primary"
                size="large"
                block
                :loading="loading"
                :disabled="loading"
                class="mb-4"
              >
                <v-icon start>mdi-account-plus</v-icon>
                Create Account
              </v-btn>

              <!-- Login Link -->
              <div class="text-center">
                <span class="text-body-2 text-grey-darken-1">
                  Already have an account?
                </span>
                <v-btn
                  variant="text"
                  color="primary"
                  @click="navigateToLogin"
                  class="ml-2"
                >
                  Sign In
                </v-btn>
              </div>
            </v-form>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-app>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'

const router = useRouter()
const userStore = useUserStore()

// Form data
const form = reactive({
  name: '',
  email: '',
  company_name: '',
  phone: '',
  business_type_id: null,
  address: '',
  password: '',
  password_confirmation: '',
  accept_terms: false
})

// UI state
const loading = ref(false)
const showPassword = ref(false)
const showPasswordConfirm = ref(false)
const errors = ref({})
const businessTypes = ref([])

// Form validation rules
const nameRules = [
  v => !!v || 'Name is required',
  v => v.length >= 2 || 'Name must be at least 2 characters'
]

const emailRules = [
  v => !!v || 'Email is required',
  v => /.+@.+\..+/.test(v) || 'Email must be valid'
]

const companyRules = [
  v => !!v || 'Company name is required',
  v => v.length >= 2 || 'Company name must be at least 2 characters'
]

const phoneRules = [
  v => !!v || 'Phone number is required',
  v => /^[\+]?[0-9\s\-\(\)]{10,}$/.test(v) || 'Phone number must be valid'
]

const businessTypeRules = [
  v => !!v || 'Business type is required'
]

const addressRules = [
  v => !!v || 'Address is required',
  v => v.length >= 10 || 'Address must be at least 10 characters'
]

const passwordRules = [
  v => !!v || 'Password is required',
  v => v.length >= 8 || 'Password must be at least 8 characters',
  v => /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(v) || 'Password must contain uppercase, lowercase, and number'
]

const passwordConfirmRules = [
  v => !!v || 'Password confirmation is required',
  v => v === form.password || 'Passwords do not match'
]

const termsRules = [
  v => !!v || 'You must accept the terms and conditions'
]

// Methods
const loadBusinessTypes = async () => {
  try {
    const response = await fetch('/api/v1/business-types')
    const data = await response.json()
    businessTypes.value = data.data || []
  } catch (error) {
    console.error('Failed to load business types:', error)
  }
}

const handleRegister = async () => {
  // Clear previous errors
  errors.value = {}
  
  // Validate form
  const { valid } = await registerForm.value.validate()
  if (!valid) return

  loading.value = true

  try {
    await userStore.register(form)
    
    // Show success message
    // You can add a toast notification here
    
    // Navigate to business type selection or dashboard
    router.push({ name: 'BusinessTypeSelection' })
  } catch (error) {
    console.error('Registration error:', error)
    
    // Handle different error types
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      // Generic error message
      errors.value = {
        email: 'Registration failed. Please try again.'
      }
    }
  } finally {
    loading.value = false
  }
}

const navigateToLogin = () => {
  router.push({ name: 'Login' })
}

// Template refs
const registerForm = ref(null)

// Load business types on mount
onMounted(() => {
  loadBusinessTypes()
})
</script>

<style scoped>
.register-container {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

.register-container .v-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

@media (max-width: 600px) {
  .register-container {
    padding: 1rem;
  }
  
  .register-container .v-card {
    padding: 2rem !important;
  }
}
</style>
