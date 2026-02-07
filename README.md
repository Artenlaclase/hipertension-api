# Hipertensión API

API REST para la aplicación móvil de nutrición y control de hipertensión arterial.

> **Stack:** Laravel 10 · JWT · MySQL · cPanel  
> **Cliente:** Flutter  
> **Endpoints:** 58 rutas (3 públicas + 55 protegidas)

---

## Requisitos

- PHP ≥ 8.1
- Composer
- MySQL 5.7+
- Extensiones PHP: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`

## Instalación

```bash
# 1. Clonar el repositorio
git clone <url-del-repo> hipertension-api
cd hipertension-api

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
# DB_DATABASE=hipertension
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_clave

# 5. Generar clave JWT
php artisan jwt:secret

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Iniciar servidor de desarrollo
php artisan serve
```

La API estará disponible en `http://localhost:8000/api`.

## Autenticación

Se utiliza **JWT** (JSON Web Token) mediante el paquete `tymon/jwt-auth`.

```
POST /api/register   → Registro (retorna token)
POST /api/login      → Login (retorna token + onboarding_completed)
POST /api/refresh    → Renovar token
POST /api/logout     → Cerrar sesión
```

Incluir el token en cada petición protegida:

```
Authorization: Bearer <token>
```

## Módulos funcionales

### 1. Onboarding (RF-01)
Captura datos clínicos iniciales: PA, peso, altura, nivel de actividad, restricciones alimentarias.

```
POST /api/onboarding
```

### 2. Presión arterial (RF-02 / RF-03)
Registro de mediciones con **semáforo de clasificación automático**:

| Color | Nivel | Sistólica | Diastólica |
|-------|-------|-----------|------------|
| 🟢 Verde | Controlada | < 120 | < 80 |
| 🟡 Amarillo | Elevada | 120–139 | 80–89 |
| 🔴 Rojo | Alta | ≥ 140 | ≥ 90 |

```
GET|POST|DELETE  /api/blood-pressure
GET              /api/blood-pressure-stats?period=weekly
```

### 3. Plan alimenticio (RF-04)
CRUD completo de planes semanales personalizados.

```
GET|POST|PUT|DELETE  /api/meal-plans
```

### 4. Recomendaciones nutricionales DASH (RF-05)
Recomendaciones dinámicas basadas en la última medición de PA: sustituciones, alimentos sugeridos y a evitar.

```
GET /api/nutritional-recommendations
```

### 5. Alimentos y registro de consumo (RF-06)
Catálogo de alimentos con datos nutricionales + registro diario.

```
GET|POST       /api/foods
GET|POST|DEL   /api/food-logs
```

### 6. Contenido educativo (RF-07)
Artículos educativos con orden progresivo.

```
GET /api/educational-contents
GET /api/educational-contents/{id}
```

### 7. Hábitos saludables y rachas (RF-08)
Seguimiento de hábitos con cálculo de rachas consecutivas y mensajes de refuerzo positivo.

```
GET|POST|DEL   /api/habit-logs
GET            /api/habit-streaks
GET            /api/habit-streaks/{habit}
```

### 8. Medicamentos y adherencia (RF-09)
Registro de medicamentos, alarmas (notificación local en Flutter), logs de toma y estadísticas de adherencia.

```
GET|POST|PUT|DEL  /api/medications
POST              /api/medications/{id}/alarms
GET|POST          /api/medications/{id}/logs
GET               /api/medication-adherence?period=monthly
```

### 9. Dashboard consolidado (RF-10)
Vista resumen + historial unificado filtrable por fechas.

```
GET /api/dashboard
GET /api/history?from=2026-01-01&to=2026-02-07
```

### 10. Hidratación e infusiones
Catálogo de 16 infusiones clasificadas por seguridad para HTA + seguimiento de ingesta diaria con meta de 2L.

```
GET|POST       /api/infusions
GET            /api/infusions/{id}
GET|POST|DEL   /api/hydration-logs
GET            /api/hydration-summary?date=2026-02-07
```

**Semáforo de infusiones:** las marcadas como `avoid` se bloquean al registrar (422), las de `caution` devuelven advertencia.

## Base de datos

**14 tablas** gestionadas por migraciones de Eloquent:

| Tabla | Descripción |
|-------|-------------|
| `users` | Usuarios con perfil clínico |
| `blood_pressure_records` | Mediciones de PA |
| `foods` | Catálogo de alimentos |
| `food_logs` | Registro de consumo |
| `meal_plans` | Planes alimenticios semanales |
| `medications` | Medicamentos del usuario |
| `medication_alarms` | Horarios de toma |
| `medication_logs` | Registro de tomas |
| `educational_contents` | Artículos educativos |
| `habits` | Catálogo de hábitos saludables |
| `habit_logs` | Seguimiento diario de hábitos |
| `infusions` | Catálogo de infusiones para HTA |
| `hydration_logs` | Registro de ingesta de líquidos |
| `password_reset_tokens` | Tokens de reset de contraseña |

### Seeders incluidos

| Seeder | Registros |
|--------|-----------|
| `FoodSeeder` | 23 alimentos |
| `HabitSeeder` | 10 hábitos |
| `EducationalContentSeeder` | 8 artículos |
| `InfusionSeeder` | 16 infusiones (7 seguras, 5 precaución, 4 evitar) |

```bash
php artisan migrate --seed
```

## Estructura del proyecto

```
app/
├── Http/Controllers/
│   ├── AuthController.php              # Auth + onboarding
│   ├── BloodPressureRecordController   # PA + semáforo + stats
│   ├── NutritionalRecommendationController  # DASH
│   ├── DashboardController             # Vista consolidada
│   ├── HabitStreakController           # Rachas + refuerzo
│   ├── MedicationAdherenceController   # Adherencia
│   ├── InfusionController             # Catálogo infusiones
│   ├── HydrationLogController         # Registro hidratación
│   ├── FoodController / FoodLogController
│   ├── MealPlanController
│   ├── MedicationController / MedicationAlarmController / MedicationLogController
│   ├── EducationalContentController
│   ├── HabitController / HabitLogController
│   └── Controller.php
├── Models/
│   ├── User.php
│   ├── BloodPressureRecord.php
│   ├── Food.php / FoodLog.php / MealPlan.php
│   ├── Medication.php / MedicationAlarm.php / MedicationLog.php
│   ├── EducationalContent.php
│   ├── Habit.php / HabitLog.php
│   ├── Infusion.php / HydrationLog.php
│   └── ...
database/
├── migrations/   # 14 migraciones
└── seeders/      # 4 seeders
routes/
└── api.php       # 58 rutas
```

## Deploy en cPanel

1. Subir el proyecto al servidor vía Git o FTP
2. Ejecutar `composer install --no-dev --optimize-autoloader`
3. Configurar `.env` con las credenciales de MySQL del hosting
4. Apuntar el dominio/subdominio a la carpeta `public/`
5. Ejecutar `php artisan migrate --seed`
6. Ejecutar `php artisan jwt:secret`

## Documentación adicional

- [`doc/resumen-modelo-er.md`](doc/resumen-modelo-er.md) – Modelo ER completo con diagrama Mermaid
- [`doc/actualizaciones-api.md`](doc/actualizaciones-api.md) – Detalle de todas las actualizaciones (SRS + hidratación)

## Aviso legal

> Esta aplicación no reemplaza la indicación médica profesional. Es una herramienta de apoyo y educación. Consulte siempre a su médico para decisiones sobre su tratamiento.
