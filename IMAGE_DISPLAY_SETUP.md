# 🖼️ Server-Side Image Display Setup

## ✅ What Was Implemented

All images and documents now work seamlessly with **both Supabase URLs and local storage paths**.

---

## 🔧 Changes Made

### **1. Created Image URL Helper**

**File**: `app/Helpers/image.php`

Created two helper functions:
- `image_url($path)` - Handles both Supabase URLs and local paths
- `document_url($path)` - Alias for document files

**How it works:**
```php
// If Supabase URL (starts with http/https) → return as is
// If local path → convert to asset('storage/' . $path)
```

### **2. Model Accessors Updated**

✅ **Apartment Model** (`app/Models/Apartment.php`)
- `getCoverImageUrlAttribute()` - Already handles Supabase URLs
- `getGalleryUrlsAttribute()` - Already handles Supabase URLs

✅ **Unit Model** (`app/Models/Unit.php`)
- `getCoverImageUrlAttribute()` - Already handles Supabase URLs
- `getGalleryUrlsAttribute()` - Already handles Supabase URLs

✅ **Property Model** (`app/Models/Property.php`)
- `getImageUrlAttribute()` - Already handles Supabase URLs

### **3. View Files Updated**

Updated all document viewing to use `document_url()` helper:

✅ **Landlord Assignment Details**
- File: `resources/views/landlord/assignment-details.blade.php`
- Changed: `asset('storage/' . $document->file_path)` → `document_url($document->file_path)`

✅ **Tenant Dashboard**
- File: `resources/views/tenant/dashboard.blade.php`
- Changed: `asset('storage/' . $document->file_path)` → `document_url($document->file_path)`

✅ **Super Admin - Landlord Document Review**
- File: `resources/views/super-admin/review-landlord-docs.blade.php`
- Changed: `asset('storage/' . $doc->file_path)` → `document_url($doc->file_path)`

---

## 📊 How It Works Now

### **For New Uploads (Supabase)**

1. File uploaded to Supabase
2. Full URL stored in database:
   ```
   https://your-project.supabase.co/storage/v1/object/public/house-sync/apartments/...
   ```
3. `document_url()` detects it starts with `https://`
4. Returns URL as-is ✅

### **For Old Files (Local Storage)**

1. File path stored in database:
   ```
   apartment-covers/image.jpg
   ```
2. `document_url()` detects it's NOT a full URL
3. Converts to: `asset('storage/apartment-covers/image.jpg')`
4. Returns full local URL ✅

---

## 🎯 Benefits

✅ **Backward Compatible** - Old local files still work
✅ **Forward Compatible** - New Supabase files work automatically
✅ **No Breaking Changes** - Seamless transition
✅ **Consistent API** - Same helper for all file types
✅ **Easy to Use** - Just call `document_url($path)` or `image_url($path)`

---

## 📝 Usage Examples

### **In Blade Templates**

```blade
{{-- For images --}}
<img src="{{ image_url($apartment->cover_image) }}" alt="Cover">

{{-- For documents --}}
<a href="{{ document_url($document->file_path) }}" target="_blank">
    View Document
</a>

{{-- For JavaScript --}}
<button onclick="viewImage('{{ document_url($document->file_path) }}')">
    View
</button>
```

### **Model Accessors Already Use This Logic**

```blade
{{-- These already work with both Supabase and local paths --}}
<img src="{{ $apartment->cover_image_url }}" alt="Cover">
<img src="{{ $unit->cover_image_url }}" alt="Unit">
<img src="{{ $property->image_url }}" alt="Property">

@foreach($apartment->gallery_urls as $imageUrl)
    <img src="{{ $imageUrl }}" alt="Gallery">
@endforeach
```

---

## 🔍 Where Images/Documents Are Displayed

### **Apartment & Unit Images**
- ✅ Explore page (`resources/views/explore.blade.php`)
- ✅ Property details (`resources/views/property-details.blade.php`)
- ✅ Property cards (`resources/views/partials/property-cards.blade.php`)
- ✅ Landlord apartments (`resources/views/landlord/apartments.blade.php`)
- ✅ Landlord units (`resources/views/landlord/units.blade.php`)

### **Tenant Documents**
- ✅ Landlord assignment details (`resources/views/landlord/assignment-details.blade.php`)
- ✅ Tenant dashboard (`resources/views/tenant/dashboard.blade.php`)

### **Landlord Documents**
- ✅ Super admin document review (`resources/views/super-admin/review-landlord-docs.blade.php`)
- ✅ Admin pending landlords (`resources/views/super-admin/pending-landlords.blade.php`)

---

## 🧪 Testing

### **Test Supabase Images**

1. Login as landlord
2. Create new apartment with cover image
3. Upload image - should upload to Supabase
4. Check browser console for upload logs
5. Verify image displays correctly in:
   - Landlord dashboard
   - Explore page (if property created)
   - Property details page

### **Test Documents**

1. **Tenant Documents:**
   - Login as tenant
   - Upload documents
   - Check in tenant dashboard - should display
   - Landlord should see documents in assignment details

2. **Landlord Documents:**
   - Register as new landlord
   - Upload business documents
   - Admin should see documents in pending landlords

### **Test Legacy Files (if any)**

1. If you have old local storage files
2. They should still display using `asset('storage/...')`
3. No broken images

---

## 📦 File Structure

```
Housync/
├── app/
│   ├── Helpers/
│   │   ├── supabase.php          (Supabase service helper)
│   │   └── image.php              (NEW - Image URL helper)
│   ├── Models/
│   │   ├── Apartment.php          (Has cover_image_url accessor)
│   │   ├── Unit.php               (Has cover_image_url accessor)
│   │   └── Property.php           (Has image_url accessor)
│   └── Services/
│       └── SupabaseService.php    (Supabase upload service)
└── resources/
    └── views/
        ├── landlord/
        │   └── assignment-details.blade.php  (Updated)
        ├── tenant/
        │   └── dashboard.blade.php            (Updated)
        └── super-admin/
            └── review-landlord-docs.blade.php (Updated)
```

---

## 🚀 Next Steps

1. ✅ Helper created and registered
2. ✅ Views updated to use helper
3. ✅ Model accessors already handle both URL types
4. ✅ Backward compatibility maintained

**You're all set!** Images and documents will now display correctly whether they're stored in Supabase or local storage.

---

## 💡 Tips

- **New files** automatically go to Supabase (after setup)
- **Old files** remain in local storage and still work
- **No migration needed** - both systems work simultaneously
- Use **`document_url()`** or **`image_url()`** helper for all file paths
- Model accessors (like `$apartment->cover_image_url`) handle this automatically

---

## 🐛 Troubleshooting

### Images not showing?

1. **Check browser console** for 404 errors
2. **Check the URL** - is it Supabase or local?
3. **For Supabase**: Verify bucket is public and policies are set
4. **For local**: Verify file exists in `storage/app/public/`

### Documents not opening?

1. **Check file_path** in database - full URL or relative path?
2. **Verify helper is working** - add `{{ document_url($path) }}` to see output
3. **Check Supabase CORS** if getting cross-origin errors

---

**All systems ready for display! 🎉**

