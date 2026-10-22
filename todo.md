# TODO — Ángel × LoveMakeup (estado del día: 2026-10-21)

> Congelación del estado al cerrar la sesión de integración en la VM (Ubuntu 26.04 en Termux proot).
> Este archivo va versionado (no está en `.gitignore`): continúa desde la PC con este checklist.

## Mapa rápido

| Proyecto | Ruta en la VM | Estado global |
|---|---|---|
| **IAngel** (FastAPI, este repo) | `/root/IAngel` | Código y docs al día; falta terminar MariaDB + e2e |
| **Lovemakeup-3.0** (PHP tienda) | `/root/Lovemakeup-3.0` (rama `Local`) | `chat.php` y llave A en disco; falta commit de la pública + passconfig + despliegue |

## ✅ Hecho hoy (no repetir)

- [x] **RS256 en IAngel**: `config.py` (`JWT_ALGORITHM=RS256` por defecto + `JWT_PUBLIC_KEY`; `jwt_secret` queda como modo legado HS256), `auth.py` (verifica la firma leyendo el PEM), `test_api.py` con par RSA efímero → `pytest` **7/7 verde** (válido/inválido/expirado/sin token + NLP + dimensiones)
- [x] **RS256 desde el lado PHP ya existía**: `login.php`, `issue_jwt.php` y ~8 APIs firman/verifican con `config/jwt_{private,public}.pem` (openssl_sign SHA256); no hay secreto compartido HS256 que sincronizar
- [x] **LLM de pago**: `qwen/qwen3-30b-a3b-instruct-2507` (instructivo, sin razonamiento) y `LLM_TIMEOUT_SECONDS=6.0`
- [x] **Embeddings**: `qwen/qwen3-embedding-0.6b` **ya no tiene endpoints en OpenRouter** (404 "No endpoints found"). Reemplazado por **`baai/bge-m3`**: 1024 dimensiones nativas (la columna `VECTOR(1024)` queda intacta), multilingüe, ~$0.01/M tokens. Cambio aplicado en `config.py`, `llm.py`, `.env`, `.env.example`, `README.md`, `AGENTS.md`
- [x] **Llaves RSA par A** (2048 bits, emparejadas y verificadas) copiadas a:
  - `IAngel/ai_private.pem` + `ai_public.pem` (ignoradas por `*.pem` del `.gitignore`)
  - `Lovemakeup-3.0/config/jwt_private.pem` (600) y `config/jwt_public.pem` (600)
- [x] **`.env` de IAngel**: completo y validado (RS256, MariaDB local, `OPENROUTER_API_KEY` con crédito $50/mes) — **línea `EMBEDDING_MODEL=baai/bge-m3` incluida**. ES SECRETO: no versionar
- [x] **MariaDB 11.8.6 instalada** en la VM (repo de Ubuntu; soporta `VECTOR`). La instalación de `dpkg` quedó a mitad de configuración (ver pendiente 1)
- [x] **`chat.php` en el clon** (rama `Local`, commit "Angel V1"): firma su propio JWT RS256 de 1 h (`sub=love_makeup_user`) con `ANGEL_PRIVATE_KEY_PATH` de `passconfig.env`, llama a `ANGEL_API_URL` con `Authorization: Bearer` y envía `historial` (IAngel lo ignora por ahora, base de la Fase 2)
- [x] Docs de IAngel actualizadas y sincronizadas con el código (README, AGENTS.md, .env.example)

## 🔜 Pendiente inmediato — IAngel / VM local

- [ ] **1. Terminar MariaDB** (la instalación quedó interrumpida a medio configurar):
  `dpkg --configure -a` → comprobar `mariadb --version` ≥ 11.7 y `dpkg -l mariadb-server` en estado `ii`
- [ ] **2. Levantar el servidor manualmente** (proot: sin systemd; si setuid falla, correr como root):
  `mariadb-install-db --datadir=/var/lib/mysql` → `mariadbd --user=root --socket=/run/mysqld/mysqld.sock &`
  Verificar: `mariadb -u root -e "SELECT VERSION();"` (debe ser 11.8.x)
