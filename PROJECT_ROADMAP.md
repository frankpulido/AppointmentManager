# AppointmentManager Project Roadmap

## Project Overview
Laravel 12 API for appointment management serving two frontends:
1. **Public website** - Anonymous visitors schedule appointments via JSON file consumption
2. **Private UI** - Authenticated practitioners/admins manage calendars and appointments

## Core Models
- **User** - Practitioners and admins with role-based access
- **Practitioner** - Healthcare providers
- **Appointment** - Scheduled visits (60min treatment / 90min diagnosis)
- **AvailableTimeSlot** - Available 60-minute treatment slots
- **AvailableTimeSlotDiagnosis** - Available 90-minute diagnosis slots
- **Holiday** - System holidays affecting availability
- **Vacation** - Practitioner-specific unavailable periods

## Implementation Progress

### ✅ Phase 1: Authentication & Authorization (COMPLETE)
- Form Request authorization & role-based filtering
- Authentication endpoint & JSON error responses
- `on_line` attribute implementation
- AvailableTimeSlotSeederService integration
- Queue assessment (determined unnecessary for current scale)

### ✅ Phase 2: feature/slot-seeder (COMPLETE)
- PractitionerAvailableSlotController.seedFromTo() method
- Service creation using AvailableTimeSlotSeederService

### ✅ Phase 3: feature/slot-json-persistance (COMPLETE)
- Observer-triggered JSON file generation
- Strategy pattern for flexible delivery (local/remote API)
- Queue-based regeneration with natural deduplication
- Integration with controllers and observers
- Deployment configuration and troubleshooting

**Jobs Created:**
- `RegenerateTreatmentSlotsJsonJob`
- `RegenerateDiagnosisSlotsJsonJob`

**Strategy System:**
- `SlotJsonDeliveryStrategy` (abstract)
- `LocalFileStrategy` (development/production)
- `RemoteApiStrategy` (cross-server deployment)

### ✅ Phase 4: feature/slot-db-cleaning (COMPLETE)
- Artisan command `slots:clean-past` for daily cleanup
- Laravel scheduling in `/routes/console.php`
- Holiday management command `app:refresh-holidays`

**Commands Created:**
- `CleanPastAvailableSlots` - Daily cleanup at midnight
- `RefreshHolidays` - Yearly holiday seeding and cleanup

### ✅ Phase 5: feature/vacation-management (COMPLETE)
- Vacation model with proper relationships and validation
- VacationObserver for automatic slot management
- PractitionerVacationController with full CRUD operations
- Role-based authorization in form requests
- Automatic slot cleanup on vacation creation
- Smart slot regeneration on vacation deletion using seeder service
- JSON file synchronization after vacation changes
- API routes integration with authentication middleware

**Components Created:**
- `Vacation` model with observer binding
- `VacationObserver` - Handles slot cleanup/regeneration and JSON updates
- `PractitionerVacationController` - Full CRUD with role-based access
- Form validation requests (Store, Update, Delete)
- Migration for vacations table with proper foreign key constraints

## Pending Phases

### Phase 6: User Customization System (PARTIALLY COMPLETE)
**Goal:** Practitioner-specific configurations

**✅ Completed Components:**
- Database schema with `custom_settings` JSON column in `practitioners` table
- Model integration with automatic defaults on practitioner creation
- `getPractitionerSetting()` method for settings retrieval
- `calculateEndTime()` method using practitioner-specific durations
- Service layer integration (seeder, overlap checker, booking validator)
- Auto-populated defaults:
  - `buffer_minutes` (15)
  - `duration_diagnosis` (90)
  - `duration_treatment` (60)
  - `price_diagnosis` (€75)
  - `price_treatment` (€65)
  - `max_days_ahead` (91 days)
  - `treatment_slots` (8 daily slots)
  - `diagnosis_slots` (6 daily slots)
- `specialties` field with array casting

**❌ Pending Implementation:**
- CRUD endpoints for managing `custom_settings`:
  - GET `/superadmin/practitioners/{id}/settings` - Retrieve current settings
  - PUT/PATCH `/superadmin/practitioners/{id}/settings` - Update settings
- Form Request validation for settings changes
- Settings update service with validation logic
- Slot regeneration on settings changes (when treatment/diagnosis slots modified)
- Documentation for settings schema and validation rules

