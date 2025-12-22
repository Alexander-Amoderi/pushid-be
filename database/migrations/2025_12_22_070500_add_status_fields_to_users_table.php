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
        Schema::table('users', function (Blueprint $table) {
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
            
            // Add ban_until column if it doesn't exist
            if (!Schema::hasColumn('users', 'ban_until')) {
                $table->timestamp('ban_until')->nullable()->after('status');
            }
            
            // Add admin_note column if it doesn't exist
            if (!Schema::hasColumn('users', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('ban_until');
            }
            
            // Add last_active_at column if it doesn't exist
            if (!Schema::hasColumn('users', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable();
            }
        });
        
        // Convert is_banned column data to status if is_banned exists
        if (Schema::hasColumn('users', 'is_banned')) {
            DB::statement("UPDATE users SET status = CASE WHEN is_banned = 1 THEN 'banned' ELSE 'active' END");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['status', 'ban_until', 'admin_note', 'last_active_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