- [ ] **3. BD + usuario + permisos** (raspa valor exacto de credenciales desde `IAngel/.env`, no desde aquí):
  - Crear base `u417714339_Lovemakeupbd` y aplicar `BD/schema.sql` (crea `angel_producto_ai` con `VECTOR(1024) NULL`)
  - Crear usuario `angel` con la contraseña de `.env` y sus `GRANT`s: `SELECT` en `producto/categoria/marca`; `SELECT, INSERT, UPDATE` en `angel_producto_ai`
- [ ] **4. Importar el catálogo del hosting**: `mariadb -u root u417714339_Lovemakeupbd < BD/bd.sql` (el dump trae 21 tablas; `producto` tiene `precio_detal/stock_disponible/estatus`)
- [ ] **5. Indexar embeddings**: `python seed.py reindex` (una llamada a OpenRouter por producto activo con `baai/bge-m3`; ya validado: 1024 dims)
- [ ] **6. Servir y verificar salud**: `venv/bin/python -m uvicorn main:app --port 8000` → `GET /health` debe devolver `{"status":"ok"}`
- [ ] **7. Prueba e2e de chat**: firmar token RS256 con `ai_private.pem` (PyJWT, payload `{sub, iat, exp}` — igual que `issue_jwt.php`) → `POST /api/chat` con mensaje tipo "tengo la piel grasa y acné, busco una base de menos de $15" → comprobar productos reales con precio y stock; probar también 401 (token basura) y 422 (mensaje corto)
- [ ] **8. (Opcional) smoke-test del widget**: `apt install php-cli` y renderizar `vista/complementos/chat.php` por CLI con un `passconfig.env` mínimo para comprobar que `openssl_sign` construye el token sin errores PHP

## 🔜 Pendiente — Lovemakeup-3.0 (PHP)

- [ ] **1. Commitear la pública del par A**: HEAD de `Local` aún tiene el par B antiguo (`7ab5ba…`); el disco ya trae el par A (`ad7190…` = idéntico a `IAngel/ai_public.pem`). Commit: `config/jwt_public.pem` (+ este archivo y `.gitignore` ya ajustado)
- [ ] **2. `passconfig.env` LOCAL** (gitignored, no viene en el clon):
  ```dotenv
  ANGEL_API_URL=http://127.0.0.1:8000/api/chat
  ANGEL_PRIVATE_KEY_PATH=config/jwt_private.pem
  ```
  (la privada del par A ya está en esa ruta del disco, sin passphrase; el resto de variables `DB_*` irán cuando se sincronicen las del hosting)
- [ ] **3. Despliegue del par (cuando toque)**: en el hosting colocar `config/jwt_private.pem` = privada A **y** `config/jwt_public.pem` = pública A **ambos a la vez** (si no, los logins fallarían: quien firma y quien verifica quedarían con pares distintos). Tokens de 1 h → ventana sin impacto. Y push de la rama `Local`
- [ ] **4. Prueba post-despliegue**: login real de la tienda → token → `/api/chat` del servidor HTTPS público de Ángel; y ajustar `CORS_ORIGINS` con el dominio real de la tienda

## 🚧 Fase 2 (decisión del usuario: pospuesto)

- [ ] **Historial de conversación**: el widget ya envía `historial` (≤12) y `ConsultaChat` lo ignora; implementar implies añadir campo, prompt con últimos N mensajes y (opcional) contexto en `texto_para_embedding`
- [ ] Limpieza: borrar `package-lock.json` huérfano (85 bytes, sin `package.json`) y decidir sobre `chat_widget.js` de ejemplo
- [ ] `/health` riguroso: comprobar `VERSION()` de MariaDB ≥ 11.7 y disponibilidad del modelo de embeddings
- [ ] `seed.py` en lotes (entrada en arreglo) con límite de tasa para catálogos grandes

## Notas de entorno (VM)

- Los **pipes** largos (`cmd | tail`) pueden colgarse si hay I/O saturado; redirige a archivo y `tail` aparte
- `OPENROUTER_API_KEY` vive solo en `IAngel/.env` (nunca versionar; validar con `/api/v1/auth/key`)
- Módulos/config se leen al arrancar el proceso: tras editar `.env` hay que **reiniciar uvicorn**
- MariaDB: ver también `AGENTS.md` — reglas de BD (solo-lectura del catálogo; tabla propia `angel_producto_ai`)
