# 🤝 Contributing to ERP Modular

Terima kasih atas minat Anda untuk berkontribusi pada ERP Modular! Kami sangat menghargai setiap kontribusi yang membantu membuat project ini lebih baik.

## 📋 Table of Contents

- [Code of Conduct](#-code-of-conduct)
- [Getting Started](#-getting-started)
- [How to Contribute](#-how-to-contribute)
- [Development Setup](#-development-setup)
- [Coding Standards](#-coding-standards)
- [Commit Guidelines](#-commit-guidelines)
- [Pull Request Process](#-pull-request-process)
- [Issue Guidelines](#-issue-guidelines)

## 📜 Code of Conduct

Project ini dan semua partisipan di dalamnya diatur oleh [Code of Conduct](CODE_OF_CONDUCT.md). Dengan berpartisipasi, Anda diharapkan untuk mematuhi kode etik ini.

## 🚀 Getting Started
 
### Prerequisites

- **PHP 8.2+** dengan extensions: mbstring, dom, fileinfo, mysql, redis
- **Composer** untuk dependency management
- **Node.js 18+** dan **npm**
- **Docker & Docker Compose** (optional)
- **Git** untuk version control

### Fork & Clone

1. **Fork** repository ini
2. **Clone** fork Anda:
   ```bash
   git clone https://github.com/YOUR_USERNAME/erp-modular.git
   cd erp-modular
   ```
3. **Add upstream** remote:
   ```bash
   git remote add upstream https://github.com/wahyudedik/erp-modular.git
   ```

## 🔄 How to Contribute

### Types of Contributions

- 🐛 **Bug Reports** - Report bugs dan issues
- ✨ **Feature Requests** - Suggest new features
- 📚 **Documentation** - Improve documentation
- 🧪 **Testing** - Add tests atau improve existing ones
- 💻 **Code** - Fix bugs atau implement features
- 🎨 **UI/UX** - Improve user interface
- 🔧 **DevOps** - Improve CI/CD, deployment, dll

### Contribution Workflow

1. **Create Issue** - Discuss changes melalui issue terlebih dahulu
2. **Create Branch** - Buat feature branch dari `develop`
3. **Make Changes** - Implement changes dengan coding standards
4. **Test Changes** - Pastikan semua tests pass
5. **Submit PR** - Create pull request dengan template yang lengkap

## 🛠️ Development Setup

### Option 1: Docker (Recommended)

```bash
# Clone repository
git clone https://github.com/YOUR_USERNAME/erp-modular.git
cd erp-modular

# Start development environment
docker-compose -f docker-compose.dev.yml up -d

# Install dependencies
docker-compose exec app composer install
docker-compose exec node npm install

# Setup database
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Access application
# Frontend: http://localhost:3000
# Backend: http://localhost:8001
```

### Option 2: Local Development

```bash
# Backend setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed

# Frontend setup
npm install
npm run dev

# Start Laravel server
php artisan serve --port=8001
```

## 📏 Coding Standards

### PHP/Laravel

- **PSR-12** coding standard
- **Laravel conventions** untuk naming dan structure
- **PHPDoc** comments untuk semua public methods
- **Type hints** untuk parameters dan return types

```php
/**
 * Get user's active modules
 *
 * @param int $userId
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getActiveModules(int $userId): Collection
{
    // Implementation
}
```

### JavaScript/Vue.js

- **ESLint** configuration
- **Vue 3 Composition API** preferred
- **TypeScript** untuk complex logic
- **Camel case** untuk variables dan functions

```javascript
// ✅ Good
const userStore = useUserStore()
const activeModules = computed(() => userStore.activeModules)

// ❌ Bad
const user_store = useUserStore()
const active_modules = computed(() => user_store.active_modules)
```

### CSS/SCSS

- **BEM methodology** untuk class naming
- **Consistent spacing** (2 spaces)
- **Mobile-first** responsive design

```scss
// ✅ Good
.module-card {
  &__title {
    font-weight: 600;
  }
  
  &--active {
    border-color: $primary-color;
  }
}
```

## 📝 Commit Guidelines

### Commit Message Format

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks
- `perf`: Performance improvements

### Examples

```bash
feat(auth): add user registration with email verification
fix(dashboard): resolve module loading issue
docs(api): update authentication endpoints documentation
style(frontend): format Vue components with Prettier
refactor(modules): extract common module logic to service
test(backend): add unit tests for BusinessType model
chore(deps): update Laravel to version 10.1
perf(database): optimize user modules query
```

### Scope Examples

- `auth`, `user`, `module`, `dashboard`
- `api`, `frontend`, `backend`
- `database`, `migration`, `seeder`

## 🔄 Pull Request Process

### Before Submitting

- [ ] **Fork** repository dan create feature branch
- [ ] **Update** documentation jika diperlukan
- [ ] **Add tests** untuk new functionality
- [ ] **Ensure** semua tests pass
- [ ] **Run** code quality checks
- [ ] **Update** CHANGELOG.md jika applicable

### PR Requirements

- **Clear title** dan description
- **Link** related issues
- **Screenshots** untuk UI changes
- **Test coverage** untuk code changes
- **Documentation** updates jika diperlukan

### Review Process

1. **Automated checks** must pass
2. **Code review** oleh maintainers
3. **Testing** pada staging environment
4. **Approval** dari maintainers
5. **Merge** ke target branch

## 🐛 Issue Guidelines

### Bug Reports

- **Clear title** describing the issue
- **Steps to reproduce** with expected vs actual behavior
- **Environment details** (OS, browser, versions)
- **Screenshots** atau error logs jika applicable

### Feature Requests

- **Clear problem statement** dan proposed solution
- **Business value** atau user benefit
- **Technical considerations** jika applicable
- **Acceptance criteria** untuk implementation

### Questions

- **Search existing issues** terlebih dahulu
- **Provide context** dan what you've tried
- **Be specific** tentang what you need to know

## 🧪 Testing Guidelines

### Backend Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=BusinessTypeTest

# Run with coverage
php artisan test --coverage
```

### Frontend Testing

```bash
# Run unit tests
npm run test:unit

# Run e2e tests
npm run test:e2e

# Run linting
npm run lint
```

### Test Requirements

- **Unit tests** untuk business logic
- **Integration tests** untuk API endpoints
- **Feature tests** untuk user workflows
- **Minimum 80%** code coverage

## 📚 Documentation

### Code Documentation

- **PHPDoc** untuk PHP methods
- **JSDoc** untuk JavaScript functions
- **README** updates untuk new features
- **API documentation** untuk endpoints

### User Documentation

- **Wiki** untuk user guides
- **Screenshots** untuk UI features
- **Video tutorials** untuk complex workflows

## 🏷️ Labels

Kami menggunakan labels untuk mengorganisir issues dan PRs:

### Issue Labels

- `bug` - Something isn't working
- `enhancement` - New feature or request
- `documentation` - Improvements or additions to documentation
- `good first issue` - Good for newcomers
- `help wanted` - Extra attention is needed
- `question` - Further information is requested

### Priority Labels

- `priority: low` - Low priority
- `priority: medium` - Medium priority
- `priority: high` - High priority
- `priority: critical` - Critical priority

## 🎯 Getting Help

- 💬 **Discussions**: [GitHub Discussions](https://github.com/wahyudedik/erp-modular/discussions)
- 📖 **Documentation**: [Wiki](https://github.com/wahyudedik/erp-modular/wiki)
- 🐛 **Issues**: [GitHub Issues](https://github.com/wahyudedik/erp-modular/issues)
- 💝 **Sponsor**: [GitHub Sponsors](https://github.com/sponsors/wahyudedik)

## 🏆 Recognition

Kontributor akan diakui dalam:

- **CONTRIBUTORS.md** file
- **Release notes** untuk significant contributions
- **GitHub contributors** page
- **Special thanks** dalam documentation

## 📄 License

Dengan berkontribusi, Anda setuju bahwa kontribusi Anda akan dilisensikan di bawah [MIT License](LICENSE).

---

**Terima kasih telah berkontribusi pada ERP Modular! 🎉**

Jika Anda memiliki pertanyaan tentang proses kontribusi, jangan ragu untuk membuat issue atau discussion.
