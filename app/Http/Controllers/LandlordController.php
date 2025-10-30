<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LandlordProfile;
use App\Models\LandlordDocument;
use App\Models\Apartment;
use App\Models\Unit;
use App\Models\TenantAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\SupabaseService;
class LandlordController extends Controller
{
    public function dashboard()
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        
        $stats = [
            'total_apartments' => $landlord->apartments()->count(),
            'total_units' => Unit::whereHas('apartment', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->count(),
            'occupied_units' => Unit::whereHas('apartment', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->where('status', 'occupied')->count(),
            'available_units' => Unit::whereHas('apartment', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->where('status', 'available')->count(),
            'total_revenue' => Unit::whereHas('apartment', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })->where('status', 'occupied')->sum('rent_amount'),
        ];

        $apartments = $landlord->apartments()->with('units')->latest()->take(5)->get();
        $recentUnits = Unit::whereHas('apartment', function($query) use ($landlord) {
            $query->where('landlord_id', $landlord->id);
        })->with('apartment')->latest()->take(10)->get();

        return view('landlord.dashboard', compact('stats', 'apartments', 'recentUnits'));
    }

    public function apartments(Request $request)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $query = $landlord->apartments()->with('units');
        
        // Sorting
        $sortBy = $request->get('sort', 'name'); // Default: alphabetical by name
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'units':
                $query->withCount('units')->orderByDesc('units_count');
                break;
            case 'occupancy':
                // Sort by occupancy rate (will handle in view)
                $query->orderBy('name');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->orderBy('name');
        }
        
        $apartments = $query->paginate(15);
        
        // Calculate stats
        $totalUnits = $apartments->sum(function($apt) { return $apt->units->count(); });
        $occupiedUnits = $apartments->sum(function($apt) { return $apt->units->where('status', 'occupied')->count(); });
        $monthlyRevenue = $apartments->sum(function($apt) { return $apt->units->where('status', 'occupied')->sum('rent_amount'); });
        
