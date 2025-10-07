<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Amenity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $landlords = User::where('role', 'landlord')->get();
        
        // If no landlords exist, create a default one
        if ($landlords->isEmpty()) {
            $landlords = collect([
                User::create([
                    'name' => 'Demo Landlord',
                    'email' => 'landlord@demo.com',
                    'password' => bcrypt('password'),
                    'role' => 'landlord',
                    'status' => 'approved',
                ])
            ]);
        }

        $amenities = Amenity::all();

        $properties = [
            [
                'title' => 'Modern Studio Apartment Downtown',
                'description' => 'Beautiful modern studio apartment in the heart of downtown. Features include high ceilings, hardwood floors, and large windows with city views.',
                'type' => 'studio',
                'price' => 15000.00,
                'address' => '123 Main Street',
                'city' => 'Manila',
                'state' => 'Metro Manila',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'area' => 35.5,
                'availability_status' => 'available',
            ],
            [
                'title' => 'Spacious 2-Bedroom Condo',
                'description' => 'Spacious 2-bedroom condo with modern amenities. Perfect for small families or professionals.',
                'type' => 'condo',
                'price' => 25000.00,
                'address' => '456 Skyline Avenue',
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area' => 65.0,
                'availability_status' => 'available',
            ],
            [
                'title' => 'Luxury 3-Bedroom House',
                'description' => 'Luxurious 3-bedroom house with garden and garage. Quiet neighborhood with excellent schools nearby.',
                'type' => 'house',
                'price' => 45000.00,
                'address' => '789 Garden Street',
                'city' => 'Makati',
                'state' => 'Metro Manila',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 120.0,
                'availability_status' => 'available',
            ],
            [
                'title' => 'Cozy 1-Bedroom Apartment',
                'description' => 'Cozy 1-bedroom apartment perfect for singles or couples. Close to public transportation.',
                'type' => 'apartment',
                'price' => 12000.00,
                'address' => '321 Sunset Boulevard',
                'city' => 'Pasig',
                'state' => 'Metro Manila',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'area' => 40.0,
                'availability_status' => 'available',
            ],
            [
                'title' => 'Premium Penthouse',
                'description' => 'Premium penthouse with panoramic city views. Features include a private terrace and premium finishes throughout.',
                'type' => 'condo',
                'price' => 75000.00,
                'address' => '999 Highrise Tower',
                'city' => 'Bonifacio Global City',
                'state' => 'Metro Manila',
                'bedrooms' => 3,
                'bathrooms' => 3,
                'area' => 150.0,
                'availability_status' => 'occupied',
            ],
        ];

        foreach ($properties as $propertyData) {
            $property = Property::create([
                ...$propertyData,
                'slug' => Str::slug($propertyData['title']),
                'landlord_id' => $landlords->random()->id,
                'is_featured' => rand(0, 1) == 1,
                'is_active' => true,
            ]);

            // Attach random amenities (3-6 amenities per property)
            if ($amenities->isNotEmpty()) {
                $randomAmenities = $amenities->random(rand(3, min(6, $amenities->count())));
                $property->amenities()->attach($randomAmenities->pluck('id'));
            }
        }
    }
}