**Note:** Settings management endpoints reserved for superadmin use (Phase 8)

### Phase 7: Subscribable Calendars (iCal/ICS)
**Goal:** Calendar integration for practitioners

**Implementation Plan:**
- Use `spatie/icalendar-generator` package
- Create endpoint: `GET /practitioners/{id}/calendar.ics`
- Generate subscribable calendar feed
- Practitioners subscribe once; phone calendar auto-syncs
- Zero conflict with existing User roles/permissions
- Entirely additive feature (read-only view of existing data)

**Output:**
- Response header: `Content-Type: text/calendar`
- Body: `.ics` content stream
- Auto-updating on mobile devices (Google Calendar, Apple Calendar, Outlook)

### Phase 8: Superadmin Implementation
**Goal:** Multi-tenant management

**Features:**
- Multi-tenant management capabilities
- System-wide administration features
- Advanced user management
- Controls Phase 6 customization endpoints

### Phase 9: Enhanced Authentication
**Goal:** Advanced security

**Features:**
- Advanced security features
- Multi-factor authentication
- Session management improvements
- Superadmin authentication with 2FA

## Additional Features

### Middleware & Authorization
- Role-based access control (admin/practitioner)
- Policies for Appointment and AvailableTimeSlot
- Form Request validation with authorization
- Laravel 12 exception handling via `bootstrap/app.php`

### Phone Numbers
- E.164 international format (+34912345678)
- Max 15 digits + plus sign
- Public website: Backend appends +34 for Spain
- Practitioner UI: Country code selector + local number input
- Store in DB as VARCHAR(16)

### Localization (Future)
- Current: Spanish-only
- Future: EN - ES - CA support
- Use Laravel's localization system
- `resources/lang/{locale}/validation.php`
- Dynamic locale detection from browser/user preferences

### CronJob Scheduling
- **Daily at 00:00:** Clean past available time slots (`slots:clean-past`)
- **Yearly on Jan 1 at 00:05:** Refresh holidays 2 years ahead (`app:refresh-holidays`)

## Technical Architecture

### Design Patterns
- **Observer pattern** - Automatic data consistency across slot tables
- **Strategy pattern** - Flexible deployment (local files, remote API, S3)
- **Queue-based updates** - Natural deduplication for concurrent requests
- **Dependency injection** - Services in controllers for testability

### Security Architecture
- **Public frontend:** Consumes static JSON files (no database access)
- **Private frontend:** Direct database access with authentication
- **Observer pattern:** Maintains data consistency across slot tables

### Performance Optimizations
- Static JSON file generation for public consumption
- Targeted job dispatch based on affected models
- Natural queue deduplication for concurrent updates
- Atomic file operations for consistency

### Deployment Solutions
- Railway compatibility with free tier constraints
- Container vs traditional hosting considerations
- Database separation in build vs runtime phases
- Service provider timing issues resolved

## Environment Configuration

### Development
```env
SLOT_JSON_DELIVERY_STRATEGY=local
SLOT_JSON_ENABLE_BACKUP=true
```

### Production (Railway)
```env
SLOT_JSON_DELIVERY_STRATEGY=remote_api
SLOT_JSON_REMOTE_API_URL=https://frontend.example.com/api/slots
SLOT_JSON_REMOTE_API_KEY=your-api-key-here
```

## Lessons Learned
1. **Service Provider Timing:** Bootstrap sequence affects service binding
2. **Platform Architecture:** Container platforms require different deployment strategies
3. **Observer Pattern:** Effective for maintaining data consistency
4. **Queue Benefits:** Natural handling of concurrent operations
5. **Strategy Pattern:** Provides deployment flexibility without code changes

## Controller Best Practices
- Use Dependency Injection for services
- Move validation to Form Requests
- Keep controller methods focused (accept → validate → call service → return response)
- Extract business logic to services/action classes
- Consider single action controllers for simple endpoints

## Next Steps
Current focus areas based on roadmap:
1. **Phase 6:** User customization system for practitioner-specific settings
2. **Phase 7:** iCal/ICS calendar feed implementation
3. **Phase 8:** Superadmin role and multi-tenant management
4. **Phase 9:** Enhanced authentication with 2FA
