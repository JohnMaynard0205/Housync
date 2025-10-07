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

        // Initial page load
        $properties = Property::with(['amenities', 'landlord'])
            ->active()
            ->latest()
            ->paginate(12);

        $propertyTypes = ['apartment', 'house', 'condo', 'studio'];
        
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
            $query->filterByAvailability($request->availability);
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
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->latest();
                break;
            default:
                $query->latest();
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
            ->limit(4)
            ->get();

        return view('property-details', compact('property', 'relatedProperties'));
    }
}

