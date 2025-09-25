# Technical Specifications - ERP Modular Multi-Industri

## 🏗️ System Architecture

### High-Level Architecture
```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend Layer                           │
│  ┌─────────────┬─────────────┬─────────────┬─────────────┐  │
│  │   Vue.js    │   Mobile    │   Desktop   │   Admin     │  │
│  │  (Web App)  │     App     │     App     │    Panel    │  │
│  └─────────────┴─────────────┴─────────────┴─────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     API Gateway                             │
│  ┌─────────────┬─────────────┬─────────────┬─────────────┐  │
│  │  REST API   │  GraphQL    │  WebSocket  │   Auth      │  │
│  │   (v1/v2)   │    API      │  (Real-time)│  Service    │  │
│  └─────────────┴─────────────┴─────────────┴─────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                  Application Layer                          │
│  ┌─────────────┬─────────────┬─────────────┬─────────────┐  │
│  │   Core      │  Business   │   Module    │  Industry   │  │
│  │  Services   │   Logic     │  Manager    │  Specific   │  │
│  └─────────────┴─────────────┴─────────────┴─────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     Data Layer                              │
│  ┌─────────────┬─────────────┬─────────────┬─────────────┐  │
│  │   MySQL     │    Redis    │   File      │   Queue     │  │
│  │ (Primary)   │   (Cache)   │  Storage    │  (Jobs)     │  │
│  └─────────────┴─────────────┴─────────────┴─────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

## 🗄️ Database Schema

### Core Tables

#### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    business_type_id BIGINT UNSIGNED NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (business_type_id) REFERENCES business_types(id),
    INDEX idx_business_type (business_type_id),
    INDEX idx_email (email),
    INDEX idx_active (is_active)
);
```

#### Business Types Table
```sql
CREATE TABLE business_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    icon VARCHAR(100) NULL,
    color VARCHAR(7) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
);
```

#### Modules Table
```sql
CREATE TABLE modules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    category VARCHAR(50) NOT NULL, -- core, manufacturing, retail, etc.
    icon VARCHAR(100) NULL,
    version VARCHAR(20) DEFAULT '1.0.0',
    is_core BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_slug (slug),
    INDEX idx_category (category),
    INDEX idx_core (is_core),
    INDEX idx_active (is_active)
);
```

#### Module Recommendations Table
```sql
CREATE TABLE module_recommendations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    business_type_id BIGINT UNSIGNED NOT NULL,
    module_id BIGINT UNSIGNED NOT NULL,
    priority INT DEFAULT 0, -- 1=high, 2=medium, 3=low
    is_required BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (business_type_id) REFERENCES business_types(id),
    FOREIGN KEY (module_id) REFERENCES modules(id),
    UNIQUE KEY unique_business_module (business_type_id, module_id),
    INDEX idx_business_type (business_type_id),
    INDEX idx_module (module_id),
    INDEX idx_priority (priority)
);
```

#### User Modules Table
```sql
CREATE TABLE user_modules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    module_id BIGINT UNSIGNED NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    activated_at TIMESTAMP NULL,
    configuration JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id),
    UNIQUE KEY unique_user_module (user_id, module_id),
    INDEX idx_user (user_id),
    INDEX idx_module (module_id),
    INDEX idx_active (is_active)
);
```

## 🔧 Technology Stack Details - Native & Powerful

### Backend Stack - Native & High Performance

#### PHP 8.3 + Laravel 11 (Latest & Greatest)
```php
// composer.json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^11.0",
        "laravel/sanctum": "^4.0",
        "laravel/telescope": "^5.0",
        "laravel/horizon": "^5.0",
        "laravel/octane": "^2.0",
        "laravel/pulse": "^1.0",
        "spatie/laravel-permission": "^6.0",
        "spatie/laravel-activitylog": "^4.0",
        "spatie/laravel-query-builder": "^5.0",
        "league/flysystem-aws-s3-v3": "^3.0",
        "predis/predis": "^2.0",
        "opcodesio/log-viewer": "^2.0"
    }
}
```

