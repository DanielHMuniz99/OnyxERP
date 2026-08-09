export type Customer = {
  id: number
  name: string
  trade_name?: string | null
  document?: string | null
  email?: string | null
  phone?: string | null
  city?: string | null
  state?: string | null
  notes?: string | null
  is_active: boolean
}

export type CustomerFormData = {
  name: string
  trade_name: string
  document: string
  email: string
  phone: string
  city: string
  state: string
  notes: string
}

export type CustomerListResponse = {
  customers: Customer[]
}

export type CustomerShowResponse = {
  customer: Customer
}

export type CustomerMutationResponse = {
  message: string
  customer: Customer
}

export type ApiError = {
  message?: string
  errors?: Record<string, string[]>
}

export type CustomerRouteMode = 'list' | 'create' | 'edit'
