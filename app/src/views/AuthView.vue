<script setup lang="ts">
import { computed, ref } from 'vue'
import { apiUrl } from '@/config/api'

type AuthMode = 'login' | 'register'

type AuthUser = {
  id: number
  name: string
  email: string
  document?: string | null
  phone?: string | null
  role?: string
  is_active?: boolean
}

type ApiSuccess = {
  message: string
  token?: string
  user?: AuthUser
}

type ApiError = {
  message?: string
  errors?: Record<string, string[]>
}

const emit = defineEmits<{
  authenticated: [payload: { token: string; user: AuthUser | null; message: string }]
}>()

const mode = ref<AuthMode>('login')
const isSubmitting = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const loginData = ref({
  email: '',
  password: '',
})

const registerData = ref({
  name: '',
  email: '',
  document: '',
  phone: '',
  role: 'operator',
  password: '',
  password_confirmation: '',
})

const submitLabel = computed(() => (mode.value === 'login' ? 'Entrar' : 'Cadastrar'))

function setMode(nextMode: AuthMode): void {
  mode.value = nextMode
  successMessage.value = ''
  errorMessage.value = ''
}

function getValidationMessage(payload: ApiError): string {
  const firstErrorList = payload.errors ? Object.values(payload.errors)[0] : null
  const firstError = firstErrorList && firstErrorList.length > 0
    ? firstErrorList[0]
    : null

  if (firstError) {
    return firstError
  }

  return payload.message || 'Nao foi possivel concluir a operacao.'
}

async function requestApi(path: string, body: Record<string, string>): Promise<ApiSuccess> {
  const response = await fetch(apiUrl(path), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
    body: JSON.stringify(body),
  })

  const payload = (await response.json()) as ApiSuccess | ApiError

  if (!response.ok) {
    throw new Error(getValidationMessage(payload as ApiError))
  }

  return payload as ApiSuccess
}

function normalizeRegisterPayload(): Record<string, string> {
  return {
    name: registerData.value.name,
    email: registerData.value.email,
    document: registerData.value.document,
    phone: registerData.value.phone,
    role: registerData.value.role,
    password: registerData.value.password,
    password_confirmation: registerData.value.password_confirmation,
  }
}