#### Advanced Laravel Packages & Performance
- **Laravel Octane**: High-performance server dengan Swoole/RoadRunner
- **Laravel Pulse**: Real-time performance monitoring
- **Laravel Horizon**: Advanced queue monitoring & management
- **Laravel Telescope**: Advanced debugging & profiling
- **Spatie Query Builder**: Advanced API query building
- **Laravel Sanctum**: Token-based authentication
- **Spatie Permission**: Advanced role & permission management
- **Log Viewer**: Advanced log management interface

#### Database Stack - High Performance
- **MySQL 8.4** (Latest) dengan optimasi InnoDB
- **Redis 7.2** untuk caching & session
- **ClickHouse** untuk analytics & reporting (optional)
- **Elasticsearch** untuk advanced search (optional)

### Frontend Stack - Modern & Powerful

#### Vue.js 3 + Vite + TypeScript
```javascript
// package.json
{
    "dependencies": {
        "vue": "^3.4.0",
        "vue-router": "^4.2.0",
        "pinia": "^2.1.0",
        "axios": "^1.6.0",
        "chart.js": "^4.4.0",
        "vue-chartjs": "^5.2.0",
        "vue-i18n": "^9.8.0",
        "vueuse": "^10.7.0",
        "@vueuse/core": "^10.7.0"
    },
    "devDependencies": {
        "typescript": "^5.3.0",
        "vite": "^5.0.0",
        "@vitejs/plugin-vue": "^5.0.0",
        "@vue/tsconfig": "^0.5.0",
        "eslint": "^8.55.0",
        "prettier": "^3.1.0",
        "unplugin-auto-import": "^0.17.0"
    }
}
```

#### CSS Framework - TailwindCSS + HeadlessUI
```javascript
// tailwind.config.js
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          900: '#1e3a8a',
        }
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
```

#### UI Component Libraries
- **TailwindCSS 3.4**: Utility-first CSS framework
- **HeadlessUI**: Unstyled, accessible UI components
- **Heroicons**: Beautiful SVG icons
- **Chart.js**: Advanced charting library
- **VueUse**: Collection of Vue composition utilities

#### Alternative CSS Framework Options
```javascript
// Option 1: TailwindCSS (Recommended)
// - Utility-first approach
// - Highly customizable
// - Excellent performance
// - Great for complex dashboards

// Option 2: UnoCSS (Ultra-fast alternative)
// - Faster than TailwindCSS
// - Compatible with TailwindCSS syntax
// - Better tree-shaking

// Option 3: Vuetify 3 (Material Design)
// - Component-based approach
// - Rich component library
// - Good for rapid prototyping
// - Larger bundle size
```

### Database Configuration

#### MySQL Configuration
```sql
-- Optimized MySQL settings for ERP
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_file_per_table = 1
max_connections = 200
query_cache_size = 64M
query_cache_type = 1
```

#### Redis Configuration
```redis
# redis.conf
maxmemory 512mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

## 🔐 Security Implementation

### Authentication & Authorization

#### Laravel Sanctum Configuration
```php
// config/sanctum.php
return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s%s',
        'localhost,localhost:3000,localhost:3001,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : '',
        env('FRONTEND_URL') ? ','.parse_url(env('FRONTEND_URL'), PHP_URL_HOST) : ''
    ))),
    
    'guard' => ['web'],
    
    'expiration' => 60 * 24 * 7, // 7 days
    
    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

#### Permission System
```php
// Module-based permissions
class ModulePermissionMiddleware
{
    public function handle($request, Closure $next, $module)
    {
        if (!auth()->user()->hasModuleAccess($module)) {
            return response()->json(['error' => 'Module access denied'], 403);
        }
        
        return $next($request);
    }
}
```

### Data Security

#### Encryption
```php
// Encrypt sensitive data
class User extends Model
{
    protected $casts = [
        'password' => 'hashed',
        'phone' => 'encrypted',
        'address' => 'encrypted'
    ];
}
```

