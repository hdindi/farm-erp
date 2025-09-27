# Farm ERP System - Technical Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Getting Started](#getting-started)
4. [Domain Structure](#domain-structure)
5. [Development Guidelines](#development-guidelines)
6. [API Documentation](#api-documentation)
7. [Database Design](#database-design)
8. [Testing Strategy](#testing-strategy)
9. [Deployment Guide](#deployment-guide)
10. [Troubleshooting](#troubleshooting)

---

## System Overview

The Farm ERP System is a comprehensive Laravel-based application designed to manage poultry farming operations. It handles batch management, daily records, health management, feed tracking, sales, and comprehensive reporting.

### Key Features
- **Batch Management**: Track bird batches from arrival to completion
- **Daily Operations**: Record daily activities, mortality, and performance
- **Health Management**: Vaccination schedules, disease management, and drug administration
- **Feed Management**: Track feed consumption, costs, and suppliers
- **Sales Management**: Record sales, manage pricing, and track performance
- **Reporting**: Comprehensive KPI dashboards and analytics
- **User Management**: Role-based access control with permissions

### Technology Stack
- **Backend**: Laravel 12.x (PHP 8.2+)
- **Database**: SQLite (development) / MySQL/PostgreSQL (production)
- **Frontend**: Blade templates with Tailwind CSS
- **API**: Laravel Sanctum for authentication
- **Testing**: PHPUnit with Feature and Unit tests
- **Queue**: Laravel Queue system
- **Caching**: Redis/File-based caching

---

## Architecture

### Clean Architecture Implementation

The application follows Clean Architecture principles with the following structure:

```
app/
├── Domain/                 # Core business logic (entities, value objects)
│   ├── Batch/             # Batch domain
│   ├── Health/            # Health management domain  
│   ├── Sales/             # Sales domain
│   ├── Feed/              # Feed management domain
│   └── Shared/            # Shared domain concepts
├── Application/           # Use cases and application services
│   ├── Services/          # Application services
│   ├── DTOs/              # Data Transfer Objects
│   ├── Events/            # Domain events
│   └── Listeners/         # Event listeners
├── Infrastructure/        # External concerns
│   ├── Repositories/      # Data access layer
│   ├── External/          # Third-party integrations
│   └── Providers/         # Service providers
└── Presentation/          # Web and API controllers
    ├── Http/
    │   ├── Controllers/   # Controllers
    │   ├── Requests/      # Form requests
    │   ├── Resources/     # API resources
    │   └── Middleware/    # Custom middleware
    └── Console/           # Artisan commands
```

### Design Principles

#### 1. SOLID Principles
- **Single Responsibility**: Each class has one reason to change
- **Open/Closed**: Open for extension, closed for modification
- **Liskov Substitution**: Derived classes must be substitutable for base classes
- **Interface Segregation**: Clients shouldn't depend on unused interfaces
- **Dependency Inversion**: Depend on abstractions, not concretions

#### 2. Domain-Driven Design (DDD)
- **Entities**: Objects with identity (Batch, User, etc.)
- **Value Objects**: Immutable objects without identity (Money, DateRange)
- **Aggregates**: Consistency boundaries around related entities
- **Domain Services**: Operations that don't naturally fit in entities
- **Repositories**: Abstract data access

#### 3. Event-Driven Architecture
- **Domain Events**: Represent something important that happened
- **Event Handlers**: React to domain events
- **Asynchronous Processing**: Handle complex operations in background

---

## Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- SQLite/MySQL/PostgreSQL
- Redis (optional, for caching and queues)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd farm-erp
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Build assets**
   ```bash
   npm run dev
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

### Development Setup

1. **Configure your .env file**
   ```env
   APP_NAME="Farm ERP"
   APP_ENV=local
   APP_DEBUG=true
   
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   
   CACHE_DRIVER=file
   QUEUE_CONNECTION=sync
   ```

2. **Run tests**
   ```bash
   php artisan test
   ```

3. **Code quality checks**
   ```bash
   ./vendor/bin/pint  # Laravel Pint for code formatting
   ```

---

## Domain Structure

### 1. Batch Domain

**Purpose**: Manages bird batches throughout their lifecycle

**Key Entities**:
- `Batch`: Main entity representing a group of birds
- `DailyRecord`: Daily performance tracking
- `Stage`: Growth stages (chick, grower, layer, etc.)

**Value Objects**:
- `Population`: Manages bird counts and mortality
- `AgeCalculator`: Calculates age-related metrics

**Domain Services**:
- `BatchLifecycleService`: Manages batch transitions
- `MortalityCalculationService`: Calculates mortality rates
- `PerformanceAnalysisService`: Analyzes batch performance

### 2. Health Domain

**Purpose**: Manages bird health, vaccinations, and disease management

**Key Entities**:
- `VaccineSchedule`: Vaccination planning and tracking
- `DiseaseManagement`: Disease outbreak management
- `VaccinationLog`: Individual vaccination records

**Domain Services**:
- `VaccinationPlanningService`: Creates vaccination schedules
- `DiseaseManagementService`: Manages disease outbreaks
- `HealthReportingService`: Generates health reports

### 3. Sales Domain

**Purpose**: Manages sales operations and pricing

**Key Entities**:
- `SalesRecord`: Individual sales transactions
- `SalesPrice`: Pricing information
- `SalesTeam`: Sales team management

**Domain Services**:
- `PricingService`: Manages pricing strategies
- `SalesAnalysisService`: Analyzes sales performance
- `CommissionCalculationService`: Calculates commissions

### 4. Feed Domain

**Purpose**: Manages feed consumption and costs

**Key Entities**:
- `FeedRecord`: Daily feed consumption
- `FeedType`: Different types of feed
- `Supplier`: Feed suppliers

**Domain Services**:
- `FeedCostCalculationService`: Calculates feed costs
- `ConsumptionAnalysisService`: Analyzes consumption patterns
- `SupplierManagementService`: Manages supplier relationships

---

## Development Guidelines

### Code Style
- Follow PSR-12 coding standards
- Use Laravel Pint for automatic formatting
- Write self-documenting code with clear variable names
- Add type hints for all method parameters and return types

### Naming Conventions
- **Classes**: PascalCase (`BatchService`, `MortalityCalculationService`)
- **Methods**: camelCase (`calculateMortality`, `updateBatchStatus`)
- **Variables**: camelCase (`$batchId`, `$mortalityRate`)
- **Constants**: UPPER_SNAKE_CASE (`MAX_BATCH_SIZE`, `DEFAULT_STAGE_ID`)
- **Database**: snake_case (`batch_id`, `mortality_rate`)

### Documentation Standards
- Add PHPDoc comments for all classes, methods, and properties
- Document complex business logic with inline comments
- Maintain this technical documentation
- Write clear commit messages following conventional commits

### Testing Guidelines
- Write tests for all business logic
- Aim for 80%+ code coverage
- Use descriptive test method names
- Follow AAA pattern (Arrange, Act, Assert)
- Use factories for test data generation

### Error Handling
- Use custom exception classes for domain-specific errors
- Log all exceptions with relevant context
- Provide meaningful error messages to users
- Handle edge cases gracefully

### Performance Guidelines
- Use eager loading for related models
- Implement caching for expensive operations
- Optimize database queries
- Use queues for long-running processes
- Monitor performance with telescope

---

## API Documentation

### Authentication
All API endpoints require authentication using Laravel Sanctum tokens.

```http
Authorization: Bearer {token}
```

### Base URL
```
https://your-domain.com/api/v1
```

### Common Headers
```http
Content-Type: application/json
Accept: application/json
```

### Response Format
All API responses follow a consistent format:

```json
{
  "data": {
    // Response data
  },
  "meta": {
    "current_page": 1,
    "total": 100
  },
  "links": {
    "first": "http://...",
    "last": "http://...",
    "prev": null,
    "next": "http://..."
  }
}
```

### Error Responses
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

---

## Database Design

### Core Principles
- Normalized database design (3NF)
- Consistent foreign key naming
- Comprehensive indexes for performance
- Database constraints for data integrity
- Audit trails for sensitive operations

### Key Tables

#### Batches
```sql
CREATE TABLE batches (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    batch_code VARCHAR(20) UNIQUE NOT NULL,
    bird_type_id BIGINT UNSIGNED NOT NULL,
    breed_id BIGINT UNSIGNED NOT NULL,
    source_farm VARCHAR(255),
    bird_age_days SMALLINT UNSIGNED NOT NULL,
    initial_population INT UNSIGNED NOT NULL,
    current_population INT UNSIGNED NOT NULL,
    date_received DATE NOT NULL,
    hatch_date DATE,
    expected_end_date DATE,
    status ENUM('active', 'completed', 'culled') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_dates (date_received, expected_end_date),
    FOREIGN KEY (bird_type_id) REFERENCES bird_types(id),
    FOREIGN KEY (breed_id) REFERENCES breeds(id)
);
```

#### Daily Records
```sql
CREATE TABLE daily_records (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    batch_id BIGINT UNSIGNED NOT NULL,
    record_date DATE NOT NULL,
    stage_id BIGINT UNSIGNED NOT NULL,
    day_in_stage SMALLINT UNSIGNED NOT NULL,
    alive_count INT UNSIGNED NOT NULL,
    dead_count INT UNSIGNED DEFAULT 0,
    culls_count INT UNSIGNED DEFAULT 0,
    mortality_rate DECIMAL(5,2),
    average_weight_grams INT UNSIGNED,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE KEY unique_batch_date (batch_id, record_date),
    INDEX idx_record_date (record_date),
    FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE,
    FOREIGN KEY (stage_id) REFERENCES stages(id)
);
```

### Database Views
The system includes several database views for reporting:

- `v_farm_kpis`: Overall farm performance metrics
- `v_daily_egg_summary`: Daily egg production summary
- `v_sales_by_salesperson`: Sales performance by salesperson
- `vw_batch_summary`: Comprehensive batch overview
- `vw_batch_daily_performance`: Daily batch performance metrics

---

## Testing Strategy

### Test Structure
```
tests/
├── Feature/           # Integration tests
│   ├── Api/          # API endpoint tests
│   ├── Web/          # Web interface tests
│   └── Services/     # Service integration tests
├── Unit/             # Unit tests
│   ├── Models/       # Model tests
│   ├── Services/     # Service unit tests
│   └── Repositories/ # Repository tests
└── Browser/          # Browser tests (Laravel Dusk)
```

### Test Categories

#### Unit Tests
- Test individual classes in isolation
- Mock dependencies
- Focus on business logic
- Fast execution

#### Integration Tests
- Test component interactions
- Use test database
- Cover user workflows
- Ensure data consistency

#### Feature Tests
- Test complete features
- HTTP request/response testing
- Authentication and authorization
- End-to-end functionality

### Test Data Management
- Use factories for consistent test data
- Database seeding for complex scenarios
- Clean database state between tests
- Realistic test data scenarios

---

## Deployment Guide

### Production Environment Setup

#### Server Requirements
- PHP 8.2+ with required extensions
- Composer
- Web server (Nginx/Apache)
- Database server (MySQL 8.0+/PostgreSQL 13+)
- Redis (recommended for caching and queues)
- SSL certificate

#### Environment Configuration
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farm_erp
DB_USERNAME=username
DB_PASSWORD=password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Deployment Steps
1. Clone repository to production server
2. Install dependencies: `composer install --no-dev --optimize-autoloader`
3. Configure environment variables
4. Run migrations: `php artisan migrate --force`
5. Optimize application: `php artisan optimize`
6. Set proper file permissions
7. Configure web server
8. Set up SSL certificate
9. Configure process manager for queues

#### Performance Optimization
- Enable OPcache
- Configure application caching
- Set up CDN for static assets
- Implement database connection pooling
- Monitor performance metrics

---

## Troubleshooting

### Common Issues

#### Database Connection Errors
**Symptoms**: "SQLSTATE[HY000] [2002] Connection refused"
**Solutions**:
- Check database server status
- Verify connection credentials
- Ensure database exists
- Check firewall settings

#### Permission Errors
**Symptoms**: "Permission denied" errors
**Solutions**:
- Set proper file permissions: `chmod -R 775 storage bootstrap/cache`
- Ensure web server user owns files
- Check SELinux settings if applicable

#### Queue Processing Issues
**Symptoms**: Jobs not processing
**Solutions**:
- Check queue worker status
- Verify queue configuration
- Restart queue workers
- Check failed jobs table

#### Performance Issues
**Symptoms**: Slow response times
**Solutions**:
- Enable query logging to identify slow queries
- Check database indexes
- Implement caching strategies
- Optimize eager loading

### Logging and Monitoring

#### Log Files
- Application logs: `storage/logs/laravel.log`
- Web server logs: `/var/log/nginx/` or `/var/log/apache2/`
- Database logs: Check database-specific locations

#### Monitoring Tools
- Laravel Telescope (development)
- Application performance monitoring (APM) tools
- Database monitoring
- Server resource monitoring

### Debug Mode
Never enable debug mode in production. For debugging production issues:
1. Check log files
2. Use Laravel Tinker for database queries
3. Enable query logging temporarily
4. Use profiling tools

---

## Contributing

### Development Workflow
1. Create feature branch from main
2. Implement changes following guidelines
3. Write/update tests
4. Update documentation
5. Submit pull request
6. Code review process
7. Merge after approval

### Code Review Checklist
- [ ] Code follows style guidelines
- [ ] Tests are included and passing
- [ ] Documentation is updated
- [ ] No security vulnerabilities
- [ ] Performance considerations addressed
- [ ] Error handling implemented

---

## Appendix

### Useful Commands
```bash
# Development
php artisan serve                    # Start development server
php artisan migrate:fresh --seed    # Reset database with seed data
php artisan test                     # Run tests
php artisan queue:work              # Process queues

# Production
php artisan optimize                # Optimize application
php artisan config:cache           # Cache configuration
php artisan route:cache            # Cache routes
php artisan view:cache             # Cache views

# Maintenance
php artisan down                   # Enable maintenance mode
php artisan up                     # Disable maintenance mode
php artisan backup:run             # Run backup
```

### External Resources
- [Laravel Documentation](https://laravel.com/docs)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Domain-Driven Design](https://martinfowler.com/bliki/DomainDrivenDesign.html)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)

---

*This documentation is maintained by the development team. Please keep it updated as the system evolves.*