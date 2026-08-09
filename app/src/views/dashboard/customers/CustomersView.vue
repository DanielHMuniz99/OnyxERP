<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { apiUrl } from '@/config/api'
import CustomerFormView from './CustomerFormView.vue'
import CustomerListView from './CustomerListView.vue'
import type {
  ApiError,
  Customer,
  CustomerFormData,
  CustomerListResponse,
  CustomerRouteMode,
  CustomerShowResponse,
  CustomerMutationResponse,
} from './types'

const props = defineProps<{
  token: string
  mode: CustomerRouteMode
  customerId: number | null
}>()

const emit = defineEmits<{
  navigate: [path: string]
}>()

const customers = ref<Customer[]>([])
const selectedCustomer = ref<Customer | null>(null)
const isLoading = ref(false)
const isSubmitting = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const formTitle = computed(() => {
  return selectedCustomer.value
    ? 'Atualize os dados do cliente selecionado na tabela.'
    : 'Cadastre novos clientes e mantenha o relacionamento organizado.'
})

const isListMode = computed(() => props.mode === 'list')
const isFormMode = computed(() => props.mode === 'create' || props.mode === 'edit')

function getAuthHeaders(token: string): HeadersInit {
  return {
    Accept: 'application/json',
    Authorization: `Bearer ${token}`,
    'Content-Type': 'application/json',
  }
}

function getErrorMessage(payload: ApiError, fallback: string): string {
  const firstErrorList = payload.errors ? Object.values(payload.errors)[0] : null
  const firstError = firstErrorList && firstErrorList.length > 0 ? firstErrorList[0] : null

  return firstError || payload.message || fallback
}

async function fetchCustomers(): Promise<void> {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(apiUrl('/customers'), {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${props.token}`,
      },
    })

    if (!response.ok) {
      throw new Error('Nao foi possivel carregar os clientes.')
    }

    const payload = (await response.json()) as CustomerListResponse
    customers.value = payload.customers
  } catch (error) {
    errorMessage.value = error instanceof Error
      ? error.message
      : 'Erro inesperado ao carregar clientes.'
  } finally {
    isLoading.value = false
  }
}

async function fetchCustomer(customerId: number): Promise<void> {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(apiUrl(`/customers/${customerId}`), {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${props.token}`,
      },
    })

    if (!response.ok) {
      throw new Error('Nao foi possivel carregar o cliente selecionado.')
    }

    const payload = (await response.json()) as CustomerShowResponse
    selectedCustomer.value = payload.customer
  } catch (error) {
    errorMessage.value = error instanceof Error
      ? error.message
      : 'Erro inesperado ao carregar cliente.'
  } finally {
    isLoading.value = false
  }
}

async function saveCustomer(formData: CustomerFormData): Promise<void> {
  isSubmitting.value = true
  successMessage.value = ''
  errorMessage.value = ''

  const isEditing = Boolean(selectedCustomer.value)
  const endpoint = selectedCustomer.value
    ? apiUrl(`/customers/${selectedCustomer.value.id}`)
    : apiUrl('/customers')
  const method = selectedCustomer.value ? 'PUT' : 'POST'

  try {
    const response = await fetch(endpoint, {
      method,
      headers: getAuthHeaders(props.token),
      body: JSON.stringify(formData),
    })

    const payload = (await response.json()) as CustomerMutationResponse | ApiError

    if (!response.ok) {
      throw new Error(getErrorMessage(payload as ApiError, 'Nao foi possivel salvar o cliente.'))
    }

    const successPayload = payload as CustomerMutationResponse

    if (isEditing && selectedCustomer.value) {
      customers.value = customers.value.map((customer) => {
        return customer.id === selectedCustomer.value?.id ? successPayload.customer : customer
      })
    } else {
      customers.value = [successPayload.customer, ...customers.value]
    }

    successMessage.value = successPayload.message
    selectedCustomer.value = null
    emit('navigate', '/clientes')
  } catch (error) {
    errorMessage.value = error instanceof Error
      ? error.message
      : 'Erro inesperado ao salvar cliente.'
  } finally {
    isSubmitting.value = false
  }
}