#### API Rate Limiting
```php
// routes/api.php
Route::middleware(['throttle:60,1'])->group(function () {
    Route::apiResource('modules', ModuleController::class);
});
```

## 📊 Module Architecture

### Module Structure
```
app/
├── Modules/
│   ├── Core/
│   │   ├── Accounting/
│   │   │   ├── Controllers/
│   │   │   ├── Models/
│   │   │   ├── Services/
│   │   │   ├── Resources/
│   │   │   └── Routes/
│   │   ├── Inventory/
│   │   ├── Sales/
│   │   └── Purchase/
│   ├── Manufacturing/
│   │   ├── Production/
│   │   ├── QualityControl/
│   │   └── Maintenance/
│   └── Industry/
│       ├── Concrete/
│       ├── Bakery/
│       └── Retail/
```

### Module Registration
```php
// ModuleService.php
class ModuleService
{
    public function registerModule($moduleSlug, $config)
    {
        return [
            'slug' => $moduleSlug,
            'name' => $config['name'],
            'version' => $config['version'],
            'dependencies' => $config['dependencies'] ?? [],
            'routes' => $config['routes'] ?? [],
            'permissions' => $config['permissions'] ?? [],
            'widgets' => $config['widgets'] ?? []
        ];
    }
    
    public function activateModule($userId, $moduleId)
    {
        // Check dependencies
        // Create module permissions
        // Initialize module data
        // Register module widgets
    }
}
```

## 🔄 API Design

### RESTful API Structure
```
GET    /api/v1/business-types           # List business types
GET    /api/v1/business-types/{id}      # Get business type
GET    /api/v1/modules                  # List available modules
GET    /api/v1/modules/{id}/recommendations # Get module recommendations
POST   /api/v1/user-modules             # Activate module
DELETE /api/v1/user-modules/{id}        # Deactivate module
GET    /api/v1/user-modules             # List user's modules
```

### GraphQL Schema
```graphql
type Query {
  businessTypes: [BusinessType!]!
  modules(category: String): [Module!]!
  userModules: [UserModule!]!
  moduleRecommendations(businessTypeId: ID!): [ModuleRecommendation!]!
}

type Mutation {
  activateModule(moduleId: ID!): UserModule!
  deactivateModule(moduleId: ID!): Boolean!
  updateModuleConfiguration(moduleId: ID!, config: JSON!): UserModule!
}

type BusinessType {
  id: ID!
  name: String!
  slug: String!
  description: String
  icon: String
  color: String
}

type Module {
  id: ID!
  name: String!
  slug: String!
  description: String
  category: String!
  icon: String
  version: String!
  isCore: Boolean!
}
```

## 📱 Frontend Architecture

### Vue.js Application Structure
```
src/
├── components/
│   ├── core/
│   │   ├── ModuleManager.vue
│   │   ├── BusinessTypeSelector.vue
│   │   └── Dashboard.vue
│   ├── modules/
│   │   ├── ModuleCard.vue
│   │   ├── ModuleGrid.vue
│   │   └── ModuleSettings.vue
│   └── common/
│       ├── Navigation.vue
│       ├── Sidebar.vue
│       └── Header.vue
├── views/
│   ├── Dashboard.vue
│   ├── Modules.vue
│   ├── Settings.vue
│   └── Profile.vue
├── stores/
│   ├── auth.js
│   ├── modules.js
│   ├── businessTypes.js
│   └── user.js
├── services/
│   ├── api.js
│   ├── auth.js
│   └── modules.js
└── router/
    └── index.js
```

### State Management (Pinia)
```javascript
// stores/modules.js
export const useModuleStore = defineStore('modules', {
    state: () => ({
        availableModules: [],
        userModules: [],
        activeModules: [],
        loading: false
    }),
    
    actions: {
        async fetchModules() {
            this.loading = true;
            try {
                const response = await api.get('/modules');
                this.availableModules = response.data;
            } finally {
                this.loading = false;
            }
        },
        
        async activateModule(moduleId) {
            const response = await api.post('/user-modules', { module_id: moduleId });
            this.userModules.push(response.data);
        }
    }
});
```

