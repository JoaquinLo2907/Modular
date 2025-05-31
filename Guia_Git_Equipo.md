# 🚀 Guía de trabajo en equipo con Git y GitHub

## 📁 Paso 1: Clonar el repositorio (solo una vez)
```bash
git clone https://github.com/usuario/repositorio.git
cd repositorio
```
Esto descarga el proyecto completo y entra a la carpeta del repositorio.

---

## 🔀 Paso 2: Crear tu propia rama
```bash
git checkout -b nombre-de-tu-rama
```
Ejemplo:
```bash
git checkout -b doges
```
Esto crea y cambia a una nueva rama donde trabajarás sin afectar `main`.

---

## 📝 Paso 3: Hacer cambios y guardarlos
```bash
git add .
git commit -m "Descripción de los cambios"
```
Ejemplo:
```bash
git commit -m "Agregué formulario de registro"
```

---

## 🚀 Paso 4: Subir tus cambios

### ✅ A tu propia rama (por ejemplo, `doges`):
```bash
git push -u origin doges
```

### ✅ A la rama principal (`main`) **solo si está permitido**:
```bash
git checkout main
git pull origin main # Actualiza la versión local
# (Haz tus cambios)
git add .
git commit -m "Cambios en main"
git push origin main
```
> Recomendado: usar ramas y no modificar directamente `main`.

---

## 🌐 Paso 5: Subir tu rama a GitHub (si no lo habías hecho)
```bash
git push -u origin nombre-de-tu-rama
```

---

## 🔍 Verificación de estado y cambios pendientes

### 🔍 Ver si tienes cambios locales:
```bash
git status
```

### 🔢 Ver los commits que no se han hecho push:
```bash
git log origin/mi-rama..HEAD
```
(Ejemplo: `origin/doges..HEAD`)

### 🌫️ Ver las diferencias con GitHub:
```bash
git diff origin/mi-rama
```

---

## 🔄 Obtener los últimos cambios del equipo

### 🔃 ¿Qué es `git pull`?
`git pull` se usa para **descargar y actualizar** tu proyecto local con los cambios más recientes desde GitHub. 

### 🛠️ Antes de trabajar:
```bash
git checkout main
git pull origin main
```

### 🔀 Para actualizar otra rama con cambios de main:
```bash
git checkout mi-rama
git merge main
```

---

## 🧬 Fusionar ramas (ej. Doges → Buchos)

### Para traer cambios de `Doges` a `Buchos`:
```bash
git checkout Buchos
git merge Doges
```
Esto aplica todos los cambios de `Doges` en tu rama `Buchos`. Luego puedes revisar y hacer:
```bash
git push origin Buchos
```

### Para finalizar y fusionar en `main`:
```bash
git checkout main
git merge Buchos
git push origin main
```

> Recuerda siempre hacer `git pull` antes de trabajar para evitar conflictos.

---

## 🏁 Hacer un Pull Request desde GitHub
1. Ve al repositorio en GitHub.
2. Cambia a tu rama (ej. `doges`).
3. Haz clic en "Compare & pull request".
4. Revisa los cambios, escribe una descripción.
5. Envía el Pull Request para que el equipo lo revise y lo fusione con `main`.

---

## 📅 Buenas prácticas
- Trabaja en ramas, no en `main` directamente.
- Usa nombres de ramas descriptivos: `nombre-tarea`, `ana-base-datos`, `jorge-login-form`.
- Escribe mensajes de commit claros.
- Haz `pull` antes de trabajar para evitar conflictos.
- Revisa y comenta Pull Requests de otros.

---

✨ Si todos siguen esta guía, el trabajo en equipo con Git será ordenado, claro y sin sobresaltos.

☞ Puedes copiar esta guía en un archivo `README.md` dentro del repositorio para que todos la consulten.
