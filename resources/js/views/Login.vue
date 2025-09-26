<template>
  <v-app>
    <v-container fluid class="login-container">
      <v-row align="center" justify="center" class="fill-height">
        <v-col cols="12" sm="8" md="6" lg="4" xl="3">
          <v-card elevation="12" class="pa-8 rounded-xl">
            <!-- Header -->
            <div class="text-center mb-8">
              <v-icon icon="mdi-factory" size="64" color="primary" class="mb-4" />
              <h1 class="text-h4 font-weight-bold text-primary mb-2">
                Welcome Back
              </h1>
              <p class="text-body-1 text-grey-darken-1">
                Sign in to your ERP Modular account
              </p>
            </div>

            <!-- Login Form -->
            <v-form @submit.prevent="handleLogin" ref="loginForm">
              <v-text-field
                v-model="form.email"
                label="Email Address"
                type="email"
                prepend-inner-icon="mdi-email"
                variant="outlined"
                :rules="emailRules"
                :error-messages="errors.email"
                required
                class="mb-4"
              />

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
                class="mb-6"
              />

              <!-- Remember Me & Forgot Password -->
              <div class="d-flex justify-space-between align-center mb-6">
                <v-checkbox
                  v-model="form.remember"
                  label="Remember me"
                  color="primary"
                  hide-details
                />
                <v-btn
                  variant="text"
                  color="primary"
                  size="small"
                  @click="handleForgotPassword"
                >
                  Forgot Password?
                </v-btn>
              </div>

              <!-- Login Button -->
              <v-btn
                type="submit"
                color="primary"
                size="large"
                block
                :loading="loading"
                :disabled="loading"
                class="mb-4"
              >
                <v-icon start>mdi-login</v-icon>
                Sign In
              </v-btn>

              <!-- Register Link -->
              <div class="text-center">
                <span class="text-body-2 text-grey-darken-1">
                  Don't have an account?
                </span>
                <v-btn
                  variant="text"
                  color="primary"
                  @click="navigateToRegister"
                  class="ml-2"
                >
                  Create Account
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
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'

const router = useRouter()
const userStore = useUserStore()

// Form data
const form = reactive({
  email: '',
  password: '',
  remember: false
})

// UI state
const loading = ref(false)
const showPassword = ref(false)
const errors = ref({})

// Form validation rules
const emailRules = [
  v => !!v || 'Email is required',
  v => /.+@.+\..+/.test(v) || 'Email must be valid'
]

const passwordRules = [
  v => !!v || 'Password is required',
  v => v.length >= 6 || 'Password must be at least 6 characters'
]

// Methods
const handleLogin = async () => {
  // Clear previous errors
  errors.value = {}
  
  // Validate form
  const { valid } = await loginForm.value.validate()
  if (!valid) return

  loading.value = true

  try {
    await userStore.login(form.email, form.password)
    
    // Show success message
    // You can add a toast notification here
    
    // Navigate to dashboard
    router.push({ name: 'Dashboard' })
  } catch (error) {
    console.error('Login error:', error)
    
    // Handle different error types
    if (error.response?.status === 401) {
      errors.value = {
        email: 'Invalid email or password',
        password: 'Invalid email or password'
      }
    } else if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      // Generic error message
      errors.value = {
        email: 'Login failed. Please try again.'
      }
    }
  } finally {
    loading.value = false
  }
}

const handleForgotPassword = () => {
  // TODO: Implement forgot password functionality
  console.log('Forgot password clicked')
}

const navigateToRegister = () => {
  router.push({ name: 'Register' })
}

// Template refs
const loginForm = ref(null)
</script>

<style scoped>
.login-container {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

.login-container .v-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

@media (max-width: 600px) {
  .login-container {
    padding: 1rem;
  }
  
  .login-container .v-card {
    padding: 2rem !important;
  }
}
</style>
