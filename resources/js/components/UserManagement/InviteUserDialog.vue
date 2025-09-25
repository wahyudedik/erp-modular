<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
    persistent
  >
    <v-card>
      <v-card-title>
        <span class="text-h5">Invite New User</span>
      </v-card-title>

      <v-form
        ref="form"
        v-model="valid"
        @submit.prevent="submit"
      >
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.name"
                  label="Full Name"
                  :rules="nameRules"
                  required
                  variant="outlined"
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.email"
                  label="Email Address"
                  type="email"
                  :rules="emailRules"
                  required
                  variant="outlined"
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.company_name"
                  label="Company Name"
                  :rules="companyRules"
                  required
                  variant="outlined"
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.phone"
                  label="Phone Number"
                  variant="outlined"
                />
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="form.business_type_id"
                  :items="businessTypes"
                  item-title="name"
                  item-value="id"
                  label="Business Type"
                  :rules="businessTypeRules"
                  required
                  variant="outlined"
                />
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="form.address"
                  label="Address"
                  variant="outlined"
                  rows="3"
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.role"
                  :items="roleOptions"
                  label="Role"
                  :rules="roleRules"
                  required
                  variant="outlined"
                />
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="form.message"
                  label="Personal Message (Optional)"
                  variant="outlined"
                  rows="3"
                  placeholder="Add a personal message to the invitation..."
                />
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn
            color="grey"
            variant="text"
            @click="close"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            type="submit"
            :loading="loading"
            :disabled="!valid"
          >
            Send Invitation
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useUserStore } from '@/stores/user'
import { useBusinessTypeStore } from '@/stores/businessType'

// Props
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  businessTypes: {
    type: Array,
    default: () => []
  }
})

// Emits
const emit = defineEmits(['update:modelValue', 'invited'])

// Stores
const userStore = useUserStore()
const businessTypeStore = useBusinessTypeStore()

// Reactive data
const dialog = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const form = ref({
  name: '',
  email: '',
  company_name: '',
  phone: '',
  address: '',
  business_type_id: null,
  role: 'user',
  message: ''
})

const valid = ref(false)
const loading = ref(false)

// Form validation rules
const nameRules = [
  v => !!v || 'Name is required',
  v => (v && v.length >= 2) || 'Name must be at least 2 characters'
]

const emailRules = [
  v => !!v || 'Email is required',
  v => /.+@.+\..+/.test(v) || 'Email must be valid'
]

const companyRules = [
  v => !!v || 'Company name is required',
  v => (v && v.length >= 2) || 'Company name must be at least 2 characters'
]

const businessTypeRules = [
  v => !!v || 'Business type is required'
]

const roleRules = [
  v => !!v || 'Role is required'
]

// Role options
const roleOptions = [
  { title: 'User', value: 'user' },
  { title: 'Manager', value: 'manager' },
  { title: 'Admin', value: 'admin' }
]

// Methods
const submit = async () => {
  if (!valid.value) return

  loading.value = true
  try {
    await userStore.sendInvitation(form.value)
    emit('invited')
    close()
  } catch (error) {
    console.error('Error sending invitation:', error)
  } finally {
    loading.value = false
  }
}

const close = () => {
  dialog.value = false
  resetForm()
}

const resetForm = () => {
  form.value = {
    name: '',
    email: '',
    company_name: '',
    phone: '',
    address: '',
    business_type_id: null,
    role: 'user',
    message: ''
  }
}

// Watch for dialog close
watch(dialog, (newValue) => {
  if (!newValue) {
    resetForm()
  }
})
</script>

<style scoped>
.v-card-title {
  padding-bottom: 16px;
}
</style>
