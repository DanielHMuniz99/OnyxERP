const DEFAULT_BACKEND_URL = 'http://127.0.0.1:8001'
const DEFAULT_API_PREFIX = '/api'

function trimTrailingSlashes(value: string): string {
  return value.replace(/\/+$/, '')
}

function normalizePath(path: string): string {
  return path.startsWith('/') ? path : `/${path}`
}

export const BACKEND_URL = trimTrailingSlashes(
  import.meta.env.VITE_BACKEND_URL || DEFAULT_BACKEND_URL,
)

export const API_PREFIX = trimTrailingSlashes(
  import.meta.env.VITE_API_PREFIX || DEFAULT_API_PREFIX,
)

export const API_BASE_URL = `${BACKEND_URL}${API_PREFIX}`

export function apiUrl(path = ''): string {
  if (!path) {
    return API_BASE_URL
  }

  return `${API_BASE_URL}${normalizePath(path)}`
}