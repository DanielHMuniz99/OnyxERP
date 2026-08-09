<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { apiUrl } from '@/config/api'
import AuthView from '@/views/AuthView.vue'
import DashboardView from '@/views/DashboardView.vue'

type AuthUser = {
  id: number
  name: string
  email: string
  role?: string | null
}

type MeResponse = {
  user: AuthUser
}

const authToken = ref(localStorage.getItem('auth_token') || '')
const authUser = ref<AuthUser | null>(null)
const isBootstrapping = ref(false)

async function fetchCurrentUser(): Promise<void> {
  if (!authToken.value) {
    authUser.value = null
    return
  }

  isBootstrapping.value = true

  try {
    const response = await fetch(apiUrl('/auth/me'), {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${authToken.value}`,
      },
    })

    if (!response.ok) {
      throw new Error('Sessao invalida')
    }

    const payload = (await response.json()) as MeResponse
    authUser.value = payload.user
  } catch {
    authToken.value = ''
    authUser.value = null
    localStorage.removeItem('auth_token')
  } finally {
    isBootstrapping.value = false
  }
}

function handleAuthenticated(payload: { token: string; user: AuthUser | null }): void {
  authToken.value = payload.token
  authUser.value = payload.user
  localStorage.setItem('auth_token', payload.token)

  if (!payload.user) {
    void fetchCurrentUser()
  }
}

async function handleLogout(): Promise<void> {
  try {
    if (authToken.value) {
      await fetch(apiUrl('/auth/logout'), {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${authToken.value}`,
        },
      })
    }
  } finally {
    authToken.value = ''
    authUser.value = null
    localStorage.removeItem('auth_token')
  }
}

onMounted(() => {
  void fetchCurrentUser()
})
</script>

<template>
  <div v-if="isBootstrapping" class="loading-screen">
    <div class="loading-card">
      <span class="loading-chip">OnyxERP</span>
      <strong>Carregando ambiente...</strong>
    </div>
  </div>

  <DashboardView
    v-else-if="authUser"
    :user="authUser"
    @logout="handleLogout"
  />

  <AuthView v-else @authenticated="handleAuthenticated" />
</template>

<style scoped>
.loading-screen {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 1.5rem;
  background: linear-gradient(180deg, #f4f7fb 0%, #edf4ef 100%);
}

.loading-card {
  display: grid;
  gap: 0.8rem;
  padding: 1.5rem 1.8rem;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid #d9e0ea;
  box-shadow: 0 24px 50px rgba(30, 42, 58, 0.12);
  color: #162132;
}

.loading-chip {
  display: inline-block;
  width: fit-content;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  background: #e5f7ea;
  color: #16653b;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}
</style>
