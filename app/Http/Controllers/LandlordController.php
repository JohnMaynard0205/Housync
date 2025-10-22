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
use App\Services\SupabaseService;
class LandlordController extends Controller
{
    public function dashboard()
    {
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
        $query = Auth::user()->apartments()->with('units');
        
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
        \Log::info('Property creation request received', [
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
            'total_units' => 'required|integer|min:1',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'amenities' => 'nullable|array',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            // Auto-generation fields
            'auto_generate_units' => 'nullable|boolean',
            'floors' => 'required|integer|min:1',
            'default_unit_type' => 'nullable|string|max:100',
            'default_rent' => 'nullable|numeric|min:0',
            'units_per_floor' => 'nullable|integer|min:1',
            'default_bedrooms' => 'nullable|integer|min:0',
            'default_bathrooms' => 'nullable|integer|min:1',
            'numbering_pattern' => 'nullable|string|in:floor_based,sequential,letter_number',
        ]);

        try {
            $coverPath = null;
            if ($request->hasFile('cover_image')) {
                $supabase = new SupabaseService();
                
                // Generate unique filename
                $filename = 'apartment-' . time() . '-' . uniqid() . '.' . $request->file('cover_image')->getClientOriginalExtension();
                $path = 'apartments/' . $filename;
                
                // Log file info
                \Log::info('Uploading file to Supabase', [
                    'bucket' => 'house-sync',
                    'path' => $path,
                    'filename' => $filename,
                    'size' => $request->file('cover_image')->getSize(),
                    'mime' => $request->file('cover_image')->getMimeType()
                ]);
                
                // Upload file
                $uploadResult = $supabase->uploadFile('house-sync', $path, $request->file('cover_image')->getRealPath());
                
                // Log upload result
                \Log::info('Supabase upload result', ['result' => $uploadResult]);
                
                // Output to browser console for debugging
                echo "<script>
                    console.group('🚀 Supabase Cover Image Upload');
                    console.log('📁 Upload Path:', " . json_encode($path) . ");
                    console.log('📊 File Info:', {
                        filename: " . json_encode($filename) . ",
                        size: " . json_encode($request->file('cover_image')->getSize()) . ",
                        mime: " . json_encode($request->file('cover_image')->getMimeType()) . "
                    });
                    console.log('✅ Upload Result:', " . json_encode($uploadResult) . ");
                    console.log('🔗 Public URL:', " . json_encode($uploadResult['url'] ?? null) . ");
                    console.groupEnd();
                </script>";
                
                // Check if upload was successful
                if ($uploadResult['success']) {
                    $coverPath = $uploadResult['url'];
                } else {
                    \Log::error('Failed to upload cover image', ['result' => $uploadResult]);
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
                    
                    \Log::info('Gallery image uploaded', ['index' => $index, 'result' => $uploadResult]);
                    
                    // Output to browser console
                    echo "<script>
                        console.log('🖼️ Gallery Image " . ($index + 1) . ":', " . json_encode($uploadResult) . ");
                    </script>";
                    
                    // Only add if successful
                    if ($uploadResult['success']) {
                        $galleryPaths[] = $uploadResult['url'];
                    }
                }
            }

            $apartment = Auth::user()->apartments()->create([
                'name' => $request->name,
                'property_type' => $request->property_type,
                'address' => $request->address,
                'description' => $request->description,
                'total_units' => $request->total_units,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'amenities' => $request->amenities ?? [],
                'status' => 'active',
                'cover_image' => $coverPath,
                'gallery' => $galleryPaths ?: null,
            ]);

            // Auto-generate units if requested
            $autoGenerate = $request->has('auto_generate_units') && $request->auto_generate_units == '1';
            \Log::info('Auto-generation check', [
                'has_auto_generate' => $request->has('auto_generate_units'),
                'auto_generate_value' => $request->auto_generate_units,
                'auto_generate_boolean' => $autoGenerate,
                'all_request_data' => $request->all()
            ]);
            
            if ($autoGenerate) {
                \Log::info('Auto-generating units for apartment', [
                    'apartment_id' => $apartment->id,
                    'total_units' => $request->total_units,
                    'floors' => $request->floors
                ]);
                $this->autoGenerateUnits($apartment, $request);
            } else {
                \Log::info('Auto-generation disabled for apartment', [
                    'apartment_id' => $apartment->id,
                    'reason' => 'Checkbox not checked or value not 1'
                ]);
            }

            $successMessage = $autoGenerate
                ? "Apartment created successfully with {$request->total_units} units!" 
                : 'Apartment created successfully.';

            return redirect()->route('landlord.apartments')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Error creating apartment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create apartment. Please try again.');
        }
    }

