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
        Schema::table('reports', function (Blueprint $table) {
            // Add missing columns for reports functionality
            if (!Schema::hasColumn('reports', 'description')) {
                $table->text('description')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('reports', 'priority')) {
                $table->string('priority')->default('low')->after('status');
            }
            if (!Schema::hasColumn('reports', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $columns = ['description', 'priority'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('reports', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
