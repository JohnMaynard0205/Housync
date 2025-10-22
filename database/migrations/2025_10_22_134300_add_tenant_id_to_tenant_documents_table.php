<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenant_documents', function (Blueprint $table) {
            // Add tenant_id column
            $table->foreignId('tenant_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Make tenant_assignment_id nullable (documents can exist without assignment)
            $table->foreignId('tenant_assignment_id')->nullable()->change();
            
            // Add index for tenant_id
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_documents', function (Blueprint $table) {
            // Drop tenant_id column and index
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id']);
            $table->dropColumn('tenant_id');
            
            // Make tenant_assignment_id non-nullable again
            $table->foreignId('tenant_assignment_id')->nullable(false)->change();
        });
    }
};
