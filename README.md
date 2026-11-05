# Cuentas Claras

Aplicación web de finanzas personales y familiares, en español y en pesos colombianos (COP).
Centraliza el registro de gastos, avisa antes de que venza un pago fijo, simula el costo real
de un crédito, permite ahorrar en pareja o familia con metas compartidas y muestra un informe
mensual contra la regla 50/30/20.

Proyecto integrador para optar al título de **Tecnólogo en Sistemas de Información** —
Institución Universitaria Antonio José Camacho, 2026.

**Equipo:** Juan Esteban Andrade Bernal · Nikol Andrea Bustamante Vélez · Valeria Puentes Morales.

---

## Stack

- **Backend:** PHP 8.2+ · Laravel 11/12 (`bootstrap/app.php`, sin `Kernel.php`)
- **Vistas:** Blade + CSS propio
- **Base de datos:** SQLite | MySQL  
- **Tema:** claro / oscuro con persistencia en `localStorage` · tinte por rol
  (verde estándar · morado familiar · azul administrador)
- **Correos:** Blade con identidad de marca (verificación de cambio de correo + restablecer
  contraseña). En desarrollo `MAIL_MAILER=log`.

---

## Requisitos

- PHP ≥ 8.2 con extensiones `pdo_sqlite`, `pdo_mysql`,`mbstring`, `openssl`
- Composer 2
- Git

---

## Instalación (5 minutos)

```bash
# 1. Clonar
git clone <URL-del-repo> cuentas-claras
cd cuentas-claras

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Base de datos con datos de prueba
php artisan migrate:fresh --seed

# 5. Levantar el servidor
php artisan serve
