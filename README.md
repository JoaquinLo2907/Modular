🚀 Guía de trabajo en equipo con Git y GitHub
📁 Paso 1: Clonar el repositorio (solo una vez)

git clone https://github.com/usuario/repositorio.git
cd repositorio

Esto descarga el proyecto completo y entra a la carpeta del repositorio.
🔀 Paso 2: Crear tu propia rama

git checkout -b nombre-de-tu-rama

Ejemplo:

git checkout -b doges

Esto crea y cambia a una nueva rama donde trabajarás sin afectar main.
📝 Paso 3: Hacer cambios y guardarlos

git add .
git commit -m "Descripción de los cambios"

Ejemplo:

git commit -m "Agregué formulario de registro"

🚀 Paso 4: Subir tus cambios
✅ A tu propia rama (por ejemplo, doges):

git push -u origin doges

✅ A la rama principal (main) solo si está permitido:

git checkout main
git pull origin main # Actualiza la versión local
# (Haz tus cambios)
git add .
git commit -m "Cambios en main"
git push origin main

    Recomendado: usar ramas y no modificar directamente main.

🌐 Paso 5: Subir tu rama a GitHub (si no lo habías hecho)

git push -u origin nombre-de-tu-rama

🔍 Verificación de estado y cambios pendientes
🔍 Ver si tienes cambios locales:

git status

🔢 Ver los commits que no se han hecho push:

git log origin/mi-rama..HEAD

(Ejemplo: origin/doges..HEAD)
🌫️ Ver las diferencias con GitHub:

git diff origin/mi-rama

🔄 Obtener los últimos cambios del equipo
🔃 ¿Qué es git pull?

git pull se usa para descargar y actualizar tu proyecto local con los cambios más recientes desde GitHub.
🛠️ Antes de trabajar:

git checkout main
git pull origin main

🔀 Para actualizar otra rama con cambios de main:

git checkout mi-rama
git merge main

🧬 Fusionar ramas (ej. Doges → Buchos)
Para traer cambios de Doges a Buchos:

git checkout Buchos
git merge Doges

Esto aplica todos los cambios de Doges en tu rama Buchos. Luego puedes revisar y hacer:

git push origin Buchos

Para finalizar y fusionar en main:

git checkout main
git merge Buchos
git push origin main

    Recuerda siempre hacer git pull antes de trabajar para evitar conflictos.

🛰️ Revisión de cambios remotos antes de hacer pull
📥 Traer información remota sin aplicarla:

git fetch origin

🔍 Ver qué hay en GitHub que tú aún no tienes:

git log HEAD..origin/mi-rama

Ejemplo:

git log HEAD..origin/Buchos

📤 Ver qué tienes tú y aún no se ha subido a GitHub:

git log origin/mi-rama..HEAD

Ejemplo:

git log origin/Buchos..HEAD

🧠 Resumen de comparación:
¿Qué quieres ver? 	Comando
Traer las novedades sin aplicarlas 	git fetch origin
Ver qué hay nuevo en GitHub 	git log HEAD..origin/mi-rama
Ver qué falta subir a GitHub 	git log origin/mi-rama..HEAD
Aplicar cambios nuevos desde GitHub 	git pull origin mi-rama
🏁 Hacer un Pull Request desde GitHub

    Ve al repositorio en GitHub.
    Cambia a tu rama (ej. doges).
    Haz clic en "Compare & pull request".
    Revisa los cambios, escribe una descripción.
    Envía el Pull Request para que el equipo lo revise y lo fusione con main.

📅 Buenas prácticas

    Trabaja en ramas, no en main directamente.
    Usa nombres de ramas descriptivos: nombre-tarea, ana-base-datos, jorge-login-form.
    Escribe mensajes de commit claros.
    Haz pull antes de trabajar para evitar conflictos.
    Revisa y comenta Pull Requests de otros.
