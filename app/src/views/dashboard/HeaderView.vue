<script setup lang="ts">
defineProps<{
  userName: string
}>()

const emit = defineEmits<{
  logout: []
}>()
</script>

<template>
  <header class="dashboard-header">
    <div>
      <p class="section-label">Painel inicial</p>
      <h1>Bem-vindo, {{ userName }}</h1>
    </div>

    <div class="header-actions">
      <button type="button" class="support-button">Central de ajuda</button>
      <button type="button" class="logout-button" @click="emit('logout')">Sair</button>
    </div>
  </header>
</template>

<style scoped>
.dashboard-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.5rem 1.75rem;
  border-bottom: 1px solid #dde5ee;
  background: rgba(255, 255, 255, 0.92);
}

.section-label {
  margin: 0 0 0.2rem;
  color: #1f8f4e;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

h1 {
  margin: 0;
  color: #162132;
  font-size: 1.55rem;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.support-button,
.logout-button {
  border-radius: 10px;
  padding: 0.8rem 1rem;
  font-weight: 700;
  cursor: pointer;
}

.support-button {
  border: 1px solid #d7e0ea;
  background: #ffffff;
  color: #294056;
}

.logout-button {
  border: 0;
  background: #162132;
  color: #ffffff;
}

@media (max-width: 720px) {
  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .header-actions {
    width: 100%;
  }

  .support-button,
  .logout-button {
    flex: 1;
  }
}
</style>
