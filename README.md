# OnyxERP

Projeto com front-end e back-end separados:

- `app`: Vue + Vite
- `api`: Laravel

## Configurar endereco do back-end usado pelo front

Toda a configuracao fica no front-end em `app/.env`.

1. Crie `app/.env` a partir de `app/.env.example`.
2. Ajuste o host/porta do back-end:

```bash
VITE_BACKEND_URL=http://localhost:8000
VITE_API_PREFIX=/api
```

Com isso, os forms e requests do front podem usar a funcao unica `apiUrl()` em `app/src/config/api.ts`, sem espalhar URL fixa no codigo.
