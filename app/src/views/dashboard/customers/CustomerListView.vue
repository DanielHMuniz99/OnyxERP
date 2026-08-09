<script setup lang="ts">
import { computed, ref } from 'vue'
import type { Customer } from './types'

const props = defineProps<{
  customers: Customer[]
  isLoading: boolean
}>()

const emit = defineEmits<{
  create: []
  edit: [customer: Customer]
  refresh: []
}>()

const search = ref('')

const filteredCustomers = computed(() => {
  const query = search.value.trim().toLowerCase()
  if (!query) {
    return props.customers
  }

  return props.customers.filter((customer) => {
    const searchableValues = [
      customer.name,
      customer.trade_name,
      customer.document,
      customer.email,
      customer.phone,
      customer.city,
      customer.state,
    ]

    return searchableValues.some((value) => value?.toLowerCase().includes(query))
  })
})
</script>

<template>
  <article class="panel list-panel">
    <div class="panel-heading">
      <div>
        <p class="panel-eyebrow">Listagem</p>
        <h3>Base de clientes</h3>
      </div>

      <div class="panel-actions">
        <input v-model="search" type="search" placeholder="Buscar cliente" />
        <button type="button" class="primary-button" @click="emit('create')">Novo cliente</button>
        <button type="button" class="secondary-button" @click="emit('refresh')">Atualizar</button>
      </div>
    </div>

    <div v-if="isLoading" class="empty-state">Carregando clientes...</div>

    <div v-else-if="filteredCustomers.length === 0" class="empty-state">
      Nenhum cliente encontrado para os filtros atuais.
    </div>

    <div v-else class="table-wrapper">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Documento</th>
            <th>Contato</th>
            <th>Localidade</th>
            <th>Status</th>
            <th>Acoes</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="customer in filteredCustomers" :key="customer.id">
            <td>
              <strong>{{ customer.name }}</strong>
              <small>{{ customer.trade_name || 'Sem nome fantasia' }}</small>
            </td>
            <td>{{ customer.document || 'Nao informado' }}</td>
            <td>
              <span>{{ customer.email || 'Sem e-mail' }}</span>
              <small>{{ customer.phone || 'Sem telefone' }}</small>
            </td>
            <td>{{ customer.city || 'Cidade nao informada' }}{{ customer.state ? ` / ${customer.state}` : '' }}</td>
            <td>
              <span :class="customer.is_active ? 'status active' : 'status inactive'">
                {{ customer.is_active ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td>
              <div class="row-actions">
                <button type="button" class="table-button" @click="emit('edit', customer)">
                  Editar
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </article>
</template>

<style scoped>
.panel {
  padding: 1.4rem;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid #dde5ee;
}

.panel-heading {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.panel-eyebrow {
  margin: 0 0 0.35rem;
  color: #245e8c;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.panel h3 {
  margin: 0;
  color: #162132;
}

.panel-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.panel-actions input {
  min-width: 220px;
}

input {
  border: 1px solid #c8d2de;
  border-radius: 10px;
  padding: 0.8rem 0.9rem;
  font-size: 0.96rem;
  color: #0f172a;
  background: #ffffff;
}

input:focus {
  outline: 2px solid #8ec4f7;
  outline-offset: 1px;
}

.primary-button,
.secondary-button,
.table-button {
  border-radius: 10px;
  padding: 0.75rem 0.95rem;
  font-weight: 700;
  cursor: pointer;
}

.primary-button {
  border: 0;
  background: linear-gradient(120deg, #1d6c47, #245e8c);
  color: #ffffff;
}

.secondary-button {
  border: 1px solid #d3dde8;
  background: #ffffff;
  color: #24506f;
}

.table-button {
  border: 0;
  background: #162132;
  color: #ffffff;
}

.empty-state {
  display: grid;
  place-items: center;
  min-height: 240px;
  border: 1px dashed #d8e0ea;
  border-radius: 16px;
  color: #66758a;
}

.table-wrapper {
  overflow-x: auto;
  border: 1px solid #e2eaf2;
  border-radius: 16px;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 840px;
}

.data-table th,
.data-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e8eef5;
  vertical-align: top;
}

.data-table th {
  background: #f7fafc;
  color: #5f6f82;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

.data-table td {
  color: #162132;
}

.data-table td strong,
.data-table td span,
.data-table td small {
  display: block;
}

.data-table td small {
  margin-top: 0.25rem;
  color: #708095;
}

.status {
  width: fit-content;
  padding: 0.35rem 0.6rem;
  border-radius: 999px;
  font-size: 0.78rem;
  font-weight: 700;
}

.status.active {
  background: #e5f7ea;
  color: #16653b;
}

.status.inactive {
  background: #fde8e8;
  color: #9f1d1d;
}

.row-actions {
  display: flex;
  gap: 0.5rem;
}

@media (max-width: 900px) {
  .panel-heading,
  .panel-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .panel-actions input {
    min-width: 0;
  }
}
</style>
