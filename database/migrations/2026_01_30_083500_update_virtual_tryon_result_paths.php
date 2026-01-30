<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing virtual_tryons records to use new path format
        DB::table('virtual_tryons')
            ->where('result_image', 'like', 'virtual-tryon/results/%')
            ->update([
                'result_image' => DB::raw('REPLACE(result_image, "virtual-tryon/results/", "uploads/virtual-tryon/results/")')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to old path format
        DB::table('virtual_tryons')
            ->where('result_image', 'like', 'uploads/virtual-tryon/results/%')
            ->update([
                'result_image' => DB::raw('REPLACE(result_image, "uploads/virtual-tryon/results/", "virtual-tryon/results/")')
            ]);
    }
};
