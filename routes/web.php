<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\TenantAssignmentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RfidController;
use App\Models\Apartment;
use App\Models\Unit;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', function () {
    return view('register');
})->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Public Explore page (landing-like listings) - New Property System
Route::get('/explore', [App\Http\Controllers\ExploreController::class, 'index'])->name('explore');
Route::get('/property/{slug}', [App\Http\Controllers\ExploreController::class, 'show'])->name('property.show');

// Landlord Registration (public)
Route::get('/landlord/register', [LandlordController::class, 'register'])->name('landlord.register');
Route::post('/landlord/register', [LandlordController::class, 'storeRegistration'])->name('landlord.register.store');

// Landlord status pages
Route::get('/landlord/pending', [LandlordController::class, 'pending'])->name('landlord.pending');
Route::get('/landlord/rejected', [LandlordController::class, 'rejected'])->name('landlord.rejected');

// Super Admin Routes
Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::get('/pending-landlords', [SuperAdminController::class, 'pendingLandlords'])->name('pending-landlords');
    Route::post('/approve-landlord/{id}', [SuperAdminController::class, 'approveLandlord'])->name('approve-landlord');
    Route::post('/reject-landlord/{id}', [SuperAdminController::class, 'rejectLandlord'])->name('reject-landlord');
    Route::get('/landlords/{id}/documents', [SuperAdminController::class, 'reviewLandlordDocuments'])->name('landlord-docs');
    Route::post('/landlord-documents/{docId}/verify', [SuperAdminController::class, 'verifyLandlordDocument'])->name('verify-landlord-document');
    Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('create-user');
    Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('store-user');
    Route::get('/users/{id}/edit', [SuperAdminController::class, 'editUser'])->name('edit-user');
    Route::put('/users/{id}', [SuperAdminController::class, 'updateUser'])->name('update-user');
    Route::delete('/users/{id}', [SuperAdminController::class, 'deleteUser'])->name('delete-user');
    Route::get('/apartments', [SuperAdminController::class, 'apartments'])->name('apartments');
});

