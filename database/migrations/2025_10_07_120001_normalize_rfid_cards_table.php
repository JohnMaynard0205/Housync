<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, migrate existing data to the new tenant_rfid_assignments table
        $this->migrateExistingData();
        
        // Remove the tenant_assignment_id column from rfid_cards table
        Schema::table('rfid_cards', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['tenant_assignment_id']);
            // Drop the column
            $table->dropColumn('tenant_assignment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the tenant_assignment_id column
        Schema::table('rfid_cards', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_assignment_id')->nullable()->after('card_uid');
            $table->foreign('tenant_assignment_id')->references('id')->on('tenant_assignments')->onDelete('set null');
            $table->index(['tenant_assignment_id', 'status']);
        });
        
        // Migrate data back from tenant_rfid_assignments to rfid_cards
        $this->migrateDataBack();
    }
    
    /**
     * Migrate existing RFID card assignments to the new normalized structure
     */
    private function migrateExistingData(): void
    {
        // Get all RFID cards that have tenant assignments
        $cardsWithAssignments = DB::table('rfid_cards')
            ->whereNotNull('tenant_assignment_id')
            ->get();
            
        foreach ($cardsWithAssignments as $card) {
            DB::table('tenant_rfid_assignments')->insert([
                'rfid_card_id' => $card->id,
                'tenant_assignment_id' => $card->tenant_assignment_id,
                'assigned_at' => $card->issued_at,
                'expires_at' => $card->expires_at,
                'status' => $card->status === 'active' ? 'active' : 'inactive',
                'notes' => 'Migrated from original rfid_cards table',
                'created_at' => $card->created_at,
                'updated_at' => $card->updated_at,
            ]);
        }
    }
    
    /**
     * Migrate data back for rollback
     */
    private function migrateDataBack(): void
    {
        // Get active tenant RFID assignments and update the rfid_cards table
        $assignments = DB::table('tenant_rfid_assignments')
            ->where('status', 'active')
            ->get();
            
        foreach ($assignments as $assignment) {
            DB::table('rfid_cards')
                ->where('id', $assignment->rfid_card_id)
                ->update([
                    'tenant_assignment_id' => $assignment->tenant_assignment_id,
                    'updated_at' => now(),
                ]);
        }
    }
};
