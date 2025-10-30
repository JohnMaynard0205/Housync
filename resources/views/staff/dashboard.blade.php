@extends('layouts.staff-app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">Staff Dashboard</li>
                </ol>
            </div>
            <h4 class="page-title">Staff Dashboard</h4>
        </div>
    </div>
</div>

<!-- Welcome Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-lg me-3">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            <i class="mdi mdi-account font-24 text-primary"></i>
                        </span>
                    </div>
                    <div>
                        <h4 class="mb-1">Welcome, {{ Auth::user()->name }}!</h4>
                        <p class="text-muted mb-0">
                            <i class="mdi {{ $assignment->staff_type_icon }} me-1"></i>
                            {{ $assignment->staff_type_display }} - Assigned to {{ $assignment->unit->unit_number }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Details -->
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="mdi mdi-tools me-1"></i>
                    Unit Assignment Maintenance Details
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Unit Information</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Number:</label>
                            <p class="mb-1">{{ $assignment->unit->unit_number }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Apartment:</label>
                            <p class="mb-1">{{ $assignment->unit->apartment->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Address:</label>
                            <p class="mb-1">{{ $assignment->unit->apartment->address }}</p>
                        </div>
                        @if($assignment->unit->description)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description:</label>
                            <p class="mb-1">{{ $assignment->unit->description }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Maintenance Assignment Details</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Staff Type:</label>
                            <p class="mb-1">
                                <span class="badge bg-info">
                                    <i class="mdi {{ $assignment->staff_type_icon }} me-1"></i>
                                    {{ $assignment->staff_type_display }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Assignment Period:</label>
                            <p class="mb-1">
                                <strong>Start:</strong> {{ $assignment->assignment_start_date->format('M d, Y') }}<br>
                                @if($assignment->assignment_end_date)
                                    <strong>End:</strong> {{ $assignment->assignment_end_date->format('M d, Y') }}
                                @else
                                    <strong>Status:</strong> <span class="text-success">Ongoing</span>
                                @endif
                            </p>
                        </div>
                        @if($assignment->hourly_rate)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hourly Rate:</label>
                            <p class="mb-1">₱{{ number_format($assignment->hourly_rate, 2) }}/hr</p>
                        </div>
                        @endif
                        @if($assignment->notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Assignment Notes:</label>
                            <p class="mb-1">{{ $assignment->notes }}</p>
                        </div>
                        @endif
                        
                        <!-- Assignment Completion Section -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="text-primary mb-3">
                                <i class="mdi mdi-check-circle me-1"></i>
                                Assignment Completion
                            </h6>
                            @if($assignment->status === 'active')
                                <p class="text-muted small mb-3">Mark this assignment as completed when you have finished all maintenance work for this unit.</p>
                                <button type="button" class="btn btn-success" onclick="markAssignmentAsCompleted({{ $assignment->id }})">
                                    <i class="mdi mdi-check me-1"></i> Mark Assignment as Completed
                                </button>
                            @elseif($assignment->status === 'completed')
                                <div class="alert alert-success mb-0">
                                    <i class="mdi mdi-check-circle me-1"></i>
                                    This assignment has been completed successfully!
                                    @if($assignment->assignment_end_date)
                                        <br><small>Completed on: {{ $assignment->assignment_end_date->format('M d, Y') }}</small>
                                    @endif
                                </div>
                            @elseif($assignment->status === 'terminated')
                                <div class="alert alert-info mb-0">
                                    <i class="mdi mdi-information me-1"></i>
                                    This assignment has been terminated by the landlord.
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="mdi mdi-pause me-1"></i>
                                    This assignment is currently inactive.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="mdi mdi-account me-1"></i>
                    Landlord Information
                </h4>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            {{ substr($assignment->landlord->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $assignment->landlord->name }}</h6>
                        <small class="text-muted">{{ $assignment->landlord->email }}</small>
                    </div>
                </div>
                @if($assignment->landlord->phone)
                <div class="mb-2">
                    <label class="form-label fw-bold">Phone:</label>
                    <p class="mb-1">{{ $assignment->landlord->phone }}</p>
                </div>
                @endif
                @if($assignment->landlord->address)
                <div class="mb-2">
                    <label class="form-label fw-bold">Address:</label>
                    <p class="mb-1">{{ $assignment->landlord->address }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="mdi mdi-lightning-bolt me-1"></i>
                    Quick Actions
                </h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-primary" onclick="viewAllMaintenanceRequests()">
                        <i class="mdi mdi-tools me-1"></i> View All Maintenance Requests
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="mdi mdi-message me-1"></i> Contact Landlord
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="mdi mdi-account-edit me-1"></i> Update Profile
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Requests Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="mdi mdi-tools me-1"></i>
                    Maintenance Requests
                </h4>
            </div>
            <div class="card-body">
                @if($maintenanceRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-striped">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Issue</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Requested Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($maintenanceRequests as $request)
                                <tr>
                                    <td>#{{ $request->id }}</td>
                                    <td>
                                        <strong>{{ $request->title }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($request->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i class="mdi {{ $request->category_icon }} me-1"></i>
                                            {{ ucfirst($request->category) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $request->priority_badge_class }}">
                                            {{ ucfirst($request->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $request->status_badge_class }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->requested_date->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewMaintenanceDetails({{ $request->id }})" title="View Details">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                            @if($request->status !== 'completed')
                                            <button type="button" class="btn btn-sm btn-success" onclick="markAsCompleted({{ $request->id }})" title="Mark as Completed">
                                                <i class="mdi mdi-check"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-check-circle text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No Maintenance Requests</h5>
                        <p class="text-muted">There are currently no maintenance requests for your assigned unit.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Unit Details -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="mdi mdi-information me-1"></i>
                    Unit Specifications
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="mdi mdi-bed text-primary" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">{{ $assignment->unit->bedrooms }}</h5>
                            <p class="text-muted">Bedrooms</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="mdi mdi-shower text-info" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">{{ $assignment->unit->bathrooms }}</h5>
                            <p class="text-muted">Bathrooms</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="mdi mdi-ruler text-success" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">{{ $assignment->unit->floor_area ?? 'N/A' }}</h5>
                            <p class="text-muted">Floor Area (sqm)</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="mdi mdi-currency-php text-warning" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">₱{{ number_format($assignment->unit->rent_amount, 0) }}</h5>
                            <p class="text-muted">Monthly Rent</p>
                        </div>
                    </div>
                </div>

                @if($assignment->unit->amenities)
                <div class="mt-4">
                    <h6 class="text-primary">Unit Amenities:</h6>
                    <div class="row">
                        @foreach($assignment->unit->amenities as $amenity)
                        <div class="col-md-3 mb-2">
                            <span class="badge bg-light text-dark">
                                <i class="mdi mdi-check me-1"></i>{{ $amenity }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewMaintenanceDetails(requestId) {
    // TODO: Implement maintenance details modal
    alert('Maintenance request details will be shown here. Request ID: ' + requestId);
}

function markAsCompleted(requestId) {
    if (confirm('Are you sure you want to mark this maintenance request as completed?')) {
        // TODO: Implement API call to mark request as completed
        fetch(`/staff/maintenance-requests/${requestId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Maintenance request marked as completed successfully!');
                location.reload(); // Refresh the page to show updated status
            } else {
                alert('Error marking request as completed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error marking request as completed. Please try again.');
        });
    }
}

function markAssignmentAsCompleted(assignmentId) {
    if (confirm('Are you sure you want to mark this assignment as completed? This will indicate that all maintenance work for this unit has been finished.')) {
        // TODO: Implement API call to mark assignment as completed
        fetch(`/staff/assignments/${assignmentId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment marked as completed successfully! The landlord will be notified.');
                location.reload(); // Refresh the page to show updated status
            } else {
                alert('Error marking assignment as completed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error marking assignment as completed. Please try again.');
        });
    }
}

function viewAllMaintenanceRequests() {
    // Scroll to maintenance requests section
    document.querySelector('.card-header h4[class*="card-title"]').scrollIntoView({ 
        behavior: 'smooth' 
    });
}

// Add any additional staff dashboard functionality here
</script>
@endpush 