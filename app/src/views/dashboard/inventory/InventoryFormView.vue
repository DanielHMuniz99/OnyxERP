<script setup lang="ts">
import { computed, reactive, watch } from 'vue'
import type { Inventory, InventoryFormData } from './types'

const props = defineProps<{
  inventory: Inventory | null
  isSubmitting: boolean
}>()

const emit = defineEmits<{
  submit: [payload: InventoryFormData]
  back: []
}>()

const form = reactive<InventoryFormData>({
  name: '',
  sku: '',
  unit: 'un',
  quantity: '0',
  minimum_quantity: '0',
  cost_price: '',
  sale_price: '',
  location: '',
  notes: '',
})

const isEditing = computed(() => Boolean(props.inventory))
const title = computed(() => (isEditing.value ? 'Editar item de estoque' : 'Novo item de estoque'))
const submitLabel = computed(() => {
  if (props.isSubmitting) {
    return isEditing.value ? 'Salvando alteracoes...' : 'Cadastrando item...'
  }

  return isEditing.value ? 'Salvar alteracoes' : 'Cadastrar item'
})

function applyInventory(inventory: Inventory | null): void {
  form.name = inventory?.name ?? ''
  form.sku = inventory?.sku ?? ''
  form.unit = inventory?.unit ?? 'un'
  form.quantity = inventory?.quantity ?? '0'
  form.minimum_quantity = inventory?.minimum_quantity ?? '0'
  form.cost_price = inventory?.cost_price ?? ''
  form.sale_price = inventory?.sale_price ?? ''
  form.location = inventory?.location ?? ''
  form.notes = inventory?.notes ?? ''
}

watch(
  () => props.inventory,
  (inventory) => {
    applyInventory(inventory)
  },
  { immediate: true },
)

function handleSubmit(): void {
  emit('submit', {
    name: form.name,
    sku: form.sku,
    unit: form.unit,
    quantity: form.quantity,
    minimum_quantity: form.minimum_quantity,
    cost_price: form.cost_price,
    sale_price: form.sale_price,
    location: form.location,
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
      <button type="button" class="form-secondary-button" :disabled="isSubmitting" @click="emit('back')">
        Voltar para listagem
      </button>
    </div>

    <form class="form-layout" @submit.prevent="handleSubmit">
      <label for="name" class="form-label">Nome do item</label>
      <input id="name" v-model="form.name" class="form-control" type="text" required />

      <div class="form-inline-fields">
        <div>
          <label for="sku" class="form-label">SKU</label>
          <input id="sku" v-model="form.sku" class="form-control" type="text" required />
        </div>
        <div>
          <label for="unit" class="form-label">Unidade</label>
          <select id="unit" v-model="form.unit" class="form-select">
            <option value="un">Unidade</option>
            <option value="cx">Caixa</option>
            <option value="kg">Quilo</option>
            <option value="lt">Litro</option>
          </select>
        </div>
      </div>

      <div class="form-inline-fields three-columns">
        <div>
          <label for="quantity" class="form-label">Quantidade atual</label>
          <input id="quantity" v-model="form.quantity" class="form-control" type="number" min="0" step="0.01" required />
        </div>
        <div>
          <label for="minimum_quantity" class="form-label">Estoque minimo</label>
          <input id="minimum_quantity" v-model="form.minimum_quantity" class="form-control" type="number" min="0" step="0.01" />
        </div>
        <div>
          <label for="location" class="form-label">Localizacao</label>
          <input id="location" v-model="form.location" class="form-control" type="text" />
        </div>
      </div>

      <div class="form-inline-fields two-columns-wide">
        <div>
          <label for="cost_price" class="form-label">Preco de custo</label>
          <input id="cost_price" v-model="form.cost_price" class="form-control" type="number" min="0" step="0.01" />
        </div>
        <div>
          <label for="sale_price" class="form-label">Preco de venda</label>
          <input id="sale_price" v-model="form.sale_price" class="form-control" type="number" min="0" step="0.01" />
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
  color: #7c5a15;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.panel h3 {
  margin: 0;
  color: #162132;
}

.three-columns {
  grid-template-columns: 1fr 1fr 1fr;
}

.two-columns-wide {
  grid-template-columns: 1fr 1fr;
}

@media (max-width: 720px) {
  .panel-heading {
    flex-direction: column;
    align-items: stretch;
  }

  .three-columns,
  .two-columns-wide {
    grid-template-columns: 1fr;
  }
}
</style>
