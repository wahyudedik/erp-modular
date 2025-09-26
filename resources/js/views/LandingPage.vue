<template>
  <v-app>
    <!-- Hero Section -->
    <v-container fluid class="hero-section">
      <v-row align="center" justify="center" class="fill-height">
        <v-col cols="12" md="8" lg="6" class="text-center">
          <v-card elevation="12" class="pa-8 rounded-xl">
            <v-card-title class="text-h3 font-weight-bold text-primary mb-4">
              ERP Modular
            </v-card-title>
            <v-card-subtitle class="text-h6 text-grey-darken-1 mb-6">
              Multi-Industri Solution untuk Bisnis Anda
            </v-card-subtitle>
            <v-card-text class="text-body-1 text-grey-darken-2 mb-8">
              Sistem ERP yang modular dan scalable untuk berbagai jenis usaha dengan konsep 
              <strong>Register → Pilih Jenis Usaha → Aktivasi Modul Rekomendasi</strong>
            </v-card-text>
            
            <!-- Action Buttons -->
            <div class="d-flex flex-column flex-sm-row gap-4 justify-center">
              <v-btn
                color="primary"
                size="large"
                variant="flat"
                class="px-8 py-3"
                @click="navigateToLogin"
              >
                <v-icon start>mdi-login</v-icon>
                Login
              </v-btn>
              <v-btn
                color="secondary"
                size="large"
                variant="outlined"
                class="px-8 py-3"
                @click="navigateToRegister"
              >
                <v-icon start>mdi-account-plus</v-icon>
                Register
              </v-btn>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Features Section -->
    <v-container class="py-16">
      <v-row>
        <v-col cols="12" class="text-center mb-12">
          <h2 class="text-h4 font-weight-bold text-primary mb-4">
            Mengapa ERP Modular?
          </h2>
          <p class="text-h6 text-grey-darken-1">
            Solusi ERP yang fleksibel dan mudah disesuaikan dengan kebutuhan bisnis Anda
          </p>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" md="4" v-for="feature in features" :key="feature.title">
          <v-card elevation="4" class="h-100 pa-6 rounded-xl">
            <v-card-text class="text-center">
              <v-icon :icon="feature.icon" size="64" :color="feature.color" class="mb-4" />
              <h3 class="text-h6 font-weight-bold mb-3">{{ feature.title }}</h3>
              <p class="text-body-2 text-grey-darken-1">{{ feature.description }}</p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Business Types Section -->
    <v-container class="py-16 bg-grey-lighten-5">
      <v-row>
        <v-col cols="12" class="text-center mb-12">
          <h2 class="text-h4 font-weight-bold text-primary mb-4">
            Dukungan Berbagai Jenis Usaha
          </h2>
          <p class="text-h6 text-grey-darken-1">
            Sistem yang dapat disesuaikan untuk berbagai industri
          </p>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" md="3" v-for="businessType in businessTypes" :key="businessType.name">
          <v-card elevation="4" class="h-100 pa-6 rounded-xl text-center">
            <v-card-text>
              <v-icon :icon="businessType.icon" size="48" :color="businessType.color" class="mb-4" />
              <h3 class="text-h6 font-weight-bold mb-3">{{ businessType.name }}</h3>
              <p class="text-body-2 text-grey-darken-1">{{ businessType.description }}</p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Footer -->
    <v-footer class="bg-primary text-white">
      <v-container>
        <v-row align="center" justify="center">
          <v-col cols="12" class="text-center">
            <p class="text-body-2 mb-0">
              &copy; {{ new Date().getFullYear() }} ERP Modular - Multi-Industri Solution
            </p>
          </v-col>
        </v-row>
      </v-container>
    </v-footer>
  </v-app>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const features = [
  {
    icon: 'mdi-puzzle',
    title: 'Modular Architecture',
    description: 'Setiap modul dapat diaktifkan/dinonaktifkan sesuai kebutuhan bisnis Anda',
    color: 'primary'
  },
  {
    icon: 'mdi-lightbulb',
    title: 'Smart Recommendations',
    description: 'Sistem merekomendasikan modul berdasarkan jenis usaha yang Anda pilih',
    color: 'success'
  },
  {
    icon: 'mdi-trending-up',
    title: 'Scalable Design',
    description: 'Mudah menambah jenis usaha dan modul baru sesuai perkembangan bisnis',
    color: 'info'
  }
]

const businessTypes = [
  {
    name: 'Pabrik Beton',
    icon: 'mdi-factory',
    description: 'Ready Mix Concrete ERP dengan mix design management',
    color: 'primary'
  },
  {
    name: 'Pabrik Roti',
    icon: 'mdi-food',
    description: 'Bakery management dengan recipe dan expiry tracking',
    color: 'orange'
  },
  {
    name: 'Retail',
    icon: 'mdi-store',
    description: 'Point of sale dan customer management',
    color: 'success'
  },
  {
    name: 'Konstruksi',
    icon: 'mdi-hammer-wrench',
    description: 'Project management dan equipment tracking',
    color: 'warning'
  }
]

const navigateToLogin = () => {
  router.push({ name: 'Login' })
}

const navigateToRegister = () => {
  router.push({ name: 'Register' })
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
}

.hero-section .v-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

.gap-4 {
  gap: 1rem;
}

@media (max-width: 600px) {
  .hero-section {
    padding: 2rem 1rem;
  }
  
  .hero-section .v-card {
    padding: 2rem !important;
  }
  
  .hero-section .v-card-title {
    font-size: 2rem !important;
  }
}
</style>