        return view('landlord.apartments', compact('apartments', 'totalUnits', 'occupiedUnits', 'monthlyRevenue'));
    }

    public function createApartment()
    {
        return view('landlord.create-apartment');
    }

    public function storeApartment(Request $request)
    {
        Log::info('Property creation request received', [
            'data' => $request->all(),
            'auto_generate_units' => $request->auto_generate_units,
            'total_units' => $request->total_units,
            'floors' => $request->floors,
            'method' => $request->method(),
            'url' => $request->url(),
            'route' => $request->route()->getName()
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'property_type' => 'required|string|in:apartment,condominium,townhouse,house,duplex,others',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|regex:/^[0-9]+$/|max:20',
            'contact_email' => 'nullable|email|max:255',
            'amenities' => 'nullable|array',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            // Property structure fields
            'floors' => 'nullable|integer|min:1',
            'bedrooms' => 'nullable|integer|min:1',
        ]);

        try {
            $coverPath = null;
            if ($request->hasFile('cover_image')) {
                $supabase = new SupabaseService();
                
                // Generate unique filename
                $filename = 'apartment-' . time() . '-' . uniqid() . '.' . $request->file('cover_image')->getClientOriginalExtension();
                $path = 'apartments/' . $filename;
                
                // Log file info
                Log::info('Uploading file to Supabase', [
                    'bucket' => 'house-sync',
                    'path' => $path,
                    'filename' => $filename,
                    'size' => $request->file('cover_image')->getSize(),
                    'mime' => $request->file('cover_image')->getMimeType()
                ]);
                
                // Upload file
                $uploadResult = $supabase->uploadFile('house-sync', $path, $request->file('cover_image')->getRealPath());
                
                // Log upload result
                Log::info('Supabase upload result', ['result' => $uploadResult]);
                
                // Output to browser console for debugging
                echo "<script>
                    console.group('Supabase Cover Image Upload');
                    console.log('Upload Path:', " . json_encode($path) . ");
                    console.log('File Info:', {
                        filename: " . json_encode($filename) . ",
                        size: " . json_encode($request->file('cover_image')->getSize()) . ",
                        mime: " . json_encode($request->file('cover_image')->getMimeType()) . "
                    });
                    console.log('Upload Result:', " . json_encode($uploadResult) . ");
                    console.log('Public URL:', " . json_encode($uploadResult['url'] ?? null) . ");
                    console.groupEnd();
                </script>";
                
                // Check if upload was successful
                if ($uploadResult['success']) {
                    $coverPath = $uploadResult['url'];
                } else {
                    Log::error('Failed to upload cover image', ['result' => $uploadResult]);
                    throw new \Exception('Failed to upload cover image: ' . ($uploadResult['message'] ?? 'Unknown error'));
                }
            }

            $galleryPaths = [];
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $index => $file) {
                    $supabase = new SupabaseService();
                    
                    // Generate unique filename for gallery
                    $filename = 'apartment-gallery-' . time() . '-' . $index . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = 'apartments/gallery/' . $filename;
                    
                    // Upload to Supabase
                    $uploadResult = $supabase->uploadFile('house-sync', $path, $file->getRealPath());
                    
                    Log::info('Gallery image uploaded', ['index' => $index, 'result' => $uploadResult]);
                    
                    // Output to browser console
                    echo "<script>
                        console.log('Gallery Image " . ($index + 1) . ":', " . json_encode($uploadResult) . ");
                    </script>";
                    
                    // Only add if successful
                    if ($uploadResult['success']) {
                        $galleryPaths[] = $uploadResult['url'];
                    }
                }
            }

            /** @var \App\Models\User $landlord */
            $landlord = Auth::user();
            
            // Determine floors or bedrooms based on property type
            $floors = $request->property_type === 'house' ? null : $request->floors;
            $bedrooms = $request->property_type === 'house' ? $request->bedrooms : null;
            
            $apartment = $landlord->apartments()->create([
                'name' => $request->name,
                'property_type' => $request->property_type,
                'address' => $request->address,
                'description' => $request->description,
                'total_units' => 0, // Start with 0 units, will be added later
                'floors' => $floors,
                'bedrooms' => $bedrooms,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'amenities' => $request->amenities ?? [],
                'status' => 'active',
                'cover_image' => $coverPath,
                'gallery' => $galleryPaths ?: null,
            ]);

            $successMessage = $request->property_type === 'house' 
                ? "House created successfully! You can now add bedrooms as units from the 'My Units' page."
                : "Property created successfully! You can now add units from the 'My Units' page.";

            return redirect()->route('landlord.apartments')->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error creating apartment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create apartment. Please try again.');
        }
    }




    public function editApartment($id)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($id);
        return view('landlord.edit-apartment', compact('apartment'));
    }

    public function updateApartment(Request $request, $id)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'total_units' => 'required|integer|min:1',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|regex:/^[0-9]+$/|max:20',
            'contact_email' => 'nullable|email|max:255',
            'amenities' => 'nullable|array',
            'status' => 'required|in:active,inactive,maintenance',
            'auto_generate_additional' => 'nullable|boolean',
            'auto_create_missing' => 'nullable|boolean',
        ]);

        try {
            $currentUnitCount = $apartment->units()->count();
            $newTotalUnits = $request->total_units;

            $apartment->update([
                'name' => $request->name,
                'address' => $request->address,
                'description' => $request->description,
                'total_units' => $request->total_units,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'amenities' => $request->amenities ?? [],
                'status' => $request->status,
            ]);

            $unitsCreated = 0;

            // Auto-create missing units if there was a discrepancy
            if ($request->auto_create_missing && $currentUnitCount < $apartment->total_units) {
                $unitsToCreate = $apartment->total_units - $currentUnitCount;
                $this->autoGenerateAdditionalUnits($apartment, $unitsToCreate, $currentUnitCount);
                $unitsCreated = $unitsToCreate;
            }
            // Auto-generate additional units if total was increased
            elseif ($request->auto_generate_additional && $newTotalUnits > $currentUnitCount) {
                $additionalUnits = $newTotalUnits - $currentUnitCount;
                $this->autoGenerateAdditionalUnits($apartment, $additionalUnits, $currentUnitCount);
                $unitsCreated = $additionalUnits;
            }

            $successMessage = $unitsCreated > 0 
                ? "Apartment updated successfully and {$unitsCreated} units auto-generated!" 
                : 'Apartment updated successfully.';

            return redirect()->route('landlord.apartments')->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error updating apartment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update apartment. Please try again.');
        }
    }

    /**
     * Auto-generate additional units for an existing apartment
     */
    private function autoGenerateAdditionalUnits($apartment, $numUnitsToCreate, $startingIndex = 0)
    {
        $existingUnits = $apartment->units()->count();
        
        // Get the highest unit number from existing units to continue numbering
        $highestUnitNumber = $apartment->units()->max('unit_number') ?? 0;
        
        // Use sensible defaults
        $defaultData = [
            'unit_type' => '2-Bedroom',
            'rent_amount' => 15000,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'status' => 'available',
            'leasing_type' => 'separate',
            'tenant_count' => 0,
            'max_occupants' => 4,
        ];

        $unitsCreated = 0;
        $currentUnitNumber = $highestUnitNumber;

        for ($i = 1; $i <= $numUnitsToCreate; $i++) {
            // Find the next available unit number
            do {
                $currentUnitNumber++;
                $unitNumber = (string) $currentUnitNumber;
            } while ($apartment->units()->where('unit_number', $unitNumber)->exists());

            // Calculate floor number based on unit number (assuming 100 units per floor)
            $floorNumber = floor($currentUnitNumber / 100) + 1;

            // Create the unit
            $apartment->units()->create([
                'unit_number' => $unitNumber,
                'unit_type' => $defaultData['unit_type'],
                'rent_amount' => $defaultData['rent_amount'],
                'status' => $defaultData['status'],
                'leasing_type' => $defaultData['leasing_type'],
                'bedrooms' => $defaultData['bedrooms'],
                'bathrooms' => $defaultData['bathrooms'],
                'tenant_count' => $defaultData['tenant_count'],
                'max_occupants' => $defaultData['max_occupants'],
                'floor_number' => $floorNumber,
                'description' => "Auto-generated unit",
                'amenities' => $apartment->amenities ?? [],
                'is_furnished' => false,
            ]);

            $unitsCreated++;
            
            Log::info("Created additional unit {$i}/{$numUnitsToCreate}", [
                'unit_number' => $unitNumber,
                'floor' => $floorNumber,
                'apartment_id' => $apartment->id
            ]);
        }

        Log::info("Auto-generated {$unitsCreated} additional units for apartment: {$apartment->name}");
        return $unitsCreated;
    }

    public function deleteApartment(Request $request, $id)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($id);
        
        try {
            $unitCount = $apartment->units()->count();
            $forceDelete = $request->boolean('force_delete');
            
            // If force delete is requested, verify password
            if ($forceDelete) {
                $request->validate([
                    'password' => 'required|string',
                ]);
                
                // Verify landlord's password
                if (!Hash::check($request->password, $landlord->password)) {
                    return back()->with('error', 'Incorrect password. Force delete cancelled.');
                }
                
                // Check if any units have active tenant assignments
                $activeTenantsCount = $apartment->units()
                    ->whereHas('tenantAssignments', function($query) {
                        $query->whereIn('status', ['active', 'pending']);
                    })->count();
                
                if ($activeTenantsCount > 0) {
                    return back()->with('error', "Cannot force delete property with active tenant assignments. Found {$activeTenantsCount} unit(s) with active tenants. Please terminate all tenant assignments first.");
                }
                
                // Force delete: Delete all units first, then the apartment
                $apartmentName = $apartment->name;
                
                // Delete all terminated/completed tenant assignments for all units
                foreach ($apartment->units as $unit) {
                    // Delete all tenant assignments (only terminated ones should exist at this point)
                    $unit->tenantAssignments()->delete();
                }
                
                // Delete all units
                $deletedUnitsCount = $apartment->units()->count();
                $apartment->units()->delete();
                
                // Delete the apartment
                $apartment->delete();
                
                return redirect()->route('landlord.apartments')->with('success', "Property '{$apartmentName}' and {$deletedUnitsCount} unit(s) force deleted successfully.");
            }
            
            // Normal delete: Check if apartment has units
            if ($unitCount > 0) {
                // Check if any units have active tenants
                $activeTenantsCount = $apartment->units()
                    ->whereHas('tenantAssignments', function($query) {
                        $query->whereIn('status', ['active', 'pending']);
                    })->count();
                
                if ($activeTenantsCount > 0) {
                    return back()->with('error', "Cannot delete property with active tenant assignments. Found {$activeTenantsCount} unit(s) with active tenants. Please terminate all tenant assignments first, then delete the units.");
                }
                
                return back()->with('error', "Cannot delete property with existing units. Found {$unitCount} unit(s). Please delete all units first from the Units page, or use Force Delete.");
            }
            
            $apartmentName = $apartment->name;
            $apartment->delete();
            
            return redirect()->route('landlord.apartments')->with('success', "Property '{$apartmentName}' deleted successfully.");
        } catch (\Exception $e) {
            Log::error('Error deleting apartment: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete property. Please try again.');
        }
    }

    public function units(Request $request, $apartmentId = null)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        
        // Sorting
        $sortBy = $request->get('sort', 'property_unit'); // Default: by property then unit number
        
        if ($apartmentId) {
            $apartment = $landlord->apartments()->findOrFail($apartmentId);
            $query = $apartment->units()->with('apartment');
            $statsQuery = $apartment->units();
        } else {
            $query = Unit::whereHas('apartment', function($q) use ($landlord) {
                $q->where('landlord_id', $landlord->id);
            })->with('apartment');
            $statsQuery = Unit::whereHas('apartment', function($q) use ($landlord) {
                $q->where('landlord_id', $landlord->id);
            });
        }
        
        // Calculate stats from the full dataset (not paginated)
        $stats = [
            'total_units' => $statsQuery->count(),
            'available_units' => (clone $statsQuery)->where('status', 'available')->count(),
            'occupied_units' => (clone $statsQuery)->where('status', 'occupied')->count(),
            'monthly_revenue' => (clone $statsQuery)->where('status', 'occupied')->sum('rent_amount') ?? 0,
        ];
        
        // Apply sorting
        switch ($sortBy) {
            case 'property_unit':
                // Sort by property name, then floor number, then unit number (numerical)
                $query->join('apartments', 'units.apartment_id', '=', 'apartments.id')
                      ->orderBy('apartments.name')
                      ->orderBy('units.floor_number')
                      ->orderByRaw('CAST(units.unit_number AS UNSIGNED)')
                      ->orderBy('units.unit_number')
                      ->select('units.*');
                break;
            case 'property':
                // Sort by property name only
                $query->join('apartments', 'units.apartment_id', '=', 'apartments.id')
                      ->orderBy('apartments.name')
                      ->select('units.*');
                break;
            case 'unit_number':
                $query->orderByRaw('CAST(unit_number AS UNSIGNED)')
                      ->orderBy('unit_number');
                break;
            case 'floor':
                $query->orderBy('floor_number')
                      ->orderByRaw('CAST(unit_number AS UNSIGNED)')
                      ->orderBy('unit_number');
                break;
            case 'status':
                $query->orderByRaw("FIELD(status, 'available', 'occupied', 'maintenance')")
                      ->orderByRaw('CAST(unit_number AS UNSIGNED)')
                      ->orderBy('unit_number');
                break;
            case 'rent':
                $query->orderByDesc('rent_amount');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->join('apartments', 'units.apartment_id', '=', 'apartments.id')
                      ->orderBy('apartments.name')
                      ->orderByRaw('CAST(units.unit_number AS UNSIGNED)')
                      ->orderBy('units.unit_number')
                      ->select('units.*');
        }
        
        $units = $query->paginate(20);
        $apartments = $landlord->apartments()->orderBy('name')->get();
        
        return view('landlord.units', compact('units', 'apartments', 'apartmentId', 'stats'));
    }

    public function createUnit($apartmentId = null)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        if ($apartmentId) {
            $apartment = $landlord->apartments()->findOrFail($apartmentId);
            return view('landlord.create-unit', compact('apartment'));
        } else {
            // Show property selection first
            $apartments = $landlord->apartments()->get();
            return view('landlord.select-property-for-unit', compact('apartments'));
        }
    }

    public function storeUnit(Request $request, $apartmentId)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($apartmentId);

        $request->validate([
            'unit_number' => 'required|string|regex:/^Unit-\d+$/|max:50|unique:units,unit_number,NULL,id,apartment_id,' . $apartmentId,
            'unit_type' => 'required|string|max:100',
            'rent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance',
            'leasing_type' => 'required|in:separate,inclusive',
            'description' => 'nullable|string|max:1000',
            'floor_area' => 'nullable|numeric|min:0',
            'floor_number' => 'nullable|integer|min:1',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:1',
            'is_furnished' => 'boolean',
            'amenities' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $supabase = new SupabaseService();
            
            // Generate unique filename
            $filename = 'unit-' . time() . '-' . uniqid() . '.' . $request->file('cover_image')->getClientOriginalExtension();
            $path = 'units/' . $filename;
            
            // Upload file
            $uploadResult = $supabase->uploadFile('house-sync', $path, $request->file('cover_image')->getRealPath());
            
            Log::info('Unit cover image upload', ['result' => $uploadResult]);
            
            // Output to browser console for debugging
            echo "<script>
                console.group('Supabase Unit Cover Image Upload');
                console.log('Upload Path:', " . json_encode($path) . ");
                console.log('Upload Result:', " . json_encode($uploadResult) . ");
                console.log('Public URL:', " . json_encode($uploadResult['url'] ?? null) . ");
                console.groupEnd();
            </script>";
            
            // Check if upload was successful
            if ($uploadResult['success']) {
                $coverPath = $uploadResult['url'];
            } else {
                Log::error('Failed to upload unit cover image', ['result' => $uploadResult]);
                return back()->withInput()->with('error', 'Failed to upload cover image: ' . ($uploadResult['message'] ?? 'Unknown error'));
            }
        }
        
        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $file) {
                $supabase = new SupabaseService();
                
                // Generate unique filename for gallery
                $filename = 'unit-gallery-' . time() . '-' . $index . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = 'units/gallery/' . $filename;
                
                // Upload to Supabase
                $uploadResult = $supabase->uploadFile('house-sync', $path, $file->getRealPath());
                
                Log::info('Unit gallery image uploaded', ['index' => $index, 'result' => $uploadResult]);
                
                // Output to browser console
                echo "<script>
                    console.log('Unit Gallery Image " . ($index + 1) . ":', " . json_encode($uploadResult) . ");
                </script>";
                
                // Only add if successful
                if ($uploadResult['success']) {
                    $galleryPaths[] = $uploadResult['url'];
                }
            }
        }

        $apartment->units()->create([
            'unit_number' => $request->unit_number,
            'unit_type' => $request->unit_type,
            'rent_amount' => $request->rent_amount,
            'status' => $request->status,
            'leasing_type' => $request->leasing_type,
            'description' => $request->description,
            'floor_area' => $request->floor_area,
            'floor_number' => $request->floor_number ?? 1,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'is_furnished' => $request->boolean('is_furnished'),
            'amenities' => $request->amenities ?? [],
            'notes' => $request->notes,
            'cover_image' => $coverPath,
            'gallery' => $galleryPaths ?: null,
        ]);

        return redirect()->route('landlord.units', $apartmentId)->with('success', 'Unit created successfully.');
    }

    public function storeBulkUnits(Request $request, $apartmentId)
    {
        // Debug: Log the request data
        \Log::info('storeBulkUnits called', [
            'apartmentId' => $apartmentId,
            'request_data' => $request->all()
        ]);
        
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($apartmentId);

        try {
            $request->validate([
                'units_per_floor' => 'nullable|integer|min:1',
                'create_all_bedrooms' => 'nullable|boolean',
                'default_unit_type' => 'required|string|max:100',
                'default_rent' => 'required|numeric|min:0',
                'default_bedrooms' => 'required|integer|min:0',
                'default_bathrooms' => 'required|integer|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in storeBulkUnits', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        // Store bulk creation parameters in session for the edit page
        session([
            'bulk_creation_params' => [
                'apartment_id' => $apartmentId,
                'creation_type' => 'bulk',
                'units_per_floor' => $request->units_per_floor,
                'create_all_bedrooms' => $request->create_all_bedrooms,
                'default_unit_type' => $request->default_unit_type,
                'default_rent' => $request->default_rent,
                'default_bedrooms' => $request->default_bedrooms,
                'default_bathrooms' => $request->default_bathrooms,
            ]
        ]);

        // Redirect to bulk edit page
        return redirect()->route('landlord.bulk-edit-units', $apartmentId);
    }

    public function createMultipleUnits($apartmentId)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($apartmentId);

        return view('landlord.create-multiple-units', compact('apartment'));
    }

    public function bulkEditUnits($apartmentId)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($apartmentId);

        // Get bulk creation parameters from session
        $bulkParams = session('bulk_creation_params', []);
        
        return view('landlord.bulk-edit-units', compact('apartment', 'bulkParams'));
    }

    public function finalizeBulkUnits(Request $request, $apartmentId)
    {
        \Log::info('finalizeBulkUnits method called', [
            'apartmentId' => $apartmentId,
            'request_data' => $request->all(),
            'has_units' => $request->has('units'),
            'units_count' => $request->has('units') ? count($request->input('units', [])) : 0
        ]);
        
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($apartmentId);

        // Check if units data exists
        if (!$request->has('units') || empty($request->input('units'))) {
            \Log::error('No units data received in finalizeBulkUnits');
            return back()->with('error', 'No units data received. Please try again.');
        }

        $request->validate([
            'units' => 'required|array',
            'units.*.unit_number' => 'required|string|regex:/^Unit-\d+$/|max:50',
            'units.*.unit_type' => 'required|string|max:100',
            'units.*.rent_amount' => 'required|numeric|min:0',
            'units.*.bedrooms' => 'required|integer|min:0',
            'units.*.bathrooms' => 'required|integer|min:1',
            'units.*.status' => 'required|in:available,maintenance',
            'units.*.leasing_type' => 'required|in:separate,inclusive',
            'units.*.max_occupants' => 'required|integer|min:1',
            'units.*.floor_number' => 'required|integer|min:1',
        ]);

        try {
            $unitsCreated = 0;
            $totalUnitsReceived = count($request->units);
            
            \Log::info('finalizeBulkUnits called', [
                'apartmentId' => $apartmentId,
                'totalUnitsReceived' => $totalUnitsReceived,
                'unitsData' => $request->units
            ]);
            
            foreach ($request->units as $unitData) {
                \Log::info('Creating unit', [
                    'unit_number' => $unitData['unit_number'],
                    'floor_number' => $unitData['floor_number'],
                    'unit_type' => $unitData['unit_type']
                ]);
                
                // Check if unit already exists
                $existingUnit = $apartment->units()->where('unit_number', $unitData['unit_number'])->first();
                if ($existingUnit) {
                    \Log::warning('Unit already exists, skipping', [
                        'unit_number' => $unitData['unit_number'],
                        'existing_unit_id' => $existingUnit->id
                    ]);
                    continue;
                }
                
                // Convert is_furnished to proper boolean
                $isFurnished = false;
                if (isset($unitData['is_furnished'])) {
                    $isFurnished = $unitData['is_furnished'] === 'true' || $unitData['is_furnished'] === true || $unitData['is_furnished'] === '1' || $unitData['is_furnished'] === 1;
                }
                
                $apartment->units()->create([
                    'unit_number' => $unitData['unit_number'],
                    'unit_type' => $unitData['unit_type'],
                    'rent_amount' => $unitData['rent_amount'],
                    'status' => $unitData['status'],
                    'leasing_type' => $unitData['leasing_type'],
                    'bedrooms' => $unitData['bedrooms'],
                    'bathrooms' => $unitData['bathrooms'],
                    'tenant_count' => 0,
                    'max_occupants' => $unitData['max_occupants'],
                    'floor_number' => $unitData['floor_number'],
                    'description' => "Customized unit {$unitData['unit_number']}",
                    'amenities' => [],
                    'is_furnished' => $isFurnished,
                ]);
                $unitsCreated++;
            }
            
            // Update apartment total_units count
            $apartment->update(['total_units' => $apartment->units()->count()]);
            
            // Clear session data
            session()->forget('bulk_creation_params');
            
            $message = "Successfully created {$unitsCreated} units!";
            return redirect()->route('landlord.units', $apartmentId)->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error finalizing bulk units: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create units. Please try again.');
        }
    }

    public function updateUnit(Request $request, $id)
    {
        $unit = Unit::whereHas('apartment', function($query) {
            $query->where('landlord_id', Auth::id());
        })->findOrFail($id);

        try {
            $request->validate([
                'unit_number' => 'required|string|regex:/^Unit-\d+$/|max:50|unique:units,unit_number,' . $unit->id . ',id,apartment_id,' . $unit->apartment_id,
                'unit_type' => 'required|string|max:100',
                'rent_amount' => 'required|numeric|min:0',
                'status' => 'required|in:available,occupied,maintenance',
                'leasing_type' => 'required|in:separate,inclusive',
                'description' => 'nullable|string|max:1000',
                'floor_area' => 'nullable|numeric|min:0',
                'bedrooms' => 'required|integer|min:0',
                'bathrooms' => 'required|integer|min:1',
                'is_furnished' => 'nullable|boolean',
                'amenities' => 'nullable|array',
                'amenities.*' => 'string',
                'notes' => 'nullable|string|max:1000',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            $unit->update([
                'unit_number' => $request->unit_number,
                'unit_type' => $request->unit_type,
                'rent_amount' => $request->rent_amount,
                'status' => $request->status,
                'leasing_type' => $request->leasing_type,
                'description' => $request->description,
                'floor_area' => $request->floor_area,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'is_furnished' => $request->boolean('is_furnished'),
                'amenities' => $request->amenities ?? [],
                'notes' => $request->notes,
            ]);

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Unit updated successfully.',
                    'unit' => $unit->fresh()
                ]);
            }

            return redirect()->route('landlord.units')->with('success', 'Unit updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating unit: ' . $e->getMessage());
            
            // Return JSON error response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update unit. Please try again.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to update unit. Please try again.');
        }
    }


    public function deleteUnit($id)
    {
        $unit = Unit::whereHas('apartment', function($query) {
            $query->where('landlord_id', Auth::id());
        })->findOrFail($id);
        
        try {
            // Check if unit has active tenant assignments
            $activeAssignments = $unit->tenantAssignments()
                ->whereIn('status', ['active', 'pending'])
                ->count();
                
            if ($activeAssignments > 0) {
                return back()->with('error', 'Cannot delete unit with active tenant assignments. Please terminate all assignments first.');
            }
            
            $unitNumber = $unit->unit_number;
            
            // Delete all terminated/completed assignments first
            $unit->tenantAssignments()->delete();
            
            // Delete the unit
            $unit->delete();
            
            return back()->with('success', "Unit '{$unitNumber}' deleted successfully.");
        } catch (\Exception $e) {
            Log::error('Error deleting unit: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete unit. Please try again.');
        }
    }

    public function register()
    {
        return view('landlord.register');
    }

    public function storeRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|regex:/^[0-9]+$/|max:20',
            'address' => 'required|string|max:500',
            'business_info' => 'required|string|max:1000',
            // Require at least one document, recommend specific types
            'documents' => 'required|array|min:1',
            'documents.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'document_types' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    $documents = $request->file('documents', []);
                    if (!is_array($documents)) {
                        $documents = [];
                    }
                    if (count($value) !== count($documents)) {
                        $fail('The number of selected document types must match the number of uploaded documents.');
                    }
                },
            ],
            'document_types.*' => 'required|string|in:business_permit,mayors_permit,bir_certificate,barangay_clearance,lease_contract_sample,valid_id,other',
        ]);

        $landlord = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'landlord',
        ]);

        // Create landlord profile for role-specific data
        LandlordProfile::create([
            'user_id' => $landlord->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'business_info' => $request->business_info,
            'status' => 'pending',
        ]);

        // Store uploaded documents for review (pending verification)
        foreach ($request->file('documents') as $index => $file) {
            $docType = $request->document_types[$index] ?? 'other';
            $supabase = new SupabaseService();
            
            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $fileName = 'landlord-doc-' . $landlord->id . '-' . time() . '-' . $index . '-' . uniqid() . '.' . $extension;
            $path = 'landlord-documents/' . $fileName;
            
            // Upload to Supabase
            $uploadResult = $supabase->uploadFile('house-sync', $path, $file->getRealPath());
            
            Log::info('Landlord document uploaded', [
                'landlord_id' => $landlord->id,
                'index' => $index,
                'type' => $docType,
                'result' => $uploadResult
            ]);
            
            // Output to browser console
            echo "<script>
                console.log('📄 Landlord Document " . ($index + 1) . " (" . $docType . "):', " . json_encode($uploadResult) . ");
            </script>";
            
            // Only create record if successful
            if ($uploadResult['success']) {
                LandlordDocument::create([
                    'landlord_id' => $landlord->id,
                    'document_type' => $docType,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $uploadResult['url'],
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now(),
                    'verification_status' => 'pending',
                ]);
            } else {
                Log::error('Failed to upload landlord document', [
                    'landlord_id' => $landlord->id,
                    'index' => $index,
                    'result' => $uploadResult
                ]);
            }
        }

        return redirect()->route('landlord.pending')->with('success', 'Registration submitted successfully. Please wait for admin approval.');
    }

    public function pending()
    {
        return view('landlord.pending');
    }

    public function rejected()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('landlord.rejected', compact('user'));
    }

    public function tenants() {
        $landlordId = Auth::id();
        $tenants = User::where('role', 'tenant')
            ->whereHas('tenantAssignments', function($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            })->get();
        return view('landlord.tenants', compact('tenants'));
    }

    /**
     * Show tenant history with filtering options
     */
    public function tenantHistory(Request $request)
    {
        $landlordId = Auth::id();
        
        // Build the query for tenant assignments
        $query = TenantAssignment::where('landlord_id', $landlordId)
            ->with(['tenant', 'unit.apartment']);
        
        // Apply filters
        if ($request->filled('property_id')) {
            $query->whereHas('unit.apartment', function($q) use ($request) {
                $q->where('id', $request->property_id);
            });
        }
        
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        
        if ($request->filled('tenant_name')) {
            $searchTerm = $request->tenant_name;
            $query->whereHas('tenant', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->where('lease_start_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('lease_end_date', '<=', $request->date_to);
        }
        
        // Order by most recent first
        $assignments = $query->orderBy('lease_start_date', 'desc')->paginate(20);
        
        // Get all apartments for filter dropdown
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartments = $landlord->apartments()->orderBy('name')->get();
        
        // Get all units for filter dropdown
        $units = Unit::whereHas('apartment', function($q) use ($landlordId) {
            $q->where('landlord_id', $landlordId);
        })->with('apartment')->orderBy('unit_number')->get();
        
        // Calculate statistics
        $stats = [
            'total_assignments' => TenantAssignment::where('landlord_id', $landlordId)->count(),
            'active_assignments' => TenantAssignment::where('landlord_id', $landlordId)->where('status', 'active')->count(),
            'terminated_assignments' => TenantAssignment::where('landlord_id', $landlordId)->where('status', 'terminated')->count(),
            'total_revenue' => TenantAssignment::where('landlord_id', $landlordId)->where('status', 'active')->sum('rent_amount'),
        ];
        
        return view('landlord.tenant-history', compact('assignments', 'apartments', 'units', 'stats'));
    }

    /**
     * Export tenant history to CSV
     */
    public function exportTenantHistoryCSV(Request $request)
    {
        $landlordId = Auth::id();
        
        // Build the query with same filters as tenant history
        $query = TenantAssignment::where('landlord_id', $landlordId)
            ->with(['tenant', 'unit.apartment']);
        
        // Apply filters
        if ($request->filled('property_id')) {
            $query->whereHas('unit.apartment', function($q) use ($request) {
                $q->where('id', $request->property_id);
            });
        }
        
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        
        if ($request->filled('tenant_name')) {
            $searchTerm = $request->tenant_name;
            $query->whereHas('tenant', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->where('lease_start_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('lease_end_date', '<=', $request->date_to);
        }
        
        $assignments = $query->orderBy('lease_start_date', 'desc')->get();
        
        // Generate CSV
        $filename = 'tenant-history-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($assignments) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Tenant Name',
                'Email',
                'Phone',
                'Property Name',
                'Unit Number',
                'Bedrooms',
                'Move-in Date',
                'Move-out Date',
                'Lease Duration (months)',
                'Rent Amount',
                'Security Deposit',
                'Status',
                'Payment Status',
                'Notes'
            ]);
            
            // Add data rows
            foreach ($assignments as $assignment) {
                $leaseStartDate = \Carbon\Carbon::parse($assignment->lease_start_date);
                $leaseEndDate = \Carbon\Carbon::parse($assignment->lease_end_date);
                $leaseDuration = $leaseStartDate->diffInMonths($leaseEndDate);
                
                // Determine payment status based on documents
                $paymentStatus = $assignment->documents_verified ? 'Verified' : ($assignment->documents_uploaded ? 'Pending Verification' : 'Pending Documents');
                
                fputcsv($file, [
                    $assignment->tenant->name ?? 'N/A',
                    $assignment->tenant->email ?? 'N/A',
                    $assignment->tenant->phone ?? 'N/A',
                    $assignment->unit->apartment->name ?? 'N/A',
                    $assignment->unit->unit_number ?? 'N/A',
                    $assignment->unit->bedrooms ?? 'N/A',
                    $assignment->lease_start_date ? $assignment->lease_start_date->format('M d, Y') : 'N/A',
                    $assignment->lease_end_date ? $assignment->lease_end_date->format('M d, Y') : 'N/A',
                    $leaseDuration,
                    '₱' . number_format($assignment->rent_amount, 2),
                    '₱' . number_format($assignment->security_deposit, 2),
                    ucfirst($assignment->status),
                    $paymentStatus,
                    $assignment->notes ?? ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    // API endpoints for apartment management
    public function getApartmentDetails($id)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->with('units')->findOrFail($id);
        
        return response()->json([
            'id' => $apartment->id,
            'name' => $apartment->name,
            'total_units' => $apartment->units->count(),
            'occupied_units' => $apartment->getOccupiedUnitsCount(),
            'available_units' => $apartment->getAvailableUnitsCount(),
            'maintenance_units' => $apartment->getMaintenanceUnitsCount(),
            'occupancy_rate' => $apartment->getOccupancyRate(),
            'total_revenue' => $apartment->getTotalRevenue(),
        ]);
    }

    public function getApartmentUnits($id)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($id);
        $units = $apartment->units()->orderBy('unit_number')->get();
        
        return response()->json([
            'units' => $units->map(function($unit) {
                return [
                    'id' => $unit->id,
                    'unit_number' => $unit->unit_number,
                    'unit_type' => $unit->unit_type,
                    'rent_amount' => $unit->rent_amount,
                    'status' => $unit->status,
                    'bedrooms' => $unit->bedrooms,
                    'bathrooms' => $unit->bathrooms,
                    'max_occupants' => $unit->max_occupants ?? $unit->tenant_count,
                    'floor_number' => $unit->floor_number ?? 1,
                    'floor_area' => $unit->floor_area,
                    'amenities' => $unit->amenities,
                    'description' => $unit->description,
                ];
            })
        ]);
    }

    public function getUnitDetails($id)
    {
        $unit = Unit::whereHas('apartment', function($query) {
            $query->where('landlord_id', Auth::id());
        })->with(['apartment', 'tenantAssignments.tenant'])->findOrFail($id);
        
        // Get current active tenant assignment
        $currentAssignment = $unit->tenantAssignments()->where('status', 'active')->with('tenant')->first();
        
        return response()->json([
            'id' => $unit->id,
            'unit_number' => $unit->unit_number,
            'apartment_name' => $unit->apartment->name,
            'apartment_id' => $unit->apartment->id,
            'unit_type' => $unit->unit_type,
            'rent_amount' => $unit->rent_amount,
            'status' => $unit->status,
            'leasing_type' => $unit->leasing_type,
            'bedrooms' => $unit->bedrooms,
            'bathrooms' => $unit->bathrooms,
            'max_occupants' => $unit->max_occupants,
            'floor_number' => $unit->floor_number,
            'floor_area' => $unit->floor_area,
            'is_furnished' => $unit->is_furnished,
            'amenities' => $unit->amenities ?? [],
            'description' => $unit->description,
            'notes' => $unit->notes,
            'created_at' => $unit->created_at->format('M d, Y'),
            'updated_at' => $unit->updated_at->format('M d, Y'),
            'current_tenant' => $currentAssignment ? [
                'id' => $currentAssignment->tenant->id,
                'name' => $currentAssignment->tenant->name,
                'email' => $currentAssignment->tenant->email,
                'phone' => $currentAssignment->tenant->phone,
                'lease_start' => $currentAssignment->lease_start_date->format('M d, Y'),
                'lease_end' => $currentAssignment->lease_end_date->format('M d, Y'),
                'assignment_id' => $currentAssignment->id,
            ] : null,
            'total_assignments' => $unit->tenantAssignments()->count(),
            'assignment_history' => $unit->tenantAssignments()->count(),
        ]);
    }

    public function storeApartmentUnit(Request $request, $apartmentId)
    {
        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();
        $apartment = $landlord->apartments()->findOrFail($apartmentId);

        $request->validate([
            'unit_number' => 'required|string|regex:/^Unit-\d+$/|max:50|unique:units,unit_number,NULL,id,apartment_id,' . $apartmentId,
            'unit_type' => 'required|string|max:100',
            'rent_amount' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:1',
            'max_occupants' => 'required|integer|min:1',
            'floor_number' => 'nullable|integer|min:1',
            'floor_area' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'amenities' => 'nullable|array',
        ]);

        try {
            $unit = $apartment->units()->create([
                'unit_number' => $request->unit_number,
                'unit_type' => $request->unit_type,
                'rent_amount' => $request->rent_amount,
                'status' => 'available',
                'leasing_type' => 'separate',
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'tenant_count' => 0,
                'max_occupants' => $request->max_occupants,
                'floor_number' => $request->floor_number ?? 1,
                'floor_area' => $request->floor_area,
                'description' => $request->description,
                'amenities' => $request->amenities ?? [],
                'is_furnished' => in_array('furnished', $request->amenities ?? []),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully.',
                'unit' => $unit
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating unit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create unit. Please try again.'
            ], 500);
        }
    }
}