async function handleSubmit(): Promise<void> {
  isSubmitting.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const payload = mode.value === 'login'
      ? await requestApi('/auth/login', loginData.value)
      : await requestApi('/auth/register', normalizeRegisterPayload())

    if (!payload.token) {
      throw new Error('Token de autenticacao nao retornado pelo servidor.')
    }

    successMessage.value = payload.message

    emit('authenticated', {
      token: payload.token,
      user: payload.user ?? null,
      message: payload.message,
    })
  } catch (error) {
    errorMessage.value = error instanceof Error
      ? error.message
      : 'Erro inesperado ao processar autenticacao.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <main class="auth-page">
    <section class="auth-card">
      <div class="brand-block">
        <span class="eyebrow">OnyxERP</span>
        <h1>Controle seu negocio em um painel unico.</h1>
        <p class="subtitle">Acesse ou crie sua conta para continuar.</p>
      </div>

      <div class="mode-switch">
        <button
          type="button"
          :class="{ active: mode === 'login' }"
          @click="setMode('login')"
        >
          Login
        </button>
        <button
          type="button"
          :class="{ active: mode === 'register' }"
          @click="setMode('register')"
        >
          Cadastro
        </button>
      </div>

      <form class="auth-form" @submit.prevent="handleSubmit">
        <template v-if="mode === 'register'">
          <label for="name">Nome completo</label>
          <input id="name" v-model="registerData.name" name="name" type="text" required />
        </template>

        <template v-if="mode === 'login'">
          <label for="login_email">E-mail</label>
          <input id="login_email" v-model="loginData.email" name="email" type="email" required />

          <label for="login_password">Senha</label>
          <input
            id="login_password"
            v-model="loginData.password"
            name="password"
            type="password"
            minlength="8"
            required
          />
        </template>

        <template v-else>
          <label for="register_email">E-mail</label>
          <input
            id="register_email"
            v-model="registerData.email"
            name="email"
            type="email"
            required
          />

          <label for="register_document">Documento</label>
          <input
            id="register_document"
            v-model="registerData.document"
            name="document"
            type="text"
            placeholder="CPF ou CNPJ"
          />

          <label for="register_phone">Telefone</label>
          <input
            id="register_phone"
            v-model="registerData.phone"
            name="phone"
            type="text"
            placeholder="(11) 99999-9999"
          />

          <label for="register_role">Perfil de acesso</label>
          <select id="register_role" v-model="registerData.role" name="role">
            <option value="operator">Operador</option>
            <option value="manager">Gerente</option>
            <option value="admin">Administrador</option>
          </select>

          <label for="register_password">Senha</label>
          <input
            id="register_password"
            v-model="registerData.password"
            name="password"
            type="password"
            minlength="8"
            required
          />
        </template>

        <template v-if="mode === 'register'">
          <label for="password_confirmation">Confirmar senha</label>
          <input
            id="password_confirmation"
            v-model="registerData.password_confirmation"
            name="password_confirmation"
            type="password"
            minlength="8"
            required
          />
        </template>

        <button type="submit" :disabled="isSubmitting">
          {{ isSubmitting ? 'Processando...' : submitLabel }}
        </button>
      </form>

      <p v-if="successMessage" class="feedback success">{{ successMessage }}</p>
      <p v-if="errorMessage" class="feedback error">{{ errorMessage }}</p>
    </section>
  </main>
</template>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 1.5rem;
  background: radial-gradient(circle at top left, #d4f4dd, transparent 38%),
    radial-gradient(circle at bottom right, #f5d6bf, transparent 40%),
    linear-gradient(180deg, #f4f7fb 0%, #edf4ef 100%);
}

.auth-card {
  width: 100%;
  max-width: 520px;
  padding: 2rem;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.94);
  border: 1px solid #d9e0ea;
  box-shadow: 0 24px 50px rgba(30, 42, 58, 0.12);
  backdrop-filter: blur(16px);
}

.brand-block {
  margin-bottom: 1.2rem;
}

.eyebrow {
  display: inline-block;
  margin-bottom: 0.6rem;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  background: #e5f7ea;
  color: #16653b;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

h1 {
  margin: 0;
  font-size: 2rem;
  line-height: 1.1;
  color: #1f2937;
}

.subtitle {
  margin: 0.7rem 0 0;
  color: #536172;
}

.mode-switch {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.4rem;
  margin-bottom: 1rem;
  background: #eef2f6;
  border-radius: 10px;
  padding: 0.25rem;
}

.mode-switch button {
  border: 0;
  border-radius: 8px;
  background: transparent;
  color: #415063;
  font-weight: 700;
  padding: 0.7rem 0.8rem;
  cursor: pointer;
}

.mode-switch button.active {
  background: #ffffff;
  color: #0f172a;
  box-shadow: 0 8px 18px rgba(30, 42, 58, 0.14);
}

.auth-form {
  display: grid;
  gap: 0.45rem;
}

label {
  margin-top: 0.35rem;
  font-size: 0.9rem;
  color: #2f3f53;
}

input,
select {
  border: 1px solid #c8d2de;
  border-radius: 10px;
  padding: 0.8rem 0.9rem;
  font-size: 1rem;
  color: #0f172a;
  background: #ffffff;
}

input:focus,
select:focus {
  outline: 2px solid #9ed8ad;
  outline-offset: 1px;
}

.auth-form button {
  margin-top: 0.8rem;
  border: 0;
  border-radius: 10px;
  padding: 0.9rem;
  font-weight: 700;
  color: #ffffff;
  background: linear-gradient(120deg, #1f8f4e, #2f9f5f);
  cursor: pointer;
}

.auth-form button:disabled {
  cursor: wait;
  opacity: 0.8;
}

.feedback {
  margin: 1rem 0 0;
  border-radius: 10px;
  padding: 0.75rem 0.85rem;
  font-size: 0.93rem;
}

.feedback.success {
  background: #e5f7ea;
  color: #16653b;
}

.feedback.error {
  background: #fde8e8;
  color: #9f1d1d;
}
</style>
