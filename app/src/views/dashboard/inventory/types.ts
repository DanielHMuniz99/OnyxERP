export type Inventory = {
  id: number
  name: string
  sku: string
  unit: string
  quantity: string
  minimum_quantity: string
  cost_price?: string | null
  sale_price?: string | null
  location?: string | null
  notes?: string | null
  is_active: boolean
}

export type InventoryFormData = {
  name: string
  sku: string
  unit: string
  quantity: string
  minimum_quantity: string
  cost_price: string
  sale_price: string
  location: string
  notes: string
}

export type InventoryListResponse = {
  inventories: Inventory[]
}

export type InventoryShowResponse = {
  inventory: Inventory
}

export type InventoryMutationResponse = {
  message: string
  inventory: Inventory
}

export type ApiError = {
  message?: string
  errors?: Record<string, string[]>
}

export type InventoryRouteMode = 'list' | 'create' | 'edit'
