# 🏭 ERP Modular Multi-Industri

Sistem ERP yang modular dan scalable untuk berbagai jenis usaha dengan konsep **Register → Pilih Jenis Usaha → Aktivasi Modul Rekomendasi**.

## 🎯 Konsep Utama

- **Modular Architecture**: Setiap modul dapat diaktifkan/dinonaktifkan sesuai kebutuhan
- **Business Type Selection**: User memilih jenis usaha saat registrasi
- **Smart Recommendations**: Sistem merekomendasikan modul berdasarkan jenis usaha
- **Scalable Design**: Mudah menambah jenis usaha dan modul baru
- **Multi-tenant Ready**: Siap untuk multiple business types

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────┐
│           Authentication            │
├─────────────────────────────────────┤
│         Business Type Selection     │
├─────────────────────────────────────┤
│         Module Management           │
├─────────────────────────────────────┤
│        Core Shared Modules          │
│  ┌─────────┬─────────┬─────────┐    │
│  │Accounting│Inventory│   HR    │    │
│  └─────────┴─────────┴─────────┘    │
├─────────────────────────────────────┤
│       Industry Specific Modules     │
│  ┌─────────┬─────────┬─────────┐    │
│  │Manufacturing│Retail│Construction│    │
│  └─────────┴─────────┴─────────┘    │
└─────────────────────────────────────┘
```

## 🚀 Tech Stack

### Backend - Native & High Performance
- **PHP 8.3** + **Laravel 11** (Latest)
- **Laravel Octane** dengan **Swoole** untuk performa tinggi
- **Laravel Pulse** untuk real-time monitoring
- **Laravel Horizon** untuk queue management
- **MySQL 8.4** dengan optimasi InnoDB
- **Redis 7.2** untuk caching & session
- **ClickHouse** untuk analytics (optional)

### Frontend - Modern & Powerful
- **Vue.js 3.4** + **TypeScript** + **Vite 5**
- **TailwindCSS 3.4** - Utility-first CSS framework
- **HeadlessUI** - Unstyled, accessible components
- **Heroicons** - Beautiful SVG icons
- **Chart.js** - Advanced charting
- **VueUse** - Vue composition utilities

### Infrastructure
- **Docker** untuk containerization
- **Nginx** sebagai reverse proxy
- **Multi-stage builds** untuk optimasi

## 📊 Jenis Usaha & Modul Rekomendasi

### 🏭 Pabrik Beton
**Modul Rekomendasi:**
- **Core Modules:** Akuntansi, Inventori, Penjualan, Pembelian, HR
- **Manufacturing:** Produksi, QC, Maintenance, Batching
- **Specialized:** Mix Design, Concrete Testing, Delivery Management

### 🍞 Pabrik Roti
**Modul Rekomendasi:**
- **Core Modules:** Akuntansi, Inventori, Penjualan, Pembelian, HR
- **Manufacturing:** Produksi, QC, Recipe Management
- **Specialized:** Expiry Tracking, Batch Management, Distribution

### 🏪 Retail/Perdagangan
**Modul Rekomendasi:**
- **Core Modules:** Akuntansi, Inventori, Penjualan, Pembelian, HR
- **Retail:** Point of Sale, Customer Management, Promotions
- **Specialized:** Multi-location, Supplier Management, Analytics

### 🏢 Konstruksi
**Modul Rekomendasi:**
- **Core Modules:** Akuntansi, Inventori, Penjualan, Pembelian, HR
- **Project:** Project Management, Progress Tracking, Cost Control
- **Specialized:** Equipment Management, Subcontractor Management

## 🐳 Quick Start dengan Docker

### Prerequisites
- Docker & Docker Compose
- Git

### Development Setup
```bash
# Clone repository
git clone https://github.com/your-username/erp-modular.git
cd erp-modular

# Development environment
docker-compose -f docker-compose.dev.yml up -d

# Install dependencies
docker-compose exec app composer install
docker-compose exec node npm install

# Setup database
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Access application
# Frontend: http://localhost:3000
# Backend API: http://localhost:8000
# Laravel Telescope: http://localhost:8000/telescope
```

### Production Setup
```bash
# Production environment
docker-compose up -d --build

