<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('brand_model')) {
            Schema::drop('brand_model');
        }

        if (Schema::hasTable('asset_type_brand')) {
            Schema::drop('asset_type_brand');
        }

        if (!Schema::hasColumn('models', 'brand_id')) {
            Schema::table('models', function (Blueprint $table) {
                $table->foreignId('brand_id')->nullable()->after('name')->constrained('brands')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('models', 'asset_type_id')) {
            Schema::table('models', function (Blueprint $table) {
                $table->foreignId('asset_type_id')->nullable()->after('brand_id')->constrained('assets_type')->nullOnDelete();
            });
        }

        try {
            Schema::table('models', function (Blueprint $table) {
                $table->dropUnique('models_name_unique');
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

    public function down(): void
    {
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

        if (Schema::hasColumn('models', 'brand_id')) {
            Schema::table('models', function (Blueprint $table) {
                $table->dropConstrainedForeignId('brand_id');
            });
        }

        try {
            Schema::table('models', function (Blueprint $table) {
                $table->unique('name');
            });
        } catch (\Throwable $e) {
        }
    }
};
