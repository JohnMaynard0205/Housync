# RFID Database Normalization Guide

## Overview

This document explains the database normalization performed to separate RFID card management from tenant assignments, improving data integrity and system flexibility.

## Problem with Previous Structure

The original `rfid_cards` table contained a direct foreign key to `tenant_assignments`, which created several issues:

1. **Tight Coupling**: RFID cards were directly tied to tenant assignments
2. **Limited Reusability**: Cards couldn't be easily reassigned to different tenants
3. **No Historical Tracking**: No record of previous card assignments
4. **Data Integrity Issues**: Deleting tenant assignments could affect card data

## New Normalized Structure

### 1. `rfid_cards` Table (Physical Cards)
```sql
- id (Primary Key)
- card_uid (Unique card identifier)
- landlord_id (Who manages the card)
- apartment_id (Which building)
- card_name (Optional descriptive name)
- status (active, inactive, lost, stolen)
- issued_at (When card was created)
- expires_at (Physical card expiration)
- notes (General card notes)
- timestamps
```

### 2. `tenant_rfid_assignments` Table (Junction Table)
```sql
- id (Primary Key)
- rfid_card_id (Foreign key to rfid_cards)
- tenant_assignment_id (Foreign key to tenant_assignments)
- assigned_at (When assignment was made)
- expires_at (Assignment expiration)
- status (active, inactive, revoked)
- notes (Assignment-specific notes)
- timestamps
```

## Benefits of Normalization

1. **Separation of Concerns**: Physical cards and tenant assignments are separate entities
2. **Card Reusability**: Cards can be reassigned without losing history
3. **Historical Tracking**: Complete audit trail of card assignments
4. **Better Data Integrity**: Cleaner relationships and constraints
5. **Flexibility**: Cards can exist without being assigned to anyone

## Migration Process

### Step 1: Create New Table
```bash
php artisan migrate --path=database/migrations/2025_10_07_120000_create_tenant_rfid_assignments_table.php
```

### Step 2: Migrate Existing Data and Normalize
```bash
php artisan migrate --path=database/migrations/2025_10_07_120001_normalize_rfid_cards_table.php
```

### Step 3: Update Access Logs
```bash
php artisan migrate --path=database/migrations/2025_10_07_120002_update_access_logs_for_normalized_rfid.php
```

## Model Changes

### RfidCard Model
- **Removed**: Direct `tenant_assignment_id` relationship
- **Added**: `tenantRfidAssignments()` - hasMany relationship
- **Added**: `activeTenantAssignment()` - current active assignment
- **Updated**: Access control methods to use normalized structure

### TenantAssignment Model
- **Updated**: `rfidCards()` - now uses hasManyThrough
- **Added**: `tenantRfidAssignments()` - direct assignments
- **Added**: `activeRfidCards()` - currently assigned cards

### New TenantRfidAssignment Model
- **Purpose**: Manages the many-to-many relationship between cards and tenants
- **Features**: Status tracking, expiration dates, assignment history

## Controller Updates

### RfidController Changes
1. **Card Creation**: Now creates both card and assignment records
2. **Access Verification**: Uses normalized structure for permission checks
3. **Logging**: Updated to reference correct tenant assignments
4. **Queries**: Updated to use new relationship structure

## API Compatibility

The API endpoints remain the same, but internal logic has been updated:

- `POST /api/rfid/verify` - Still works with card UIDs
- `POST /api/rfid/scan/direct` - Updated to use normalized structure
- All existing endpoints maintain backward compatibility

## Database Queries

### Get Active Cards for a Tenant
```php
$tenant = TenantAssignment::find($id);
$activeCards = $tenant->activeRfidCards;
```

### Get Current Tenant for a Card
```php
$card = RfidCard::find($id);
$currentTenant = $card->activeTenantAssignment?->tenantAssignment;
```

### Assign Card to New Tenant
```php
// Revoke current assignment
$card->tenantRfidAssignments()->where('status', 'active')->update(['status' => 'revoked']);

// Create new assignment
TenantRfidAssignment::create([
    'rfid_card_id' => $card->id,
    'tenant_assignment_id' => $newTenantId,
    'assigned_at' => now(),
    'status' => 'active'
]);
```

## Testing the Migration

### Before Migration
1. Note down current RFID card assignments
2. Export access logs for verification
3. Backup database

### After Migration
1. Verify all cards have corresponding assignments in `tenant_rfid_assignments`
2. Test card access functionality
3. Verify access logs are properly linked
4. Test card reassignment functionality

## Rollback Process

If needed, the migration can be rolled back:

```bash
php artisan migrate:rollback --step=3
```

This will:
1. Restore `tenant_assignment_id` column to `rfid_cards` table
2. Migrate data back from `tenant_rfid_assignments`
3. Drop the new junction table

## Performance Considerations

1. **Indexes**: Added appropriate indexes on junction table
2. **Eager Loading**: Updated queries to use proper eager loading
3. **Caching**: Consider caching active assignments for frequently accessed cards

## Security Improvements

1. **Audit Trail**: Complete history of card assignments
2. **Granular Control**: Individual assignment status management
3. **Expiration Handling**: Separate expiration for cards vs assignments
4. **Access Logging**: More detailed access attempt tracking

## Future Enhancements

With this normalized structure, you can now easily implement:

1. **Multiple Cards per Tenant**: A tenant can have multiple active cards
2. **Temporary Access**: Time-limited card assignments
3. **Card Sharing**: Cards shared between multiple tenants (with proper controls)
4. **Advanced Reporting**: Detailed usage analytics and audit reports
5. **Card Pools**: Manage pools of unassigned cards for quick deployment

## Troubleshooting

### Common Issues

1. **Missing Assignments**: If cards show as unassigned, check the migration logs
2. **Access Denied**: Verify the card has an active assignment in the junction table
3. **Performance**: Ensure proper eager loading is used in queries

### Verification Queries

```sql
-- Check for cards without active assignments
SELECT rc.* FROM rfid_cards rc 
LEFT JOIN tenant_rfid_assignments tra ON rc.id = tra.rfid_card_id AND tra.status = 'active'
WHERE tra.id IS NULL;

-- Check assignment history for a card
SELECT * FROM tenant_rfid_assignments 
WHERE rfid_card_id = ? 
ORDER BY assigned_at DESC;

-- Verify access logs are properly linked
SELECT COUNT(*) FROM access_logs 
WHERE rfid_card_id IS NOT NULL AND tenant_assignment_id IS NULL;
```

## Support

If you encounter any issues during or after the migration, please:

1. Check the migration logs for errors
2. Verify database integrity using the provided queries
3. Test the API endpoints to ensure functionality
4. Review the updated model relationships

The normalization provides a solid foundation for future RFID system enhancements while maintaining backward compatibility with existing functionality.
