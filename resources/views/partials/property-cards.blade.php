@if($properties->count() > 0)
    <div class="properties-grid">
        @foreach($properties as $property)
            <a href="{{ route('property.show', $property->slug) }}" class="property-card">
                @if($property->image_path && file_exists(public_path($property->image_path)))
                    <img src="{{ asset($property->image_path) }}" alt="{{ $property->title }}" class="property-image">
                @else
                    <div class="property-image-placeholder">
                        <div>
                            <i class="fas fa-home fa-3x mb-2"></i>
                            <div>No Image Available</div>
                        </div>
                    </div>
                @endif

                <div class="property-content">
                    <span class="property-type">{{ ucfirst($property->type) }}</span>
                    
                    <h3 class="property-title">{{ $property->title }}</h3>
                    
                    @if($property->address)
                        <div class="property-address">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ Str::limit($property->address, 40) }}</span>
                        </div>
                    @endif

                    <div class="property-features">
                        <div class="property-feature">
                            <i class="fas fa-bed"></i>
                            <span>{{ $property->bedrooms }} Bed</span>
                        </div>
                        <div class="property-feature">
                            <i class="fas fa-bath"></i>
                            <span>{{ $property->bathrooms }} Bath</span>
                        </div>
                        @if($property->area)
                            <div class="property-feature">
                                <i class="fas fa-ruler-combined"></i>
                                <span>{{ number_format($property->area) }} m²</span>
                            </div>
                        @endif
                    </div>

                    <div class="property-price">
                        ₱{{ number_format($property->price, 2) }}
                        <small style="font-size: 0.875rem; font-weight: 400; color: #64748b;">/month</small>
                    </div>

                    <span class="property-availability {{ $property->availability_status }}">
                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                        {{ ucfirst($property->availability_status) }}
                    </span>

                    @if($property->amenities->count() > 0)
                        <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            @foreach($property->amenities->take(3) as $amenity)
                                <span style="padding: 0.25rem 0.5rem; background: #f8fafc; border-radius: 4px; font-size: 0.75rem; color: #64748b;">
                                    <i class="{{ $amenity->icon }}"></i>
                                </span>
                            @endforeach
                            @if($property->amenities->count() > 3)
                                <span style="padding: 0.25rem 0.5rem; background: #f8fafc; border-radius: 4px; font-size: 0.75rem; color: #64748b;">
                                    +{{ $property->amenities->count() - 3 }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-search"></i>
        <h3>No Properties Found</h3>
        <p>Try adjusting your filters to see more results.</p>
    </div>
@endif

