# 🎓 Sistema de Gestión de Matrículas Académicas

[![Laravel Version](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

## 🏢 Contexto Empresarial y Problemática

En las instituciones de educación superior de tamaño mediano, el proceso de matrícula es crítico para la operación académica: de este dependen la asignación de cupos, la carga docente y la información institucional oficial. 

### El Desafío
Anteriormente, el manejo de matrículas mediante procesos manuales, hojas de cálculo o sistemas aislados generaba:
1. **Errores de trazabilidad:** Duplicidad de datos y errores de digitación.
2. **Falta de control de cupos:** Sobrecupo no controlado en asignaturas por falta de validación en tiempo real.
3. **Poca transparencia:** Imposibilidad del estudiante de verificar el estado de su matrícula en tiempo real.
4. **Vulnerabilidad de datos:** Incumplimiento de la protección de datos personales (ej. Ley 1581 de 2012 en Colombia) debido a accesos no centralizados ni segmentados por rol.

### La Solución
Un prototipo web robusto desarrollado en **Laravel** bajo la arquitectura **Modelo-Vista-Controlador (MVC)**, que centraliza el proceso, garantiza la consistencia de los datos, aplica reglas de negocio estrictas (cupos, periodos lectivos) y restringe el acceso mediante roles y políticas de seguridad (Gates/Policies & Middlewares).

---

## 🚀 Requerimientos del Sistema

### 📋 Requerimientos Funcionales (RF)

| Código | Descripción | Perfil / Rol |
| :--- | :--- | :--- |
| **RF-01** | Autenticación de usuarios mediante credenciales seguras. | Admin, Estudiante, Funcionario |
| **RF-02 - RF-05** | Gestión CRUD completa de Programas, Asignaturas, Periodos Académicos y Grupos. | Administrador |
| **RF-06 - RF-09** | Consulta de Programas, Asignaturas, Periodos Lectivos y Oferta de Grupos. | Todos los roles |
| **RF-10 - RF-17** | Edición y eliminación lógica de entidades académicas base. | Administrador |
| **RF-18 - RF-19** | Inscripción y asignación manual de asignaturas y grupos a estudiantes. | Admin, Funcionario |
| **RF-20 - RF-21** | Autogestión de matrícula y selección de grupos según cupo disponible. | Estudiante |
| **RF-22 - RF-23** | Búsqueda avanzada y filtrado de matrículas por periodo/estudiante. | Admin, Funcionario |

### 🔒 Requerimientos No Funcionales (RNF)

* **RNF-01 (Seguridad):** Cifrado estricto de contraseñas utilizando algoritmos hash de grado industrial (`Bcrypt`/`Argon2`).
* **RNF-02 (Seguridad):** Protección contra ataques CSRF (Cross-Site Request Forgery) en todos los formularios.
* **RNF-03 & RNF-04 (Autorización):** Control de acceso granular por rutas mediante `Middlewares` y `Policies` integradas.
* **RNF-05 & RNF-06 (Usabilidad/UX):** Interfaz limpia, accesible y diseño adaptativo (*Responsive*) para cualquier dispositivo.
* **RNF-07 (Rendimiento):** Tiempo de respuesta del servidor en consultas estándar menor a 5 segundos.
* **RNF-08 (Arquitectura):** Separación estricta de responsabilidades bajo el patrón MVC.

---

## 🛠️ Stack Tecnológico

* **Backend:** PHP 8.2+ / Framework Laravel
* **Frontend:** Blade Templating / TailwindCSS o Bootstrap
* **Base de Datos:** MySQL
* **Gestor de Dependencias:** Composer (PHP) & NPM (JavaScript/Node)

---

## 🗄️ Modelo de Datos (Esquema Relacional)

La entidad central **`matriculas`** resuelve la relación de muchos a muchos entre estudiantes y grupos:

* **`users`**: `id`, `name`, `email`, `password`, `role`
* **`estudiantes`**: `id`, `user_id`, `codigo`, `documento`, `programa_id`
* **`programas`**: `id`, `nombre`, `nivel`
* **`asignaturas`**: `id`, `programa_id`, `codigo`, `nombre`, `creditos`
* **`periodos`**: `id`, `nombre`, `fecha_inicio`, `fecha_fin`, `abierto`
* **`grupos`**: `id`, `asignatura_id`, `periodo_id`, `docente`, `horario`, `cupo`
* **`matriculas`**: `id`, `estudiante_id`, `grupo_id`, `estado`, `fecha`

---

## ⚡ Guía de Instalación y Reconstrucción del Proyecto

### Perfiles y acceso de demostración

El sistema incluye los roles **Administrador**, **Funcionario** y **Estudiante**. El administrador puede crear, consultar, editar y eliminar usuarios desde su panel; cada perfil autenticado ve su propio panel. Los usuarios de demostración se crean únicamente en entornos `local` y `testing` al ejecutar `php artisan migrate --seed`:

| Rol | Correo | Contraseña |
| :--- | :--- | :--- |
| Administrador | `admin@matriculas.test` | `Matriculas2026!` |
| Funcionario | `funcionario@matriculas.test` | `Matriculas2026!` |
| Estudiante | `estudiante@matriculas.test` | `Matriculas2026!` |

Estas credenciales son solo para desarrollo local y no se crean en producción.

El seeder también carga un programa, dos asignaturas, el periodo `2026-2`, un perfil estudiantil y dos grupos con sus matrículas de demostración. En producción, `php artisan migrate` crea el esquema; los datos de demostración solo se cargan en entornos `local` y `testing`.

Cuando descargas o clonas este repositorio, notarás que faltan dos carpetas pesadas: `vendor/` y `node_modules/`. Esto es una práctica estándar en la ingeniería de software para mantener el repositorio liviano y seguro.

### ❓ ¿Por qué no están en el repositorio?

1. **`vendor/`**: Contiene todas las librerías backend de PHP gestionadas por Composer. Pesa considerablemente y no se sube a Git. Cada desarrollador la genera localmente ejecutando `composer install`.
2. **`node_modules/`**: Contiene los módulos y dependencias de JavaScript/Node.js requeridos para compilar el frontend. Al igual que `vendor/`, se regenera en la máquina local corriendo `npm install`.

---

### 🔧 Pasos para la Reinstalación Local

Sigue estos pasos en orden para poner en marcha el proyecto:

#### 1. Clonar el repositorio
```bash
git clone https://github.com/steven14821/sistema-matriculas.git
cd sistema-matriculas
```

#### 2. Instalar dependencias de PHP (Generar la carpeta `vendor/`)
```bash
composer install
```

#### 3. Instalar dependencias de Node.js (Generar la carpeta `node_modules/`)
```bash
npm install
```

#### 4. Configurar el archivo de Entorno (`.env`)
Duplica el archivo de ejemplo y configúralo:
```bash
cp .env.example .env
```
Edita `.env` con las credenciales de tu base de datos local:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_matriculas
DB_USERNAME=root
DB_PASSWORD=tu_clave
```

#### 5. Generar la clave de la aplicación
```bash
php artisan key:generate
```

#### 6. Ejecutar las Migraciones y Seeders (Base de datos)
```bash
php artisan migrate --seed
```

#### 7. Compilar los assets del Frontend
Para desarrollo:
```bash
npm run dev
```
O para producción:
```bash
npm run build
```

#### 8. Iniciar el servidor local
```bash
php artisan serve
```
Accede a la aplicación desde tu navegador en: `http://127.0.0.1:8000`

---

## 👥 Autores

* **Fergie Alejandra Carreño Gómez**
* **John Steven Sánchez Tolosa**

*Materia: Desarrollo de Aplicaciones Empresariales*