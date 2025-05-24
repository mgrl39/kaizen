# 📦 PostgreSQL con Podman para Proyecto Kaizen

Este entorno te permite levantar una instancia de PostgreSQL 15 usando **Podman**, ideal para entornos de desarrollo locales del proyecto **Kaizen**.

---

## 🚀 Características

* PostgreSQL 15 con volumen persistente
* Script Bash para gestión completa del contenedor
* Soporte para dump de esquema SQL con fecha y hora
* Reinicio limpio de base de datos (modo seguro)
* Inicialización automática con scripts en el directorio `init/`

---

## 🧰 Requisitos

* [Podman](https://podman.io/) instalado y en el PATH
* Bash (para ejecutar el script)
* Cliente `psql` (opcional, para acceso directo a la base de datos)

### 🧪 Comandos disponibles

```bash
./postgres.sh [comando]
```

| Comando   | Descripción                                      |
| --------- | ------------------------------------------------ |
| `start`   | Inicia el contenedor PostgreSQL                  |
| `stop`    | Detiene el contenedor                            |
| `restart` | Reinicia el contenedor                           |
| `status`  | Muestra el estado actual                         |
| `logs`    | Muestra los logs del contenedor                  |
| `shell`   | Abre una shell `psql` dentro del contenedor      |
| `dump`    | Crea un dump del esquema SQL                     |
| `reset`   | ⚠️ Elimina todos los datos y reinicia desde cero |

---

### Ejemplos

Iniciar el contenedor:

```bash
./postgres.sh start
```

Acceder al cliente `psql`:

```bash
./postgres.sh shell
```

Crear un dump del esquema SQL:

```bash
./postgres.sh dump
```

Eliminar todo y empezar de cero:

```bash
./postgres.sh reset
```

---

## 🗃️ Estructura del proyecto

```
.
├── postgres.sh                     # Script Bash para manejo con Podman
├── init/                          # Scripts SQL que se ejecutan al iniciar por primera vez
├── dumps/                         # Dumps generados automáticamente
└── README.md                      # Este archivo
```

---

## 📌 Credenciales por defecto

* **Usuario:** `kaizen`
* **Contraseña:** `kaizen`
* **Base de datos:** `kaizendb`
* **Puerto local expuesto:** `5433`

> ⚠️ Estas credenciales son solo para desarrollo. No las uses en producción.
