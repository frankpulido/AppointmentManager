# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

AppointmentManager is a Laravel 12 API for healthcare appointment management serving dual frontends:

1. **Public website** - Anonymous visitors schedule appointments via JSON file consumption
2. **Private UI** - Authenticated practitioners/admins manage calendars and appointments

## Development Commands

### Core Laravel Commands
```bash
# Start full development environment (server, queue, logs, vite)
composer dev

# Run tests
composer test
# OR
php artisan test

# Start individual services
php artisan serve                    # Development server (port 8000)
php artisan queue:listen --tries=1  # Queue worker
php artisan pail --timeout=0        # Real-time log viewer
npm run dev                         # Vite development server
```

### Database & Migrations
```bash
php artisan migrate                 # Run migrations
php artisan migrate:fresh --seed   # Fresh database with seeders
php artisan db:seed                 # Run seeders only
```

### Custom Artisan Commands
```bash
# Daily slot cleanup (automatically scheduled)
php artisan slots:clean-past

# Holiday management (automatically scheduled yearly)
php artisan app:refresh-holidays

# Queue management
php artisan queue:work database --queue=json-generation
php artisan queue:failed           # View failed jobs
php artisan queue:retry all        # Retry failed jobs
```

### Development Workflow
```bash
# Lint and format code
./vendor/bin/pint

# Generate application key (first setup)
php artisan key:generate

# Clear caches during development
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Testing Single Components
```bash
# Run specific test file
php artisan test tests/Feature/AppointmentTest.php

# Run specific test method
php artisan test --filter testAppointmentCreation

# Run tests with coverage
php artisan test --coverage
```

## Architecture Overview

### Design Patterns
- **Observer Pattern**: `AppointmentObserver` and `VacationObserver` automatically manage slot availability and JSON file regeneration
- **Strategy Pattern**: `SlotJsonDeliveryStrategy` with `LocalFileStrategy` and `RemoteApiStrategy` for flexible deployment
- **Service Layer**: Business logic encapsulated in dedicated services under `app/Services/`
- **Queue-based Jobs**: `RegenerateTreatmentSlotsJsonJob` and `RegenerateDiagnosisSlotsJsonJob` handle JSON file updates

### Core Models & Relationships
```
User (admin/practitioner roles)
├── Practitioner (healthcare providers)
    ├── Appointment (scheduled visits)
    ├── AvailableTimeSlot (60-min treatment slots)
    ├── AvailableTimeSlotDiagnosis (90-min diagnosis slots)
    ├── Vacation (unavailable periods)
    └── Holiday (system-wide unavailable days)
```

### Key Services
- `AppointmentCreationService`: Handles appointment creation with overlap detection
- `AvailableTimeSlotSeederService`: Generates available time slots
- `IsAlreadyBookedService`: Prevents duplicate bookings per patient
- `CheckAppointmentOverlapService`: Validates appointment timing conflicts

### Authentication & Authorization
- **Laravel Sanctum**: API token authentication
- **Role-based access**: Admin (full access) vs Practitioner (own data only)
- **Form Requests**: Authorization and validation in dedicated request classes
- **Policies**: Located in `app/Policies/` (some pending implementation per roadmap)

### Data Flow
1. **Public appointments**: JSON files consumed by public website (no direct DB access)
2. **Private management**: Direct database access via authenticated API
3. **Observer triggers**: Automatic slot cleanup and JSON regeneration on data changes
4. **Queue processing**: Background jobs for JSON file updates with error handling

### Environment Configuration
```env
# Development
SLOT_JSON_DELIVERY_STRATEGY=local
SLOT_JSON_ENABLE_BACKUP=true

# Production (Railway)
SLOT_JSON_DELIVERY_STRATEGY=remote_api
SLOT_JSON_REMOTE_API_URL=https://frontend.example.com/api/slots
SLOT_JSON_REMOTE_API_KEY=your-api-key-here
```

## Critical Architecture Decisions

### Phone Number Handling
- **Format**: E.164 international format (+34912345678)
- **Storage**: VARCHAR(16) in database
- **Input**: Public website auto-appends +34 for Spain; Practitioner UI has country selector

### Appointment Types & Durations
- **Treatment**: 60 minutes + configurable buffer (default 15 min)
- **Diagnosis**: 90 minutes + configurable buffer
- **Customization**: Practitioner-specific settings via `custom_settings` JSON field

### Security Architecture
- **Public frontend**: Static JSON file consumption (no database credentials)
- **Private frontend**: Authenticated API access with Sanctum
- **Data separation**: Complete isolation between public and private data access

## Development Best Practices

### Controller Patterns
- Use dependency injection for services
- Keep methods focused: validate → call service → return response
- Extract business logic to services/action classes
- Use Form Requests for validation and authorization

### Error Handling
- Custom exceptions: `OverlapException`, `PractitionerCreationException`
- Comprehensive logging in observers and jobs
- Transaction rollbacks for multi-step operations

### Code Standards
- **Type safety**: `declare(strict_types=1)` in all files
- **Attribute casting**: Data sanitization via Eloquent Attribute mutators
- **Relationship definitions**: Proper Eloquent relationships across models

## Known Issues (From Code Review)

### Critical Fixes Needed
1. **AvailableTimeSlot models**: `calculatedEndTime()` method calls need correction
2. **Observer methods**: Empty `updated()` and `deleted()` methods in AppointmentObserver
3. **Phone casting**: Change from integer to string in Practitioner model

### Route Issues
- Several routes use POST instead of proper HTTP verbs (DELETE, PUT)
- See CODE_REVIEW_FINDINGS.md for complete list

## Scheduled Tasks
- **Daily 00:00**: Clean past available time slots
- **Yearly Jan 1 00:05**: Refresh holidays 2 years ahead

## Project Phases

### Completed (Phases 1-5)
- Authentication & Authorization with Sanctum
- Slot seeder service and JSON persistence
- Database cleaning commands
- Vacation management with observers

### Pending Development (Phases 6-9)
- User customization system for practitioner-specific settings
- iCal/ICS subscribable calendars
- Superadmin multi-tenant management
- Enhanced authentication with 2FA

## Environment Setup

### Requirements
- PHP 8.2+
- Laravel 12
- MariaDB/MySQL
- Composer
- Node.js (for Vite)

### First-time Setup
```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
composer dev
```