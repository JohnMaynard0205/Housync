# 🧪 Testing Auto-Sync - Quick Guide

## ✅ Quick Test Commands

### Test 1: Check Current Property Count
```bash
cd "d:\apache and php\phpsite\Capstone\Housync"
php artisan tinker
```

Then run:
```php
// Count current properties
\App\Models\Property::count();

// Exit tinker
exit
```

### Test 2: Create a Test Unit (Auto-Sync Will Trigger!)
```bash
php artisan tinker
```

Then run:
```php
// Get first apartment
$apartment = \App\Models\Apartment::first();

// Create a test unit
$unit = \App\Models\Unit::create([
    'unit_number' => 'TEST-AUTO-SYNC',
    'apartment_id' => $apartment->id,
    'unit_type' => 'apartment',
    'rent_amount' => 18000.00,
    'status' => 'available',
    'bedrooms' => 2,
    'bathrooms' => 1,
    'floor_area' => 50,
    'is_furnished' => true,
    'description' => 'Test unit for auto-sync verification'
]);

// Check if property was auto-created
\App\Models\Property::where('title', 'LIKE', '%TEST-AUTO-SYNC%')->count();
// Should return 1 if auto-sync works!

exit
```

### Test 3: Update the Test Unit (Auto-Sync Will Trigger!)
```bash
php artisan tinker
```

```php
// Find the test unit
$unit = \App\Models\Unit::where('unit_number', 'TEST-AUTO-SYNC')->first();

// Update the rent amount
$unit->update(['rent_amount' => 25000.00]);

// Check if property was updated
$property = \App\Models\Property::where('title', 'LIKE', '%TEST-AUTO-SYNC%')->first();
$property->price;
// Should show 25000.00 if auto-sync works!

exit
```

### Test 4: Delete the Test Unit (Cleanup)
```bash
php artisan tinker
```

```php
// Find and delete the test unit
$unit = \App\Models\Unit::where('unit_number', 'TEST-AUTO-SYNC')->first();
$unit->delete();

// Check if property was soft-deleted
$property = \App\Models\Property::withTrashed()->where('title', 'LIKE', '%TEST-AUTO-SYNC%')->first();
$property->deleted_at;
// Should show a timestamp if auto-sync works!

// Permanent cleanup
$property->forceDelete();

exit
```

---

## 🎯 Visual Test (Easiest Way!)

1. **Login as landlord**
2. **Go to: My Units**
3. **Create a new unit** with any details
4. **Open new tab: /explore**
5. **Search for your new unit**
6. **Result**: Should appear immediately! ✨

Then:
7. **Go back and edit the unit** (change rent amount)
8. **Refresh explore page**
9. **Result**: Price should update instantly! ✨

---

## 📊 Verification Checklist

- [ ] Creating a unit → Property appears on explore
- [ ] Updating unit rent → Property price updates
- [ ] Updating unit status → Property availability changes
- [ ] Editing apartment name → All unit titles update
- [ ] Deleting unit → Property disappears from explore

---

## 🔥 If Auto-Sync Isn't Working

Run these commands:
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Re-register observers
php artisan optimize:clear
```

Then try the tests again!

---

**Auto-sync is now active and running!** 🚀

