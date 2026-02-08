# Guía de Despliegue Manual - Laravel API en cPanel (Hosting Compartido)

> **Proyecto**: Hipertensión API  
> **Framework**: Laravel 10.50.0 / PHP 8.3  
> **Hosting**: Bluehosting (cPanel, sin acceso SSH)  
> **Dominio**: `api-htapp.terapiatarot.com`  
> **Fecha**: Febrero 2026

---

## Índice

1. [Requisitos Previos](#1-requisitos-previos)
2. [Preparación del Proyecto Local](#2-preparación-del-proyecto-local)
3. [Subida de Archivos al Servidor](#3-subida-de-archivos-al-servidor)
4. [Configuración del Servidor](#4-configuración-del-servidor)
5. [Configuración del .env de Producción](#5-configuración-del-env-de-producción)
6. [Solución al Problema del Document Root](#6-solución-al-problema-del-document-root)
7. [Ejecución de Migraciones sin SSH](#7-ejecución-de-migraciones-sin-ssh)
8. [Errores Encontrados y Soluciones](#8-errores-encontrados-y-soluciones)
9. [Lista de Verificación Post-Despliegue](#9-lista-de-verificación-post-despliegue)
10. [Recomendaciones para Futuras Actualizaciones](#10-recomendaciones-para-futuras-actualizaciones)

---

## 1. Requisitos Previos

### En el servidor (cPanel)
- PHP 8.1 o superior (verificar en cPanel → Seleccionar versión de PHP)
- Extensiones PHP habilitadas: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `json`, `bcmath`, `fileinfo`, `ctype`, `curl`
- Base de datos MySQL creada
- Usuario MySQL creado y asignado a la base de datos con **todos los privilegios**

### En local
- Proyecto Laravel funcionando correctamente
- Dependencias instaladas (`vendor/` con `composer install`)
- `APP_KEY` generado (`php artisan key:generate`)
- `JWT_SECRET` generado (`php artisan jwt:secret`)

### Verificar versión de PHP en servidor
Crear un archivo `test.php` temporal:
```php
<?php phpinfo();
```
Subir a la raíz del dominio y acceder desde el navegador. **Eliminarlo después.**

---

## 2. Preparación del Proyecto Local

### 2.1 Crear archivo `.env` de producción

```env
APP_NAME="Hipertensión API"
APP_ENV=production
APP_KEY=base64:TU_APP_KEY_AQUI
APP_DEBUG=false
APP_URL=https://tu-dominio.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario_mysql
DB_PASSWORD=contraseña_mysql

JWT_SECRET=tu_jwt_secret_aqui
JWT_TTL=1440
JWT_REFRESH_TTL=20160

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

> **IMPORTANTE**: En hosting compartido, `DB_HOST` suele ser `127.0.0.1` o `localhost`.

### 2.2 Archivos y carpetas a subir

Subir **todo el proyecto** excepto:
- `.git/`
- `node_modules/`
- `.env` local (se crea uno nuevo en el servidor)
- `storage/logs/*.log`

> **SÍ subir**: `vendor/` completo (no hay acceso a Composer en servidor sin SSH).

---

## 3. Subida de Archivos al Servidor

### Vía cPanel File Manager

1. **Comprimir** el proyecto localmente en `.zip`
2. En cPanel → **Administrador de Archivos**, navegar al directorio del dominio
3. **Cargar** el `.zip`
4. **Extraer** el archivo en su ubicación
5. Verificar la estructura:

```
/home/usuario/public_html/subdirectorio/dominio/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── index.php        ← Front controller de Laravel
│   └── .htaccess        ← Rewrite rules de Laravel
├── routes/
├── storage/
├── vendor/
├── .env                 ← Configuración de producción
├── artisan
├── composer.json
├── index.php            ← Proxy (ver sección 6)
└── .htaccess            ← Rewrite a index.php raíz (ver sección 6)
```

### Permisos necesarios
En cPanel File Manager, verificar permisos:

| Carpeta | Permiso |
|---------|---------|
| `storage/` | 0777 |
| `storage/logs/` | 0777 |
| `storage/framework/` | 0777 |
| `storage/framework/cache/` | 0777 |
| `storage/framework/sessions/` | 0777 |
| `storage/framework/views/` | 0777 |
| `bootstrap/cache/` | 0777 |

---

## 4. Configuración del Servidor

### 4.1 Crear Base de Datos en cPanel

1. cPanel → **Bases de datos MySQL**
2. Crear base de datos: `usuario_nombredb`
3. Crear usuario MySQL con contraseña segura
4. **Asignar usuario a la base de datos** con TODOS los privilegios
5. Anotar los datos exactos (nombre BD, usuario, contraseña)

> **IMPORTANTE**: En cPanel, los nombres de base de datos y usuarios llevan prefijo automático (ej: `terapiat_hipertension_db`). Usar el nombre completo con prefijo en el `.env`.

### 4.2 Verificar subdirectorios de storage

Asegurarse de que existan estas carpetas (crearlas si no):
```
storage/framework/cache/data/
storage/framework/sessions/
storage/framework/views/
storage/logs/
```

---

## 5. Configuración del .env de Producción

En cPanel File Manager, crear/editar el archivo `.env` en la raíz del proyecto.

### Campos críticos que no deben faltar:

```env
APP_KEY=base64:...          # Generar con php artisan key:generate
JWT_SECRET=...              # Generar con php artisan jwt:secret
JWT_TTL=1440                # Tiempo de vida del token en minutos
JWT_REFRESH_TTL=20160       # Tiempo de refresh en minutos
DB_DATABASE=prefijo_nombre  # Con prefijo de cPanel
DB_USERNAME=prefijo_usuario # Con prefijo de cPanel
DB_PASSWORD=contraseña      # Contraseña exacta
```

> **Error común**: Olvidar `JWT_SECRET`, `JWT_TTL` y `JWT_REFRESH_TTL` causa errores 500 en endpoints de autenticación.

---

## 6. Solución al Problema del Document Root

### El problema

En hosting compartido, el dominio apunta a la **raíz del proyecto**, no a la carpeta `public/`. Laravel espera que el Document Root sea `public/`.

```
Dominio apunta aquí → /home/usuario/public_html/subdir/dominio/
Laravel espera aquí → /home/usuario/public_html/subdir/dominio/public/
```

### La solución: index.php proxy + .htaccess en la raíz

#### Archivo `index.php` en la raíz del proyecto (NO en public/)

```php
<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';
$_SERVER['SCRIPT_NAME'] = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'] ?? '');

try {
    require __DIR__ . '/vendor/autoload.php';

    $app = require_once __DIR__ . '/bootstrap/app.php';

    // Override the public path
    $app->bind('path.public', function () {
        return __DIR__ . '/public';
    });

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
```

> **Nota**: Cambiar `ini_set('display_errors', '1')` a `'0'` después de confirmar que funciona.

#### Archivo `.htaccess` en la raíz del proyecto

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Explicación

1. El `.htaccess` de la raíz envía todas las peticiones al `index.php` de la raíz
2. El `index.php` de la raíz carga Laravel directamente y hace `bind('path.public')` para que Laravel sepa dónde está `public/`
3. El `.htaccess` dentro de `public/` (el original de Laravel) queda intacto

### Lo que NO funcionó

| Intento | Resultado |
|---------|-----------|
| `.htaccess` con `RewriteRule ^(.*)$ public/$1 [L]` | Pantalla en blanco |
| `index.php` con solo `require_once __DIR__.'/public/index.php'` | Pantalla en blanco |
| Mover `index.php` de `public/` a la raíz | Problemas con rutas de assets |

---

## 7. Ejecución de Migraciones sin SSH

Sin acceso SSH, se necesita un script PHP temporal para ejecutar comandos Artisan desde el navegador.

### Script `setup-temp.php` (colocar en `public/`)

```php
<?php
$SECRET_KEY = 'clave_secreta_compleja';

if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
    http_response_code(403);
    die(json_encode(['error' => 'Acceso denegado']));
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

header('Content-Type: application/json; charset=utf-8');
$action = $_GET['action'] ?? 'status';
$results = [];

try {
    switch ($action) {
        case 'status':
            $results = [
                'php_version'  => PHP_VERSION,
                'laravel'      => app()->version(),
                'environment'  => app()->environment(),
                'debug'        => config('app.debug'),
                'db_connected' => false,
                'db_name'      => config('database.connections.mysql.database'),
            ];
            try {
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                $results['db_connected'] = true;
                $results['db_tables'] = count(
                    \Illuminate\Support\Facades\DB::select('SHOW TABLES')
                );
            } catch (\Exception $e) {
                $results['db_error'] = $e->getMessage();
            }
            break;

        case 'migrate':
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $results = [
                'message' => 'Migraciones ejecutadas',
                'output'  => \Illuminate\Support\Facades\Artisan::output(),
            ];
            break;

        case 'seed':
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $results = [
                'message' => 'Seeders ejecutados',
                'output'  => \Illuminate\Support\Facades\Artisan::output(),
            ];
            break;

        case 'clear-cache':
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            $results = ['message' => 'Caché limpiado correctamente'];
            break;

        case 'optimize':
            \Illuminate\Support\Facades\Artisan::call('config:cache');
            \Illuminate\Support\Facades\Artisan::call('route:cache');
            $results = [
                'message' => 'Optimización completada',
                'output'  => \Illuminate\Support\Facades\Artisan::output(),
            ];
            break;

        case 'key-generate':
            \Illuminate\Support\Facades\Artisan::call('key:generate', ['--force' => true]);
            $results = [
                'message' => 'APP_KEY generado',
                'output'  => \Illuminate\Support\Facades\Artisan::output(),
            ];
            break;

        default:
            $results = ['error' => 'Acción no reconocida'];
    }

    echo json_encode(['success' => true, 'data' => $results], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
        'file'    => $e->getFile() . ':' . $e->getLine(),
    ], JSON_PRETTY_PRINT);
}
```

### Orden de ejecución

```
1. /setup-temp.php?key=CLAVE&action=status         → Verificar conexión
2. /setup-temp.php?key=CLAVE&action=migrate         → Crear tablas
3. /setup-temp.php?key=CLAVE&action=seed            → Datos iniciales
4. /setup-temp.php?key=CLAVE&action=optimize        → Cachear config y rutas
```

> **⚠️ SEGURIDAD**: Eliminar `setup-temp.php` INMEDIATAMENTE después de usarlo.

---

## 8. Errores Encontrados y Soluciones

### Error 1: Pantalla en blanco (500 silencioso)

**Causa**: Laravel arranca pero no puede escribir en `storage/` o `bootstrap/cache/`.

**Diagnóstico**: Crear un archivo `diag.php` en `public/`:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "<pre>";

$dirs = [
    __DIR__ . '/../storage',
    __DIR__ . '/../storage/logs',
    __DIR__ . '/../storage/framework',
    __DIR__ . '/../storage/framework/cache',
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../bootstrap/cache',
];

foreach ($dirs as $dir) {
    $exists = file_exists($dir) ? 'EXISTS' : 'MISSING';
    $writable = is_writable($dir) ? 'WRITABLE' : 'NOT WRITABLE';
    echo basename($dir) . " -> $exists | $writable\n";
}

// Test Laravel
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        Illuminate\Http\Request::create('/api/disclaimer', 'GET')
    );
    echo "\nLaravel Status: " . $response->getStatusCode();
    echo "\nBody: " . $response->getContent();
} catch (Throwable $e) {
    echo "\nERROR: " . $e->getMessage();
}
```

**Solución**: Cambiar permisos de `storage/` y `bootstrap/cache/` a 0777.

---

### Error 2: Access denied for user (DB)

```
SQLSTATE[28000] [1045] Access denied for user 'usuario'@'localhost' (using password: YES)
```

**Causa**: Credenciales de MySQL incorrectas o usuario no asignado a la base de datos.

**Solución**:
1. Verificar en cPanel → Bases de datos MySQL que el usuario está **asignado** a la BD
2. Cambiar contraseña del usuario si no se recuerda
3. Actualizar `.env` con los datos correctos
4. Recordar que cPanel agrega prefijo: `cpanel_usuario` no `usuario`

---

### Error 3: Ruta raíz `/` devuelve 500

**Causa**: El middleware `web` de Laravel (sesiones, CSRF, cookies) falla cuando se carga desde un `index.php` fuera de `public/`.

**Solución**: Definir la ruta raíz SIN middleware `web`, directamente en `RouteServiceProvider.php`:

```php
$this->routes(function () {
    Route::middleware('api')
        ->prefix('api')
        ->group(base_path('routes/api.php'));

    Route::middleware('web')
        ->group(base_path('routes/web.php'));

    // Ruta raíz sin middleware web
    Route::get('/', function () {
        return response()->json([
            'app'     => 'Hipertensión API',
            'version' => app()->version(),
            'status'  => 'running',
        ]);
    });
});
```

---

### Error 4: 405 Method Not Allowed

**Causa**: Acceder a una ruta POST/PUT/DELETE desde el navegador (que usa GET).

**Solución**: No es un error. Usar herramientas como Thunder Client, Postman o curl para probar rutas que no son GET.

---

### Error 5: JWT_SECRET faltante

**Causa**: Se copió el `.env` sin incluir las variables de JWT.

**Solución**: Agregar al `.env`:
```env
JWT_SECRET=tu_clave_de_64_caracteres
JWT_TTL=1440
JWT_REFRESH_TTL=20160
```

---

### Error 6: .env con encoding incorrecto (BOM)

**Causa**: Al crear/editar `.env` con PowerShell `Set-Content`, se guarda con BOM UTF-8 que Laravel no puede leer.

**Solución en PowerShell**:
```powershell
$content = Get-Content .env -Raw
[System.IO.File]::WriteAllText("$PWD\.env", $content, (New-Object System.Text.UTF8Encoding $false))
```

---

### Error 7: Foreign key referencing tabla incorrecta

**Causa**: En migración de `food_logs`, `->constrained()` sin argumento busca tabla `food` en lugar de `foods`.

**Solución**: Especificar tabla explícitamente:
```php
$table->foreignId('food_id')->constrained('foods')->onDelete('cascade');
```

---

### Error 8: Modelo Food con nombre de tabla incorrecto

**Causa**: Laravel pluraliza `Food` como `food`, no `foods`.

**Solución**: Agregar en el modelo `Food.php`:
```php
protected $table = 'foods';
```

---

## 9. Lista de Verificación Post-Despliegue

- [ ] `APP_DEBUG=false` en `.env`
- [ ] `APP_ENV=production` en `.env`
- [ ] Eliminar `setup-temp.php` del servidor
- [ ] Eliminar `diag.php` del servidor
- [ ] Eliminar `test.php` del servidor
- [ ] Verificar que `https://dominio.com/` responde con JSON
- [ ] Verificar que `https://dominio.com/api/disclaimer` funciona
- [ ] Probar registro de usuario con Thunder Client
- [ ] Probar login y obtener JWT
- [ ] Probar endpoint protegido con token
- [ ] Verificar que archivos sensibles NO son accesibles:
  - `https://dominio.com/.env` → debe dar 403/404
  - `https://dominio.com/artisan` → debe dar 403/404

---

## 10. Recomendaciones para Futuras Actualizaciones

### Actualizar archivos

1. En local, hacer los cambios necesarios
2. En cPanel File Manager, navegar a la carpeta correspondiente
3. **Cargar** el archivo nuevo (marcar "Sobrescribir archivos existentes")
4. Limpiar caché: volver a subir `setup-temp.php` temporalmente y ejecutar `?action=clear-cache`

### Nuevas migraciones

1. Crear la migración en local: `php artisan make:migration nombre`
2. Subir el archivo de migración a `database/migrations/`
3. Subir `setup-temp.php` a `public/`
4. Ejecutar: `?key=CLAVE&action=migrate`
5. **Eliminar** `setup-temp.php`

### Archivos que SIEMPRE deben actualizarse juntos

| Si cambias... | También sube... |
|---------------|-----------------|
| Rutas (`routes/api.php`) | Limpiar caché de rutas |
| Configuración (`config/*.php`) | Limpiar caché de config |
| Modelos (`app/Models/`) | Solo el archivo modificado |
| Controladores (`app/Http/Controllers/`) | Solo el archivo modificado |
| Migraciones | Ejecutar migrate |
| `.env` | Limpiar caché de config |

### Estructura final en servidor

```
/home/terapiat/public_html/htapp/api-htapp.terapiatarot.com/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── index.php          ← Original de Laravel (NO modificar)
│   └── .htaccess          ← Original de Laravel (NO modificar)
├── routes/
├── storage/               ← Permisos 0777
├── vendor/
├── .env                   ← Configuración de producción
├── .htaccess              ← Redirige todo al index.php raíz
├── index.php              ← Proxy que carga Laravel y setea path.public
├── artisan
└── composer.json
```

---

## Resumen del Flujo de Despliegue

```
1. Preparar proyecto local (APP_KEY, JWT_SECRET, vendor/)
2. Comprimir y subir a cPanel
3. Crear BD + usuario en cPanel MySQL
4. Crear .env en servidor con credenciales correctas
5. Crear index.php y .htaccess en raíz (problema Document Root)
6. Subir setup-temp.php a public/
7. Ejecutar: status → migrate → seed → optimize
8. Eliminar archivos temporales
9. Cambiar APP_DEBUG=false
10. Probar endpoints con Thunder Client
```
