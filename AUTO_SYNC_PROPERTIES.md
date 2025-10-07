# 🔄 Auto-Sync Properties System

## ✨ What This Does

Your Units and Apartments now **automatically sync** with the Explore Properties page!

Whenever you:
- ✅ Create a new unit → Automatically appears on explore page
- ✅ Update a unit → Changes reflect on explore page immediately
- ✅ Delete a unit → Removed from explore page
- ✅ Update an apartment → All its units update on explore page

**No manual syncing needed!** Everything happens automatically in the background.

---

## 🎯 How It Works

### **Observers Watching Your Data**

Two observers are constantly monitoring your database:

#### **1. UnitObserver** 
Watches for changes to units and syncs them to properties.

**Actions:**
- **Created**: When a new unit is added → Creates a new property listing
- **Updated**: When unit details change → Updates the property listing
- **Deleted**: When a unit is deleted → Removes from explore page
- **Restored**: When a unit is restored → Property reappears

#### **2. ApartmentObserver**
Watches for changes to apartments and updates all related properties.

**Actions:**
- **Updated**: When apartment info changes → Updates all unit properties
  - Address changes → All units get new address
  - Name changes → All unit titles update
  - Image changes → Units without images get apartment image
- **Deleted**: When apartment is deleted → All unit properties removed
- **Restored**: When apartment is restored → All properties reappear

---

## 📋 What Gets Synced

### From Unit:
- ✅ Unit number
- ✅ Bedrooms
- ✅ Bathrooms
- ✅ Rent amount (price)
- ✅ Floor area (square meters)
- ✅ Description
- ✅ Status (available/occupied)
- ✅ Cover image
- ✅ Amenities
- ✅ Unit type → Property type

### From Apartment:
- ✅ Apartment name
- ✅ Address
- ✅ City (extracted from address)
- ✅ Landlord ID
- ✅ Status (active/inactive)
- ✅ Cover image (fallback if unit has none)
- ✅ Description (fallback if unit has none)
- ✅ Amenities (merged with unit amenities)

---

## 🎨 Property Type Mapping

Unit types are automatically mapped to property types:

| Unit Type | → | Property Type |
|-----------|---|---------------|
| Studio | → | Studio |
| 1-Bedroom | → | Apartment |
| 2-Bedroom | → | Apartment |
| 3-Bedroom | → | Apartment |
| Apartment | → | Apartment |
| Condo | → | Condo |
| House | → | House |

---

## 🔍 Examples

### Example 1: Creating a New Unit

**Landlord Action:**
```
Creates: "Sunset Apartments - Unit 09"
- Bedrooms: 2
- Bathrooms: 1
- Rent: ₱20,000
- Status: Available
```

**What Happens Automatically:**
✅ New property appears on explore page immediately
✅ Title: "Sunset Apartments - Unit 09"
✅ Shows as available
✅ All details copied
✅ Searchable and filterable

### Example 2: Updating Unit Status

**Landlord Action:**
```
Changes: Unit 01 status from "Available" to "Occupied"
```

**What Happens Automatically:**
✅ Property on explore page updates to "Occupied"
✅ Badge changes from green to red
✅ Still visible but shows as occupied

### Example 3: Updating Apartment Address

**Landlord Action:**
```
Changes: Sunset Apartments address
From: "123 Old Street, Manila"
To: "456 New Avenue, Quezon City"
```

**What Happens Automatically:**
✅ All 8 units get new address
✅ All properties on explore page update
✅ City filter updates to "Quezon City"
✅ All changes happen instantly

### Example 4: Deleting a Unit

**Landlord Action:**
```
Deletes: Unit 05
```

**What Happens Automatically:**
✅ Property removed from explore page
✅ Soft deleted (can be restored)
✅ No longer appears in search results

---

## 🧪 Testing the Auto-Sync

### Test 1: Create a New Unit
1. Go to landlord dashboard
2. Create a new unit
3. Visit `/explore`
4. **Result**: New unit appears immediately! ✨

### Test 2: Update Unit Price
1. Edit an existing unit
2. Change the rent amount
3. Visit `/explore`
4. **Result**: Price updates immediately! ✨

### Test 3: Change Apartment Name
1. Edit an apartment
2. Change its name
3. Visit `/explore`
4. **Result**: All unit titles update! ✨

---

## 🛠️ Technical Details

### Files Created:
1. **`app/Observers/UnitObserver.php`** - Handles unit changes
2. **`app/Observers/ApartmentObserver.php`** - Handles apartment changes
3. **`app/Providers/AppServiceProvider.php`** - Registers observers

### Database Tables:
- **`units`** → Source of truth for unit data
- **`apartments`** → Source of truth for apartment data
- **`properties`** → Mirror/cache for explore page
- **`property_amenity`** → Amenity relationships

### Event Flow:
```
Unit Created/Updated
    ↓
UnitObserver Triggered
    ↓
Syncs to Property Table
    ↓
Explore Page Shows Changes
    ↓
No Manual Sync Needed! ✨
```

---

## 🎯 Benefits

### For Landlords:
✅ No extra work required
✅ Changes appear instantly
✅ One place to manage properties
✅ Automatic consistency

### For Tenants:
✅ Always see up-to-date listings
✅ Real-time availability
✅ Accurate information
✅ Better search experience

### For Admins:
✅ No manual sync needed
✅ Data consistency guaranteed
✅ Less maintenance work
✅ Automatic updates

---

## 📊 Performance

**Impact**: Minimal - Observers run only when data changes
**Speed**: Instant - Updates happen in milliseconds
**Reliability**: High - Uses Laravel's built-in event system
**Scalability**: Excellent - Handles any number of units

---

## 🔒 Data Integrity

### Safeguards:
- ✅ Soft deletes (properties can be restored)
- ✅ Unique slugs (no duplicates)
- ✅ Validation on sync
- ✅ Graceful error handling
- ✅ Fallback values for missing data

### What Happens If:

**Unit has no apartment?**
- Skips sync gracefully

**Apartment is deleted?**
- All unit properties soft deleted
- Can be restored if apartment is restored

**Invalid data in unit?**
- Uses fallback/default values
- Logs error but doesn't break

---

## 🚀 Manual Sync (If Needed)

If you ever need to manually sync (after data import, etc.):

```bash
# Sync all units to properties
php artisan migrate:units-to-properties

# Fresh sync (clears existing properties first)
php artisan migrate:units-to-properties --fresh
```

**But with auto-sync, you rarely need this!** 🎉

---

## 💡 Future Enhancements

Possible additions:
- [ ] Sync tenant reviews/ratings
- [ ] Sync property images gallery
- [ ] Sync maintenance history
- [ ] Sync availability calendar
- [ ] Real-time websocket updates

---

## 📝 Summary

**Before Auto-Sync:**
1. Create unit in landlord dashboard
2. Run manual sync command
3. Property appears on explore page

**After Auto-Sync:**
1. Create unit in landlord dashboard
2. ✨ **Done!** Property appears instantly!

---

## 🎉 You're All Set!

Your properties now automatically sync to the explore page.

**Just manage your units normally and everything updates automatically!** 🏠✨

No extra steps. No manual syncing. Just works! 🚀

