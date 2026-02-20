<?php

use App\Enums\Asset\AssetTypeCategory;
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
        Schema::create('assets_type', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // $table->boolean('is_accessory')->default(false);
            $table->boolean('is_deletable')->default(true);
            $table->enum('doc_category', AssetTypeCategory::values())->default(AssetTypeCategory::LAPTOP->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_type');
    }
};
