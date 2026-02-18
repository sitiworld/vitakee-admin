# Estrategia de Ramas - Vitakee

## Ramas Principales

| Rama | Propósito | Protegida |
|------|-----------|-----------|
| `main` | **Producción** - Código en vivo | ✅ Sí |
| `qa` | **Testing/QA** - Validación antes de producción | ✅ Sí |
| `develop` | **Integración** - Donde se unen los features | ✅ Sí |

## Ramas Temporales

| Rama | Propósito | Ejemplo |
|------|-----------|---------|
| `feature/*` | Nuevas funcionalidades | `feature/login-google` |
| `bugfix/*` | Corrección de bugs | `bugfix/fix-logout` |
| `hotfix/*` | Fixes urgentes en producción | `hotfix/security-patch` |

---

## Flujo de Trabajo

```
┌─────────────┐     PR      ┌─────────────┐     PR      ┌─────────────┐     PR      ┌─────────────┐
│  feature/*  │ ──────────► │   develop   │ ──────────► │     qa      │ ──────────► │    main     │
│             │             │ (integración)│             │  (testing)  │             │ (producción)│
└─────────────┘             └─────────────┘             └─────────────┘             └─────────────┘
```

---

## Pasos para Desarrollar

### 1. Crear feature desde develop
```bash
git checkout develop
git pull origin develop
git checkout -b feature/nombre-del-feature
```

### 2. Desarrollar y hacer commits
```bash
git add .
git commit -m "feat: descripción del cambio"
```

### 3. Push y crear PR
```bash
git push origin feature/nombre-del-feature
```
→ Crear **Pull Request** hacia `develop`

### 4. Code review y merge a develop

### 5. PR de develop → qa (para testing)

### 6. PR de qa → main (release a producción)

---

## Reglas Importantes

- ❌ **Nunca** hacer push directo a `main`, `qa` o `develop`
- ✅ **Siempre** usar Pull Requests
- ✅ **Siempre** crear features desde `develop`
- ✅ **Hotfixes** urgentes: crear desde `main`, mergear a `main` Y `develop`

---

## Tips y Buenas Prácticas

### Antes de empezar a trabajar

```bash
# Siempre actualizar develop antes de crear un feature
git checkout develop
git pull origin develop
git checkout -b feature/mi-feature
```

### Durante el desarrollo

```bash
# Commits pequeños y frecuentes (más fácil de revisar y revertir)
git add .
git commit -m "feat: agregar botón de login"

# Sincronizar con develop periódicamente (evita conflictos grandes)
git fetch origin
git merge origin/develop
# o mejor aún:
git rebase origin/develop
```

### Antes de crear el PR

```bash
# SIEMPRE actualizar tu rama con los últimos cambios de develop
git fetch origin
git rebase origin/develop

# Resolver conflictos localmente (no en GitHub)
# Si hay conflictos, resolverlos y continuar:
git add .
git rebase --continue

# Push (usar -f solo si hiciste rebase)
git push origin feature/mi-feature
# o si hiciste rebase:
git push origin feature/mi-feature -f
```

---

## Convención de Commits

| Prefijo | Uso |
|---------|-----|
| `feat:` | Nueva funcionalidad |
| `fix:` | Corrección de bug |
| `docs:` | Documentación |
| `style:` | Formato (no afecta lógica) |
| `refactor:` | Refactorización |
| `test:` | Tests |
| `chore:` | Tareas de mantenimiento |

### Ejemplos
```bash
git commit -m "feat: agregar autenticación con Google"
git commit -m "fix: corregir error en validación de email"
git commit -m "docs: actualizar README con instrucciones de instalación"
```

---

## Checklist antes del PR

- [ ] `git pull origin develop` o `git rebase origin/develop`
- [ ] Código compila sin errores
- [ ] Tests pasan (si hay)
- [ ] Sin `console.log` de debug
- [ ] Commits con mensajes descriptivos
- [ ] PR con descripción clara de qué hace

---

## Errores Comunes a Evitar

| ❌ Error | ✅ Solución |
|----------|-------------|
| Push directo a `develop` | Siempre usar PR |
| Feature branch desactualizado | `git rebase origin/develop` antes del PR |
| Commits gigantes | Commits pequeños y atómicos |
| Mensaje "fix" sin contexto | `fix: corregir validación de email vacío` |
| Resolver conflictos en GitHub | Resolverlos localmente primero |
| Olvidar hacer pull | `git pull` al inicio de cada sesión |

---

## Alias Útiles (opcional)

Agregar a tu archivo `~/.gitconfig`:

```ini
[alias]
    sync = !git fetch origin && git rebase origin/develop
    st = status
    co = checkout
    br = branch
    cm = commit -m
```

**Uso:** `git sync` antes de cada PR para sincronizar con develop.

---

## Resumen Visual

```
                    ┌──────────────────────────────────────────────────────────┐
                    │                    FLUJO DE TRABAJO                       │
                    └──────────────────────────────────────────────────────────┘

    DESARROLLO                    TESTING                         PRODUCCIÓN
    ──────────                    ───────                         ──────────

    feature/login ─┐
                   │
    feature/api ───┼──► develop ──────────► qa ──────────────────► main
                   │        │                │                       │
    bugfix/error ──┘        │                │                       │
                            ▼                ▼                       ▼
                      Integración        Validación              Release
                      de código          y testing              a usuarios


    ┌─────────────────────────────────────────────────────────────────────────┐
    │  REGLA DE ORO: Siempre PR, nunca push directo a ramas protegidas        │
    └─────────────────────────────────────────────────────────────────────────┘
```

---

*Documento generado el 9 de enero de 2026*
