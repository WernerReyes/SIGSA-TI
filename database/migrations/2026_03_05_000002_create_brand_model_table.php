<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('brand_model');
        Schema::dropIfExists('asset_type_brand');
    }

    public function down(): void
    {
        Schema::create('asset_type_brand', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_type_id')->constrained('assets_type')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['asset_type_id', 'brand_id']);
        });

        Schema::create('brand_model', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->foreignId('model_id')->constrained('models')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['brand_id', 'model_id']);
        });
    }
};
