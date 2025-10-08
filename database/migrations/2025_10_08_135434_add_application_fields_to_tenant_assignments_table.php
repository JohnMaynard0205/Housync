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
        Schema::table('tenant_assignments', function (Blueprint $table) {
            // Add new columns for tenant applications
            $table->string('occupation')->nullable()->after('notes');
            $table->decimal('monthly_income', 10, 2)->nullable()->after('occupation');
        });

        // Modify status enum to include 'pending_approval'
        // Note: Laravel doesn't support modifying enums directly, so we use raw SQL
        DB::statement("ALTER TABLE tenant_assignments MODIFY COLUMN status ENUM('pending', 'active', 'terminated', 'pending_approval') DEFAULT 'pending'");
        
        // Make lease dates nullable for applications
        Schema::table('tenant_assignments', function (Blueprint $table) {
            $table->date('lease_start_date')->nullable()->change();
            $table->date('lease_end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_assignments', function (Blueprint $table) {
            $table->dropColumn(['occupation', 'monthly_income']);
        });

        // Revert status enum
        DB::statement("ALTER TABLE tenant_assignments MODIFY COLUMN status ENUM('pending', 'active', 'terminated') DEFAULT 'pending'");
        
        // Revert lease dates back to not nullable
        Schema::table('tenant_assignments', function (Blueprint $table) {
            $table->date('lease_start_date')->nullable(false)->change();
            $table->date('lease_end_date')->nullable(false)->change();
        });
    }
};
