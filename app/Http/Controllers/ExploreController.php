<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Amenity;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    /**
     * Display the explore page with properties
     */
    public function index(Request $request)
    {
        $amenities = Amenity::orderBy('name')->get();
        
        // If this is an AJAX request for filtering
        if ($request->ajax()) {
            return $this->filterProperties($request);
        }

        // Initial page load - only show available (non-occupied) properties
        $properties = Property::with(['amenities', 'landlord'])
            ->active()
            ->available()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(12);

        $propertyTypes = ['apartment', 'house', 'condo', 'studio'];
        
        // If the user is an authenticated tenant, render within the tenant layout (with sidebar)
        if (auth()->check() && auth()->user()->role === 'tenant') {
            return view('tenant.explore', compact('properties', 'amenities', 'propertyTypes'));
        }

        return view('explore', compact('properties', 'amenities', 'propertyTypes'));
    }

    /**
     * Filter properties based on request parameters
     */
    public function filterProperties(Request $request)
    {
        $query = Property::with(['amenities', 'landlord'])->active();

        // Apply filters
        if ($request->filled('type')) {
            $query->filterByType($request->type);
        }

        if ($request->filled('availability')) {
            // Only apply custom availability filter if explicitly set
            $query->filterByAvailability($request->availability);
        } else {
            // By default, only show available properties (exclude occupied)
            $query->available();
        }

        if ($request->filled('amenities')) {
            $query->filterByAmenities($request->amenities);
        }

        if ($request->filled('available_from') || $request->filled('available_to')) {
            $query->filterByDateRange($request->available_from, $request->available_to);
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->filterByPriceRange($request->min_price, $request->max_price);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc')->orderBy('id', 'desc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc')->orderBy('id', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->orderBy('id', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        }

        $properties = $query->paginate(12);

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('partials.property-cards', compact('properties'))->render(),
                'pagination' => $properties->links('pagination::bootstrap-5')->render(),
                'count' => $properties->total(),
            ]);
        }

        return view('explore', compact('properties'));
    }

    /**
     * Show single property details
     */
    public function show($slug)
    {
        $property = Property::with(['amenities', 'landlord'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProperties = Property::with(['amenities'])
            ->where('type', $property->type)
            ->where('id', '!=', $property->id)
            ->active()
            ->available()
            ->limit(4)
            ->get();

        return view('property-details', compact('property', 'relatedProperties'));
    }
}

