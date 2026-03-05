<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('asset_type_brand')) {
            Schema::create('asset_type_brand', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_type_id')->constrained('assets_type')->cascadeOnDelete();
                $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['asset_type_id', 'brand_id']);
            });
        }

        if (Schema::hasColumn('models', 'asset_type_id') && Schema::hasColumn('models', 'brand_id')) {
            DB::table('models')
                ->select('asset_type_id', 'brand_id')
                ->whereNotNull('asset_type_id')
                ->whereNotNull('brand_id')
                ->distinct()
                ->orderBy('asset_type_id')
                ->chunk(200, function ($rows) {
                    foreach ($rows as $row) {
                        DB::table('asset_type_brand')->updateOrInsert(
                            [
                                'asset_type_id' => $row->asset_type_id,
                                'brand_id' => $row->brand_id,
                            ],
                            [
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        );
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_type_brand');
    }
};
