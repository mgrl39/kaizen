# 📋 Focalboard con Podman para Proyecto Kaizen

Este entorno lanza una instancia local de **Focalboard** (una alternativa open source a Trello y Notion) usando **Podman**. Está diseñado para facilitar la planificación ágil de tareas dentro del proyecto **Kaizen**.

---

## 🚀 Características

* Focalboard alojado localmente en `http://localhost:8090`
* Gestión completa del contenedor mediante script Bash
* Volumen persistente para conservar datos
* Modo seguro de reinicio (con eliminación de datos)
* Compatible con Podman (sin Docker)

---

## 🧰 Requisitos

* [Podman](https://podman.io/) instalado y accesible desde tu terminal
* Bash

---


### 📘 Comandos disponibles

```bash
./focalboard.sh [comando]
```

| Comando   | Descripción                                      |
| --------- | ------------------------------------------------ |
| `start`   | Inicia Focalboard (o lo crea si no existe)       |
| `stop`    | Detiene el contenedor                            |
| `restart` | Reinicia el contenedor                           |
| `status`  | Muestra el estado actual                         |
| `logs`    | Muestra los logs de ejecución                    |
| `reset`   | ⚠️ Elimina todos los datos y reinicia desde cero |
| `info`    | Muestra URL de acceso y guía de uso rápida       |

---

### 🌐 Acceso a Focalboard

Una vez iniciado, accede a Focalboard en:

```
http://localhost:8090
```

---

## 💡 Guía rápida de uso

1. Abre la URL de Focalboard en tu navegador.
2. Crea un nuevo workspace (espacio de trabajo).
3. Crea un tablero del tipo **"Sprint Planning"**.
4. Usa listas para representar tus **sprints semanales**.
5. Añade **tarjetas** con las tareas específicas de cada sprint.
6. Aplica **etiquetas** para priorizar o categorizar.
7. Cambia a vista **Kanban** para flujo de trabajo visual.
8. Usa la vista **Calendario** para planificar visualmente.

---

## 🗃️ Estructura del proyecto

```
.
├── focalboard.sh                  # Script Bash para manejar el contenedor
├── README.md                      # Este archivo
```

---

## 📌 Configuración por defecto

* **Imagen:** `mattermost/focalboard:latest`
* **Contenedor:** `kaizen_focalboard`
* **Puerto local:** `8090`
* **Volumen de datos:** `kaizen_focalboard_data`

---

## 🧼 Advertencia de seguridad

Este entorno es para uso **local y de desarrollo**. No lo utilices directamente en producción sin agregar autenticación, HTTPS y otras medidas de seguridad.