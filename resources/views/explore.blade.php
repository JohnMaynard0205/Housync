<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Explore Properties - HouseSync</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        /* Header */
        .explore-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0 2rem;
        }

        .explore-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .explore-header p {
            font-size: 1.125rem;
            opacity: 0.95;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: -2rem;
            margin-bottom: 2rem;
        }

        .filter-group {
            margin-bottom: 1rem;
        }

        .filter-group label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.875rem;
        }

        .filter-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .filter-pill {
            padding: 0.5rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .filter-pill:hover {
            border-color: #667eea;
            background: #f1f5ff;
        }

        .filter-pill.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .amenity-checkbox {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .amenity-checkbox:hover {
            border-color: #667eea;
            background: #f8fafc;
        }

        .amenity-checkbox input[type="checkbox"] {
            margin-right: 0.75rem;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .amenity-checkbox.checked {
            border-color: #667eea;
            background: #f1f5ff;
        }

        /* Property Cards */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .property-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .property-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #e2e8f0;
        }

        .property-image-placeholder {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            font-weight: 600;
        }

        .property-content {
            padding: 1.25rem;
        }

        .property-type {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #f1f5ff;
            color: #667eea;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .property-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .property-address {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .property-features {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .property-feature {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .property-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .property-availability {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .property-availability.available {
            background: #d1fae5;
            color: #065f46;
        }

        .property-availability.occupied {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e2e8f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #64748b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .explore-header h1 {
                font-size: 2rem;
            }

            .properties-grid {
                grid-template-columns: 1fr;
            }

            .filter-section {
                margin-top: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="explore-header">
        <div class="container">
            <h1><i class="fas fa-search me-2"></i>Explore Properties</h1>
            <p>Find your perfect place to live</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container" style="margin-top: -2rem; padding-bottom: 3rem;">
        <!-- Filter Section -->
        <div class="filter-section">
            <form id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="filter-group">
                            <label><i class="fas fa-search me-1"></i> Search</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Search properties...">
                        </div>
            </div>

                    <!-- Property Type -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="filter-group">
                            <label><i class="fas fa-building me-1"></i> Property Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($propertyTypes as $type)
                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
            </div>
            </div>

                    <!-- Availability -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="filter-group">
                            <label><i class="fas fa-calendar-check me-1"></i> Availability</label>
                            <select name="availability" id="availability" class="form-select">
                                <option value="">All</option>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                </select>
            </div>
                    </div>

                    <!-- Price Range -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="filter-group">
                            <label><i class="fas fa-dollar-sign me-1"></i> Min Price</label>
                            <input type="number" name="min_price" id="min_price" class="form-control" placeholder="Min ₱">
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="filter-group">
                            <label><i class="fas fa-dollar-sign me-1"></i> Max Price</label>
                            <input type="number" name="max_price" id="max_price" class="form-control" placeholder="Max ₱">
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="filter-group">
                            <label><i class="fas fa-calendar me-1"></i> Available From</label>
                            <input type="date" name="available_from" id="available_from" class="form-control">
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="filter-group">
                            <label><i class="fas fa-calendar me-1"></i> Available To</label>
                            <input type="date" name="available_to" id="available_to" class="form-control">
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="col-12">
                        <div class="filter-group">
                            <label><i class="fas fa-star me-1"></i> Amenities</label>
                            <div class="row g-2">
                                @foreach($amenities as $amenity)
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                        <label class="amenity-checkbox">
                                            <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="amenity-input">
                                            <span>
                                                <i class="{{ $amenity->icon }} me-1"></i>
                                                {{ $amenity->name }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="filter-group">
                            <label><i class="fas fa-sort me-1"></i> Sort By</label>
                            <select name="sort_by" id="sort_by" class="form-select">
                                <option value="latest">Latest</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="featured">Featured</option>
                            </select>
            </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-12 col-md-6 col-lg-8">
                        <div class="filter-group">
                            <label class="d-none d-lg-block">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" id="applyFilters" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-filter me-1"></i> Apply Filters
                                </button>
                                <button type="button" id="clearFilters" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Count -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <span id="resultsCount">{{ $properties->total() }}</span> Properties Found
            </h5>
        </div>

        <!-- Properties Grid -->
        <div id="propertiesContainer">
            @include('partials.property-cards', ['properties' => $properties])
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="d-flex justify-content-center">
            {{ $properties->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Amenity checkbox styling
            $('.amenity-checkbox input').on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).closest('.amenity-checkbox').addClass('checked');
                } else {
                    $(this).closest('.amenity-checkbox').removeClass('checked');
                }
            });

            // Apply filters
            function applyFilters(page = 1) {
                const formData = {
                    search: $('#search').val(),
                    type: $('#type').val(),
                    availability: $('#availability').val(),
                    min_price: $('#min_price').val(),
                    max_price: $('#max_price').val(),
                    available_from: $('#available_from').val(),
                    available_to: $('#available_to').val(),
                    sort_by: $('#sort_by').val(),
                    amenities: $('input[name="amenities[]"]:checked').map(function() {
                        return $(this).val();
                    }).get(),
                    page: page
                };

                // Show loading
                $('#loadingOverlay').addClass('active');

                $.ajax({
                    url: '{{ route("explore") }}',
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        $('#propertiesContainer').html(response.html);
                        $('#paginationContainer').html(response.pagination);
                        $('#resultsCount').text(response.count);
                        
                        // Save filters to localStorage
                        localStorage.setItem('exploreFilters', JSON.stringify(formData));
                        
                        // Smooth scroll to top
                        $('html, body').animate({ scrollTop: 0 }, 300);
                    },
                    error: function(xhr) {
                        console.error('Filter error:', xhr);
                        alert('An error occurred while filtering properties.');
                    },
                    complete: function() {
                        $('#loadingOverlay').removeClass('active');
                    }
                });
            }

            // Apply filters button
            $('#applyFilters').on('click', function() {
                applyFilters();
            });

            // Apply filters on Enter key
            $('#filterForm input, #filterForm select').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    applyFilters();
                }
            });

            // Clear filters
            $('#clearFilters').on('click', function() {
                $('#filterForm')[0].reset();
                $('.amenity-checkbox').removeClass('checked');
                localStorage.removeItem('exploreFilters');
                applyFilters();
            });

            // Handle pagination clicks
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const page = new URL(url).searchParams.get('page');
                applyFilters(page);
            });

            // Restore filters from localStorage
            const savedFilters = localStorage.getItem('exploreFilters');
            if (savedFilters) {
                const filters = JSON.parse(savedFilters);
                $('#search').val(filters.search || '');
                $('#type').val(filters.type || '');
                $('#availability').val(filters.availability || '');
                $('#min_price').val(filters.min_price || '');
                $('#max_price').val(filters.max_price || '');
                $('#available_from').val(filters.available_from || '');
                $('#available_to').val(filters.available_to || '');
                $('#sort_by').val(filters.sort_by || 'latest');
                
                if (filters.amenities && filters.amenities.length > 0) {
                    filters.amenities.forEach(function(amenityId) {
                        $('input[name="amenities[]"][value="' + amenityId + '"]')
                            .prop('checked', true)
                            .closest('.amenity-checkbox').addClass('checked');
                    });
                }
            }

            // Auto-apply filters on select change
            $('#type, #availability, #sort_by').on('change', function() {
                applyFilters();
            });
        });
    </script>
</body>
</html>