    /**
     * Auto-generate units for an apartment
     */
    private function autoGenerateUnits($apartment, $request)
    {
        $totalUnits = $request->total_units;
        $numFloors = $request->floors ?? 1; // Use floors from Property Details
        $unitsPerFloor = $request->units_per_floor ?? ceil($totalUnits / $numFloors);
        $numberingPattern = $request->numbering_pattern ?? 'floor_based';
        
        \Log::info('Auto-generating units with parameters', [
            'apartment_id' => $apartment->id,
            'total_units' => $totalUnits,
            'numFloors' => $numFloors,
            'unitsPerFloor' => $unitsPerFloor,
            'numberingPattern' => $numberingPattern
        ]);
        
        $defaultData = [
            'unit_type' => $request->default_unit_type ?? '2-Bedroom',
            'rent_amount' => $request->default_rent ?? 15000,
            'bedrooms' => $request->default_bedrooms ?? 2,
            'bathrooms' => $request->default_bathrooms ?? 1,
            'status' => 'available',
            'leasing_type' => 'separate',
            'tenant_count' => 0,
            'max_occupants' => ($request->default_bedrooms ?? 2) * 2,
        ];

        $unitsCreated = 0;
        $currentFloor = 1;
        $unitOnFloor = 1;

        for ($i = 1; $i <= $totalUnits; $i++) {
            try {
                // Generate unit number based on pattern
                $unitNumber = $this->generateUnitNumber($i, $currentFloor, $unitOnFloor, $numberingPattern);

                // Create the unit
                $unit = $apartment->units()->create([
                    'unit_number' => $unitNumber,
                    'unit_type' => $defaultData['unit_type'],
                    'rent_amount' => $defaultData['rent_amount'],
                    'status' => $defaultData['status'],
                    'leasing_type' => $defaultData['leasing_type'],
                    'bedrooms' => $defaultData['bedrooms'],
                    'bathrooms' => $defaultData['bathrooms'],
                    'tenant_count' => $defaultData['tenant_count'],
                    'max_occupants' => $defaultData['max_occupants'],
                    'floor_number' => $currentFloor,
                    'description' => "Auto-generated unit",
                    'amenities' => $request->amenities ?? [],
                    'is_furnished' => false,
                ]);

                $unitsCreated++;
                \Log::info("Created unit {$i}/{$totalUnits}", [
                    'unit_id' => $unit->id,
                    'unit_number' => $unitNumber,
                    'floor' => $currentFloor,
                    'unit_on_floor' => $unitOnFloor
                ]);

                // Move to next floor if needed
                if ($unitOnFloor >= $unitsPerFloor && $currentFloor < $numFloors) {
                    $currentFloor++;
                    $unitOnFloor = 1;
                } else {
                    $unitOnFloor++;
                }
            } catch (\Exception $e) {
                \Log::error("Failed to create unit {$i}", [
                    'error' => $e->getMessage(),
                    'apartment_id' => $apartment->id,
                    'unit_number' => $unitNumber ?? 'unknown'
                ]);
                // Continue with next unit
            }
        }

        \Log::info("Auto-generated {$unitsCreated} units for apartment: {$apartment->name}");
    }

    /**
     * Generate unit number based on pattern
     */
    private function generateUnitNumber($index, $floor, $unitOnFloor, $pattern)
    {
        switch ($pattern) {
            case 'floor_based':
                // Format: 101, 102, 201, 202, etc.
                return ($floor * 100) + $unitOnFloor;
                
            case 'sequential':
                // Format: Unit 1, Unit 2, Unit 3, etc.
                return "Unit {$index}";
                
            case 'letter_number':
                // Format: A1, A2, B1, B2, etc.
                $letter = chr(64 + $floor); // A, B, C, etc.
                return "{$letter}{$unitOnFloor}";
                
            default:
                return ($floor * 100) + $unitOnFloor;
        }
    }


