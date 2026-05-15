<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('workshops', 'is_active')) {
            Schema::table('workshops', function (Blueprint $table) {
                $table->tinyInteger('is_active')->default(1)->after('timezone');
            });
        }

        if (Schema::hasColumn('workshops', 'status')) {
            DB::table('workshops')->update([
                'is_active' => DB::raw("case when status = 'active' then 1 else 0 end"),
            ]);

            Schema::table('workshops', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('workshops', 'status')) {
            Schema::table('workshops', function (Blueprint $table) {
                $table->string('status')->default('active')->after('timezone');
            });
        }

        if (Schema::hasColumn('workshops', 'is_active')) {
            DB::table('workshops')->update([
                'status' => DB::raw("case when is_active = 1 then 'active' else 'inactive' end"),
            ]);

            Schema::table('workshops', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