## 🚀 Docker Configuration - Production Ready

### Multi-Stage Dockerfile (Optimized)
```dockerfile
# Stage 1: Build frontend
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY . .
RUN npm run build

# Stage 2: PHP Backend
FROM php:8.3-fpm-alpine AS backend

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    icu-dev \
    postgresql-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Swoole for Laravel Octane
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Set working directory
WORKDIR /var/www

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Copy built frontend assets
COPY --from=frontend-builder /app/dist ./public/dist

# Run composer scripts
RUN composer run-script post-install-cmd

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Switch to non-root user
USER www-data

EXPOSE 8000
```

### Docker Compose - Development & Production
```yaml
# docker-compose.yml
version: '3.8'

services:
  # PHP Application with Octane
  app:
    build: 
      context: .
      target: backend
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www
      - ./docker/php/php.ini:/usr/local/etc/php/php.ini
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_HOST=mysql
      - REDIS_HOST=redis
    depends_on:
      - mysql
      - redis
    command: php artisan octane:start --host=0.0.0.0 --port=8000 --workers=4
    restart: unless-stopped

  # MySQL 8.4 - Latest & Optimized
  mysql:
    image: mysql:8.4
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql_data:/var/lib/mysql
      - ./docker/mysql/my.cnf:/etc/mysql/conf.d/my.cnf
    command: --default-authentication-plugin=mysql_native_password
    restart: unless-stopped

  # Redis 7.2 - Latest
  redis:
    image: redis:7.2-alpine
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
      - ./docker/redis/redis.conf:/usr/local/etc/redis/redis.conf
    command: redis-server /usr/local/etc/redis/redis.conf
    restart: unless-stopped

  # Nginx - High Performance
  nginx:
    image: nginx:1.25-alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - .:/var/www
      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./docker/nginx/sites:/etc/nginx/sites-available
      - ./docker/ssl:/etc/nginx/ssl
    depends_on:
      - app
    restart: unless-stopped

  # Laravel Horizon - Queue Management
  horizon:
    build: 
      context: .
      target: backend
    volumes:
      - .:/var/www
    environment:
      - APP_ENV=production
    depends_on:
      - mysql
      - redis
    command: php artisan horizon
    restart: unless-stopped

  # ClickHouse - Analytics (Optional)
  clickhouse:
    image: clickhouse/clickhouse-server:latest
    ports:
      - "8123:8123"
      - "9000:9000"
    volumes:
      - clickhouse_data:/var/lib/clickhouse
    environment:
      CLICKHOUSE_DB: analytics
      CLICKHOUSE_USER: default
      CLICKHOUSE_PASSWORD: ""
    restart: unless-stopped

volumes:
  mysql_data:
  redis_data:
  clickhouse_data:
```

### Docker Development Setup
```yaml
# docker-compose.dev.yml
version: '3.8'

services:
  app:
    build: 
      context: .
      target: backend
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
    command: php artisan serve --host=0.0.0.0 --port=8000

  mysql:
    image: mysql:8.4
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: erp_modular_dev
      MYSQL_USER: dev
      MYSQL_PASSWORD: dev
    volumes:
      - mysql_dev_data:/var/lib/mysql

  redis:
    image: redis:7.2-alpine
    ports:
      - "6379:6379"

  # Laravel Telescope - Development Debugging
  telescope:
    build: 
      context: .
      target: backend
    volumes:
      - .:/var/www
    environment:
      - APP_ENV=local
    command: php artisan telescope:install

volumes:
  mysql_dev_data:
```

### Nginx Configuration (High Performance)
```nginx
# docker/nginx/nginx.conf
user www-data;
worker_processes auto;
pid /run/nginx.pid;

events {
    worker_connections 1024;
    use epoll;
    multi_accept on;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logging
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';

    access_log /var/log/nginx/access.log main;
    error_log /var/log/nginx/error.log warn;

    # Performance
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    client_max_body_size 100M;

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    limit_req_zone $binary_remote_addr zone=login:10m rate=1r/s;

    include /etc/nginx/sites-available/*;
}
```

