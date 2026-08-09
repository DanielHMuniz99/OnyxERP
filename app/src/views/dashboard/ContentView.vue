<script setup lang="ts">
defineProps<{
  userName: string
  userEmail: string
  userRole: string
}>()

const metricCards = [
  {
    title: 'Pedidos pendentes',
    value: '18',
    detail: '4 aguardando aprovacao',
  },
  {
    title: 'Receita do dia',
    value: 'R$ 12.480',
    detail: 'Meta diaria em 82%',
  },
  {
    title: 'Itens em estoque baixo',
    value: '7',
    detail: '2 exigem reposicao imediata',
  },
]

const taskItems = [
  'Revisar pedidos aguardando faturamento',
  'Confirmar pagamentos recebidos hoje',
  'Verificar itens com estoque critico',
]
</script>

<template>
  <section class="dashboard-content">
    <div class="hero-panel">
      <div>
        <p class="hero-label">Resumo da operacao</p>
        <h2>Uma visao central do seu ERP para comecar o dia.</h2>
        <p class="hero-text">
          Usuario conectado: {{ userName }}. Perfil atual: {{ userRole }}. E-mail: {{ userEmail }}.
        </p>
      </div>
      <div class="hero-badge">
        <span>Status</span>
        <strong>Operacao ativa</strong>
      </div>
    </div>

    <div class="metrics-grid">
      <article v-for="card in metricCards" :key="card.title" class="metric-card">
        <span>{{ card.title }}</span>
        <strong>{{ card.value }}</strong>
        <small>{{ card.detail }}</small>
      </article>
    </div>

    <div class="content-grid">
      <article class="panel">
        <h3>Atividades prioritarias</h3>
        <ul>
          <li v-for="task in taskItems" :key="task">{{ task }}</li>
        </ul>
      </article>

      <article class="panel accent-panel">
        <h3>Modulo em destaque</h3>
        <p>
          Esta tela inicial pode receber atalhos, graficos e cards de cada area do sistema.
        </p>
        <button type="button">Configurar painel</button>
      </article>
    </div>
  </section>
</template>

<style scoped>
.dashboard-content {
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
  background: linear-gradient(135deg, #162132 0%, #1d6c47 100%);
  color: #f8fbff;
}

.hero-label {
  margin: 0 0 0.45rem;
  color: #b8f0c4;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.hero-panel h2 {
  margin: 0;
  max-width: 16ch;
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
  color: #d6f5dc;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.hero-badge strong {
  font-size: 1.2rem;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.metric-card {
  display: grid;
  gap: 0.4rem;
  padding: 1.3rem;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid #dde5ee;
  box-shadow: 0 18px 35px rgba(22, 33, 50, 0.06);
}

.metric-card span {
  color: #556476;
}

.metric-card strong {
  color: #162132;
  font-size: 1.9rem;
}

.metric-card small {
  color: #768599;
}

.content-grid {
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 1rem;
}

.panel {
  padding: 1.4rem;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid #dde5ee;
}

.panel h3 {
  margin: 0 0 1rem;
  color: #162132;
}

.panel ul {
  margin: 0;
  padding-left: 1.2rem;
  color: #4f5f73;
}

.panel li + li {
  margin-top: 0.7rem;
}

.accent-panel {
  background: linear-gradient(180deg, #fffdf7 0%, #f8f1db 100%);
}

.accent-panel p {
  color: #4b5563;
}

.accent-panel button {
  margin-top: 0.8rem;
  border: 0;
  border-radius: 10px;
  padding: 0.85rem 1rem;
  background: #162132;
  color: #ffffff;
  font-weight: 700;
}

@media (max-width: 980px) {
  .metrics-grid,
  .content-grid {
    grid-template-columns: 1fr;
  }

  .hero-panel {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
