<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $card_uid
 * @property int|null $rfid_card_id
 * @property int|null $tenant_assignment_id
 * @property int|null $apartment_id
 * @property string $access_result
 * @property string|null $denial_reason
 * @property \Carbon\Carbon $access_time
 * @property string $reader_location
 * @property array|null $raw_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Apartment|null $apartment
 * @property-read mixed $apartment_name
 * @property-read mixed $denial_reason_display
 * @property-read string $display_badge_class
 * @property-read string $display_result
 * @property-read string|null $entry_state
 * @property-read mixed $result_badge_class
 * @property-read mixed $tenant_name
 * @property-read \App\Models\RfidCard|null $rfidCard
 * @property-read \App\Models\TenantAssignment|null $tenantAssignment
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog betweenDates($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog denied()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog forApartment($apartmentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog forCard($cardUid)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog granted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog recentActivity($hours = 24)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereAccessResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereAccessTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereApartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereCardUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereDenialReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereRawData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereReaderLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereRfidCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereTenantAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessLog whereUpdatedAt($value)
 */
	class AccessLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Property> $properties
 * @property-read int|null $properties_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereUpdatedAt($value)
 */
	class Amenity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string|null $description
 * @property int $landlord_id
 * @property int $total_units
 * @property array|null $amenities
 * @property string|null $contact_person
 * @property string|null $contact_phone
 * @property string|null $contact_email
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $property_type
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property int|null $floors
 * @property int|null $bedrooms
 * @property int|null $year_built
 * @property int|null $parking_spaces
 * @property string|null $cover_image
 * @property array<array-key, mixed>|null $gallery
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AccessLog> $accessLogs
 * @property-read int|null $access_logs_count
 * @property-read string|null $cover_image_url
 * @property-read array $gallery_urls
 * @property-read \App\Models\User $landlord
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RfidCard> $rfidCards
 * @property-read int|null $rfid_cards_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Unit> $units
 * @property-read int|null $units_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment byLandlord($landlordId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereAmenities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereBedrooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereFloors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereGallery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereLandlordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereParkingSpaces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment wherePropertyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereTotalUnits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Apartment whereYearBuilt($value)
 */
	class Apartment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $landlord_id
 * @property string $document_type
 * @property string $file_name
 * @property string $file_path
 * @property int $file_size
 * @property string $mime_type
 * @property \Illuminate\Support\Carbon $uploaded_at
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property int|null $verified_by
 * @property string $verification_status
 * @property string|null $verification_notes
 * @property \Illuminate\Support\Carbon|null $expiry_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $landlord
 * @property-read \App\Models\User|null $verifiedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereLandlordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereUploadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereVerificationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereVerificationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordDocument whereVerifiedBy($value)
 */
	class LandlordDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $business_info
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $approved_by
 * @property string|null $rejection_reason
 * @property string|null $company_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SuperAdminProfile|null $approvedBy
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereBusinessInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordProfile whereUserId($value)
 */
	class LandlordProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $unit_id
 * @property int $tenant_id
 * @property int $landlord_id
 * @property string $title
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property string $category
 * @property \Carbon\Carbon $requested_date
 * @property \Carbon\Carbon|null $completed_date
 * @property int|null $assigned_staff_id
 * @property string|null $staff_notes
 * @property string|null $tenant_notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User|null $assignedStaff
 * @property-read mixed $category_icon
 * @property-read mixed $priority_badge_class
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\User $landlord
 * @property-read \App\Models\User $tenant
 * @property-read \App\Models\Unit $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest assignedToStaff($staffId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest byCategory($category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest byPriority($priority)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest byStatus($status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest byUnit($unitId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereAssignedStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereCompletedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereLandlordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereRequestedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereStaffNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereTenantNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceRequest whereUpdatedAt($value)
 */
	class MaintenanceRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $slug
 * @property string $type
 * @property numeric $price
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property int $bedrooms
 * @property int $bathrooms
 * @property numeric|null $area
 * @property string|null $image_path
 * @property string $availability_status
 * @property \Illuminate\Support\Carbon|null $available_from
 * @property \Illuminate\Support\Carbon|null $available_to
 * @property int|null $landlord_id
 * @property bool $is_featured
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Amenity> $amenities
 * @property-read int|null $amenities_count
 * @property-read mixed $gallery_images
 * @property-read mixed $image_url
 * @property-read \App\Models\User|null $landlord
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property filterByAmenities($amenityIds)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property filterByAvailability($availability)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property filterByDateRange($from, $to)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property filterByPriceRange($minPrice, $maxPrice)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property filterByType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property search($search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereAvailabilityStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereAvailableFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereAvailableTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereBathrooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereBedrooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereLandlordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereZipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property withoutTrashed()
 */
	class Property extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $card_uid
 * @property int $landlord_id
 * @property int $apartment_id
 * @property string|null $card_name
 * @property string $status
 * @property \Carbon\Carbon $issued_at
 * @property \Carbon\Carbon|null $expires_at
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AccessLog> $accessLogs
 * @property-read int|null $access_logs_count
 * @property-read \App\Models\TenantRfidAssignment|null $activeTenantAssignment
 * @property-read \App\Models\Apartment $apartment
 * @property-read mixed $display_status
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\User $landlord
 * @property-read \App\Models\TenantAssignment|null $tenantAssignment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TenantRfidAssignment> $tenantRfidAssignments
 * @property-read int|null $tenant_rfid_assignments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard compromised()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard forApartment($apartmentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard forLandlord($landlordId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereApartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereCardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereCardUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereIssuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereLandlordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RfidCard whereUpdatedAt($value)
 */
	class RfidCard extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string $group
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $unit_id
 * @property int $staff_id
 * @property int $landlord_id
 * @property string $staff_type
 * @property \Carbon\Carbon $assigned_at
 * @property \Carbon\Carbon $assignment_start_date
 * @property \Carbon\Carbon|null $assignment_end_date
 * @property float|null $hourly_rate
 * @property string $status
 * @property string|null $notes
 * @property string|null $generated_password
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $staff_type_display
 * @property-read mixed $staff_type_icon
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\User $landlord
 * @property-read \App\Models\User $staff
 * @property-read \App\Models\Unit $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment byLandlord($landlordId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment byStaffType($staffType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereAssignmentEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereAssignmentStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereGeneratedPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereHourlyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereLandlordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereStaffType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffAssignment whereUpdatedAt($value)
 */
	class StaffAssignment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $staff_type
 * @property string|null $license_number
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile whereLicenseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile whereStaffType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StaffProfile whereUserId($value)
 */
	class StaffProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $notes
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdminProfile whereUserId($value)
 */
	class SuperAdminProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $unit_id
 * @property int $tenant_id
 * @property int $landlord_id
 * @property \Carbon\Carbon $assigned_at
 * @property \Carbon\Carbon $lease_start_date
 * @property \Carbon\Carbon $lease_end_date
 * @property float $rent_amount
 * @property float $security_deposit
 * @property string $status
 * @property string|null $notes
 * @property string|null $generated_password
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $occupation
 * @property numeric|null $monthly_income
 * @property int $documents_uploaded
 * @property int $documents_verified
 * @property string|null $verification_notes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AccessLog> $accessLogs
 * @property-read int|null $access_logs_count
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\User $landlord
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RfidCard> $rfidCards
 * @property-read int|null $rfid_cards_count
 * @property-read \App\Models\User $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TenantRfidAssignment> $tenantRfidAssignments
 * @property-read int|null $tenant_rfid_assignments_count
 * @property-read \App\Models\Unit $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereDocumentsUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereDocumentsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereGeneratedPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereLandlordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereLeaseEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereLeaseStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereMonthlyIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereRentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereSecurityDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantAssignment whereVerificationNotes($value)
 */
	class TenantAssignment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property string $document_type
 * @property string $file_name
 * @property string $file_path
 * @property int $file_size
 * @property string $mime_type
 * @property string $verification_status
 * @property int|null $verified_by
 * @property \Carbon\Carbon|null $verified_at
 * @property string|null $verification_notes
 * @property \Carbon\Carbon|null $expiry_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int|null $tenant_assignment_id
 * @property \Illuminate\Support\Carbon $uploaded_at
 * @property-read mixed $document_type_label
 * @property-read mixed $file_size_formatted
 * @property-read mixed $verification_status_badge_class
 * @property-read \App\Models\User|null $tenant
 * @property-read \App\Models\User|null $verifiedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereTenantAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereUploadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereVerificationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereVerificationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantDocument whereVerifiedBy($value)
 */
	class TenantDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $id_number
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_phone
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereEmergencyContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereEmergencyContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantProfile whereUserId($value)
 */
	class TenantProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $rfid_card_id
 * @property int $tenant_assignment_id
 * @property \Carbon\Carbon $assigned_at
 * @property \Carbon\Carbon|null $expires_at
 * @property string $status
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $display_status
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\RfidCard $rfidCard
 * @property-read \App\Models\TenantAssignment $tenantAssignment
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment forCard($cardId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment forTenant($tenantAssignmentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment notExpired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment revoked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment whereRfidCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment whereTenantAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantRfidAssignment whereUpdatedAt($value)
 */
	class TenantRfidAssignment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $unit_number
 * @property int $apartment_id
 * @property string $unit_type
 * @property float $rent_amount
 * @property string $status
 * @property string $leasing_type
 * @property int $tenant_count
 * @property int|null $max_occupants
 * @property int|null $floor_number
 * @property string|null $description
 * @property float|null $floor_area
 * @property int $bedrooms
 * @property int $bathrooms
 * @property bool $is_furnished
 * @property array|null $amenities
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $cover_image
 * @property array<array-key, mixed>|null $gallery
 * @property-read \App\Models\Apartment|null $apartment
 * @property-read \App\Models\User|null $currentTenant
 * @property-read mixed $cover_image_url
 * @property-read mixed $formatted_rent
 * @property-read mixed $gallery_urls
 * @property-read mixed $is_available
 * @property-read mixed $leasing_type_description
 * @property-read mixed $leasing_type_label
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\TenantAssignment|null $tenantAssignment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TenantAssignment> $tenantAssignments
 * @property-read int|null $tenant_assignments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit occupied()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit rentRange($min, $max)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit underMaintenance()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereAmenities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereApartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereBathrooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereBedrooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereFloorArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereFloorNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereGallery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereIsFurnished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereLeasingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereMaxOccupants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereRentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereTenantCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUnitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUnitType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUpdatedAt($value)
 */
	class Unit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $name
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * Delegated to Profile:
 * @property string $status (via profile)
 * @property string|null $phone (via profile)
 * @property string|null $address (via profile)
 * @property string|null $remember_token
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Apartment> $apartments
 * @property-read int|null $apartments_count
 * @property-read User|null $approvedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $approvedUsers
 * @property-read int|null $approved_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TenantDocument> $documents
 * @property-read int|null $documents_count
 * @property-read \Carbon\Carbon|null $approved_at
 * @property-read int|null $approved_by
 * @property-read string|null $business_info
 * @property-read string|null $rejection_reason
 * @property-read string|null $staff_type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TenantAssignment> $landlordAssignments
 * @property-read int|null $landlord_assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LandlordDocument> $landlordDocuments
 * @property-read int|null $landlord_documents_count
 * @property-read \App\Models\LandlordProfile|null $landlordProfile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StaffAssignment> $landlordStaffAssignments
 * @property-read int|null $landlord_staff_assignments_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RfidCard> $rfidCards
 * @property-read int|null $rfid_cards_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StaffAssignment> $staffAssignments
 * @property-read int|null $staff_assignments_count
 * @property-read \App\Models\StaffProfile|null $staffProfile
 * @property-read \App\Models\SuperAdminProfile|null $superAdminProfile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TenantAssignment> $tenantAssignments
 * @property-read int|null $tenant_assignments_count
 * @property-read \App\Models\TenantProfile|null $tenantProfile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TenantDocument> $verifiedDocuments
 * @property-read int|null $verified_documents_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User approvedLandlords()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byRole($role)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User pendingLandlords()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User rejectedLandlords()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

