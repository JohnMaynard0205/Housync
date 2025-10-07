<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->title }} - HouseSync</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }

        .property-header {
            background: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .property-image-main {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 12px;
        }

        .property-image-placeholder {
            width: 100%;
            height: 500px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .property-info-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .amenity-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f8fafc;
            border-radius: 8px;
            margin: 0.25rem;
        }

        .related-property-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .related-property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="property-header">
        <div class="container">
            <a href="{{ route('explore') }}" class="btn btn-outline-primary mb-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Explore
            </a>
            <h1>{{ $property->title }}</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-map-marker-alt me-1"></i>
                {{ $property->address ?? 'Location not specified' }}
            </p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-8">
                <!-- Property Image -->
                @if($property->image_path && file_exists(public_path($property->image_path)))
                    <img src="{{ asset($property->image_path) }}" alt="{{ $property->title }}" class="property-image-main mb-4">
                @else
                    <div class="property-image-placeholder mb-4">
                        <div class="text-center">
                            <i class="fas fa-home fa-5x mb-3"></i>
                            <h3>No Image Available</h3>
                        </div>
                    </div>
                @endif

                <!-- Description -->
                <div class="property-info-card">
                    <h3>Description</h3>
                    <p>{{ $property->description ?? 'No description available.' }}</p>
                </div>

                <!-- Amenities -->
                @if($property->amenities->count() > 0)
                    <div class="property-info-card">
                        <h3>Amenities</h3>
                        <div>
                            @foreach($property->amenities as $amenity)
                                <span class="amenity-badge">
                                    <i class="{{ $amenity->icon }}"></i>
                                    {{ $amenity->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Price & Details -->
                <div class="property-info-card">
                    <h2 class="text-primary">₱{{ number_format($property->price, 2) }}</h2>
                    <p class="text-muted">per month</p>

                    <hr>

                    <div class="mb-3">
                        <strong>Type:</strong> {{ ucfirst($property->type) }}
                    </div>
                    <div class="mb-3">
                        <strong>Bedrooms:</strong> {{ $property->bedrooms }}
                    </div>
                    <div class="mb-3">
                        <strong>Bathrooms:</strong> {{ $property->bathrooms }}
                    </div>
                    @if($property->area)
                        <div class="mb-3">
                            <strong>Area:</strong> {{ number_format($property->area) }} m²
                        </div>
                    @endif
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge {{ $property->availability_status == 'available' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($property->availability_status) }}
                        </span>
                    </div>

                    <hr>

                    <button class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-envelope me-1"></i> Contact Landlord
                    </button>
                    <button class="btn btn-outline-primary w-100">
                        <i class="fas fa-calendar me-1"></i> Schedule Viewing
                    </button>
                </div>
            </div>
        </div>

        <!-- Related Properties -->
        @if($relatedProperties->count() > 0)
            <div class="mt-5">
                <h3 class="mb-4">Similar Properties</h3>
                <div class="row">
                    @foreach($relatedProperties as $related)
                        <div class="col-md-3 mb-4">
                            <a href="{{ route('property.show', $related->slug) }}" class="related-property-card">
                                @if($related->image_path)
                                    <img src="{{ asset($related->image_path) }}" alt="{{ $related->title }}" style="width: 100%; height: 200px; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white;">
                                        <div>No Image</div>
                                    </div>
                                @endif
                                <div class="p-3">
                                    <h5>{{ Str::limit($related->title, 30) }}</h5>
                                    <p class="text-primary mb-0 fw-bold">₱{{ number_format($related->price, 2) }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

