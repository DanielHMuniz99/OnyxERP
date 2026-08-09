<script setup lang="ts">
const menuItems = [
  { label: 'Visao geral', description: 'Resumo financeiro e operacional' },
  { label: 'Clientes', description: 'Cadastros e relacionamento' },
  { label: 'Estoque', description: 'Entradas, saidas e inventario' },
  { label: 'Financeiro', description: 'Fluxo de caixa e cobrancas' },
  { label: 'Relatorios', description: 'Indicadores e desempenho' },
]
</script>

<template>
  <aside class="sidebar">
    <div class="sidebar-brand">
      <span class="brand-tag">OnyxERP</span>
      <strong>Operacao central</strong>
      <p>Atalhos principais para sua rotina.</p>
    </div>

    <nav class="sidebar-nav" aria-label="Menu principal">
      <a
        v-for="item in menuItems"
        :key="item.label"
        href="#"
        class="nav-item"
      >
        <span>{{ item.label }}</span>
        <small>{{ item.description }}</small>
      </a>
    </nav>
  </aside>
</template>

<style scoped>
.sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  padding: 1.5rem;
  background: linear-gradient(180deg, #162132 0%, #1f3048 100%);
  color: #f4f7fb;
}

.sidebar-brand {
  padding: 1rem;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.08);
}

.brand-tag {
  display: inline-block;
  margin-bottom: 0.7rem;
  color: #b8f0c4;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.sidebar-brand strong {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 1.15rem;
}

.sidebar-brand p {
  margin: 0;
  color: #d0dae7;
}

.sidebar-nav {
  display: grid;
  gap: 0.75rem;
}

.nav-item {
  display: grid;
  gap: 0.2rem;
  padding: 0.95rem 1rem;
  border-radius: 14px;
  text-decoration: none;
  color: inherit;
  background: rgba(255, 255, 255, 0.06);
  transition: transform 0.2s ease, background-color 0.2s ease;
}

.nav-item:hover {
  transform: translateX(4px);
  background: rgba(255, 255, 255, 0.12);
}

.nav-item span {
  font-weight: 700;
}

.nav-item small {
  color: #c7d3e2;
}
</style>