### PHP Configuration (Optimized)
```ini
; docker/php/php.ini
[PHP]
engine = On
short_open_tag = Off
precision = 14
output_buffering = 4096
zlib.output_compression = Off
implicit_flush = Off
unserialize_callback_func =
serialize_precision = -1
disable_functions = exec,passthru,shell_exec,system
disable_classes =
zend.enable_gc = On

; Performance
opcache.enable = 1
opcache.enable_cli = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 20000
opcache.validate_timestamps = 0
opcache.revalidate_freq = 0
opcache.save_comments = 1

; Memory
memory_limit = 512M
max_execution_time = 300
max_input_time = 300

; Upload
upload_max_filesize = 100M
post_max_size = 100M
max_file_uploads = 20

; Session
session.gc_maxlifetime = 1440
session.cookie_lifetime = 0
session.cookie_secure = 1
session.cookie_httponly = 1
session.use_strict_mode = 1
```

### MySQL Configuration (High Performance)
```ini
# docker/mysql/my.cnf
[mysqld]
# Performance
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_file_per_table = 1
innodb_flush_method = O_DIRECT

# Connections
max_connections = 200
max_connect_errors = 100000

# Query Cache
query_cache_type = 1
query_cache_size = 64M
query_cache_limit = 2M

# Logging
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2

# Character Set
character_set_server = utf8mb4
collation_server = utf8mb4_unicode_ci

[mysql]
default-character-set = utf8mb4
```

### Docker Commands & Scripts
```bash
# Development
docker-compose -f docker-compose.dev.yml up -d
docker-compose -f docker-compose.dev.yml exec app php artisan migrate
docker-compose -f docker-compose.dev.yml exec app php artisan db:seed

# Production
docker-compose up -d --build
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Maintenance
docker-compose exec app php artisan down
docker-compose exec app php artisan up
docker-compose exec app php artisan queue:restart

# Monitoring
docker-compose logs -f app
docker-compose exec app php artisan horizon:status
docker-compose exec app php artisan telescope:clear
```

## 📊 Performance Optimization

### Caching Strategy
```php
// Cache module recommendations
class ModuleService
{
    public function getRecommendations($businessTypeId)
    {
        return Cache::remember(
            "module_recommendations_{$businessTypeId}",
            3600, // 1 hour
            function () use ($businessTypeId) {
                return ModuleRecommendation::where('business_type_id', $businessTypeId)
                    ->with('module')
                    ->orderBy('priority')
                    ->get();
            }
        );
    }
}
```

### Database Optimization
```sql
-- Optimized queries with proper indexing
CREATE INDEX idx_user_modules_active ON user_modules(user_id, is_active);
CREATE INDEX idx_module_recommendations_business ON module_recommendations(business_type_id, priority);
CREATE INDEX idx_modules_category_active ON modules(category, is_active);
```

### Frontend Optimization
```javascript
// Lazy loading modules
const ModuleComponent = defineAsyncComponent({
    loader: () => import(`@/modules/${moduleName}/index.vue`),
    loadingComponent: LoadingSpinner,
    errorComponent: ErrorComponent,
    delay: 200,
    timeout: 3000
});
```

## 🔍 Monitoring & Logging

### Application Monitoring
```php
// Log module activities
class ModuleActivityLogger
{
    public function logModuleActivation($userId, $moduleId)
    {
        activity()
            ->causedBy($userId)
            ->performedOn(Module::find($moduleId))
            ->withProperties(['action' => 'activated'])
            ->log('Module activated');
    }
}
```

### Performance Monitoring
```javascript
// Frontend performance tracking
import { getCLS, getFID, getFCP, getLCP, getTTFB } from 'web-vitals';

getCLS(console.log);
getFID(console.log);
getFCP(console.log);
getLCP(console.log);
getTTFB(console.log);
```

---

**Note:** Technical specifications ini akan diupdate sesuai dengan perkembangan teknologi dan kebutuhan sistem.