    public function editApartment($id)
    {
        $apartment = Auth::user()->apartments()->findOrFail($id);
        return view('landlord.edit-apartment', compact('apartment'));
    }

    public function updateApartment(Request $request, $id)
    {
        $apartment = Auth::user()->apartments()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'total_units' => 'required|integer|min:1',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
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
            \Log::error('Error updating apartment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update apartment. Please try again.');
        }
    }

    /**
     * Auto-generate additional units for an existing apartment
     */
    private function autoGenerateAdditionalUnits($apartment, $numUnitsToCreate, $startingIndex = 0)
    {
        $currentMaxFloor = $apartment->units()->max('floor_number') ?? 0;
        $existingUnits = $apartment->units()->count();
        
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
        $currentFloor = $currentMaxFloor > 0 ? $currentMaxFloor : 1;
        $unitOnFloor = 1;

        for ($i = 1; $i <= $numUnitsToCreate; $i++) {
            $globalIndex = $existingUnits + $i;
            
            // Simple floor-based numbering
            $unitNumber = ($currentFloor * 100) + $unitOnFloor;

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
                'floor_number' => $currentFloor,
                'description' => "Auto-generated unit",
                'amenities' => $apartment->amenities ?? [],
                'is_furnished' => false,
            ]);

            $unitsCreated++;
            $unitOnFloor++;

            // Move to next floor every 10 units (configurable)
            if ($unitOnFloor > 10) {
                $currentFloor++;
                $unitOnFloor = 1;
            }
        }

        \Log::info("Auto-generated {$unitsCreated} additional units for apartment: {$apartment->name}");
        return $unitsCreated;
    }

    public function deleteApartment(Request $request, $id)
    {
        $apartment = Auth::user()->apartments()->findOrFail($id);
        
        try {
            $unitCount = $apartment->units()->count();
            $forceDelete = $request->boolean('force_delete');
            
            // If force delete is requested, verify password
            if ($forceDelete) {
                $request->validate([
                    'password' => 'required|string',
                ]);
                
                // Verify landlord's password
                if (!Hash::check($request->password, Auth::user()->password)) {
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
            \Log::error('Error deleting apartment: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete property. Please try again.');
        }
    }

    public function units(Request $request, $apartmentId = null)
    {
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
                // Sort by property name, then unit number (alphanumeric)
                $query->join('apartments', 'units.apartment_id', '=', 'apartments.id')
                      ->orderBy('apartments.name')
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
                $query->orderBy('unit_number');
                break;
            case 'status':
                $query->orderByRaw("FIELD(status, 'available', 'occupied', 'maintenance')")
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
                      ->orderBy('units.unit_number')
                      ->select('units.*');
        }
        
        $units = $query->paginate(20);
        $apartments = $landlord->apartments()->orderBy('name')->get();
        
        return view('landlord.units', compact('units', 'apartments', 'apartmentId', 'stats'));
    }

    public function createUnit($apartmentId = null)
    {
        if ($apartmentId) {
            $apartment = Auth::user()->apartments()->findOrFail($apartmentId);
            return view('landlord.create-unit', compact('apartment'));
        } else {
            // Show property selection first
            $apartments = Auth::user()->apartments()->get();
            return view('landlord.select-property-for-unit', compact('apartments'));
        }
    }

    public function storeUnit(Request $request, $apartmentId)
    {
        $apartment = Auth::user()->apartments()->findOrFail($apartmentId);

        $request->validate([
            'unit_number' => 'required|string|max:50|unique:units,unit_number,NULL,id,apartment_id,' . $apartmentId,
            'unit_type' => 'required|string|max:100',
            'rent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance',
            'leasing_type' => 'required|in:separate,inclusive',
            'description' => 'nullable|string|max:1000',
            'floor_area' => 'nullable|numeric|min:0',
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
            
            \Log::info('Unit cover image upload', ['result' => $uploadResult]);
            
            // Output to browser console for debugging
            echo "<script>
                console.group('🏠 Supabase Unit Cover Image Upload');
                console.log('📁 Upload Path:', " . json_encode($path) . ");
                console.log('✅ Upload Result:', " . json_encode($uploadResult) . ");
                console.log('🔗 Public URL:', " . json_encode($uploadResult['url'] ?? null) . ");
                console.groupEnd();
            </script>";
            
            // Check if upload was successful
            if ($uploadResult['success']) {
                $coverPath = $uploadResult['url'];
            } else {
                \Log::error('Failed to upload unit cover image', ['result' => $uploadResult]);
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
                
                \Log::info('Unit gallery image uploaded', ['index' => $index, 'result' => $uploadResult]);
                
                // Output to browser console
                echo "<script>
                    console.log('🖼️ Unit Gallery Image " . ($index + 1) . ":', " . json_encode($uploadResult) . ");
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

    public function updateUnit(Request $request, $id)
    {
        $unit = Unit::whereHas('apartment', function($query) {
            $query->where('landlord_id', Auth::id());
        })->findOrFail($id);

        try {
            $request->validate([
                'unit_number' => 'required|string|max:50|unique:units,unit_number,' . $unit->id . ',id,apartment_id,' . $unit->apartment_id,
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
            \Log::error('Error updating unit: ' . $e->getMessage());
            
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

    public function bulkGenerateUnits(Request $request)
    {
        try {
            $validated = $request->validate([
                'apartment_id' => 'required|exists:apartments,id',
                'num_units' => 'required|integer|min:1|max:500',
                'default_unit_type' => 'nullable|string',
                'default_rent' => 'nullable|numeric|min:0',
                'default_bedrooms' => 'nullable|integer|min:0',
                'default_bathrooms' => 'nullable|numeric|min:0',
                'numbering_pattern' => 'required|in:floor_based,sequential,letter_number',
                'num_floors' => 'nullable|integer|min:1',
                'units_per_floor' => 'nullable|integer|min:1',
            ]);

            $apartment = Auth::user()->apartments()->findOrFail($validated['apartment_id']);
            
            // Prepare default data
            $defaultData = [
                'unit_type' => $validated['default_unit_type'] ?? 'Two Bedroom',
                'rent_amount' => $validated['default_rent'] ?? null,
                'bedrooms' => $validated['default_bedrooms'] ?? 2,
                'bathrooms' => $validated['default_bathrooms'] ?? 1,
                'status' => 'available',
            ];

            $numToGenerate = (int) $validated['num_units'];
            $pattern = $validated['numbering_pattern'];
            $unitsCreated = 0;

            for ($i = 1; $i <= $numToGenerate; $i++) {
                // Generate unit number based on pattern
                $unitNumber = $this->generateUnitNumber(
                    $i,
                    $pattern,
                    $validated['num_floors'] ?? 1,
                    $validated['units_per_floor'] ?? 1
                );

                // Check if unit number already exists for this apartment
                if ($apartment->units()->where('unit_number', $unitNumber)->exists()) {
                    continue; // Skip if exists
                }

                // Create the unit
                $apartment->units()->create(array_merge($defaultData, [
                    'unit_number' => $unitNumber,
                ]));
                
                $unitsCreated++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully generated {$unitsCreated} units for {$apartment->name}!",
                'units_created' => $unitsCreated,
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk unit generation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate units: ' . $e->getMessage(),
            ], 500);
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
            \Log::error('Error deleting unit: ' . $e->getMessage());
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
            'phone' => 'required|string|max:20',
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
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'landlord',
            'status' => 'pending',
            'phone' => $request->phone,
            'address' => $request->address,
            'business_info' => $request->business_info,
        ]);

        // Create landlord profile for role-specific data
        LandlordProfile::create([
            'user_id' => $landlord->id,
            'phone' => $request->phone,
            'address' => $request->address,
            'business_info' => $request->business_info,
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
            
            \Log::info('Landlord document uploaded', [
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
                \Log::error('Failed to upload landlord document', [
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
        $apartments = Auth::user()->apartments()->orderBy('name')->get();
        
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
        $apartment = Auth::user()->apartments()->with('units')->findOrFail($id);
        
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
        $apartment = Auth::user()->apartments()->findOrFail($id);
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
        $apartment = Auth::user()->apartments()->findOrFail($apartmentId);

        $request->validate([
            'unit_number' => 'required|string|max:50|unique:units,unit_number,NULL,id,apartment_id,' . $apartmentId,
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
            \Log::error('Error creating unit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create unit. Please try again.'
            ], 500);
        }
    }
}
