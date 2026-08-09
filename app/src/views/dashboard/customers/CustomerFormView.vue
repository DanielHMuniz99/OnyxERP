<script setup lang="ts">
import { computed, reactive, watch } from 'vue'
import type { Customer, CustomerFormData } from './types'

const props = defineProps<{
  customer: Customer | null
  isSubmitting: boolean
}>()

const emit = defineEmits<{
  submit: [payload: CustomerFormData]
  back: []
}>()

const form = reactive<CustomerFormData>({
  name: '',
  trade_name: '',
  document: '',
  email: '',
  phone: '',
  city: '',
  state: '',
  notes: '',
})

const isEditing = computed(() => Boolean(props.customer))
const title = computed(() => (isEditing.value ? 'Editar cliente' : 'Novo cliente'))
const submitLabel = computed(() => {
  if (props.isSubmitting) {
    return isEditing.value ? 'Salvando alteracoes...' : 'Cadastrando cliente...'
  }

  return isEditing.value ? 'Salvar alteracoes' : 'Cadastrar cliente'
})

function applyCustomer(customer: Customer | null): void {
  form.name = customer?.name ?? ''
  form.trade_name = customer?.trade_name ?? ''
  form.document = customer?.document ?? ''
  form.email = customer?.email ?? ''
  form.phone = customer?.phone ?? ''
  form.city = customer?.city ?? ''
  form.state = customer?.state ?? ''
  form.notes = customer?.notes ?? ''
}

watch(
  () => props.customer,
  (customer) => {
    applyCustomer(customer)
  },
  { immediate: true },
)

function handleSubmit(): void {
  emit('submit', {
    name: form.name,
    trade_name: form.trade_name,
    document: form.document,
    email: form.email,
    phone: form.phone,
    city: form.city,
    state: form.state.toUpperCase(),
    notes: form.notes,
  })
}
</script>

<template>
  <article class="panel form-panel">
    <div class="panel-heading">
      <div>
        <p class="panel-eyebrow">Formulario</p>
        <h3>{{ title }}</h3>
      </div>
      <button
        type="button"
        class="form-secondary-button"
        :disabled="isSubmitting"
        @click="emit('back')"
      >
        Voltar para listagem
      </button>
    </div>

    <form class="form-layout" @submit.prevent="handleSubmit">
      <label for="name" class="form-label">Nome</label>
      <input id="name" v-model="form.name" class="form-control" type="text" required />

      <label for="trade_name" class="form-label">Nome fantasia</label>
      <input id="trade_name" v-model="form.trade_name" class="form-control" type="text" />

      <label for="document" class="form-label">Documento</label>
      <input id="document" v-model="form.document" class="form-control" type="text" placeholder="CPF ou CNPJ" />

      <label for="email" class="form-label">E-mail</label>
      <input id="email" v-model="form.email" class="form-control" type="email" />

      <label for="phone" class="form-label">Telefone</label>
      <input id="phone" v-model="form.phone" class="form-control" type="text" />

      <div class="form-inline-fields">
        <div>
          <label for="city" class="form-label">Cidade</label>
          <input id="city" v-model="form.city" class="form-control" type="text" />
        </div>

        <div>
          <label for="state" class="form-label">UF</label>
          <input id="state" v-model="form.state" class="form-control" type="text" maxlength="2" />
        </div>
      </div>

      <label for="notes" class="form-label">Observacoes</label>
      <textarea id="notes" v-model="form.notes" class="form-textarea" rows="4" />

      <button type="submit" class="form-primary-button" :disabled="isSubmitting">
        {{ submitLabel }}
      </button>
    </form>
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

@media (max-width: 720px) {
  .panel-heading {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
