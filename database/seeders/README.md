# Database Seeders

This directory contains database seeders for generating test and development data.

## Available Seeders

### 1. DatabaseSeeder (Main Seeder)
- **File**: `DatabaseSeeder.php`
- **Purpose**: Main seeder that automatically detects environment and runs appropriate seeders
- **Behavior**:
  - In `testing` environment: Runs `TestDataSeeder`
  - In other environments: Runs production/development data seeding

### 2. TestDataSeeder
- **File**: `TestDataSeeder.php`
- **Purpose**: Comprehensive test data generation for all models
- **Creates**:
  - 10 realistic medical centers/clinics (Users)
  - 30-50 medical service offers with varied categories and prices
  - 25 clients with realistic contact information
  - 50 bookings with various statuses and realistic dates
  - Payments for bookings with appropriate statuses

### 3. TestDataSeederCommand
- **File**: `TestDataSeederCommand.php`
- **Purpose**: Enhanced version of TestDataSeeder with statistics and data clearing
- **Features**:
  - Clears existing data before seeding
  - Provides detailed statistics after seeding
  - Shows breakdown by status for bookings and payments

## Usage

### Running Seeders

#### 1. Standard Database Seeding
```bash
# Run main seeder (environment-aware)
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=TestDataSeeder
php artisan db:seed --class=TestDataSeederCommand
```

#### 2. Using the Custom Command
```bash
# Seed test data with statistics
php artisan db:seed-test-data

# Fresh migration + test data seeding
php artisan db:seed-test-data --fresh
```

#### 3. For Testing Environment
When running tests, the `DatabaseSeeder` automatically uses `TestDataSeeder` if the environment is set to `testing`.

### Generated Data Overview

#### Users (Medical Centers)
- Realistic medical center names (e.g., "Metro Healthcare", "Family Medicine Center")
- Professional email addresses
- Secure password hashing

#### Offers (Medical Services)
- Comprehensive medical services (checkups, consultations, diagnostics)
- Realistic descriptions and pricing
- Varied service categories (general, specialized, preventive)

#### Clients
- Realistic names using Faker
- Unique email addresses
- Formatted phone numbers

#### Bookings
- **Statuses**: pending, confirmed, cancelled, completed
- **Dates**: Realistic dates based on status
  - Completed: Past dates (1-60 days ago)
  - Confirmed: Future dates (1-45 days ahead)
  - Pending: Near future (1-30 days ahead)
  - Cancelled: Mix of past and future dates

#### Payments
- **Statuses**: pending, processing, completed, failed, refunded
- **Logic**: Status matches booking status appropriately
- **Timestamps**: Realistic paid_at/failed_at dates
- **Transaction IDs**: Generated for completed payments

## Factory Enhancements

All model factories have been enhanced with realistic data:

### UserFactory
- Generates medical center names with prefixes and specialties
- 70% general medical centers, 30% specialized clinics

### OfferFactory
- 12 different medical service templates
- Realistic pricing ranges for each service type
- Professional medical descriptions

### ClientFactory
- Uses Faker for realistic names and contact information

### BookingFactory
- Status-aware date generation
- Helper methods for specific statuses (pending(), confirmed(), etc.)

### PaymentFactory
- Multiple status states with appropriate timestamps
- Realistic payment intent IDs and transaction IDs

## Best Practices

1. **Testing**: Use `RefreshDatabase` trait in tests to ensure clean state
2. **Development**: Run `php artisan db:seed-test-data --fresh` for clean development data
3. **Production**: Never run test seeders in production environment

## Statistics Example

When using `TestDataSeederCommand`, you'll see output like:

```
🌱 Starting test data seeding...
🧹 Clearing existing data...
📊 Creating test data...
📈 Seeding Statistics:
   Users (Medical Centers): 10
   Offers (Medical Services): 42
   Clients: 25
   Bookings: 50
   Payments: 38
📋 Booking Status Breakdown:
   pending: 12
   confirmed: 15
   cancelled: 8
   completed: 15
💳 Payment Status Breakdown:
   pending: 8
   completed: 22
   failed: 5
   processing: 3
✅ Test data seeding completed successfully!
```
