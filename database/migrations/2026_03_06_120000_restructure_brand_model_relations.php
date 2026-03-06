<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('brands', 'type_id')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->foreignId('type_id')->nullable()->after('name')->constrained('assets_type')->nullOnDelete();
            });
        }

        try {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropUnique('brands_name_unique');
            });
        } catch (\Throwable $e) {
        }

        $brandRows = DB::table('brands')->select('id', 'name', 'created_at', 'updated_at')->get()->keyBy('id');
        $pairs = collect();

        if (Schema::hasTable('asset_type_brand')) {
            $pairs = $pairs->merge(
                DB::table('asset_type_brand')
                    ->select('brand_id', 'asset_type_id')
                    ->whereNotNull('brand_id')
                    ->whereNotNull('asset_type_id')
                    ->get()
            );
        }

        if (Schema::hasColumn('models', 'asset_type_id')) {
            $pairs = $pairs->merge(
                DB::table('models')
                    ->select('brand_id', 'asset_type_id')
                    ->whereNotNull('brand_id')
                    ->whereNotNull('asset_type_id')
                    ->get()
            );
        }

        if (Schema::hasColumn('assets', 'type_id')) {
            $pairs = $pairs->merge(
                DB::table('assets')
                    ->select('brand_id', DB::raw('type_id as asset_type_id'))
                    ->whereNotNull('brand_id')
                    ->whereNotNull('type_id')
                    ->get()
            );
        }

        $pairs = $pairs
            ->filter(fn($row) => !empty($row->brand_id) && !empty($row->asset_type_id))
            ->unique(fn($row) => $row->brand_id . '-' . $row->asset_type_id)
            ->values();

        $pairsByBrand = $pairs->groupBy('brand_id');

        foreach ($pairsByBrand as $oldBrandId => $rows) {
            $brand = $brandRows->get((int) $oldBrandId);
            if (!$brand) {
                continue;
            }

            $typeIds = collect($rows)->pluck('asset_type_id')->filter()->unique()->values();
            if ($typeIds->isEmpty()) {
                continue;
            }

            $primaryTypeId = (int) $typeIds->shift();

            DB::table('brands')
                ->where('id', (int) $oldBrandId)
                ->update([
                    'type_id' => $primaryTypeId,
                    'updated_at' => now(),
                ]);

            $mapping = [
                $primaryTypeId => (int) $oldBrandId,
            ];

            foreach ($typeIds as $typeId) {
                $newBrandId = DB::table('brands')
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $brand->name)])
                    ->where('type_id', (int) $typeId)
                    ->value('id');

                if (!$newBrandId) {
                    $newBrandId = DB::table('brands')->insertGetId([
                        'name' => $brand->name,
                        'type_id' => (int) $typeId,
                        'created_at' => $brand->created_at ?? now(),
                        'updated_at' => now(),
                    ]);
                }

                $mapping[(int) $typeId] = (int) $newBrandId;
            }

            foreach ($mapping as $typeId => $targetBrandId) {
                if ($targetBrandId === (int) $oldBrandId) {
                    continue;
                }

                if (Schema::hasColumn('models', 'asset_type_id')) {
                    DB::table('models')
                        ->where('brand_id', (int) $oldBrandId)
                        ->where('asset_type_id', (int) $typeId)
                        ->update(['brand_id' => (int) $targetBrandId, 'updated_at' => now()]);
                }

                DB::table('assets')
                    ->where('brand_id', (int) $oldBrandId)
                    ->where('type_id', (int) $typeId)
                    ->update(['brand_id' => (int) $targetBrandId, 'updated_at' => now()]);
            }
        }

        $orphans = DB::table('brands')->whereNull('type_id')->select('id')->get();
        foreach ($orphans as $orphan) {
            $inferredTypeId = null;

            if (Schema::hasColumn('models', 'asset_type_id')) {
                $inferredTypeId = DB::table('models')
                    ->where('brand_id', $orphan->id)
                    ->whereNotNull('asset_type_id')
                    ->value('asset_type_id');
            }

            if (!$inferredTypeId) {
                $inferredTypeId = DB::table('assets')
                    ->where('brand_id', $orphan->id)
                    ->whereNotNull('type_id')
                    ->value('type_id');
            }

            if ($inferredTypeId) {
                DB::table('brands')
                    ->where('id', $orphan->id)
                    ->update(['type_id' => $inferredTypeId, 'updated_at' => now()]);
            }
        }

        if (Schema::hasTable('asset_type_brand')) {
            Schema::drop('asset_type_brand');
        }

        try {
            Schema::table('brands', function (Blueprint $table) {
                $table->unique(['name', 'type_id'], 'brands_name_type_unique');
            });
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('models', function (Blueprint $table) {
                $table->dropUnique('models_name_brand_type_unique');
            });
        } catch (\Throwable $e) {
        }

        if (Schema::hasColumn('models', 'asset_type_id')) {
            Schema::table('models', function (Blueprint $table) {
                $table->dropConstrainedForeignId('asset_type_id');
            });
        }

        try {
            Schema::table('models', function (Blueprint $table) {
                $table->unique(['name', 'brand_id'], 'models_name_brand_unique');
            });
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
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

        if (!Schema::hasColumn('models', 'asset_type_id')) {
            Schema::table('models', function (Blueprint $table) {
                $table->foreignId('asset_type_id')->nullable()->after('brand_id')->constrained('assets_type')->nullOnDelete();
            });
        }

        try {
            Schema::table('models', function (Blueprint $table) {
                $table->dropUnique('models_name_brand_unique');
            });
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('models', function (Blueprint $table) {
                $table->unique(['name', 'brand_id', 'asset_type_id'], 'models_name_brand_type_unique');
            });
        } catch (\Throwable $e) {
        }
    }
};