# Setup production
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Access application
# Application: http://localhost
```

## 📁 Project Structure

```
erp-modular/
├── app/                          # Laravel application
│   ├── Http/Controllers/         # API Controllers
│   ├── Models/                   # Eloquent Models
│   ├── Services/                 # Business Logic
│   └── Modules/                  # Modular Structure
│       ├── Core/                 # Core modules
│       ├── Manufacturing/        # Manufacturing modules
│       └── Industry/             # Industry-specific modules
├── database/                     # Database files
│   ├── migrations/              # Database migrations
│   ├── seeders/                 # Database seeders
│   └── factories/               # Model factories
├── src/                         # Vue.js frontend
│   ├── components/              # Vue components
│   ├── views/                   # Vue views
│   ├── stores/                  # Pinia stores
│   ├── services/                # API services
│   └── router/                  # Vue Router
├── docker/                      # Docker configurations
│   ├── nginx/                   # Nginx config
│   ├── php/                     # PHP config
│   ├── mysql/                   # MySQL config
│   └── redis/                   # Redis config
├── docker-compose.yml           # Production Docker setup
├── docker-compose.dev.yml       # Development Docker setup
├── Dockerfile                   # Multi-stage Dockerfile
└── package.json                 # Frontend dependencies
```

## 🔧 Development Commands

### Backend (Laravel)
```bash
# Development server
docker-compose exec app php artisan serve

# Database operations
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan migrate:refresh --seed

# Cache operations
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan cache:clear

# Queue operations
docker-compose exec app php artisan queue:work
docker-compose exec horizon php artisan horizon

# Testing
docker-compose exec app php artisan test
docker-compose exec app phpunit
```

### Frontend (Vue.js)
```bash
# Development server
docker-compose exec node npm run dev

# Build for production
docker-compose exec node npm run build

# Linting
docker-compose exec node npm run lint

# Type checking
docker-compose exec node npm run type-check
```

## 🧪 Testing

```bash
# Backend tests
docker-compose exec app php artisan test

# Frontend tests
docker-compose exec node npm run test

# E2E tests
docker-compose exec node npm run test:e2e
```

## 📊 Monitoring & Debugging

### Development Tools
- **Laravel Telescope**: `http://localhost:8000/telescope`
- **Laravel Horizon**: `http://localhost:8000/horizon`
- **Laravel Pulse**: `http://localhost:8000/pulse`

### Production Monitoring
- **Application Logs**: `docker-compose logs -f app`
- **Database Logs**: `docker-compose logs -f mysql`
- **Queue Status**: `docker-compose exec app php artisan horizon:status`

## 🔐 Environment Configuration

Copy `.env.example` to `.env` dan konfigurasi sesuai kebutuhan:

```bash
# Application
APP_NAME="ERP Modular"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=erp_modular
DB_USERNAME=erp_user
DB_PASSWORD=erp_password

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis
```

## 🚀 Deployment

### Production Deployment
```bash
# Build and deploy
docker-compose up -d --build

# Setup SSL (optional)
# Copy SSL certificates to docker/ssl/

# Configure domain
# Update nginx configuration in docker/nginx/
```

### CI/CD Pipeline
- GitHub Actions untuk automated testing
- Docker Hub untuk image registry
- Automated deployment ke production

## 📈 Performance Features

- **Laravel Octane**: 10x faster than traditional PHP
- **Redis Caching**: Sub-millisecond response times
- **Database Optimization**: Proper indexing dan query optimization
- **CDN Ready**: Static asset optimization
- **Horizontal Scaling**: Docker container scaling

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: [Wiki](https://github.com/your-username/erp-modular/wiki)
- **Issues**: [GitHub Issues](https://github.com/your-username/erp-modular/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-username/erp-modular/discussions)

## 🎯 Roadmap

- [x] **Phase 1**: Foundation & Core Modules (Bulan 1-3)
- [ ] **Phase 2**: Industry Modules (Bulan 4-6)
- [ ] **Phase 3**: Advanced Features (Bulan 7-9)
- [ ] **Phase 4**: Scaling & Expansion (Bulan 10-12)

---

**Made with ❤️ for Indonesian Businesses**