// Landlord Routes
Route::middleware(['role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/dashboard', [LandlordController::class, 'dashboard'])->name('dashboard');
    Route::get('/apartments', [LandlordController::class, 'apartments'])->name('apartments');
    Route::get('/apartments/create', [LandlordController::class, 'createApartment'])->name('create-apartment');
    Route::post('/apartments', [LandlordController::class, 'storeApartment'])->name('store-apartment');
    Route::get('/apartments/{id}/edit', [LandlordController::class, 'editApartment'])->name('edit-apartment');
    Route::put('/apartments/{id}', [LandlordController::class, 'updateApartment'])->name('update-apartment');
    Route::delete('/apartments/{id}', [LandlordController::class, 'deleteApartment'])->name('delete-apartment');
    
    // Create Unit routes must come BEFORE parameterized units route to avoid "create" being captured as {apartmentId}
    Route::get('/units/create', [LandlordController::class, 'createUnit'])->name('create-unit');
    Route::get('/apartments/{apartmentId}/units/create', [LandlordController::class, 'createUnit'])->name('create-unit-for-apartment')->whereNumber('apartmentId');
    Route::post('/apartments/{apartmentId}/units', [LandlordController::class, 'storeUnit'])->name('store-unit')->whereNumber('apartmentId');
    Route::get('/units/{apartmentId?}', [LandlordController::class, 'units'])->name('units')->whereNumber('apartmentId');
    
    // Bulk Unit Generation Route
    Route::post('/units/bulk-generate', [LandlordController::class, 'bulkGenerateUnits'])->name('bulk-generate-units');
    
    // Unit Update Route (for AJAX modal)
    Route::put('/units/{id}', [LandlordController::class, 'updateUnit'])->name('update-unit')->whereNumber('id');
    
    // Unit Delete Route
    Route::delete('/units/{id}', [LandlordController::class, 'deleteUnit'])->name('delete-unit')->whereNumber('id');
    
    // Tenant Assignment Routes
    Route::get('/tenant-assignments', [TenantAssignmentController::class, 'index'])->name('tenant-assignments');
    // Standalone assign-tenant page removed; use modal in tenant-assignments instead
    Route::post('/units/{unitId}/assign-tenant', [TenantAssignmentController::class, 'store'])->name('store-tenant-assignment');
    Route::get('/tenant-assignments/{id}', [TenantAssignmentController::class, 'show'])->name('assignment-details');
    Route::put('/tenant-assignments/{id}/status', [TenantAssignmentController::class, 'updateStatus'])->name('update-assignment-status');
    Route::post('/tenant-assignments/{id}/reassign', [TenantAssignmentController::class, 'reassign'])->name('reassign-tenant');
    Route::delete('/tenant-assignments/{id}', [TenantAssignmentController::class, 'destroy'])->name('delete-tenant-assignment');
    Route::post('/tenant-assignments/{id}/verify-documents', [TenantAssignmentController::class, 'verifyDocuments'])->name('verify-documents');
    Route::post('/documents/{documentId}/verify', [TenantAssignmentController::class, 'verifyIndividualDocument'])->name('verify-individual-document');
    Route::get('/tenant-assignments/{id}/credentials', [TenantAssignmentController::class, 'getCredentials'])->name('get-credentials');
    Route::get('/available-units', [TenantAssignmentController::class, 'getAvailableUnits'])->name('available-units');
    Route::get('/download-document/{documentId}', [TenantAssignmentController::class, 'downloadDocument'])->name('download-document');
    
    // Staff Management Routes
    Route::get('/staff', [StaffController::class, 'index'])->name('staff');
    Route::post('/staff/add', [StaffController::class, 'addStaff'])->name('add-staff');
    Route::get('/staff/by-type/{staffType}', [StaffController::class, 'getStaffByType'])->name('staff-by-type');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('create-staff');
    Route::get('/units/{unitId}/assign-staff', [StaffController::class, 'create'])->name('assign-staff');
    Route::post('/staff', [StaffController::class, 'store'])->name('store-staff');
    Route::get('/staff/{id}', [StaffController::class, 'show'])->name('staff-details');
    Route::put('/staff/{id}/status', [StaffController::class, 'updateStatus'])->name('update-staff-status');
    Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('delete-staff');
    Route::get('/staff/{id}/credentials', [StaffController::class, 'getCredentials'])->name('get-staff-credentials');
    
    // Tenant History Routes
    Route::get('/tenant-history', [LandlordController::class, 'tenantHistory'])->name('tenant-history');
    Route::get('/tenant-history/export-csv', [LandlordController::class, 'exportTenantHistoryCSV'])->name('tenant-history.export-csv');
    
    // RFID Security Management Routes
    Route::get('/security', [RfidController::class, 'index'])->name('security');
    Route::get('/security/cards/create', [RfidController::class, 'create'])->name('security.create-card');
    Route::post('/security/cards', [RfidController::class, 'store'])->name('security.store-card');
    Route::get('/security/cards/{id}', [RfidController::class, 'show'])->name('security.card-details');
    Route::put('/security/cards/{id}/toggle-status', [RfidController::class, 'toggleStatus'])->name('security.toggle-card-status');
    Route::get('/security/access-logs', [RfidController::class, 'accessLogs'])->name('security.access-logs');
    
    // API endpoints for apartment management
    Route::get('/apartments/{id}/details', [LandlordController::class, 'getApartmentDetails'])->name('apartment-details')->whereNumber('id');
    Route::get('/apartments/{id}/units', [LandlordController::class, 'getApartmentUnits'])->name('apartment-units')->whereNumber('id');
    Route::get('/units/{id}/details', [LandlordController::class, 'getUnitDetails'])->name('unit-details')->whereNumber('id');
    // Keep JSON API separate from form POST to avoid route conflicts with landlord.store-unit
    Route::post('/apartments/{apartmentId}/units/json', [LandlordController::class, 'storeApartmentUnit'])->name('store-apartment-unit-json')->whereNumber('apartmentId');
});

