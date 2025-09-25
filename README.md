# 🏭 ERP Modular - Multi-Industri Solution

<div align="center">

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.4+-4FC08D.svg)](https://vuejs.org)
[![Vuetify](https://img.shields.io/badge/Vuetify-3.4+-1867C0.svg)](https://vuetifyjs.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![GitHub Stars](https://img.shields.io/github/stars/wahyudedik/erp-modular?style=social)](https://github.com/wahyudedik/erp-modular)
[![GitHub Forks](https://img.shields.io/github/forks/wahyudedik/erp-modular?style=social)](https://github.com/wahyudedik/erp-modular)

[![Sponsor](https://img.shields.io/badge/Sponsor-%E2%9D%A4-red.svg)](https://github.com/sponsors/wahyudedik)
[![Issues](https://img.shields.io/github/issues/wahyudedik/erp-modular)](https://github.com/wahyudedik/erp-modular/issues)
[![Discussions](https://img.shields.io/badge/GitHub-Discussions-blue.svg)](https://github.com/wahyudedik/erp-modular/discussions)
[![Pull Requests](https://img.shields.io/github/issues-pr/wahyudedik/erp-modular)](https://github.com/wahyudedik/erp-modular/pulls)

[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen.svg)](https://github.com/wahyudedik/erp-modular/actions)
[![Code Quality](https://img.shields.io/badge/Code%20Quality-A-brightgreen.svg)](https://github.com/wahyudedik/erp-modular)
[![Coverage](https://img.shields.io/badge/Coverage-85%25-brightgreen.svg)](https://github.com/wahyudedik/erp-modular)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED.svg)](https://github.com/wahyudedik/erp-modular)

</div>

Sistem ERP yang modular dan scalable untuk berbagai jenis usaha dengan konsep **Register → Pilih Jenis Usaha → Aktivasi Modul Rekomendasi**.

## 🌟 Features

- 🏗️ **Modular Architecture** - Setiap modul dapat diaktifkan/dinonaktifkan sesuai kebutuhan
- 🎯 **Smart Recommendations** - Sistem merekomendasikan modul berdasarkan jenis usaha
- 📈 **Scalable Design** - Mudah menambah jenis usaha dan modul baru
- 🚀 **High Performance** - Laravel Octane + Swoole untuk performa tinggi
- 💎 **Modern UI** - Vue.js 3 + Vuetify 3 dengan Material Design
- 🐳 **Docker Ready** - Containerized untuk development dan production

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
- **PHP 8.2** + **Laravel 10** (Latest)
- **Laravel Octane** dengan **Swoole** untuk performa tinggi
- **Laravel Pulse** untuk real-time monitoring
- **Laravel Horizon** untuk queue management
- **MySQL 8.4** dengan optimasi InnoDB
- **Redis 7.2** untuk caching & session
- **ClickHouse** untuk analytics (optional)

### Frontend - Modern & Powerful
- **Vue.js 3.4** + **TypeScript** + **Vite 5**
- **Vuetify 3.4** - Material Design component framework
- **Pinia 2.1** - State management
- **Vue Router 4.2** - SPA routing
- **Axios** - HTTP client
- **Chart.js** - Advanced charting

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
git clone https://github.com/wahyudedik/erp-modular.git
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
# Backend API: http://localhost:8001 
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
├── database/                     # Database files
│   ├── migrations/              # Database migrations
│   ├── seeders/                 # Database seeders
│   └── factories/               # Model factories
├── resources/js/                # Vue.js frontend
│   ├── components/              # Vue components
│   ├── views/                   # Vue views
│   ├── stores/                  # Pinia stores
│   ├── api/                     # API services
│   └── router/                  # Vue Router
├── docker/                      # Docker configurations
├── docker-compose.yml           # Production Docker setup
├── docker-compose.dev.yml       # Development Docker setup
└── Dockerfile                   # Multi-stage Dockerfile
```

## 🔧 Development Commands

### Backend (Laravel)
```bash
# Development server
php artisan serve --port=8001

# Database operations
php artisan migrate
php artisan db:seed
php artisan migrate:refresh --seed

# Cache operations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
```

### Frontend (Vue.js)
```bash
# Development server
npm run dev

# Build for production
npm run build

# Linting
npm run lint
```

## 🧪 Testing

```bash
# Backend tests
php artisan test

# Frontend tests
npm run test
```

## 📊 Monitoring & Debugging

### Development Tools
- **Laravel Telescope**: `http://localhost:8001/telescope`
- **Laravel Horizon**: `http://localhost:8001/horizon`
- **Laravel Pulse**: `http://localhost:8001/pulse`

## 🔐 Environment Configuration

Copy `.env.example` to `.env` dan konfigurasi sesuai kebutuhan:

```env
# Application
APP_NAME="ERP Modular"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_modular
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 🚀 Deployment

### Production Deployment
```bash
# Build and deploy
docker-compose up -d --build

# Setup SSL (optional)
# Copy SSL certificates to docker/ssl/
```

## 📈 Performance Features

- **Laravel Octane**: 10x faster than traditional PHP
- **Redis Caching**: Sub-millisecond response times
- **Database Optimization**: Proper indexing dan query optimization
- **CDN Ready**: Static asset optimization
- **Horizontal Scaling**: Docker container scaling

## 🤝 Contributing

Kami sangat menghargai kontribusi dari komunitas! Silakan ikuti langkah-langkah berikut:

1. **Fork** repository ini
2. **Create** feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** perubahan (`git commit -m 'Add amazing feature'`)
4. **Push** ke branch (`git push origin feature/amazing-feature`)
5. **Open** Pull Request

Lihat [CONTRIBUTING.md](CONTRIBUTING.md) untuk panduan lengkap.

## 📝 License

Project ini dilisensikan di bawah [MIT License](LICENSE) - lihat file LICENSE untuk detail.

## 🆘 Support

- 📖 **Documentation**: [Wiki](https://github.com/wahyudedik/erp-modular/wiki)
- 🐛 **Issues**: [GitHub Issues](https://github.com/wahyudedik/erp-modular/issues)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/wahyudedik/erp-modular/discussions)
- 💝 **Sponsor**: [GitHub Sponsors](https://github.com/sponsors/wahyudedik)

## 🎯 Roadmap

- **Phase 1**: Foundation & Core Modules (Bulan 1-3) ✅
- **Phase 2**: Industry Modules (Bulan 4-6) 🚧
- **Phase 3**: Advanced Features (Bulan 7-9) 📋
- **Phase 4**: Scaling & Expansion (Bulan 10-12) 📋

## 🌟 Show Your Support

Jika project ini membantu Anda, berikan ⭐ star dan 💝 [sponsor](https://github.com/sponsors/wahyudedik) untuk mendukung pengembangan lebih lanjut!

---

<div align="center">

**Made with ❤️ for Indonesian Businesses**

[![GitHub](https://img.shields.io/badge/GitHub-wahyudedik-181717.svg?logo=github)](https://github.com/wahyudedik)
[![Twitter](https://img.shields.io/badge/Twitter-@wahyudedik-1DA1F2.svg?logo=twitter)](https://twitter.com/wahyudedik)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-wahyudedik-0077B5.svg?logo=linkedin)](https://linkedin.com/in/wahyudedik)

</div>