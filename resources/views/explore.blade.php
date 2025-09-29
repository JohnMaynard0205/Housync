<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Explore Properties - HouseSync</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 0; color: #1f2937; }
        .topbar { display:flex; align-items:center; justify-content:space-between; padding:14px 24px; border-bottom: 1px solid #e5e7eb; position: sticky; top:0; background:#fff; z-index:10; }
        .brand { display:flex; align-items:center; gap:10px; font-weight:700; color:#1f2937; }
        .brand i { color:#3b82f6; }
        .search { display:flex; gap:10px; background:#fff; border:1px solid #e5e7eb; border-radius:999px; padding:8px 12px; box-shadow:0 1px 2px rgba(0,0,0,.04); }
        .search input { border:none; outline:none; width:200px; font:inherit; }
        .filters { display:flex; gap:10px; }
        .pill { border:1px solid #e5e7eb; padding:8px 12px; border-radius:999px; background:#fff; cursor:pointer; font-size:14px; }
        .container { max-width:1200px; margin: 0 auto; padding: 20px; }
        .section-title { font-size:18px; font-weight:700; margin: 12px 0 16px; }
        .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(240px,1fr)); gap:16px; }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; overflow:hidden; box-shadow:0 1px 2px rgba(0,0,0,.04); }
        .thumb { aspect-ratio: 4/3; background: #f3f4f6; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:12px; }
        .card-body { padding:12px; }
        .title { font-weight:600; margin-bottom:4px; }
        .addr { color:#6b7280; font-size:12px; margin-bottom:8px; }
        .meta { display:flex; justify-content:space-between; align-items:center; font-size:12px; }
        .badge { background:#ecfdf5; color:#059669; padding:4px 8px; border-radius:999px; }
        .price { font-weight:700; }
        .empty { text-align:center; color:#6b7280; padding:40px 0; }
        .foot { text-align:center; margin:16px 0 24px; }
        .login-link { color:#2563eb; text-decoration:none; font-weight:600; }
        .login-link:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand"><i class="fas fa-building"></i> HouseSync</div>
        <div class="search">
            <i class="fas fa-magnifying-glass" style="color:#6b7280"></i>
            <input type="text" placeholder="Search properties..." onkeypress="if(event.key==='Enter'){alert('Search coming soon');}">
        </div>
        <div class="filters">
            <button class="pill" onclick="alert('Filter coming soon')">Price</button>
            <button class="pill" onclick="alert('Filter coming soon')">Beds</button>
            <a class="pill" href="{{ route('login') }}">Login</a>
            <a class="pill" href="{{ route('register') }}">Register</a>
        </div>
    </div>
    <div class="container">
        <div class="section-title">Available properties</div>

        <!-- Filters -->
        <form method="GET" action="{{ route('explore') }}" style="margin:12px 0 20px; display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:12px; align-items:end;">
            <div>
                <label style="display:block; font-size:12px; color:#6b7280; margin-bottom:4px;">City</label>
                <input type="text" name="city" value="{{ request('city') }}" placeholder="e.g., Manila" style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px;">
            </div>
            <div>
                <label style="display:block; font-size:12px; color:#6b7280; margin-bottom:4px;">Min Price</label>
                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px;">
            </div>
            <div>
                <label style="display:block; font-size:12px; color:#6b7280; margin-bottom:4px;">Max Price</label>
                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="20000" style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px;">
            </div>
            <div>
                <label style="display:block; font-size:12px; color:#6b7280; margin-bottom:4px;">Bedrooms</label>
                <select name="bedrooms" style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px;">
                    <option value="">Any</option>
                    <option value="0" {{ request('bedrooms')==='0' ? 'selected' : '' }}>Studio</option>
                    <option value="1" {{ request('bedrooms')==='1' ? 'selected' : '' }}>1+</option>
                    <option value="2" {{ request('bedrooms')==='2' ? 'selected' : '' }}>2+</option>
                    <option value="3" {{ request('bedrooms')==='3' ? 'selected' : '' }}>3+</option>
                </select>
            </div>
            <div>
                <button type="submit" class="pill" style="width:100%; text-align:center;">Filter</button>
            </div>
        </form>
        <div class="grid">
            @forelse($properties as $apt)
                @php
                    $units = collect(data_get($apt, 'units', []));
                    $available = $units->count();
                    $starting = $available ? $units->min('rent_amount') : null;
                    $images = [];
                    if (data_get($apt, 'cover_image')) $images[] = asset('storage/'.data_get($apt, 'cover_image'));
                    foreach ((array) data_get($apt, 'gallery', []) as $g) { $images[] = asset('storage/'.$g); }
                    if (!$images) {
                        foreach ($units as $u) {
                            if (data_get($u, 'cover_image')) { $images[] = asset('storage/'.data_get($u,'cover_image')); break; }
                        }
                    }
                @endphp
                <div class="card">
                    <div class="thumb">
                        @if(!empty($images))
                            <img src="{{ $images[0] }}" alt="{{ data_get($apt,'name') }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                        @else
                            No image
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="title">{{ data_get($apt, 'name') }}</div>
                        <div class="addr">{{ data_get($apt, 'address') }}</div>
                        <div class="meta">
                            <span class="badge">{{ $available }} available</span>
                            <span class="price">@if($starting) ₱{{ number_format($starting,2) }} / mo @else N/A @endif</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty">No properties to show.</div>
            @endforelse
        </div>
        <div class="foot">
            {{ $properties->links() }}
        </div>
    </div>
</body>
</html>


