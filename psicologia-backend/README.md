# 🏥 Sistema de Gestión Psicológica Escolar - I.E. Mariano Melgar

Sistema web para la gestión integral de atención psicológica en instituciones educativas, desarrollado con Laravel 12 y MySQL.

---

## 👥 EQUIPO DE DESARROLLO

| Miembro | Rama | GitHub |
|---------|------|--------|
| **Josue** | `josue-dev` | @josue (Líder del proyecto) |
| **Camila** | `camila-dev` | @camila |
| **Valery** | `valery-dev` | @valery |
| **Jose** | `jose-dev` | @jose |

---

## 🚀 REQUISITOS PREVIOS

Antes de empezar, asegúrate de tener instalado:

### **Software necesario:**
- ✅ **PHP** >= 8.2 ([Descargar](https://www.php.net/downloads))
- ✅ **Composer** ([Descargar](https://getcomposer.org/download/))
- ✅ **MySQL** >= 8.0 o **XAMPP** ([Descargar](https://www.apachefriends.org/))
- ✅ **Git** ([Descargar](https://git-scm.com/downloads))
- ✅ **Node.js** >= 18 (para el frontend)

### **Verificar instalación:**
```bash
php -v          # Debe mostrar PHP 8.2 o superior
composer -v     # Debe mostrar Composer
mysql --version # Debe mostrar MySQL 8.0 o superior
git --version   # Debe mostrar Git
```

---

## 📦 INSTALACIÓN PARA CADA MIEMBRO DEL EQUIPO

### **PASO 1: Clonar el repositorio**
```bash
# Clonar el proyecto
git clone https://github.com/josue18K/sistema-psicologia-escolar.git

# Entrar a la carpeta
cd sistema-psicologia-escolar
```

---

### **PASO 2: Cambiar a tu rama personal**
```bash
# Para Josue
git checkout josue-dev

# Para Camila
git checkout camila-dev

# Para Valery
git checkout valery-dev

# Para Jose
git checkout jose-dev
```

---

### **PASO 3: Instalar dependencias de Laravel**
```bash
# Instalar dependencias PHP
composer install

# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

---

### **PASO 4: Configurar base de datos**

#### **Opción A: Usando MySQL en terminal**
```bash
mysql -u root -p
CREATE DATABASE psicologia_escolar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

#### **Opción B: Usando phpMyAdmin**

1. Ir a `http://localhost/phpmyadmin`
2. Click en "Nueva"
3. Nombre: `psicologia_escolar`
4. Cotejamiento: `utf8mb4_unicode_ci`
5. Click en "Crear"

---

### **PASO 5: Configurar .env**

Edita el archivo `.env` y configura tu conexión a MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=psicologia_escolar
DB_USERNAME=root
DB_PASSWORD=tu_password_mysql
```

---

### **PASO 6: Ejecutar migraciones y seeders**
```bash
# Ejecutar migraciones y poblar base de datos
php artisan migrate:fresh --seed

# Esto creará:
# - 7 tablas en la base de datos
# - Usuarios de prueba
# - Datos de ejemplo
```

---

### **PASO 7: Iniciar el servidor**
```bash
# Iniciar servidor de desarrollo
php artisan serve

# El backend estará disponible en:
# http://localhost:8000
```

---

## 🔑 CREDENCIALES DE ACCESO

Después de ejecutar los seeders, puedes usar estas cuentas:

| Rol | Email | Contraseña |
|-----|-------|-----------|
| **TOE (Admin)** | toe@colegio.com | password123 |
| **Psicólogo** | psicologo@colegio.com | password123 |
| **Psicólogo 2** | psicologo2@colegio.com | password123 |
| **Director** | director@colegio.com | password123 |
| **Tutor** | tutor1@colegio.com | password123 |

---

## 🌿 FLUJO DE TRABAJO GIT

### **REGLAS DEL EQUIPO:**

1. ✅ **NUNCA** trabajar directamente en `main`
2. ✅ Siempre trabajar en tu rama personal
3. ✅ Hacer commits frecuentes con mensajes claros
4. ✅ Sincronizar con `main` antes de empezar cada día
5. ✅ Crear Pull Request para integrar cambios

---

### **RUTINA DIARIA:**

#### **🌅 Al INICIAR el día:**
```bash
# 1. Asegurarte de estar en tu rama
git checkout tu-rama-dev

# 2. Traer los últimos cambios de main
git checkout main
git pull origin main

# 3. Volver a tu rama e integrar cambios
git checkout tu-rama-dev
git merge main

# 4. Si hay conflictos, resolverlos y hacer commit
git add .
git commit -m "merge: integrar cambios de main"
```

#### **💻 Durante el DESARROLLO:**
```bash
# Hacer commits frecuentes
git add .
git commit -m "feat: descripción del cambio"

# Ejemplo de mensajes:
# git commit -m "feat: agregar validación de DNI"
# git commit -m "fix: corregir error en login"
# git commit -m "style: mejorar diseño de tabla"
```

#### **🌙 Al FINALIZAR el día:**
```bash
# Subir tus cambios a GitHub
git push origin tu-rama-dev

# Luego crear Pull Request en GitHub
```

---

### **TIPOS DE COMMITS:**
```bash
feat:     # Nueva funcionalidad
fix:      # Corrección de bug
style:    # Cambios de diseño/formato
docs:     # Documentación
refactor: # Refactorización de código
test:     # Agregar tests
```

**Ejemplos:**
```bash
git commit -m "feat: agregar módulo de estudiantes"
git commit -m "fix: corregir validación de fechas"
git commit -m "style: mejorar responsive del dashboard"
git commit -m "docs: actualizar README con nuevas rutas"
```

---

### **CREAR PULL REQUEST:**

1. Ve a GitHub
2. Click en tu rama
3. Click en **"Compare & pull request"**
4. Escribe:
   - **Título:** Descripción breve del cambio
   - **Descripción:** Qué cambios incluye
5. Asigna a **Josue** como reviewer
6. Click en **"Create pull request"**
7. Esperar aprobación antes de hacer merge

---

## 📁 ESTRUCTURA DEL PROYECTO
```
sistema-psicologia-escolar/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/   # Controladores de la API
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── EstudianteController.php
│   │   │   ├── ApoderadoController.php
│   │   │   ├── DiagnosticoController.php
│   │   │   ├── CitaController.php
│   │   │   ├── ReporteController.php
│   │   │   └── NotificacionController.php
│   │   │
│   │   └── Requests/           # Validaciones
│   │       ├── StoreUserRequest.php
│   │       ├── UpdateUserRequest.php
│   │       └── ...
│   │
│   └── Models/                 # Modelos Eloquent
│       ├── User.php
│       ├── Estudiante.php
│       ├── Apoderado.php
│       ├── Diagnostico.php
│       ├── Cita.php
│       ├── Reporte.php
│       └── Notificacion.php
│
├── database/
│   ├── migrations/             # Migraciones de BD
│   ├── seeders/                # Datos de prueba
│   └── factories/              # Generadores de datos
│
├── routes/
│   └── api.php                 # Rutas de la API
│
├── .env.example                # Plantilla de configuración
├── .gitignore                  # Archivos ignorados por Git
├── composer.json               # Dependencias PHP
└── README.md                   # Esta documentación
```

---

## 🛣️ ENDPOINTS DE LA API

### **Autenticación**
- `POST /api/login` - Iniciar sesión
- `POST /api/logout` - Cerrar sesión
- `GET /api/me` - Usuario autenticado
- `POST /api/register` - Registrar usuario

### **Usuarios**
- `GET /api/users` - Listar usuarios
- `POST /api/users` - Crear usuario
- `GET /api/users/{id}` - Ver usuario
- `PUT /api/users/{id}` - Actualizar usuario
- `DELETE /api/users/{id}` - Eliminar usuario

### **Estudiantes**
- `GET /api/estudiantes` - Listar estudiantes
- `POST /api/estudiantes` - Crear estudiante
- `GET /api/estudiantes/{dni}` - Ver estudiante
- `PUT /api/estudiantes/{dni}` - Actualizar estudiante
- `DELETE /api/estudiantes/{dni}` - Eliminar estudiante

### **Diagnósticos**
- `GET /api/diagnosticos` - Listar diagnósticos
- `POST /api/diagnosticos` - Crear diagnóstico
- `GET /api/diagnosticos-estadisticas` - Estadísticas

### **Citas**
- `GET /api/citas` - Listar citas
- `POST /api/citas` - Crear cita
- `GET /api/citas-pendientes` - Citas pendientes

Ver documentación completa de endpoints en: [API.md](./API.md)

---

## 🧪 PROBAR LA API

### **Con cURL:**
```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"toe@colegio.com","password":"password123"}'

# Listar estudiantes
curl http://localhost:8000/api/estudiantes

# Crear estudiante
curl -X POST http://localhost:8000/api/estudiantes \
  -H "Content-Type: application/json" \
  -d '{
    "dni": "87654321",
    "nombres": "Ana",
    "apellidos": "Torres Silva",
    "fecha_nacimiento": "2010-03-15",
    "edad": 14,
    "grado": "3ro",
    "seccion": "B"
  }'
```

### **Con Postman/Thunder Client:**

1. Importar colección desde `postman_collection.json`
2. Configurar environment con `BASE_URL=http://localhost:8000`
3. Probar endpoints

---

## 🐛 SOLUCIÓN DE PROBLEMAS COMUNES

### **Error: "Class 'PDO' not found"**
```bash
# Habilitar extensión pdo_mysql en php.ini
# Descomentar: extension=pdo_mysql
```

### **Error: "Access denied for user 'root'@'localhost'"**
```bash
# Verificar usuario y contraseña en .env
# Asegurarse que MySQL esté corriendo
```

### **Error: "vendor/autoload.php not found"**
```bash
composer install
```

### **Error al ejecutar migraciones**
```bash
# Limpiar y volver a migrar
php artisan migrate:fresh --seed
```

### **Puerto 8000 en uso**
```bash
# Usar otro puerto
php artisan serve --port=8001
```

---

## 📖 RECURSOS Y DOCUMENTACIÓN

- **Laravel 12:** https://laravel.com/docs/12.x
- **Laravel Eloquent:** https://laravel.com/docs/12.x/eloquent
- **Laravel Validation:** https://laravel.com/docs/12.x/validation
- **MySQL:** https://dev.mysql.com/doc/
- **Git:** https://git-scm.com/doc

---

## 🤝 COMUNICACIÓN DEL EQUIPO

- **Reuniones:** Coordinar en grupo de WhatsApp/Telegram
- **Dudas técnicas:** Preguntar en el grupo o a Josue
- **Reportar bugs:** Crear un issue en GitHub
- **Sugerencias:** Discutir en reunión de equipo

---

## ⚠️ NOTAS IMPORTANTES

### **Autenticación con Tokens:**
El sistema actualmente funciona **SIN autenticación de tokens** para facilitar el desarrollo.

Cuando el docente lo indique, se activará el sistema de autenticación completo.

Todos los lugares que deben modificarse están marcados con comentarios `TODO:` en el código.

### **NO SUBIR archivos sensibles:**
- ❌ `.env` (contiene contraseñas)
- ❌ `/vendor` (dependencias de Composer)
- ❌ `/node_modules` (dependencias de Node)

Estos ya están en `.gitignore`

---

## 📝 COMANDOS ÚTILES
```bash
# Ver rutas de la API
php artisan route:list

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Crear nueva migración
php artisan make:migration nombre_migracion

# Crear nuevo modelo
php artisan make:model NombreModelo

# Crear nuevo controlador
php artisan make:controller NombreController

# Crear nuevo request
php artisan make:request NombreRequest

# Ver logs
tail -f storage/logs/laravel.log
```

---

## ✅ CHECKLIST DE VERIFICACIÓN

Antes de hacer push, verifica:

- [ ] El código funciona sin errores
- [ ] No hay archivos sensibles (.env, contraseñas)
- [ ] Los commits tienen mensajes claros
- [ ] El código está comentado si es complejo
- [ ] Has probado tus cambios localmente

---

## 🎉 ¡LISTO PARA DESARROLLAR!

Si tienes dudas o problemas, contacta a:
- **Josue** (Líder del proyecto)
- Grupo de WhatsApp/Telegram del equipo

**¡Buena suerte y a programar! 💪**
