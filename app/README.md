# Front-end (Vue + Vite)

Este app usa uma configuracao central para endereco do back-end.
Assim, todos os forms e requests usam a mesma base de URL e voce troca porta/host em um lugar so.

## 1) Configurar ambiente

Copie `.env.example` para `.env` e ajuste:

```bash
VITE_BACKEND_URL=http://localhost:8000
VITE_API_PREFIX=/api
```

- `VITE_BACKEND_URL`: host/porta do Laravel (ex.: `http://localhost:8001`)
- `VITE_API_PREFIX`: prefixo das rotas da API (normalmente `/api`)

## 2) Usar helper unico para montar endpoints

Use o helper em `src/config/api.ts`:

```ts
import { apiUrl } from '@/config/api'

const loginEndpoint = apiUrl('/auth/login')
```

### Exemplo com fetch

```ts
const response = await fetch(apiUrl('/clientes'), {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(payload),
})
```

### Exemplo com form action

```vue
<script setup lang="ts">
import { apiUrl } from '@/config/api'

const criarPedidoAction = apiUrl('/pedidos')
</script>

<template>
  <form :action="criarPedidoAction" method="post">
    <input name="descricao" />
    <button type="submit">Salvar</button>
  </form>
</template>
```

## 3) Proxy no desenvolvimento

O `vite.config.ts` ja esta configurado para proxy em `/api` para `VITE_BACKEND_URL`.
Isso ajuda no desenvolvimento e evita fixar porta no codigo.

## Scripts

```bash
npm install
npm run dev
npm run build
```