function editCustomer(customer: Customer): void {
  successMessage.value = ''
  errorMessage.value = ''
  emit('navigate', `/clientes/${customer.id}/editar`)
}

function openCreate(): void {
  selectedCustomer.value = null
  successMessage.value = ''
  errorMessage.value = ''
  emit('navigate', '/clientes/novo')
}

function goToList(): void {
  selectedCustomer.value = null
  successMessage.value = ''
  errorMessage.value = ''
  emit('navigate', '/clientes')
}

watch(
  () => [props.mode, props.customerId, props.token] as const,
  ([mode, customerId]) => {
    if (!props.token) {
      return
    }

    if (mode === 'list') {
      selectedCustomer.value = null
      void fetchCustomers()
      return
    }

    if (mode === 'create') {
      selectedCustomer.value = null
      return
    }

    if (mode === 'edit' && customerId) {
      void fetchCustomer(customerId)
    }
  },
  { immediate: true },
)

onMounted(() => {
  if (props.token && props.mode === 'list') {
    void fetchCustomers()
  }
})
</script>

<template>
  <section class="customers-view">
    <div class="hero-panel">
      <div>
        <p class="hero-label">Clientes</p>
        <h2>Cadastros e relacionamento comercial com padrao unico do sistema.</h2>
        <p class="hero-text">
          A mesma tela controla criacao, edicao e consulta da base de clientes do OnyxERP.
        </p>
      </div>
      <div class="hero-badge">
        <span>Total em base</span>
        <strong>{{ customers.length }}</strong>
      </div>
    </div>

    <div v-if="isFormMode" class="single-column">
      <section class="form-column">
        <div class="context-card">
          <span class="context-label">Cadastro</span>
          <p>{{ formTitle }}</p>
        </div>

        <CustomerFormView
          :customer="selectedCustomer"
          :is-submitting="isSubmitting"
          @submit="saveCustomer"
          @back="goToList"
        />

        <p v-if="successMessage" class="form-feedback success">{{ successMessage }}</p>
        <p v-if="errorMessage" class="form-feedback error">{{ errorMessage }}</p>
      </section>
    </div>

    <div v-else class="single-column">
      <CustomerListView
        :customers="customers"
        :is-loading="isLoading"
        @create="openCreate"
        @edit="editCustomer"
        @refresh="fetchCustomers"
      />
    </div>
  </section>
</template>

<style scoped>
.customers-view {
  display: grid;
  gap: 1.25rem;
  padding: 1.5rem 1.75rem 2rem;
}

.hero-panel {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.5rem;
  border-radius: 24px;
  background: linear-gradient(135deg, #162132 0%, #245e8c 100%);
  color: #f8fbff;
}

.hero-label {
  margin: 0 0 0.45rem;
  color: #cfe7ff;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.hero-panel h2 {
  margin: 0;
  max-width: 20ch;
  font-size: 1.9rem;
  line-height: 1.05;
}

.hero-text {
  margin: 0.8rem 0 0;
  color: #dfe8f4;
}

.hero-badge {
  min-width: 180px;
  padding: 1rem 1.1rem;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.12);
}

.hero-badge span {
  display: block;
  margin-bottom: 0.4rem;
  color: #d6eaff;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.hero-badge strong {
  font-size: 1.5rem;
}

.single-column {
  display: grid;
  width: 100%;
}

.form-column {
  display: grid;
  width: 100%;
  align-content: start;
  gap: 1rem;
}

.context-card {
  padding: 1rem 1.1rem;
  border-radius: 18px;
  background: #eff6ff;
  border: 1px solid #d8e8fb;
}

.context-label {
  display: inline-block;
  margin-bottom: 0.45rem;
  color: #245e8c;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.context-card p {
  margin: 0;
  color: #3d546c;
}

@media (max-width: 720px) {
  .hero-panel {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