// Original dashboard route - redirect based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    
    switch ($user->role) {
        case 'super_admin':
            return redirect()->route('super-admin.dashboard');
        case 'landlord':
            if ($user->status === 'approved') {
                return redirect()->route('landlord.dashboard');
            } elseif ($user->status === 'pending') {
                return redirect()->route('landlord.pending');
            } else {
                return redirect()->route('landlord.rejected');
            }
        case 'tenant':
            return redirect()->route('tenant.dashboard');
        case 'staff':
            return redirect()->route('staff.dashboard');
        default:
            return redirect()->route('login');
    }
})->middleware('auth')->name('dashboard');

// Tenant Routes
Route::middleware(['role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [TenantAssignmentController::class, 'tenantDashboard'])->name('dashboard');
    Route::get('/upload-documents', [TenantAssignmentController::class, 'uploadDocuments'])->name('upload-documents');
    Route::post('/upload-documents', [TenantAssignmentController::class, 'storeDocuments'])->name('store-documents');
    Route::get('/download-document/{documentId}', [TenantAssignmentController::class, 'downloadDocument'])->name('download-document');
    Route::delete('/delete-document/{documentId}', [TenantAssignmentController::class, 'deleteDocument'])->name('delete-document');
    Route::get('/profile', [TenantAssignmentController::class, 'tenantProfile'])->name('profile');
    Route::get('/lease', [TenantAssignmentController::class, 'tenantLease'])->name('lease');
    Route::post('/get-password', [TenantAssignmentController::class, 'getTenantPassword'])->name('get-password');
    Route::post('/update-password', [TenantAssignmentController::class, 'updatePassword'])->name('update-password');
});

// Staff Routes
Route::middleware(['role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'staffDashboard'])->name('dashboard');
    Route::post('/assignments/{id}/complete', [StaffController::class, 'completeAssignment'])->name('complete-assignment');
    Route::get('/profile', [StaffController::class, 'staffProfile'])->name('profile');
    Route::post('/update-password', [StaffController::class, 'updatePassword'])->name('update-password');
});

Route::get('/tenant-payments', function () {
    return view('tenant-payments');
})->name('tenant.payments');

Route::get('/tenant-maintenance', function () {
    return view('tenant-maintenance');
})->name('tenant.maintenance');

Route::get('/tenant-messages', function () {
    return view('tenant-messages');
})->name('tenant.messages');



// Units routes (need to be updated for role-based access)
Route::middleware(['role:super_admin'])->group(function () {
    Route::get('/admin/units', [UnitController::class, 'index'])->name('admin.units');
    Route::post('/admin/units', [UnitController::class, 'store'])->name('admin.units.store');
    Route::get('/admin/units/filter', [UnitController::class, 'filter'])->name('admin.units.filter');
    Route::get('/admin/units/stats', [UnitController::class, 'getStats'])->name('admin.units.stats');
    Route::get('/admin/units/types', [UnitController::class, 'getUnitTypes'])->name('admin.units.types');
});

Route::get('/tenants', function () {
    return view('tenants');
})->name('tenants');

Route::get('/landlord/tenants', [LandlordController::class, 'tenants'])->name('landlord.tenants');

Route::get('/billing', function () {
    return view('billing');
})->name('billing');

Route::get('/messages', function () {
    return view('messages');
})->name('messages');

Route::get('/security', function () {
    return view('security');
})->name('security');

// Health Check Route (for Railway monitoring)
Route::get('/health', function () {
    try {
        $dbConnected = DB::connection()->getPdo() ? 'connected' : 'disconnected';
    } catch (Exception $e) {
        $dbConnected = 'disconnected: ' . $e->getMessage();
    }
    
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'database' => $dbConnected,
        'version' => app()->version(),
        'app_key' => config('app.key') ? 'set' : 'missing',
        'app_debug' => config('app.debug'),
        'app_env' => config('app.env')
    ]);
});

// Simple debug route
Route::get('/debug', function () {
    return response()->json([
        'message' => 'Laravel is working!',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'environment' => app()->environment(),
        'config_cached' => app()->configurationIsCached(),
        'routes_cached' => app()->routesAreCached(),
        'app_key' => config('app.key') ? 'configured' : 'missing',
        'database' => [
            'default' => config('database.default'),
            'host' => config('database.connections.mysql.host'),
            'database' => config('database.connections.mysql.database')
        ]
    ]);
});

// Authentication routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
