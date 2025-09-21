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
        // For MySQL, we need to use raw SQL to modify the enum
        DB::statement("ALTER TABLE staff_assignments MODIFY COLUMN status ENUM('active', 'inactive', 'terminated', 'completed') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE staff_assignments MODIFY COLUMN status ENUM('active', 'inactive', 'terminated') NOT NULL DEFAULT 'active'");
    }
};
