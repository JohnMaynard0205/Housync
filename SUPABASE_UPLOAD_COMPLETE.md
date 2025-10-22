# 🚀 Supabase Upload Implementation - Complete Guide

## ✅ What Was Updated

All file uploads in the HouSync application now use **Supabase Storage** with comprehensive browser console logging for debugging.

---

## 📦 Updated Upload Locations

### 1. **Apartment Images** ✅
- **Location**: `LandlordController@storeApartment`
- **Files**: Cover image + Gallery images
- **Storage Path**: `house-sync/apartments/`
- **Naming**: `apartment-{timestamp}-{uniqid}.{ext}`
- **Console Log**: 🚀 Supabase Cover Image Upload + 🖼️ Gallery Images

### 2. **Unit Images** ✅
- **Location**: `LandlordController@storeUnit`
- **Files**: Cover image + Gallery images
- **Storage Path**: `house-sync/units/`
- **Naming**: `unit-{timestamp}-{uniqid}.{ext}`
- **Console Log**: 🏠 Supabase Unit Cover Image Upload + 🖼️ Unit Gallery Images

### 3. **Landlord Documents** ✅
- **Location**: `LandlordController@storeRegistration`
- **Files**: Business permits, IDs, certificates (JPG, PNG, PDF)
- **Storage Path**: `house-sync/landlord-documents/`
- **Naming**: `landlord-doc-{landlordId}-{timestamp}-{index}-{uniqid}.{ext}`
- **Console Log**: 📄 Landlord Document {N} ({type})

### 4. **Tenant Documents (Upload)** ✅
- **Location**: `TenantAssignmentController@uploadDocuments`
- **Files**: Valid IDs, proof of income, employment docs (JPG, PNG, PDF)
- **Storage Path**: `house-sync/tenant-documents/`
- **Naming**: `tenant-doc-{assignmentId}-{timestamp}-{index}-{uniqid}.{ext}`
- **Console Log**: 📄 Tenant Document {N} ({type})

### 5. **Tenant Documents (Apply)** ✅
- **Location**: `TenantAssignmentController@apply`
- **Files**: Application documents (JPG, PNG, PDF)
- **Storage Path**: `house-sync/tenant-documents/`
- **Naming**: `tenant-apply-doc-{assignmentId}-{timestamp}-{index}-{uniqid}.{ext}`
- **Console Log**: 📄 Application Document {N} ({type})

---

## 🔧 Required Setup

### 1. **Environment Variables**

Add to your `.env` file:

```env
SUPABASE_URL=https://your-project-id.supabase.co
SUPABASE_KEY=your-anon-public-key-here
SUPABASE_SERVICE_KEY=your-service-role-key-here
```

