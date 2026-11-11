# Cuentas Claras

Aplicación web de finanzas personales y familiares, desarrollada en español y con valores expresados en pesos colombianos (COP).

Cuentas Claras permite centralizar el registro y seguimiento de gastos, gestionar pagos fijos, simular créditos, establecer metas de ahorro compartidas y consultar informes financieros mensuales basados en la regla 50/30/20.

Proyecto integrador para optar al título de **Tecnólogo en Sistemas de Información** — Institución Universitaria Antonio José Camacho, 2026.

**Equipo de desarrollo:**
- Juan Esteban Andrade Bernal
- Nikol Andrea Bustamante Vélez
- Valeria Puentes Morales

---

## 1. Funcionalidades principales

- **Gestión de gastos:** registro, edición, eliminación y clasificación de gastos por categorías.
- **Gastos fijos:** administración de pagos recurrentes y notificaciones para recordar sus fechas de vencimiento.
- **Simulador de créditos:** cálculo del costo de un crédito y sus cuotas.
- **Metas de ahorro compartidas:** creación y seguimiento de objetivos de ahorro entre dos personas.
- **Informes financieros:** seguimiento mensual de la distribución del dinero mediante la regla 50/30/20.
- **Gastos hormiga:** identificación de consumos recurrentes y análisis de su impacto en los gastos mensuales.
- **Historial de informes:** consulta y conservación de informes de meses anteriores.
- **Exportación de informes:** descarga de informes en formatos PDF, Word y CSV.
- **Notificaciones:** avisos relacionados con pagos fijos e informes mensuales disponibles.
- **Personalización:** interfaz con modo claro y oscuro.
- **Roles de usuario:** estándar, familiar y administrador.

## 2. Tecnologías utilizadas

| Área | Tecnología |
|---|---|
| Backend | PHP 8.2 o superior |
| Framework | Laravel 11/12 |
| Frontend | Blade, HTML, CSS y JavaScript |
| Estilos | CSS personalizado |
| Gráficas | Chart.js |
| Compilación de recursos | Vite |
| Base de datos | MySQL o SQLite |
| Dependencias PHP | Composer |
| Dependencias JavaScript | Node.js y npm |
| Control de versiones | Git y GitHub |

## 3. Requisitos previos

Antes de instalar el proyecto, es necesario tener:

- PHP 8.2 o superior.
- Composer 2.
- Node.js y npm.
- Git.
- MySQL, si se desea utilizar esta base de datos; también se puede utilizar SQLite.

Extensiones de PHP necesarias:

- `pdo_sqlite`
- `pdo_mysql`
- `mbstring`
- `openssl`

## 4. Instalación y configuración

### Paso 1. Clonar el repositorio

```bash
git clone https://github.com/jeandrade-code/cuentas-claras.git
cd cuentas-claras
```

### Paso 2. Instalar las dependencias de PHP

```bash
composer install
```

### Paso 3. Configurar el entorno

Crear el archivo de configuración a partir del ejemplo:

```bash
cp .env.example .env
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

### Paso 4. Configurar la base de datos

Cuentas Claras permite utilizar SQLite o MySQL.

**Opción A. SQLite**

Configurar en el archivo `.env`:

```env
DB_CONNECTION=sqlite
```

Verificar que exista el archivo:

```text
database/database.sqlite
```

Si no existe, crearlo antes de ejecutar las migraciones.

**Opción B. MySQL**

Crear una base de datos en MySQL y configurar las siguientes variables en el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cuentas_claras
DB_USERNAME=root
DB_PASSWORD=
```

Reemplazar los datos de conexión según la configuración del equipo.

### Paso 5. Ejecutar las migraciones y los datos iniciales

```bash
php artisan migrate:fresh --seed
```

Este comando crea las tablas y carga los datos iniciales definidos por el proyecto.

**Importante:** `migrate:fresh` elimina las tablas existentes antes de crearlas nuevamente. Se recomienda utilizarlo únicamente en instalaciones nuevas o entornos de prueba.

### Paso 6. Instalar las dependencias de JavaScript

Instalar las dependencias del proyecto:

```bash
npm install
```

Instalar Chart.js, utilizado para generar las gráficas de los informes financieros:

```bash
npm install chart.js
```

### Paso 7. Compilar los recursos del frontend

```bash
npm run build
```

Este comando utiliza Vite para compilar los archivos CSS y JavaScript necesarios para el funcionamiento de la interfaz.

### Paso 8. Iniciar el servidor

```bash
php artisan serve
```

Abrir en el navegador:

```text
http://127.0.0.1:8000
```

## 5. Modo de desarrollo

Si se van a realizar modificaciones en los archivos CSS o JavaScript, se puede utilizar Vite en modo desarrollo:

```bash
npm run dev
```

Este comando mantiene activo el proceso de compilación mientras se trabaja en el proyecto.

## 6. Pruebas del proyecto

Para ejecutar las pruebas automatizadas:

```bash
php artisan test
```

Este comando permite verificar el funcionamiento de las diferentes funcionalidades y componentes del sistema.

## 7. Tareas programadas

El proyecto cuenta con tareas programadas para gestionar recordatorios de gastos fijos y la disponibilidad de informes mensuales.

En un entorno de desarrollo, se puede mantener activo el programador de Laravel mediante:

```bash
php artisan schedule:work
```

Para que las tareas se ejecuten automáticamente en un servidor de producción, es necesario configurar el programador de Laravel en el entorno de despliegue.

## 8. Estructura general del proyecto

```text
cuentas-claras/
├── app/
│   ├── Console/
│   ├── Http/
│   ├── Models/
│   └── Services/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── docs/
├── public/
│   ├── css/
│   └── js/
├── resources/
│   └── views/
├── routes/
├── tests/
├── .env.example
├── composer.json
├── package.json
└── README.md
```

## 9. Consideraciones

- No compartir el archivo `.env`, ya que contiene información de configuración y credenciales.
- Mantener instaladas las dependencias de Composer y npm para ejecutar correctamente el proyecto.
- Ejecutar `npm run build` después de instalar las dependencias para generar los recursos del frontend.
- Utilizar una base de datos configurada antes de ejecutar las migraciones.
- Los correos electrónicos pueden configurarse para desarrollo utilizando `MAIL_MAILER=log`.

---

**Cuentas Claras — Proyecto Integrador 2026**

Institución Universitaria Antonio José Camacho.

Desarrollado por Juan Esteban Andrade Bernal, Nikol Andrea Bustamante Vélez y Valeria Puentes Morales.