**Where to find these:**
1. Go to [Supabase Dashboard](https://app.supabase.com/)
2. Select your project
3. Navigate to **Settings** → **API**
4. Copy:
   - Project URL → `SUPABASE_URL`
   - `anon` `public` key → `SUPABASE_KEY`
   - `service_role` key → `SUPABASE_SERVICE_KEY` (⚠️ Keep secret!)

### 2. **Create Storage Bucket**

In your Supabase Dashboard:

1. Go to **Storage** section
2. Click **New bucket**
3. Name it: `house-sync`
4. Set as **Public** bucket (or configure policies below)

### 3. **Configure Storage Policies**

#### Option A: Make Bucket Fully Public (Easiest)

In Supabase SQL Editor, run:

```sql
-- Allow anyone to read files
CREATE POLICY "Allow public reads"
ON storage.objects FOR SELECT
TO public
USING (bucket_id = 'house-sync');

-- Allow authenticated users to upload
CREATE POLICY "Allow authenticated uploads"
ON storage.objects FOR INSERT
TO authenticated
WITH CHECK (bucket_id = 'house-sync');

-- Allow authenticated users to update their files
CREATE POLICY "Allow authenticated updates"
ON storage.objects FOR UPDATE
TO authenticated
USING (bucket_id = 'house-sync');

-- Allow authenticated users to delete their files
CREATE POLICY "Allow authenticated deletes"
ON storage.objects FOR DELETE
TO authenticated
USING (bucket_id = 'house-sync');
```

#### Option B: Service Role Only (More Secure)

```sql
-- Only service role can do everything
CREATE POLICY "Service role has full access"
ON storage.objects FOR ALL
TO service_role
USING (bucket_id = 'house-sync');

-- Public can only read
CREATE POLICY "Public can read"
ON storage.objects FOR SELECT
TO public
USING (bucket_id = 'house-sync');
```

---

## 🧪 How to Test

### 1. **Browser Console Logging**

When you upload files, open **Browser DevTools** (F12) and check the **Console** tab. You'll see:

```javascript
🚀 Supabase Cover Image Upload
  📁 Upload Path: "apartments/apartment-1729598765-abc123.jpg"
  📊 File Info: {
    filename: "apartment-1729598765-abc123.jpg",
    size: 245678,
    mime: "image/jpeg"
  }
  ✅ Upload Result: {
    success: true,
    status_code: 200,
    response: {...},
    url: "https://your-project.supabase.co/storage/v1/object/public/house-sync/apartments/...",
    message: "File uploaded successfully"
  }
  🔗 Public URL: "https://your-project.supabase.co/storage/v1/object/public/house-sync/..."
```

### 2. **Laravel Logs**

Check `storage/logs/laravel.log` for detailed server-side logs:

```
[2025-10-22 12:00:00] local.INFO: Uploading file to Supabase {"bucket":"house-sync","path":"apartments/..."}
[2025-10-22 12:00:00] local.INFO: Attempting Supabase upload {"bucket":"house-sync","path":"...","size":245678}
[2025-10-22 12:00:00] local.INFO: Supabase upload response {"status":200,"body":{...}}
[2025-10-22 12:00:01] local.INFO: Supabase upload result {"result":{"success":true,...}}
```

### 3. **Verify in Supabase Dashboard**

1. Go to **Storage** → **house-sync** bucket
2. Browse the folders:
   - `apartments/` - Apartment images
   - `units/` - Unit images
   - `landlord-documents/` - Landlord registration documents
   - `tenant-documents/` - Tenant application documents
3. Click on any file to see it and get the public URL

### 4. **Test Each Upload Type**

#### Test Apartment Upload:
1. Login as Landlord
2. Go to "Create Apartment"
3. Upload cover image and gallery
4. Check browser console for upload logs
5. Verify apartment shows images correctly

#### Test Unit Upload:
1. Login as Landlord
2. Go to "Create Unit"
3. Upload cover image and gallery
4. Check browser console
5. Verify unit displays images

#### Test Landlord Registration:
1. Register as new landlord
2. Upload required documents (permits, IDs)
3. Check console for document upload logs
4. Admin can verify documents appear in dashboard

#### Test Tenant Application:
1. Login/Register as Tenant
2. Browse properties and apply for a unit
3. Upload required documents
4. Check console logs
5. Landlord can see documents in pending applications

---

## 🐛 Troubleshooting

### ❌ Error: "bucket not found"
**Solution**: Create the `house-sync` bucket in Supabase Storage

### ❌ Error: "new row violates row-level security policy"
**Solution**: Add the storage policies (see Setup section above)

### ❌ Error: 401 Unauthorized
**Solution**: 
- Verify `SUPABASE_SERVICE_KEY` is correct
- Make sure it's the **service_role** key, not anon key
- Check if key is wrapped in quotes in `.env`

### ❌ Upload succeeds but image doesn't display
**Solution**:
- Verify bucket is public
- Check storage policies allow SELECT
- Confirm URL format is correct
- Check browser console for network errors

### ❌ Error: "Failed to read file contents"
**Solution**:
- Check file permissions on upload
- Verify file size is within limits
- Check file type is allowed

### ⚠️ Console logs not showing
**Solution**:
- Open Browser DevTools (F12)
- Check Console tab (not Network or Elements)
- Clear console and try upload again
- Check for JavaScript errors

---

## 📊 Database Storage

All Supabase URLs are stored directly in the database:

- `apartments.cover_image` → Full Supabase URL
- `apartments.gallery` → Array of Supabase URLs (JSON)
- `units.cover_image` → Full Supabase URL
- `units.gallery` → Array of Supabase URLs (JSON)
- `landlord_documents.file_path` → Full Supabase URL
- `tenant_documents.file_path` → Full Supabase URL

**Example URL format:**
```
https://abcdefgh.supabase.co/storage/v1/object/public/house-sync/apartments/apartment-1729598765-abc123.jpg
```

---

## 🔐 Security Best Practices

### ✅ DO:
- Use `SUPABASE_SERVICE_KEY` for server-side uploads
- Keep service key in `.env` file (never commit to git)
- Set appropriate file size limits in validation
- Validate file types before upload
- Use unique filenames to prevent overwrites

### ❌ DON'T:
- Expose service key in client-side code
- Allow unlimited file sizes
- Skip file type validation
- Use predictable filenames
- Store service key in JavaScript

---

## 📝 File Structure

```
house-sync/                          (Supabase Storage Bucket)
├── apartments/
│   ├── apartment-{timestamp}-{uniqid}.jpg
│   └── gallery/
│       └── apartment-gallery-{timestamp}-{index}-{uniqid}.jpg
├── units/
│   ├── unit-{timestamp}-{uniqid}.jpg
│   └── gallery/
│       └── unit-gallery-{timestamp}-{index}-{uniqid}.jpg
├── landlord-documents/
│   └── landlord-doc-{landlordId}-{timestamp}-{index}-{uniqid}.pdf
└── tenant-documents/
    ├── tenant-doc-{assignmentId}-{timestamp}-{index}-{uniqid}.pdf
    └── tenant-apply-doc-{assignmentId}-{timestamp}-{index}-{uniqid}.pdf
```

---

## 🎯 Key Features

### ✅ Unique Filenames
Every file gets a unique name using:
- Timestamp (`time()`)
- Unique ID (`uniqid()`)
- Original extension

### ✅ Browser Console Logging
Real-time feedback with emojis for easy identification:
- 🚀 = Main upload start
- 📁 = File path
- 📊 = File info
- ✅ = Success result
- 🔗 = Public URL
- 🖼️ = Gallery images
- 📄 = Documents
- 🏠 = Units

### ✅ Server Logs
Comprehensive Laravel logging for debugging and auditing

### ✅ Error Handling
- Returns detailed error messages
- Logs failures to Laravel log
- Shows user-friendly error messages
- Transaction rollback on failure

### ✅ Direct URL Storage
Public URLs stored directly in database - no local storage needed

---

## 💡 Tips

1. **Clear old files**: Old files in `storage/app/public/` can be deleted
2. **Monitor usage**: Check Supabase dashboard for storage usage
3. **Set limits**: Configure file size limits in validation rules
4. **Test uploads**: Always check browser console during testing
5. **Check logs**: Use Laravel logs for server-side debugging

---

## 🆘 Need Help?

1. Check **Browser Console** (F12) for client-side errors
2. Check **Laravel Logs** (`storage/logs/laravel.log`) for server errors
3. Check **Supabase Dashboard** → Storage to verify files
4. Check **Supabase Dashboard** → Logs for API errors

---

## ✨ What's Next?

- ✅ All uploads now use Supabase
- ✅ Browser console logging enabled
- ✅ Error handling implemented
- ✅ Laravel logs configured

**Ready to test!** 🎉

